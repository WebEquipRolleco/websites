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
            'id_quotation' => array('type' => self::TYPE_INT),
        )
    );

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
	* Retourne le lien de l'image
	**/
	public function getImageLink() {

		if(is_file(_PS_ROOT_DIR_."/img/quotations".$this->id_quotation."_".$this->id.".png"))
			return "/img/quotations/".$this->id_quotation."_".$this->id.".png";
		else
			return "/img/quotations/default.jpg";
	}

}