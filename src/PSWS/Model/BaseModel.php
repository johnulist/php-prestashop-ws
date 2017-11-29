<?php

namespace PSWS\Model;

use PSWS\Field\BaseField;
use PSWS\Field\IntegerField;
use PSWS\Field\SimpleField;
use PSWS\Field\StringField;

/**
 * @property integer $id
 * @property string  $external_id
 */
abstract class BaseModel
{
    /**
     * @var IntegerField
     */
    protected $id;

    /**
     * @var StringField
     */
    protected $external_id;

    public function __construct()
    {
        $this->id          = new IntegerField();
        $this->external_id = new StringField();
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    final public function __set($name, $value)
    {
        if (!property_exists($this, $name)) {
            throw new \InvalidArgumentException('Unknown property ' . $name);
        }

        $field = $this->{$name};

        if ($field instanceof BaseField) {
            $field->set($value);
        } else {
            $this->{$name} = $value;
        }
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    final public function __get($name)
    {
        if (!property_exists($this, $name)) {
            throw new \InvalidArgumentException('Unknown property ' . $name);
        }

        $field = $this->{$name};

        // All simple fields return raw value, other fields return instance
        return $field instanceof SimpleField
            ? $field->get()
            : $field;
    }
}