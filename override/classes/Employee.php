<?php

class Employee extends EmployeeCore {

	/** @var bool $sav */
	public $sav;

	/**
	* OVERRIDE : ajout SAV
	**/
	public function __construct($id_employee = null, $id_lang = null, $id_shop = null) {

		self::$definition['fields']['sav'] = array('type' => self::TYPE_BOOL);
		parent::__construct($id_employee, $id_lang, $id_shop);
	}
	
	/**
	* Retourne la liste des employées responsables du suivi SAV
	* @return array
	**/
	public static function findAfterSaleAccountants() {

		$data = array();
		foreach(Bb::getInstance()->executeS("SELECT id_employee FROM ps_employee WHERE sav = 1 AND active = 1") as $row)
			$data[] = new Employee($row['id_employee']);

		return $data;
	}

}