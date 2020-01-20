<?php

class AdminSpecificPricesController extends AdminController {

	public function __construct() {
        
        $this->bootstrap = true;
        $this->table = 'specific_price';
        $this->className = 'SpecificPrice';
        $this->allow_export = true;
        
        $this->addRowAction('delete');

        parent::__construct();

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->trans('Delete selected', array(), 'Admin.Notifications.Info'),
                'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Info'),
                'icon' => 'icon-trash'
            )
        );

        $this->_select = "a.*, l.name, s.name AS shop, g.name AS group_name, p.reference AS product_reference, pa.reference AS combination_reference";
        $this->_join = ' LEFT JOIN '._DB_PREFIX_.'product p ON (a.id_product = p.id_product)';
        $this->_join .= ' LEFT JOIN '._DB_PREFIX_.'product_lang l ON (a.id_product = l.id_product)';
        $this->_join .= ' LEFT JOIN '._DB_PREFIX_.'product_attribute pa ON (a.id_product_attribute = pa.id_product_attribute)';
        $this->_join .= ' LEFT JOIN '._DB_PREFIX_.'shop s ON (a.id_shop = s.id_shop)';
        $this->_join .= ' LEFT JOIN '._DB_PREFIX_.'group_lang g ON (a.id_group = g.id_group)';

        $this->fields_list = array(
            'id_specific_price' => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'name' => array(
                'title' => $this->trans('Produit', array(), 'Admin.Global'),
            ),
            'product_reference' => array(
                'title' => $this->trans('Référence produit', array(), 'Admin.Globabl'),
                'align' => 'text-center'
            ),
            'combination_reference' => array(
                'title' => $this->trans('Déclinaison', array(), 'Admin.Global'),
                'align' => 'text-center',
            ),
            'shop' => array(
                'title' => $this->trans('Boutique', array(), 'Admin.Global'),
                'align' => 'text-center',
            ),
            'from_quantity' => array(
                'title' => $this->trans('A partir de', array(), 'Admin.Global'),
                'align' => 'text-center',
            ),
            'reduction_type' => array(
                'title' => $this->trans('Type', array(), 'Admin.Global'),
                'align' => 'text-center',
                'callback' => 'getTypeLabel',
            ),
            'price' => array(
                'title' => $this->trans('Impact (prix)', array(), 'Admin.Global'),
                'align' => 'text-center',
            ),
            'group_name' => array(
                'title' => $this->trans('Groupe client', array(), 'Admin.Global'),
                'align' => 'text-center',
            ),
            'from' => array(
                'title' => $this->trans('Début', array(), 'Admin.Global'),
                'align' => 'text-center',
                'callback' => 'formatDate',
                'type' => 'date',
            ),
            'to' => array(
                'title' => $this->trans('Fin', array(), 'Admin.Global'),
                'align' => 'text-center',
                'callback' => 'formatDate',
                'type' => 'date',
            ),
        );
    }

    /**
    * Formate les dates pour la page liste
    **/
    public function formatDate($value) {

    	if($value and $value != "0000-00-00 00:00:00") {
        	$date = DateTime::createFromFormat('Y-m-d H:i:s', $value);
        	return $date->format('d/m/Y');
        }

        return "-";
    }

    /**
    * Gestion de l'import
    **/
    public function initContent() {
    	
    	if(Tools::isSubmit('import')) {
    		if($file = $_FILES['file']) {

    			$handle = fopen($file['tmp_name'], 'r');

    			if(Tools::getValue('skip'))
    				fgetcsv($handle, 0, ";");

    			while($row = fgetcsv($handle, 0, ";")) {

    				$price = new SpecificPrice($row[0]);
    				$price->id_product = $row[1];
    				$price->id_product_attribute = $row[2];
    				$price->from_quantity = $row[3];
    				$price->reduction_type = $this->getType($row[4]);
    				$price->price = $row[5];
    				$price->reduction = $row[6] ? $row[6] : 0;
    				$price->id_shop = $row[7] ? $row[7] : 0;
    				$price->id_group = $row[8] ? $row[8] : 0;
    				$price->from = $row[9] ? $row[9] : '0000-00-00 00:00:00';
    				$price->to = $row[10] ? $row[10] : '0000-00-00 00:00:00';
    				$price->id_customer = 0;
    				$price->id_currency = 1;
    				$price->id_country = 8;
    				$price->save();
    			}
    		}
    	}

    	parent::initContent();
    }

    /**
    * Convertit le type de réduction
    **/
    private function getType($int) {

    	$data[1] = 'amount';
    	$data[2] = 'percentage';

    	return $data[$int];
    }

    /**
    * Convertit le type de réduction pour l'affichage de la liste
    **/
    public function getTypeLabel($value) {

    	if($value == 'amount')
    		return "<span class='label label-info'><b>Montant fixe</b></span>";
    	elseif($value == 'percentage')
    		return "<span class='label label-info'><b>Pourcentage</b></span>";

    	return;
    }

    /**
    * assign default action in toolbar_btn smarty var, if they are not set.
    * uses override to specifically add, modify or remove items
    */
    public function initToolbar()
    {
        switch ($this->display) {
            case 'add':
            case 'edit':
                // Default save button - action dynamically handled in javascript
                $this->toolbar_btn['save'] = array(
                    'href' => '#',
                    'desc' => $this->l('Save')
                );
                $back = Tools::safeOutput(Tools::getValue('back', ''));
                if (empty($back)) {
                    $back = self::$currentIndex.'&token='.$this->token;
                }
                if (!Validate::isCleanHtml($back)) {
                    die(Tools::displayError());
                }
                if (!$this->lite_display) {
                    $this->toolbar_btn['cancel'] = array(
                        'href' => $back,
                        'desc' => $this->l('Cancel')
                    );
                }
                break;
            case 'view':
                // Default cancel button - like old back link
                $back = Tools::safeOutput(Tools::getValue('back', ''));
                if (empty($back)) {
                    $back = self::$currentIndex.'&token='.$this->token;
                }
                if (!Validate::isCleanHtml($back)) {
                    die(Tools::displayError());
                }
                if (!$this->lite_display) {
                    $this->toolbar_btn['back'] = array(
                        'href' => $back,
                        'desc' => $this->l('Back to list')
                    );
                }
                break;
            case 'options':
                $this->toolbar_btn['save'] = array(
                    'href' => '#',
                    'desc' => $this->l('Save')
                );
                break;
            default:
                // list
            	$this->toolbar_btn['import'] = array(
		            'href' => '#import',
		            'desc' => $this->l('Import')
		        );
                if ($this->allow_export) {
                    $this->toolbar_btn['export'] = array(
                        'href' => self::$currentIndex.'&export'.$this->table.'&token='.$this->token,
                        'desc' => $this->l('Export')
                    );
                }
        }
        $this->addToolBarModulesListButton();
    }

    /**
    * Ajoute la modal import à la page liste
    **/
    public function renderList() {

    	$tpl = $this->context->smarty->createTemplate(_PS_ROOT_DIR_."/override/controllers/admin/templates/specific_prices/import.tpl");
    	return parent::renderList().$tpl->fetch();
    }

}