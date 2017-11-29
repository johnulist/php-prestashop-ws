<?php

namespace PSWS\Model;

final class Data
{
    /**
     * @var Category[]
     */
    public $categories = [];

    /**
     * @var Combination[]
     */
    public $combinations = [];

    /**
     * @var Manufacturer[]
     */
    public $manufacturers = [];

    /**
     * @var Attribute[]
     */
    public $product_option_values = [];

    /**
     * @var AttributeGroup[]
     */
    public $product_options = [];

    /**
     * @var Product[]
     */
    public $products = [];

    /**
     * @var StockAvailable[]
     */
    public $stock_availables = [];

    /**
     * @var Customer[]
     */
    public $customers = [];

    /**
     * @var Images[]
     */
    public $images = [];
}