<?php

class AccountTypeCore extends ObjectModel {

	const TABLE_NAME = 'account_type';
	const TABLE_PRIMARY = 'id';

	/** @var mixed string */
    public $name;

    /** @var bool Extra Information */
    public $extra_infomation = false;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => self::TABLE_PRIMARY,
        'fields' => array(
        	'name' => array('type' => self::TYPE_STRING),
        	'extra_infomation' => array('type' => self::TYPE_BOOL)
        )
    );

    public static function getAccountTypes() {

    	$data = array();
    	$ids = Db::getInstance()->executeS("SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME);

    	foreach($ids as $row)
    		$data[] = new AccountType($row['id']);

    	return $data;
    }

}