<?php

namespace PSWS\Field;

final class TranslatableField extends BaseField
{
    /**
     * @var int[]
     */
    private static $languageIDs = [];

    /**
     * @param int $mode
     */
    public function __construct($mode = 0)
    {
        parent::__construct($mode);

        foreach (static::$languageIDs as $languageID) {
            $this->value[$languageID] = null;
        }
    }

    /**
     * @param string[] $value
     */
    public function set($value)
    {
        if (!is_array($value)) {
            $value = array_map(function() use ($value) {
                return $value;
            }, $this->value);
        }

        foreach (array_keys($this->value) as $languageID) {
            $this->value[$languageID] = isset($value[$languageID])
                ? $value[$languageID]
                : null;
        }
    }

    /**
     * @return mixed
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * @return int[]
     */
    public static function getLanguageIDs()
    {
        return static::$languageIDs;
    }

    /**
     * @param int[] $languageIDs
     */
    public static function setLanguageIDs($languageIDs)
    {
        static::$languageIDs = $languageIDs;
    }
}