<?php
/**
 * SunNY Creative Technologies
 *
 *   #####                                ##     ##    ##      ##
 * ##     ##                              ###    ##    ##      ##
 * ##                                     ####   ##     ##    ##
 * ##           ##     ##    ## #####     ## ##  ##      ##  ##
 *   #####      ##     ##    ###    ##    ##  ## ##       ####
 *        ##    ##     ##    ##     ##    ##   ####        ##
 *        ##    ##     ##    ##     ##    ##    ###        ##
 * ##     ##    ##     ##    ##     ##    ##     ##        ##
 *   #####        #######    ##     ##    ##     ##        ##
 *
 * C  R  E  A  T  I  V  E     T  E  C  H  N  O  L  O  G  I  E  S
 */

require_once 'PSWS_Model_Association.php';
require_once 'PSWS_Model_Translatable.php';

abstract class PSWS_Model_Base
{
    /**
     * @var string[]
     */
    protected static $required = [];

    /**
     * @var string[]
     */
    protected static $read_only = [];

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $external_id;

    /**
     * @var bool
     */
    private $processed = false;

    /**
     * @param string $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return property_exists($this, $name);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return property_exists($this, $name) ? $this->{$name} : null;
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        if (!property_exists($this, $name)) {
            return;
        }

        if ($this->{$name} instanceof PSWS_Model_Translatable) {
            $this->{$name}->value($value);
        }
    }

    /**
     * @param \SimpleXMLElement|\SimpleXMLElement[] $element
     */
    public function fillXML(\SimpleXMLElement $element)
    {
        $this->id = (string) $element->id;

        foreach (static::$read_only as $property) {
            unset($element->{$property});
        }

        foreach (get_object_vars($this) as $property => $value) {
            if (in_array($property, ['processed', 'external_id'], false) || in_array($property, static::$read_only, false)) {
                continue;
            }

            if ($value instanceof PSWS_Model_Base) {
                $value = (string) $value->id;
            }

            if ($value instanceof PSWS_Model_Translatable) {
                unset($element->{$property});

                /* @var $node \SimpleXMLElement */
                $node = $element->addChild($property);
                foreach ($value->all() as $languageID => $translation) {
                    $language    = $node->addChild('language');
                    $language[0] = $translation;

                    $language->addAttribute('id', $languageID);
                }
            } else if ($value instanceof PSWS_Model_Association) {
                // Collect new ids
                $ids = [];
                foreach ($value->all() as $association) {
                    if ($association instanceof PSWS_Model_Base) {
                        $association = (string) $association->id;
                    }

                    $ids[$association] = true;
                }

                // Ensure node exists
                if (!isset($element->associations->{$property})) {
                    $element->associations->addChild($property);
                }

                // Collect ids to remove and skip
                $remove = $exists = [];
                foreach ($element->associations->{$property}->children() as $child) {
                    $id = (string) $child->id;

                    if (!array_key_exists($id, $ids)) {
                        $remove[] = $child;
                    } else {
                        $exists[$id] = true;
                    }
                }

                // Remove old ids
                foreach ($remove as $node) {
                    unset($node[0]);
                }

                // Add new ids
                foreach (array_keys($ids) as $id) {
                    if (!array_key_exists($id, $exists)) {
                        $element->associations->{$property}->addChild($value->getNodeType())->addChild('id', $id);
                    }
                }

                //unset($element->associations->{$property});

                /* @var $node \SimpleXMLElement */
                //$node = $element->associations->addChild($property);

                /*foreach ($value->all() as $association) {
                    if ($association instanceof PSWS_Model_Base) {
                        $association = (string) $association->id;
                    }

                    $node->addChild($value->getNodeType())->addChild('id', $association);
                }*/

                if (!count($element->associations->{$property}->children())) {
                    $element->associations->{$property}->addChild($value->getNodeType())->addChild('id', 0);
                }
            } else {
                $element->{$property} = $this->{$property} = $value;
            }
        }
    }

    /**
     * @return bool
     */
    public function isProcessed()
    {
        return $this->processed;
    }

    /**
     * @param bool $flag
     */
    public function setProcessed($flag = true)
    {
        $this->processed = (bool) $flag;
    }
}