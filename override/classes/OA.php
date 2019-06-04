<?php

class OA extends ObjectModel {

	/** @var int Order **/
	public $id_order;

	/** @var int Supplier **/
	public $id_supplier;

	/** @var string Code **/
	public $code;

	/** @var datetime Date BC **/
	public $date_BC;

	/** @var datetime Date BL **/
	public $date_BL;

	// Variables temporaires
	private $supplier, $order;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'oa',
		'primary' => 'id',
		'fields' => array(
			'id_order' => array('type' => self::TYPE_INT),
			'id_supplier' => array('type' => self::TYPE_INT),
			'code' => 	array('type' => self::TYPE_STRING),
			'date_BC'	=> array('type' => self::TYPE_DATE),
			'date_BL'	=> array('type' => self::TYPE_DATE)
		)
	);

	/**
	* Récupère le fournisseur associé
	**/
	public function getSupplier() {

		if(!$this->supplier)
			$this->supplier = new Supplier($this->id_supplier, 1);

		return $this->supplier;
	}

	/**
	* Retourne la commande associée
	**/
	public function getOrder() {

		if(!$this->order)
			$this->order = new Order($this->id_order);

		return $this->order;
	}
	
	/**
	* Récupère la liste des OA d'une commande
	**/
	public static function findByOrder($id_order) {

		if(!$id_order)
			return array();
		
		$data = array();
		$rows = Db::getInstance()->executeS("SELECT id FROM ps_oa WHERE id_order = $id_order");

		foreach($rows as $row)
			$data[] = new OA($row['id']);

		return $data;
	}

	/**
	* Récupère un OA en fonction d'une commande et d'un fournisseur
	**/
	public static function find($id_order, $id_supplier) {

		$id = Db::getInstance()->getValue("SELECT id FROM ps_oa WHERE id_order = $id_order AND id_supplier = $id_supplier");
		
		$oa = new OA($id);
		$oa->id_order = $id_order;
		$oa->id_supplier = $id_supplier;

		return $oa;
	}
	
}