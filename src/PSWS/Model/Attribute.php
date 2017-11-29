<?php

namespace PSWS\Model;

use PSWS\Field\BaseField;
use PSWS\Field\IntegerField;
use PSWS\Field\StringField;
use PSWS\Field\TranslatableField;

/**
 * @property integer           $id_attribute_group
 * @property string            $color
 * @property integer           $position
 * @property TranslatableField $name
 */
final class Attribute extends BaseModel
{
    /**
     * @var IntegerField
     */
    protected $id_attribute_group;

    /**
     * @var StringField
     */
    protected $color;

    /**
     * @var IntegerField
     */
    protected $position;

    /**
     * @var TranslatableField
     */
    protected $name;

    public function __construct()
    {
        parent::__construct();

        $this->id_attribute_group = new IntegerField(BaseField::IS_REQUIRED);
        $this->color              = new StringField();
        $this->position           = new IntegerField();
        $this->name               = new TranslatableField(BaseField::IS_REQUIRED);
    }
}