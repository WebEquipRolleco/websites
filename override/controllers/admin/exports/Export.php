<?php

require_once("AttributeGroups.php");
require_once("AttributeValues.php");
require_once("Beezup.php");
require_once("Categories.php");
require_once("Comments.php");
require_once("FeatureGroups.php");
require_once("FeatureValues.php");
require_once("Iconography.php");
require_once("Orders.php");
require_once("Prices.php");
require_once("Products.php");
require_once("URL.php");

require_once("CombinationsWithoutPrices.php");
require_once("ProductsWithoutPrices.php");

class Export {

	protected $separator;
    protected $delimiter;

	const END_OF_LINE = "\n";

    const TYPE_PRODUCT = "Produit";
    const TYPE_COMBINATION = "Declinaison";

    const ACTIVE_PRODUCTS_ONLY = 1;
    const INACTIVE_PRODUCTS_ONLY = 2;

    /**
    * Initialiser les valeurs par défaut
    **/
    public function __construct() {
        $this->setOptions();
    }
    
    /**
    * Renseigne les options de séparation du CSV
    **/
    public function setOptions($separator = ";", $delimiter = "|") {
        
        $this->separator = $separator;
        $this->delimiter = $delimiter;
    }

	/**
    * Export CSV
    * @param string $file_name
    * @param string $content
    **/
    protected function renderCSV($file_name, $content) {

        header("Content-Type: application/vnd.ms-excel; name=$file_name; charset=UTF-8");
        header("Content-Transfer-Encoding: binary");
        header("Content-Disposition: attachment; filename=$file_name");
        header("Expires: 0");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        die($content);
    }
}