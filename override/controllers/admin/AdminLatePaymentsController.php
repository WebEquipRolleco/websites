<?php

class AdminLatePaymentsController extends AdminController {

	const SEPARATOR = ";";
	const END_OF_LINE = "\n";

	public function __construct() {
        
        $this->bootstrap = true;
        parent::__construct();
    }

	public function initContent() {

		$nums = Tools::getValue('numbers');
		if($nums) {

			$csv = implode(self::SEPARATOR, $this->getHeaderCSV()).self::END_OF_LINE;
			foreach($this->getRows($nums) as $row)
				$csv .= implode(self::SEPARATOR, $row).self::END_OF_LINE;

			header('Content-Disposition: attachment; filename="factures impayées.csv";');
			die($csv);
		}
	}

	private function getHeaderCSV() {

		$data[] = "Numero Facture";
		$data[] = "Num commande";
		$data[] = "Date de commande";
		$data[] = "Boutique";
		$data[] = "Nom de la société";
		$data[] = "Civilité client";
		$data[] = "Nom client";
		$data[] = "Prénom client";
		$data[] = "Adresse facturation";
		$data[] = "Complément adresse de facturation";
		$data[] = "Code postal adresse de facturation";
		$data[] = "Ville adresse de facturation";

		return $data;
	}

	/**
	* Récupère les informations en fonction d'une liste de numéros de facture
	**/
	private function getRows($nums) {

		$sql = "SELECT o.invoice_number, o.reference, o.date_add, s.name AS 'shop', a.company, gl.name AS 'gender', c.lastname, c.firstname, a.address1, a.address2, a.postcode, a.city
				FROM ps_orders o, ps_shop s, ps_customer c, ps_gender_lang gl, ps_address a
				WHERE o.id_shop = s.id_shop
				AND o.id_customer = c.id_customer
				AND c.id_gender = gl.id_gender
				AND gl.id_lang = 1
				AND o.id_address_invoice = a.id_address
				AND o.invoice_number IN($nums)";

		return Db::getInstance()->executeS($sql);
	}

}