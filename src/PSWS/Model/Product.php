<?php

namespace PSWS\Model;

use PSWS\Field\AssociationField;
use PSWS\Field\BaseField;
use PSWS\Field\BooleanField;
use PSWS\Field\FloatField;
use PSWS\Field\IntegerField;
use PSWS\Field\StringField;
use PSWS\Field\TranslatableField;

/**
 * @property integer           $id_manufacturer
 * @property integer           $id_category_default
 * @property boolean           $active
 * @property float             $price
 * @property boolean           $show_price
 * @property boolean           $available_for_order
 * @property string            $manufacturer_name
 * @property integer           $quantity
 * @property TranslatableField $name
 * @property TranslatableField $link_rewrite
 * @property TranslatableField $description
 * @property TranslatableField $description_short
 * @property TranslatableField $meta_title
 * @property TranslatableField $meta_description
 * @property TranslatableField $meta_keywords
 * @property AssociationField  $categories
 * @property AssociationField  $images
 * @property AssociationField  $combinations
 * @property AssociationField  $product_option_values
 * @property AssociationField  $product_features
 * @property AssociationField  $tags
 * @property AssociationField  $stock_availables
 * @property AssociationField  $accessories
 * @property AssociationField  $product_bundle
 */
final class Product extends BaseModel
{
    /**
     * @var IntegerField
     */
    protected $id_manufacturer;

    /**
     * @var IntegerField
     */
    protected $id_category_default;

    /**
     * @var BooleanField
     */
    protected $active;

    /**
     * @var FloatField
     */
    protected $price;

    /**
     * @var BooleanField
     */
    protected $show_price;

    /**
     * @var BooleanField
     */
    protected $available_for_order;

    /**
     * @var StringField
     */
    protected $manufacturer_name;

    /**
     * @var IntegerField
     */
    protected $quantity;

    /**
     * @var TranslatableField
     */
    protected $link_rewrite;

    /**
     * @var TranslatableField
     */
    protected $name;

    /**
     * @var TranslatableField
     */
    protected $description;

    /**
     * @var TranslatableField
     */
    protected $description_short;

    /**
     * @var TranslatableField
     */
    protected $meta_title;

    /**
     * @var TranslatableField
     */
    protected $meta_description;

    /**
     * @var TranslatableField
     */
    protected $meta_keywords;

    /**
     * @var AssociationField
     */
    protected $categories;

    /**
     * @var AssociationField
     */
    protected $images;

    /**
     * @var AssociationField
     */
    protected $combinations;

    /**
     * @var AssociationField
     */
    protected $product_option_values;

    /**
     * @var AssociationField
     */
    protected $product_features;

    /**
     * @var AssociationField
     */
    protected $tags;

    /**
     * @var AssociationField
     */
    protected $stock_availables;

    /**
     * @var AssociationField
     */
    protected $accessories;

    /**
     * @var AssociationField
     */
    protected $product_bundle;

    public function __construct()
    {
        parent::__construct();

        $this->id_manufacturer       = new IntegerField();
        $this->id_category_default   = new IntegerField();
        $this->active                = new BooleanField();
        $this->price                 = new FloatField(BaseField::IS_REQUIRED);
        $this->show_price            = new BooleanField();
        $this->available_for_order   = new BooleanField();
        $this->manufacturer_name     = new StringField(BaseField::IS_READ_ONLY);
        $this->quantity              = new IntegerField(BaseField::IS_READ_ONLY);
        $this->link_rewrite          = new TranslatableField(BaseField::IS_REQUIRED);
        $this->name                  = new TranslatableField(BaseField::IS_REQUIRED);
        $this->description           = new TranslatableField();
        $this->description_short     = new TranslatableField();
        $this->meta_title            = new TranslatableField();
        $this->meta_description      = new TranslatableField();
        $this->meta_keywords         = new TranslatableField();
        $this->categories            = new AssociationField('category');
        $this->images                = new AssociationField('image');
        $this->combinations          = new AssociationField('combination');
        $this->product_option_values = new AssociationField('product_option_value');
        $this->product_features      = new AssociationField('product_feature');
        $this->tags                  = new AssociationField('tag');
        $this->stock_availables      = new AssociationField('stock_available');
        $this->accessories           = new AssociationField('product');
        $this->product_bundle        = new AssociationField('product');
    }
}