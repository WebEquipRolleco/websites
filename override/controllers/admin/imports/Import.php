<?php

require_once("AttributeGroups.php");
require_once("AttributeValues.php");
require_once("Categories.php");
require_once("FeatureGroups.php");
require_once("FeatureValues.php");
require_once("Iconography.php");
require_once("Prices.php");
require_once("Products.php");

class Import {

    protected $context;
	protected $separator;
    protected $delimiter;
    protected $skip;
    protected $handle;

    public $nb_lines = 0;
    public $has_errors = false;

	const END_OF_LINE = "\n";

    const TYPE_PRODUCT = "Produit";
    const TYPE_COMBINATION = "Declinaison";

    /**
    * Initialiser les valeurs par défaut
    **/
    public function __construct() {
        $this->setOptions();
    }
    
    /**
    * Renseigne les options de séparation du CSV
    **/
    public function setOptions($separator = ";", $delimiter = "|", $skip = 1) {
        
        $this->context = Context::getContext();
        $this->separator = $separator;
        $this->delimiter = $delimiter;
        $this->skip = $skip;
    }

    /**
    * Ouvre le fichier importé et passe les 1ères lignes
    **/
    protected function openFile() {

        $this->nb_lines = 0;
        $this->has_errors = false;

        if($file = $_FILES['import_file']) {

            $this->handle = fopen($file['tmp_name'], 'r');

            // Lignes à ignorer
            for($x=0; $x<$this->skip; $x++)
                fgetcsv($this->handle, 0, $this->separator);
        }
    }

    /**
    * Retourne la prochaine ligne du fichier importé
    * @return array|null
    **/
    protected function getNextRow() {

        if(!$this->handle)
            return false;

        return fgetcsv($this->handle, 0, $this->separator);
    }

}