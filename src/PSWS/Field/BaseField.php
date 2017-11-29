<?php

namespace PSWS\Field;

//TODO reference to model
abstract class BaseField
{
    const IS_REQUIRED  = 1;
    const IS_READ_ONLY = 2;

    /**
     * @var integer
     */
    private $mode;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @param integer $mode
     */
    public function __construct($mode = 0)
    {
        $this->mode = $mode;
    }

    /**
     * @param mixed $value
     */
    public function set($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return self::IS_REQUIRED === (self::IS_REQUIRED & $this->mode);
    }

    /**
     * @return boolean
     */
    public function isReadOnly()
    {
        return self::IS_READ_ONLY === (self::IS_READ_ONLY & $this->mode);
    }
}