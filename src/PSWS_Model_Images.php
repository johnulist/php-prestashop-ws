<?php

require_once __DIR__ . '/PSWS_Model_Base.php';
require_once __DIR__ . '/PSWS_Model_ImagesFile.php';

final class PSWS_Model_Images extends PSWS_Model_Base
{
    /**
     * Entity multiple name to manage image of
     *
     * @var string
     */
    public $resourceNameMultiple;

    /**
     * Entity single name to manage image of
     *
     * @var string
     */
    public $resourceNameSingle;

    /**
     * Entity name to manage image of
     *
     * @var string
     */
    public $resourceID;

    /**
     * @var PSWS_Model_ImagesFile[]
     */
    public $files = [];
}