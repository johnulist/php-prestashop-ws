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

require_once __DIR__ . '/PSWS_Model_Base.php';

class PSWS_Model_StockAvailable extends PSWS_Model_Base
{
    /**
     * @var int
     */
    public $id_product;

    /**
     * @var int[]
     */
    private $stock = [];

    /**
     * @var PSWS_Model_Combination[]
     */
    private $refs = [];

    public function setQuantity(PSWS_Model_Combination $combination, $quantity)
    {
        $this->stock[$combination->external_id] = $quantity;
        $this->refs[$combination->external_id]  = $combination;
    }

    public function getQuantity($combinationID)
    {
        $key = null;
        foreach ($this->refs as $ref) {
            if ((int) $ref->id === (int) $combinationID) {
                $key = $ref->external_id;
            }
        }

        return $key && array_key_exists($key, $this->stock) ? $this->stock[$key] : 0;
    }
}