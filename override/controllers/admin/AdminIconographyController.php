<?php

require_once("exports/Export.php");
require_once("imports/Import.php");

class AdminIconographyControllerCore extends AdminController {

	public function __construct() {
        
        $this->bootstrap = true;
        $this->table = ProductIcon::TABLE_NAME;
        $this->className = 'ProductIcon';

        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->allow_export = true;

        parent::__construct();

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->trans('Delete selected', array(), 'Admin.Notifications.Info'),
                'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Info'),
                'icon' => 'icon-trash'
            )
        );

        $this->toolbar_btn['import'] = array(
            'href' => '#import',
            'desc' => $this->l('Import')
        );

        $this->_select = "a.*, g.name AS group_name";
        $this->_join = ' LEFT JOIN '._DB_PREFIX_.ProductIcon::TABLE_NAME.'_group g ON (a.id_group = g.id_product_icon_group)';

        $this->_orderBy = 'id_product_icon';
        $this->_orderWay = 'asc';
        $this->_use_found_rows = true;

        $this->fields_list = array(
            'id_product_icon' => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
            ),
            'name' => array(
                'title' => $this->trans('Name', array(), 'Admin.Global'),
                'align' => 'center',
            ),
            'group_name' => array(
                'title' => $this->trans('Groupe', array(), 'Admin.Global'),
                'align' => 'center',
            ),
            'location' => array(
                'title' => $this->trans('Position', array(), 'Admin.Global'),
                'align' => 'center',
                'callback' => 'renderLocation'
            ),
            'position' => array(
                'title' => $this->trans('Position', array(), 'Admin.Global'),
                'align' => 'center',
                'type' => 'int',
                'search' => false
            ),
            'active' => array(
                'title' => $this->trans('Actif', array(), 'Admin.Global'),
                'align' => 'text-center',
                'type' => 'bool',
                'active' => 'status',
                'search' => false
            ),
        );
    }

    public function renderLocation($value) {
        return ProductIcon::getLocations()[$value];
    }
    
    public function initContent() {
    	parent::initContent();

        if(Tools::isSubmit('submitResetproduct_icon'))
            $this->processResetFilters();

    	if(Tools::getIsset('updateproduct_icon') or Tools::getIsset('addproduct_icon'))
    		$this->displayForm();
        else {
            $export = new ExportIconography();
            $this->context->smarty->assign("columns", $export->getHeader());
            $this->displayList();
        }
    }

    public function postProcess() {
        parent::postProcess();

        if(Tools::getIsset('exportproduct_icon')) {

            $export = new ExportIconography();
            $export->export();
        }
    }

    /**
    * Affiche la page liste
    **/
    private function displayList() {

    	// Vérifier le dossier de destination des images
    	$path = getcwd().'/../img/icons';
		if(!is_dir($path)) mkdir($path, 0777);

    	// Suppression
    	if($id = Tools::getValue('delete')) {
    		$icon = new ProductIcon($id);
    		if($icon->id) $icon->delete();
    	}

    	// Changement statut 
    	if($id = Tools::getValue('toggle')) {
    		$icon = new ProductIcon($id);
    		$icon->active = !$icon->active;
    		$icon->save();
    	}

        // Import
        if(Tools::isSubmit('import')) {
            $import = new ImportIconography();
            $import->import();

            if($import->nb_lines)
                $this->confirmations[] = "Import terminé : ".$import->nb_lines." lignes impactées";
        }
    
        $this->getList(1);
        $this->context->smarty->assign('list', $this->renderList());
    }

    /**
    * Affiche le formulaire de création / modification
    **/
    private function displayForm() {

    	$icon = new ProductIcon(Tools::getValue('id_product_icon'));

    	// Validation du formulaire
    	if($form = Tools::getValue('form')) {

    		$icon->name = $form['name'];
    		$icon->title = $form['title'];
    		$icon->url = $form['url'];
    		$icon->active = $form['active'];
    		$icon->position = $form['position'];
            $icon->location = $form['location'];
            $icon->id_group = $form['id_group'];

    		if(isset($form['height']))
    			$icon->height = $form['height'];
    		if(isset($form['width']))
    		$icon->width = $form['width'];

    		$icon->save();
    	}

    	// Modification de l'image
    	if(isset($_FILES['picture']) and $_FILES['picture']['name']) {

    		if($icon->extension)
    			@unlink(getcwd()."/../img/icons/".$this->id.".".$icon->extension);

    		$rows = explode('.', $_FILES['picture']['name']);
    		$icon->extension = end($rows);
    		$icon->save();

    		move_uploaded_file($_FILES['picture']['tmp_name'], getcwd()."/../img/icons/".$icon->id.".".$icon->extension);
    	}

    	$this->context->smarty->assign('icon', $icon);
        $this->context->smarty->assign('groups', ProductIconGroup::find());
        //$this->context->controller->addjQueryPlugin('select2');

    	$this->setTemplate('details.tpl');
    }

}