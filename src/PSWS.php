<?php

class PSWS_Client
{
    const RETRIES = 5;

    const DELAY = 200;

    const REDIRECTS = 5;

    const PS_COMPATIBLE_VERSION_MIN = '1.4.0';
    const PS_COMPATIBLE_VERSION_MAX = '1.6.2';

    /**
     * API endpoint url
     *
     * @var string
     */
    private $url;

    /**
     * API access key
     *
     * @var string
     */
    private $key;

    /**
     * @var PSWS_Cache
     */
    private $cache;

    /**
     * @var string
     */
    private $message;

    /**
     * Available parameters names for GET method
     *
     * @var array
     */
    private static $availableGETParams = array(
        'id',
        'schema',
        'filter',
        'display',
        'sort',
        'limit',
        'id_shop',
        'id_group_shop',
    );

    /**
     * CURL default options
     *
     * @var array
     */
    private static $defaults = array(
        CURLOPT_HEADER         => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLINFO_HEADER_OUT    => true,
    );

    /**
     * PSWS_Client constructor.
     *
     * @param string $url
     * @param string $key
     */
    public function __construct($url, $key)
    {
        $this->url = rtrim($url, '\\/');
        $this->key = $key;

        $this->cache = new PSWS_Cache();
    }

    /**
     * Get single resource or list
     *
     * @param string   $resource Resource path
     * @param int|null $id       Resource ID
     * @param array    $params   HTTP query params array
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function get($resource, $id = null, array $params = array())
    {
        if (!is_string($resource) || $resource === '') {
            throw new \InvalidArgumentException('Resource path must be a non empty string');
        }

        if (null !== $id) {
            if (!is_numeric($id)) {
                throw new \InvalidArgumentException('Resource ID must be a integer or numeric string');
            }

            if ($this->cache->has($resource, (string) $id)) {
                return $this->cache->get($resource, (string) $id);
            }

            $resource .= '/' . $id;
        }

        $urlParams = array();
        foreach (static::$availableGETParams as $name) {
            foreach ($params as $key => $val) {
                if (strpos($key, $name) !== false) {
                    $urlParams[$key] = $val;
                }
            }
        }

        if (count($urlParams)) {
            $resource .= '?' . http_build_query($urlParams);
        }

        $data = $this->execute(
            $resource,
            array(CURLOPT_CUSTOMREQUEST => 'GET')
        );

        if (null !== $id) {
            $this->cache->set($resource, (string) $id, $data);
        }

        return $data;
    }

    /**
     * Create resource
     *
     * @param  string                  $resource Resource path
     * @param  array|\SimpleXMLElement $data     SimpleXMLElement object or raw array for CURLOPT_POSTFIELDS
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function create($resource, $data)
    {
        $this->message = null;

        if (!is_string($resource) || $resource === '') {
            throw new \InvalidArgumentException('Resource path must be a non empty string');
        }

        return $this->execute(
            $resource,
            array(
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS    => PSWS_Util::prepareRequestData($data),
            )
        );
    }

    /**
     * Update resource
     *
     * @param  string                  $resource Resource path
     * @param  int|string              $id       Resource ID
     * @param  array|\SimpleXMLElement $data     SimpleXMLElement object or raw array for CURLOPT_POSTFIELDS
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function update($resource, $id, $data)
    {
        $this->message = null;

        if (!is_string($resource) || $resource === '') {
            throw new \InvalidArgumentException('Resource path must be a non empty string');
        }

        if (!is_numeric($id)) {
            throw new \InvalidArgumentException('Resource ID must be a integer or numeric string');
        }

        $this->cache->delete($resource, (string) $id);

        return $this->execute(
            $resource . '/' . $id,
            array(
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_POSTFIELDS    => PSWS_Util::prepareRequestData($data),
            )
        );
    }

    /**
     * Delete resource(s)
     *
     * @param string       $resource Resource path
     * @param string|array $id       Resource ID
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function delete($resource, $id)
    {
        $this->message = null;

        if (!is_string($resource) || $resource === '') {
            throw new \InvalidArgumentException('Resource path must be a non empty string');
        }

        if (!is_numeric($id) && !is_array($id)) {
            throw new \InvalidArgumentException('Resource ID must be a integer or numeric string or an non empty array of these');
        }

        if (!is_array($id)) {
            $this->cache->delete($resource, (string) $id);
            $path = $resource . '/' . $id;
        } else {
            if (!count($id)) {
                throw new \InvalidArgumentException('Resource ID must be an non empty array');
            }

            foreach ($id as $item) {
                $this->cache->delete($resource, (string) $item);
            }

            $path = $resource . '/?id=[' . implode(',', $id) . ']';
        }

        return $this->execute($path, array(CURLOPT_CUSTOMREQUEST => 'DELETE'));
    }

    /**
     * Get resource schema
     *
     * @param string $resource Resource path
     * @param string $type     Schema type
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function schema($resource, $type = 'blank')
    {
        $this->message = null;

        if (!in_array($type, array('blank', 'synopsis'), true)) {
            throw new \InvalidArgumentException(
                'Schema type allowed values is "blank" or "synopsis"'
            );
        }

        if ($this->cache->has($resource, 'schema' . $type)) {
            return $this->cache->get($resource, 'schema' . $type);
        }

        $data = $this->get($resource, null, array('schema' => $type));

        $this->cache->set($resource, 'schema' . $type, $data);

        return $data;
    }

    /**
     * Execute request
     *
     * @param string $path
     * @param array  $options
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException         If request fails
     */
    private function execute($path, array $options = [])
    {
        if (false === strpos($path, '?')) {
            $path .= '?ws_key=' . $this->key;
        } else {
            $path .= '&ws_key=' . $this->key;
        }

        $session = curl_init($this->url . '/api/' . $path);

        $this->message = (@$options[CURLOPT_CUSTOMREQUEST] ?: 'GET') . ': ' . $path;

        // Merge options with preserving keys
        $curlOptions = static::$defaults;

        foreach ($options as $key => $val) {
            $curlOptions[$key] = $val;
        }

        $curlOptions[CURLOPT_HTTPHEADER] = [
            'Expect:',
            //'Authorization: Basic '. base64_encode($this->key . ':'),
        ];

        curl_setopt_array($session, $curlOptions);

        $response = $this->executeRetries($session);

        $code  = (int) curl_getinfo($session, CURLINFO_HTTP_CODE);
        $error = curl_error($session);

        curl_close($session);

        if ($code === 0) {
            throw new \RuntimeException('CURL Error: ' . $error);
        }

        $index = strpos($response, "\r\n\r\n");

        $headers = PSWS_Util::parseHeaders($index !== false ? substr($response, 0, $index) : $response);
        $content = $index !== false ? substr($response, $index + 4) : null;

        if (null === $content && $curlOptions[CURLOPT_CUSTOMREQUEST] !== 'HEAD') {
            throw new \RuntimeException('Bad HTTP response');
        }

        if (array_key_exists('PSWS-Version', $headers)) {
            if (
                version_compare(static::PS_COMPATIBLE_VERSION_MIN, $headers['PSWS-Version']) === 1 ||
                version_compare(static::PS_COMPATIBLE_VERSION_MAX, $headers['PSWS-Version']) === -1
            ) {

                throw new \RuntimeException(sprintf(
                    'Incompatible version of PrestaShop, min supported %s, max supported %s',
                    static::PS_COMPATIBLE_VERSION_MIN,
                    static::PS_COMPATIBLE_VERSION_MAX
                ));
            }
        }

        $content = PSWS_Util::prepareResponseData($content, @$curlOptions[CURLOPT_POSTFIELDS]);

        $this->message .= ' OK';

        if ($code >= 200 && $code < 400 && $code !== 204) {
            return $content;
        }

        if ($code === 404) {
            return null;
        }

        throw new \RuntimeException(PSWS_Util::getExceptionMessageFromCode($code) . htmlentities((string) $content->error), $code);
    }

