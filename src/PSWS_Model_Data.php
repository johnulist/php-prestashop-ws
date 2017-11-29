<?php
/**
 * SunNY Creative Technologies
 *
 *   #####                                ##     ##    ##      ##
 * ##     ##                              ###    ##    ##      ##
 * ##                                     ####   ##     ##    ##
 * ##           ##     ##    ## #####     ## ##  ##      ##  ##
 *   #####      ##     ##    ###    ##    ##  ## ##       ####
 *        ##    ##     ##    ##     ##    ##   ####        ##
 *        ##    ##     ##    ##     ##    ##    ###        ##
 * ##     ##    ##     ##    ##     ##    ##     ##        ##
 *   #####        #######    ##     ##    ##     ##        ##
 *
 * C  R  E  A  T  I  V  E     T  E  C  H  N  O  L  O  G  I  E  S
 */

require_once 'PSWS_Model_Attribute.php';
require_once 'PSWS_Model_AttributeGroup.php';
require_once 'PSWS_Model_Category.php';
require_once 'PSWS_Model_Combination.php';
require_once 'PSWS_Model_Images.php';
require_once 'PSWS_Model_Manufacturer.php';
require_once 'PSWS_Model_Product.php';
require_once 'PSWS_Model_Customer.php';
require_once 'PSWS_Model_StockAvailable.php';

final class PSWS_Model_Data
{
    /**
     * @var PSWS_Model_Category[]
     */
    public $categories = [];

    /**
     * @var PSWS_Model_Combination[]
     */
    public $combinations = [];

    /**
     * @var PSWS_Model_Manufacturer[]
     */
    public $manufacturers = [];

    /**
     * @var PSWS_Model_Attribute[]
     */
    public $product_option_values = [];

    /**
     * @var PSWS_Model_AttributeGroup[]
     */
    public $product_options = [];

    /**
     * @var PSWS_Model_Product[]
     */
    public $products = [];

    /**
     * @var PSWS_Model_StockAvailable[]
     */
    public $stock_availables = [];

    /**
     * @var PSWS_Model_Customer[]
     */
    public $customers = [];

    /**
     * @var PSWS_Model_Images[]
     */
    public $images = [];

    /**
     * @return bool
     */
    public function isProcessed()
    {
        /* @var $records PSWS_Model_Base[] */
        foreach (get_object_vars($this) as $type => $records) {
            foreach ($records as $record) {
                if (!$record->isProcessed()) {
                    return false;
                }
            }
        }

        return true;
    }
}