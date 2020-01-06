<?php

class Address extends AddressCore {

	/**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'address',
        'primary' => 'id_address',
        'fields' => array(
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'id_manufacturer' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'id_supplier' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'id_warehouse' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'id_country' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_state' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId'),
            'alias' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 32),
            'company' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'lastname' => array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true),
            'firstname' => array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true),
            'vat_number' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'address1' => array('type' => self::TYPE_STRING, 'validate' => 'isAddress', 'required' => true, 'size' => 128),
            'address2' => array('type' => self::TYPE_STRING, 'validate' => 'isAddress', 'size' => 128),
            'postcode' => array('type' => self::TYPE_STRING, 'validate' => 'isPostCode', 'size' => 12),
            'city' => array('type' => self::TYPE_STRING, 'validate' => 'isCityName', 'required' => true, 'size' => 64),
            'other' => array('type' => self::TYPE_STRING, 'validate' => 'isMessage', 'size' => 300),
            'phone' => array('type' => self::TYPE_STRING, 'validate' => 'isPhoneNumber', 'size' => 32),
            'phone_mobile' => array('type' => self::TYPE_STRING, 'validate' => 'isPhoneNumber', 'size' => 32),
            'dni' => array('type' => self::TYPE_STRING, 'validate' => 'isDniLite', 'size' => 16),
            'deleted' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        ),
    );

	/**
	* Vérifie si l'adresse possède au moins un numéro de téléphone
	**/
	public function hasPhone() {
		return $this->phone || $this->phone_mobile;
	}

	/** 
	* Vérifie si les 2 numéros de téléphone sont renseignés
	**/
	public function hasBothPhones() {
		return $this->phone && $this->phone_mobile;
	}

}