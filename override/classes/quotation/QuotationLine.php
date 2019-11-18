<?php

class QuotationLine extends ObjectModel {

	const TABLE_NAME = 'quotation_line';
	const TABLE_PRIMARY = 'id';

	const DELIMITER = "|";

	public $reference;
	public $reference_supplier;
	public $name;
	public $properties;
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
            'properties' => array('type' => self::TYPE_STRING),
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
    * Efface le contenu de la table
    **/
    public static function erazeContent() {
        Db::getInstance()->execute("DELETE FROM "._DB_PREFIX_.self::TABLE_NAME);
    }
    
	/**
	* Retourne le prix total 
	* @param bool $use_taxes
	* @param bool $fees
	* @param bool $eco_tax
	* @param int $quantity
	* @return float
	**/
	public function getPrice($use_taxes = false, $fees = false, $eco_tax = false, $quantity = 0) {

		if(!$quantity) $quantity = $this->quantity;

		$price = $this->selling_price * $quantity;
		if($fees) $price += $this->getFees();
		if($use_taxes) $price *= 1.2;
		if($eco_tax) $price += $this->getEcoTax($quantity);

		return round($price * $this->quantity, 2);
	}

	/**
	* Retourne les frais de port total
	* @param bool $use_taxes
	* @return float
	**/
	public function getFees($use_taxes = false) {

		$price = $this->buying_fees * $this->quantity;
		if($use_taxes) $price *= 1.2;

		return $price;
	}

	/**
    * Calcule la participation éco totale
    * @param int $quantity
    * @return float
    **/
    public function getEcoTax($quantity = 0) {

    	if(!$quantity) $quantity = $this->quantity;
    	return $this->eco_tax * $quantity;
    }

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
			return null;
	}

	/**
	* Retourne le nom du produit
	* @return string
	**/
	public function getProductName() {

		$rows = explode(self::DELIMITER, $this->name);
		return $rows[0] ?? null;
	}

	/**
	* Retourne les propriétés du produit
	* @return string
	**/
	public function getProductProperties() {

		$rows = explode(self::DELIMITER, $this->name);
		array_shift($rows);

		return $rows;
	}

}