    /**
     * Execute curl session with retry support
     *
     * @param $session
     *
     * @return string
     */
    private function executeRetries($session)
    {
        $retries  = 0;
        $response = $this->executeRedirects($session);

        while (curl_getinfo($session, CURLINFO_HTTP_CODE) === 500 && $retries < static::RETRIES) {
            usleep(static::DELAY * 1000);
            $response = $this->executeRedirects($session);
            $retries++;
        }

        return $response;
    }

    /**
     * Execute curl session with redirects support
     *
     * @param $session
     *
     * @return string
     */
    private function executeRedirects($session)
    {
        $redirects = 0;
        $response  = curl_exec($session);

        while (in_array(curl_getinfo($session, CURLINFO_HTTP_CODE), [301, 302], false) && $redirects < static::REDIRECTS) {
            $response = curl_exec($session);
            $redirects++;
        }

        return $response;
    }

    /**
     * Fill multilingual field values
     *
     * @param \SimpleXMLElement   $dstField
     * @param null|string         $default
     * @param null|array|callable $values
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function fillMultilingualFieldValues(\SimpleXMLElement $dstField, $default = null, $values = null)
    {
        foreach ($dstField->children() as $language) {
            $languageCode = (string) $this->get('languages', (string) $language['id'])->language->language_code;

            $value = (string) $default;

            if (is_callable($values)) {
                $value = (string) $values($languageCode);
            } else if (is_array($values) && isset($values[$languageCode])) {
                $value = (string) $values[$languageCode];
            }

            dom_import_simplexml($language)->nodeValue = $value;
        }
    }

    /**
     * Find resource ID by field - value pair
     *
     * @param string $resource
     * @param string $field
     * @param string $value
     *
     * @return string|null
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function findResourceIDByField($resource, $field, $value)
    {
        $response = $this->get($resource, null, array(
            'display'                => '[id,'. $field .']',
            'filter[' . $field . ']' => '[' . $value . ']',
        ));

        if (isset($response->{$resource})) {
            /* @var $records \SimpleXMLElement */
            $records = $response->{$resource};
            foreach ($records->children() as $record) {
                $id = (string) $record->id;
                if ($id) {
                    return $id;
                }
            }
        }

        return null;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}

