<?php

namespace PSWS\Field;

final class EnumField extends SimpleField
{
    /**
     * @var string[]
     */
    private $options;

    /**
     * @param string[] $options
     * @param integer  $mode
     */
    public function __construct(array $options, $mode = 0)
    {
        parent::__construct($mode);

        $this->options = $options;
    }

    /**
     * @inheritdoc
     */
    public function set($value)
    {
        if (!in_array($value, $this->options)) {
            throw new \InvalidArgumentException(sprintf('Only one of % allowed', json_encode($this->options)));
        }

        parent::set($value);
    }
}