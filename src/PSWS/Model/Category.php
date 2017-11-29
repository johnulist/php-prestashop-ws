<?php

namespace PSWS\Model;

use PSWS\Field\AssociationField;
use PSWS\Field\BaseField;
use PSWS\Field\BooleanField;
use PSWS\Field\IntegerField;
use PSWS\Field\StringField;
use PSWS\Field\TranslatableField;

/**
 * @property integer           $id_parent;
 * @property integer           $level_depth;
 * @property integer           $nb_products_recursive;
 * @property boolean           $active;
 * @property integer           $id_shop_default;
 * @property boolean           $is_root_category;
 * @property integer           $position;
 * @property string            $date_add;
 * @property string            $date_upd;
 * @property TranslatableField $name
 * @property TranslatableField $link_rewrite
 * @property TranslatableField $description
 * @property TranslatableField $meta_title
 * @property TranslatableField $meta_description
 * @property TranslatableField $meta_keywords
 * @property AssociationField  $categories
 * @property AssociationField  $products
 */
final class Category extends BaseModel
{
    /**
     * @var IntegerField
     */
    protected $id_parent;

    /**
     * @var IntegerField
     */
    protected $level_depth;

    /**
     * @var IntegerField
     */
    protected $nb_products_recursive;

    /**
     * @var BooleanField
     */
    protected $active;

    /**
     * @var IntegerField
     */
    protected $id_shop_default;

    /**
     * @var BooleanField
     */
    protected $is_root_category;

    /**
     * @var IntegerField
     */
    protected $position;

    /**
     * @var StringField
     */
    protected $date_add;

    /**
     * @var StringField
     */
    protected $date_upd;

    /**
     * @var TranslatableField
     */
    protected $name;

    /**
     * @var TranslatableField
     */
    protected $link_rewrite;

    /**
     * @var TranslatableField
     */
    protected $description;

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
    protected $products;

    public function __construct()
    {
        parent::__construct();

        $this->id_parent             = new IntegerField();
        $this->level_depth           = new IntegerField(BaseField::IS_READ_ONLY);
        $this->nb_products_recursive = new IntegerField(BaseField::IS_READ_ONLY);
        $this->active                = new BooleanField(BaseField::IS_REQUIRED);
        $this->id_shop_default       = new IntegerField();
        $this->is_root_category      = new BooleanField();
        $this->position              = new IntegerField();
        $this->date_add              = new StringField();
        $this->date_upd              = new StringField();
        $this->name                  = new TranslatableField(BaseField::IS_REQUIRED);
        $this->link_rewrite          = new TranslatableField(BaseField::IS_REQUIRED);
        $this->description           = new TranslatableField();
        $this->meta_title            = new TranslatableField();
        $this->meta_description      = new TranslatableField();
        $this->meta_keywords         = new TranslatableField();
        $this->categories            = new AssociationField('category');
        $this->products              = new AssociationField('products');
    }
}