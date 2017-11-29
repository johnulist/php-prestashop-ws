<?php

namespace PSWS\Model;

use PSWS\Field\AssociationField;
use PSWS\Field\BaseField;
use PSWS\Field\BooleanField;
use PSWS\Field\EnumField;
use PSWS\Field\IntegerField;
use PSWS\Field\StringField;
use PSWS\Field\TranslatableField;

/**
 * @property boolean           $is_color_group
 * @property string            $group_type
 * @property integer           $position
 * @property TranslatableField $name
 * @property TranslatableField $public_name
 * @property AssociationField  $product_option_values
 */
final class AttributeGroup extends BaseModel
{
    /**
     * @var BooleanField
     */
    protected $is_color_group;

    /**
     * @var StringField
     */
    protected $group_type;

    /**
     * @var IntegerField
     */
    protected $position;

    /**
     * @var TranslatableField
     */
    protected $name;

    /**
     * @var TranslatableField
     */
    protected $public_name;

    /**
     * @var AssociationField
     */
    protected $product_option_values;

    public function __construct()
    {
        parent::__construct();

        $this->is_color_group        = new BooleanField();
        $this->group_type            = new EnumField(['select', 'radio', 'color'], BaseField::IS_REQUIRED);
        $this->position              = new IntegerField();
        $this->name                  = new TranslatableField(BaseField::IS_REQUIRED);
        $this->public_name           = new TranslatableField(BaseField::IS_REQUIRED);
        $this->product_option_values = new AssociationField('product_option_value');
    }
}