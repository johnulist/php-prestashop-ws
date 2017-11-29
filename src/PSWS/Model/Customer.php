<?php

namespace PSWS\Model;

use PSWS\Field\AssociationField;
use PSWS\Field\BaseField;
use PSWS\Field\BooleanField;
use PSWS\Field\StringField;

/**
 * @property string           $passwd
 * @property string           $lastname
 * @property string           $firstname
 * @property string           $email
 * @property boolean          $active
 * @property string           $last_passwd_gen
 * @property string           $secure_key
 * @property AssociationField $groups
 */
final class Customer extends BaseModel
{
    /**
     * @var StringField
     */
    protected $passwd;

    /**
     * @var StringField
     */
    protected $lastname;

    /**
     * @var StringField
     */
    protected $firstname;

    /**
     * @var StringField
     */
    protected $email;

    /**
     * @var BooleanField
     */
    protected $active;

    /**
     * @var StringField
     */
    protected $last_passwd_gen;

    /**
     * @var StringField
     */
    protected $secure_key;

    /**
     * @var AssociationField
     */
    public $groups;

    public function __construct()
    {
        parent::__construct();

        $this->passwd          = new StringField(BaseField::IS_REQUIRED);
        $this->lastname        = new StringField(BaseField::IS_REQUIRED);
        $this->firstname       = new StringField(BaseField::IS_REQUIRED);
        $this->email           = new StringField(BaseField::IS_REQUIRED);
        $this->active          = new BooleanField();
        $this->last_passwd_gen = new StringField(BaseField::IS_READ_ONLY);
        $this->secure_key      = new StringField(BaseField::IS_READ_ONLY);
        $this->groups          = new AssociationField('group');
    }
}