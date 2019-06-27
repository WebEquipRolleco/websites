<?php 

class AdminDocumentsController extends AdminController {

	public function __construct() {

		$this->bootstrap = true;
	    parent::__construct();
	}

	public function initContent() {

		$shops = array();
		foreach(Shop::getShops() as $row)
			$shops[] = new Shop($row['id_shop']);
		$this->context->smarty->assign('shops', $shops);

		parent::initContent();
	}

	public function postProcess() {

		$shop = new Shop(Tools::getValue('selected_shop'));

		// Upload d'un nouveau fichier
		if($shop->id and isset($_FILES['new_conditions']) and $_FILES['new_conditions']['tmp_name']) {
			if($_FILES['new_conditions']['type'] == 'application/pdf') {
				@unlink($shop->getConditionsFilePath(true));
				move_uploaded_file($_FILES['new_conditions']['tmp_name'], $shop->getConditionsFilePath(true));
				$this->context->smarty->assign('alert', array('type'=>'success', 'content'=>'Fichier enregistré'));
			}
			else
				$this->context->smarty->assign('alert', array('type'=>'danger', 'content'=>'Le fichier doit être au format PDF'));
		}

		// Suppression d'un fichier
		if(Tools::isSubmit('remove_conditions')) {
			@unlink($shop->getConditionsFilePath(true));
			$this->context->smarty->assign('alert', array('type'=>'success', 'content'=>'Fichier supprimé'));
		}

		parent::postProcess();
	}

}