class PSWS_Util
{
    /**
     * @param mixed $data
     *
     * @return string|array
     *
     * @throws \InvalidArgumentException If data is not has valid type
     */
    public static function prepareRequestData($data)
    {
        if ($data instanceof \SimpleXMLElement) {
            return $data->asXML();
        }

        if (is_array($data)) {
            foreach ((array) $data as $key => $value) {
                if (is_string($value) && '@' === @$value[0]) {
                    $data[$key] = curl_file_create(substr($value, 1));
                }
            }

            return $data;
        }

        throw new \InvalidArgumentException(
            'Data is required and must be instance of SimpleXMLElement or an array'
        );
    }

    /**
     * Convert response to XML if possible
     *
     * @param mixed $data
     * @param mixed $request
     *
     * @return null|SimpleXMLElement
     *
     * @throws \RuntimeException If errors occurred in response xml
     */
    public static function prepareResponseData($data, $request)
    {
        if (is_string($data)) {
            $data = static::parseXML($data);

            if (null !== $data && count($errors = static::parseErrors($data))) {
                throw new \RuntimeException('There are errors: ' . implode('; ', $errors) . (is_array($request) ? var_export($request, true) : htmlspecialchars($request)));
            }
        }

        return $data;
    }

    /**
     * Parse response error xml to exceptions array
     *
     * @param \SimpleXMLElement $xml
     *
     * @return array
     */
    private static function parseErrors(\SimpleXMLElement $xml)
    {
        $errors = array();

        if (isset($xml->errors)) {
            foreach ($xml->errors->children() as $error) {
                $errors[] = $error->message;
            }
        }

        return $errors;
    }

