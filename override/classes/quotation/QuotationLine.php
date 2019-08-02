<?php

class QuotationLine extends ObjectModel {

	const TABLE_NAME = 'webequip_quotation_lines';
	const TABLE_PRIMARY = 'id';

	public $reference;
	public $name;
	public $information;
	public $comment;

	public $buying_price = 0;
	public $selling_price = 0;
	public $quantity = 1;

	public $position;
	public $id_quotation;
	public $id_supplier;

	// Variables temporaires
	private $quotation;
	private $supplier;

	public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => self::TABLE_PRIMARY,
        'fields' => array(
            'reference' => array('type' => self::TYPE_STRING),
            'name' => array('type' => self::TYPE_STRING),
            'information' => array('type' => self::TYPE_STRING),
            'comment' => array('type' => self::TYPE_STRING),
            'buying_price' => array('type' => self::TYPE_FLOAT),
            'selling_price' => array('type' => self::TYPE_FLOAT),
            'quantity' => array('type' => self::TYPE_INT),
            'position' => array('type' => self::TYPE_INT),
            'id_supplier' => array('type' => self::TYPE_INT),
            'id_quotation' => array('type' => self::TYPE_INT),
        )
    );

	/**
	* Création statique
	**/
	public static function find($id) {
		return new self($id);
	}

	/**
	* Retourne le devis lié
	**/
	public function getQuotation() {

		if(!$this->quotation)
			$this->quotation = new Quotation($this->id_quotation);

		return $this->quotation;
	}

	/**
	* Retourne le fournisseur du produit
	**/
	public function getSupplier() {

		if(!$this->supplier and $thsi->id_supplier)
			$this->supplier = new Supplier($this->id_supplier);

		return $this->supplier;
	}

	/**
	* Retourne le prix total
	**/
	public function getPrice($tax = false) {
		$price = ($tax) ? $this->selling_price * 1.2 : $this->selling_price;
		return round($price * $this->quantity, 2);
	}

	/**
	* Calcule la marge du produit
	**/
	public function getMargin() {
		return $this->selling_price - $this->buying_price;
	}

	/**
	* Calcule le taux de marge du produit
	**/
	public function getMarginRate() {
		if(!$this->selling_price or !$this->buying_price) return 0;
		return ($this->selling_price * $this->buying_price) / 100;
	}

	/**
	* Retourn la prochaine position pour un produit devis
	**/
	public static function getNextPosition($id_quotation) {
		$position = (int)Db::getInstance()->getValue('SELECT MAX(position) FROM '._DB_PREFIX_.self::TABLE_NAME.' WHERE id_quotation = '.$id_quotation);
		return ++$position;
	}

	/**
	* Retourne le chemin du dossier image
	**/
	public function getDirectory($absolute = false) {

		$path = '/img/quotations/'.$this->getQuotation()->id."/";

		if(!is_dir(_PS_ROOT_DIR_.$path))
        	mkdir(_PS_ROOT_DIR_.$path, 0777, true);

		if($absolute)
            $path = _PS_ROOT_DIR_.$path;

        return $path;
	}

	/**
	* Retourne le nom de l'image personnalisée asociée
	**/
	public function getFileName() {
		return $this->id.'.png';
	}

	/**
	* Retourne le lien de l'image
	**/
	public function getImageLink() {

		if(is_file($this->getDirectory(true).$this->getFileName()))
			return $this->getDirectory().$this->getFileName();
		else
			return "/img/quotations/default.jpg";
	}

}