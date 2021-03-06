<?php

class AdminContactInformationControllerCore extends AdminController {

	public function __construct() {
        
        $this->bootstrap = true;
        parent::__construct();
    }

	public function initContent() {	
    	parent::initContent();

    	foreach($this->getConfig() as $domains)
    		foreach($domains as $domain)
				foreach($domain as $config)
					if(Tools::getIsset($config['name'])) Configuration::updateValue($config['name'], Tools::getValue($config['name']));

		$this->context->smarty->assign('cols', $this->getConfig());
    }

	private function getConfig() {

		$data[1]['Adresse'] = array(
			array('label'=>'Titre', 'name'=>'PS_SHOP_TITLE'),
			array('label'=>'Nom', 'name'=>'PS_SHOP_NAME'),
			array('label'=>'Adresse 1', 'name'=>'PS_SHOP_ADDR1'),
			array('label'=>'Adresse 2', 'name'=>'PS_SHOP_ADDR2'),
			array('label'=>'Code postal', 'name'=>'PS_SHOP_CODE'),
			array('label'=>'Ville', 'name'=>'PS_SHOP_CITY'),
			array('label'=>'Pays', 'name'=>'PS_SHOP_COUNTRY')
		);

		$data[1]['Bon de préparation'] = array(
			array('label'=>'Préfix commande M3', 'name'=>'PS_SHOP_PREFIX_M3'),
			array('label'=>"Préfix numéro d'OA", 'name'=>'PS_SHOP_PREFIX_OA')
		);

		$data[2]['Information légales'] = array(
			array('label'=>'Type société', 'name'=>'PS_SHOP_TYPE'),
			array('label'=>'RIB', 'name'=>'PS_SHOP_RIB'),
			array('label'=>'IBAN', 'name'=>'PS_SHOP_IBAN'),
			array('label'=>'CIC', 'name'=>'PS_SHOP_CIC'),
			array('label'=>'BIC', 'name'=>'PS_SHOP_BIC'),
			array('label'=>'SIRET', 'name'=>'PS_SHOP_SIRET'),
			array('label'=>'TVA', 'name'=>'PS_SHOP_TVA'),
			array('label'=>'APE', 'name'=>'PS_SHOP_APE'),
			array('label'=>'RCS', 'name'=>'PS_SHOP_RCS')
		);

		$data[3]['Contact'] = array(
			array('label'=>'E-mail', 'name'=>'PS_SHOP_EMAIL'),
			array('label'=>'Téléphone', 'name'=>'PS_SHOP_PHONE'),
			array('label'=>'Fax', 'name'=>'PS_SHOP_FAX')
		);

		$data[3]['SAV'] = array(
			array('label'=>'E-mail Expéditeur', 'name'=>'PS_SHOP_EMAIL_SAV_FROM', 'help'=>"Les mails partant du site proviendront de cette adresse."),
			array('label'=>'E-mail Expéditeur fournisseur', 'name'=>'PS_SHOP_EMAIL_SAV_SUPPLIER_FROM', 'help'=>"Les mails envoyés au fournisseurs proviendront de cette adresse."),
			array('label'=>'E-mail Destinataire', 'name'=>'PS_SHOP_EMAIL_SAV_TO', 'help'=>"Les mails envoyés par le client arriveront à cette adresse."),
		);

		$data[4]['Information équipe'] = array(
			array('label'=>'Téléphone', 'name'=>'PS_TEAM_PHONE'),
			array('label'=>'E-mail', 'name'=>'PS_TEAM_EMAIL')
		);
		
		$data[4]['Trustpilot'] = array(
			array('label'=>"Clé client", 'name'=>'WEBEQUIP_TRUST_KEY'),
			array('label'=>"URL", 'name'=>'WEBEQUIP_TRUST_URL')
		);

		$data[4]['Gestion API'] = array(
			array('label'=>"Clé bundle Font-Awesome", 'name'=>'KEY_FONT_AWESOME'),
			array('label'=>"Clé Google Tag Manager", 'name'=>'KEY_GOOGLE_TAG_MANAGER')
		);

		$data[4]['Reference devis'] = array(
			array('label'=>"Préfixe", 'name'=>'QUOTATION_PREFIX'),
			array('label'=>"Index en cours", 'name'=>'QUOTATION_INDEX')
		);

		return $data;
	}

}