    /**
     * Parse xml from string, only if it contains xml header
     *
     * @param null $content
     *
     * @return null|\SimpleXMLElement
     *
     * @throws \RuntimeException If errors occurred during parse xml string
     */
    public static function parseXML($content = null)
    {
        if (0 !== strpos($content, '<?xml')) {
            // If content is not xml - do nothing
            return null;
        }

        libxml_clear_errors();
        libxml_use_internal_errors(true);

        $xml    = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);
        $errors = libxml_get_errors();

        if (count($errors)) {
            $message = array_map(function(\LibXMLError $error){
                return $error->message;
            }, $errors);

            libxml_clear_errors();
            throw new \RuntimeException("XML response is not parsable:\n" . implode("\n", $message));
        }

        return $xml;
    }

    /**
     * Parse headers from string
     *
     * @param string $headers
     *
     * @return array
     *
     * @throws \InvalidArgumentException If headers is not raw string
     */
    public static function parseHeaders($headers)
    {
        if (!is_string($headers)) {
            throw new \InvalidArgumentException('$headers must be a string');
        }

        $headersSrc = explode("\n", $headers);
        $headersDst = array();

        foreach ($headersSrc as $header) {
            $header = array_map('trim', explode(':', $header));
            if (count($header) === 2) {
                $headersDst[$header[0]] = $header[1];
            }
        }

        return $headersDst;
    }

    /**
     * Get exception message from passed HTTP status code
     *
     * @param int $code
     *
     * @return string
     */
    public static function getExceptionMessageFromCode($code)
    {
        $message = 'Error while execute request, code: ' . (int) $code;

        switch ((int) $code) {
            case 200:
            case 201:
                break;
            case 204:
                $message = 'No content';
                break;
            case 400:
                $message = 'Bad Request';
                break;
            case 401:
                $message = 'Unauthorized';
                break;
            case 404:
                $message = 'Not Found';
                break;
            case 405:
                $message = 'Method Not Allowed';
                break;
            case 500:
                $message = 'Internal Server Error'
                ;break;
        }

        return $message;
    }

    /**
     * Remove read only fields from xml based on schema xml
     *
     * NOTE: no recursive
     *
     * @param \SimpleXMLElement|\SimpleXMLElement[] $dst
     * @param \SimpleXMLElement|\SimpleXMLElement[] $schema
     */
    public static function removeReadOnlyXML(\SimpleXMLElement $dst, \SimpleXMLElement $schema)
    {
        $fieldsToDelete = array();

        /* @var $field \SimpleXMLElement */
        foreach ($dst->children() as $key => $field) {
            if (
                (string) $schema->{$field->getName()}['read_only'] === 'true' ||
                (string) $schema->{$field->getName()}['readOnly'] === 'true'
            ) {
                $fieldsToDelete[] = $field->getName();
            }
        }

        foreach ($fieldsToDelete as $fieldToDelete) {
            unset($dst->{$fieldToDelete});
        }
    }
}

class PSWS_Cache
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @param string $resource
     * @param int    $id
     *
     * @return bool
     */
    public function has($resource, $id)
    {
        return array_key_exists($id, (array) @$this->data[$resource]);
    }

    /**
     * @param string $resource
     * @param int    $id
     *
     * @return mixed
     */
    public function get($resource, $id)
    {
        return @$this->data[$resource][$id];
    }

    /**
     * @param string $resource
     * @param int    $id
     * @param mixed  $data
     */
    public function set($resource, $id, $data)
    {
        $this->data[$resource][$id] = $data;
    }

    /**
     * @param string $resource
     * @param int    $id
     */
    public function delete($resource, $id)
    {
        unset($this->data[$resource][$id]);
    }
}
