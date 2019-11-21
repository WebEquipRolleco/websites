<?php

class Supplier extends SupplierCore {

	const SEPARATOR = ",";

    /** @var string Reference **/
    public $reference;

	/** @var string Emails **/
    public $emails;

    /** @var string Email SAV **/
    public $email_sav;

	/** @var bool BC **/
    public $BC;

    /** @var bool BL **/
    public $BL;

    // Variables temporaires
    private $address;

    /**
    * @see ObjectModel::$definition
    **/
    public function __construct($id = null, $id_lang = null, $id_shop = null) {

        self::$definition['fields']['reference'] = array('type' => self::TYPE_STRING);
        self::$definition['fields']['emails'] = array('type' => self::TYPE_STRING);
		self::$definition['fields']['email_sav'] = array('type' => self::TYPE_STRING);
		self::$definition['fields']['BC'] = array('type' => self::TYPE_BOOL);
		self::$definition['fields']['BL'] = array('type' => self::TYPE_BOOL);

		parent::__construct($id, $id_lang, $id_shop);
	}
	
    /**
    * Sépare et retourne les e-mails
    **/
    public function getEmails() {
    	return explode(self::SEPARATOR, $this->emails);
    }

    /**
    * Retourne l'adresse du fournisseur
    **/
    public function getAddress() {

        if(!$this->address) {
        
            $id = Db::getInstance()->getValue('SELECT id_address FROM ps_address WHERE id_supplier = '.$this->id);
            if($id) $this->address = new SupplierAddress($id);
        }

        return $this->address;
    }

    /**
    * Retourne une liste des noms de fournisseurs
    * @return array
    **/
    public function findNames() {
        $rows = Db::getInstance()->executeS("SELECT DISTINCT(name) FROM ps_supplier");
        return array_map(function($e) { return $e['name']; }, $rows);
    }

    /**
    * Retourne le sous traitant assigné à un produit
    * @param int $id_product
    * @param int $id_product_attribute
    **/
    public static function fetchReferenceForProduct($id_product, $id_product_attribute = null) {

    }

}