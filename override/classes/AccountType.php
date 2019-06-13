<?php

class AccountTypeCore extends ObjectModel {

	const TABLE_NAME = 'account_type';
	const TABLE_PRIMARY = 'id_account_type';

	/** @var mixed string */
    public $name;

    /** @var bool Company **/
    public $company = false;

    /** @var bool Siret */
    public $siret = false;

    /** @var bool Chorus **/
    public $chorus = false;

    /** @var bool TVA **/
    public $tva = false;

    /** @var bool Default **/
    public $default_value = false;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => self::TABLE_PRIMARY,
        'fields' => array(
        	'name' => array('type' => self::TYPE_STRING),
            'company' => array('type' => self::TYPE_BOOL),
        	'siret' => array('type' => self::TYPE_BOOL),
            'chorus' => array('type' => self::TYPE_BOOL),
            'tva' => array('type' => self::TYPE_BOOL),
            'default_value' => array('type' => self::TYPE_BOOL)
        )
    );

    public static function getAccountTypes() {

    	$data = array();
    	$ids = Db::getInstance()->executeS("SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME);

    	foreach($ids as $row)
    		$data[] = new AccountType($row[self::TABLE_PRIMARY]);

    	return $data;
    }

}