<?php

namespace PSWS\Model;

use PSWS\Field\AssociationField;
use PSWS\Field\BaseField;
use PSWS\Field\BooleanField;
use PSWS\Field\StringField;
use PSWS\Field\TranslatableField;

/**
 * @property boolean           $active;
 * @property string            $link_rewrite;
 * @property string            $name;
 * @property string            $date_add;
 * @property string            $date_upd;
 * @property TranslatableField $description
 * @property TranslatableField $short_description
 * @property TranslatableField $meta_title
 * @property TranslatableField $meta_description
 * @property TranslatableField $meta_keywords
 * @property AssociationField  $addresses
 */
final class Manufacturer extends BaseModel
{
    /**
     * @var BooleanField
     */
    protected $active;

    /**
     * @var StringField
     */
    protected $link_rewrite;

    /**
     * @var StringField
     */
    protected $name;

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
    protected $description;

    /**
     * @var TranslatableField
     */
    protected $short_description;

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
    protected $addresses;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();

        $this->active            = new BooleanField();
        $this->link_rewrite      = new StringField(BaseField::IS_READ_ONLY);
        $this->name              = new StringField(BaseField::IS_REQUIRED);
        $this->date_add          = new StringField();
        $this->date_upd          = new StringField();
        $this->description       = new TranslatableField();
        $this->short_description = new TranslatableField();
        $this->meta_title        = new TranslatableField();
        $this->meta_description  = new TranslatableField();
        $this->meta_keywords     = new TranslatableField();
        $this->addresses         = new AssociationField('address');
    }
}