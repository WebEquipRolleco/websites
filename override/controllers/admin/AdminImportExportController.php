<?php

require_once("exports/Export.php");
require_once("imports/Import.php");

class AdminImportExportControllerCore extends AdminController {

    const SEPARATOR = "@";
    const DELIMITER = "|";

    private $separator;
    private $delimiter;
    private $skip;

    /**
    * Initialisation des paramètres et désactivation de la limite de temps
    **/
    public function __construct() {
        
        $this->bootstrap = true;
        $this->separator = Tools::getValue('separator', self::SEPARATOR);
        $this->delimiter = Tools::getValue('delimiter', self::DELIMITER);
        $this->skip = Tools::getValue('skip', 1);

        parent::__construct();
        set_time_limit(0);
    }

    /**
    * Gérer la configuration
    **/
    public function initContent() {
        $this->context->controller->addjQueryPlugin('select2');

        $this->context->smarty->assign('separator', self::SEPARATOR);
        $this->context->smarty->assign('delimiter', self::DELIMITER);
        $this->context->smarty->assign('suppliers', Supplier::getSuppliers(1));
        $this->context->smarty->assign('categories', Category::getAllCategoriesName(null, 1));

        parent::initContent();
    }

    /**
    * Gestion des actions demandées
    **/
    public function postProcess() {

        $options = explode("_", Tools::getValue('action'));

        // Exports
        if($options[0] == 'export') {
            switch($options[1]) {

                case 'products':
                    $export = new ExportProducts();
                break;

                case 'prices':
                    $export = new ExportPrices();
                break;

                case 'orders':
                    $export = new ExportOrders();
                break;

                case 'attribute-groups':
                    $export = new ExportAttributeGroups();
                break;

                case 'feature-groups':
                    $export = new ExportFeatureGroups();
                break;

                case 'attribute-values':
                    $export = new ExportAttributeValues();
                break;

                case 'feature-values':
                    $export = new ExportFeatureValues();
                break;

                case 'url':
                    $export = new ExportProductURL();
            }

            if(isset($export)) {
                $export->setOptions($this->separator, $this->delimiter);
                $export->export();
            }
        }

        // Imports
        if($options[0] == 'import') {
            switch($options[1]) {

                case 'products':
                    $import = new ImportProducts();
                break;

                case 'prices':
                    $import = new ImportPrices();
                break;

                case 'attribute-groups':
                    $import = new ImportAttributeGroups();
                break;
     
                case 'feature-groups':
                    $import = new ImportFeatureGroups();
                break;

                case 'attribute-values':
                    $import = new ImportAttributeValues();
                break;

                case 'feature-values':
                    $import = new ImportFeatureValues();
                break;
            }

            if(isset($import)) {
                $import->setOptions($this->separator, $this->delimiter, $this->skip);

                if($import->import())
                    $this->confirmations[] = "Import terminé : ".$import->nb_lines." lignes impactées";
                if($import->has_errors)
                    $this->errors = "Erreur lors de l'import du fichier. Merci de vérifier le type de fichier, l'encodage et les séparateurs utilisés";
            }
        }
    }

}