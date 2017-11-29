<?php

namespace PSWS\Field;

final class AssociationField extends BaseField
{
    /**
     * @var string
     */
    private $node_type;

    /**
     * @param string $node_type
     */
    public function __construct($node_type)
    {
        parent::__construct();

        $this->value     = [];
        $this->node_type = $node_type;
    }

    /**
     * @return string
     */
    public function getNodeType()
    {
        return $this->node_type;
    }

    /**
     * @inheritdoc
     */
    public function set($value)
    {
        if (!in_array($value, $this->value, true)) {
            $this->value[] = $value;
        }
    }
}