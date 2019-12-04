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
		if($shop->id and $files = $_FILES['new_file']) {
			foreach($shop->getDocuments() as $document) {
				$doc = $document['name'];
				if(isset($files['tmp_name'][$doc]) and $files['tmp_name'][$doc])
					if($files['type'][$doc] == 'application/pdf') {
						@unlink($shop->getFilePath($doc, true));
						move_uploaded_file($files['tmp_name'][$doc], $shop->getFilePath($doc, true));
						$this->context->smarty->assign('alert', array('type'=>'success', 'content'=>'Fichier enregistré'));
					}
					else
						$this->context->smarty->assign('alert', array('type'=>'danger', 'content'=>'Le fichier doit être au format PDF'));
			}
		}

		// Suppression d'un fichier
		if($name = Tools::getValue('remove_file')) {
			if(unlink($shop->getFilePath($name, true)))
				$this->context->smarty->assign('alert', array('type'=>'success', 'content'=>'Fichier supprimé'));
		}

		parent::postProcess();
	}

}