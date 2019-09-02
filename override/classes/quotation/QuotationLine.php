<?php

class QuotationLine extends ObjectModel {

	const TABLE_NAME = 'quotation_line';
	const TABLE_PRIMARY = 'id';

	public $reference;
	public $reference_supplier;
	public $name;
	public $information;
	public $comment;

	public $buying_price = 0;
	public $buying_fees = 0;
	public $selling_price = 0;
	public $eco_tax = 0;
	
	public $quantity = 1;
	public $min_quantity = 1;

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
            'reference_supplier' => array('type' => self::TYPE_STRING),
            'name' => array('type' => self::TYPE_STRING),
            'information' => array('type' => self::TYPE_STRING),
            'comment' => array('type' => self::TYPE_STRING),
            'buying_price' => array('type' => self::TYPE_FLOAT),
            'buying_fees' => array('type' => self::TYPE_FLOAT),
            'selling_price' => array('type' => self::TYPE_FLOAT),
            'eco_tax' => array('type' => self::TYPE_FLOAT),
            'quantity' => array('type' => self::TYPE_INT),
            'min_quantity' => array('type' => self::TYPE_INT),
            'position' => array('type' => self::TYPE_INT),
            'id_supplier' => array('type' => self::TYPE_INT),
            'id_quotation' => array('type' => self::TYPE_INT),
        )
    );

	/**
	* Création statique
	* @return QuotationLine
	**/
	public static function find($id) {
		return new self($id);
	}

	/**
	* Retourne le devis lié
	* @return Quotation|null
	**/
	public function getQuotation() {

		if(!$this->quotation)
			$this->quotation = new Quotation($this->id_quotation);

		return $this->quotation;
	}

	/**
	* Retourne le fournisseur du produit
	* @return Supplier|null
	**/
	public function getSupplier() {

		if(!$this->supplier and $thsi->id_supplier)
			$this->supplier = new Supplier($this->id_supplier);

		return $this->supplier;
	}

	/**
	* Retourne le prix total 
	* @param bool $use_taxes
	* @param bool $eco_tax
	* @return float
	**/
	public function getPrice($use_taxes = false, $eco_tax = false) {

		$price = ($use_taxes) ? $this->selling_price * 1.2 : $this->selling_price;
		if($eco_tax) $price += $this->eco_tax;

		return round($price * $this->quantity, 2);
	}

	/**
	* Calcule le prix d'achat (charges comprises : ports)
	* @return float
	**/
	public function getBuyingPrice() {
		return $this->buying_price + $this->buying_fees;
	}

	/**
	* Calcule la marge du produit
	* @return float
	**/
	public function getMargin() {
		return $this->selling_price - $this->getBuyingPrice();
	}

	/**
	* Calcule le taux de marge du produit
	* @return float
	**/
	public function getMarginRate() {
		return Tools::getMarginRate($this->getMargin(), $this->selling_price);
	}

	/**
	* Retourn la prochaine position pour un produit devis
	* @param int $id_quotation
	* @return int
	**/
	public static function getNextPosition($id_quotation) {
		$position = (int)Db::getInstance()->getValue('SELECT MAX(position) FROM '._DB_PREFIX_.self::TABLE_NAME.' WHERE id_quotation = '.$id_quotation);
		return ++$position;
	}

	/**
	* Retourne le chemin du dossier image
	* @return string
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
	* @return string
	**/
	public function getFileName() {
		return $this->id.'.png';
	}

	/**
	* Retourne le lien de l'image
	* @return string
	**/
	public function getImageLink() {

		if(is_file($this->getDirectory(true).$this->getFileName()))
			return $this->getDirectory().$this->getFileName();
		else
			return "/img/quotations/default.jpg";
	}

}