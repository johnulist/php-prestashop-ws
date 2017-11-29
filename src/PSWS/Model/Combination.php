<?php

namespace PSWS\Model;
use PSWS\Field\AssociationField;
use PSWS\Field\BaseField;
use PSWS\Field\FloatField;
use PSWS\Field\IntegerField;

/**
 * @property integer          $id_product
 * @property integer          $minimal_quantity
 * @property float            $price
 * @property AssociationField $product_option_values
 * @property AssociationField $images
 */
final class Combination extends BaseModel
{
    /**
     * @var IntegerField
     */
    protected $id_product;

    /**
     * @var IntegerField
     */
    protected $minimal_quantity;

    /**
     * @var FloatField
     */
    protected $price;

    /**
     * @var AssociationField
     */
    protected $product_option_values;

    /**
     * @var AssociationField
     */
    protected $images;

    public function __construct()
    {
        parent::__construct();

        $this->id_product            = new IntegerField(BaseField::IS_REQUIRED);
        $this->minimal_quantity      = new IntegerField(BaseField::IS_REQUIRED);
        $this->price                 = new FloatField();
        $this->product_option_values = new AssociationField('product_option_value');
        $this->images                = new AssociationField('image');
    }
}