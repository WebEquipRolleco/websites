<?php

class Newsletter extends ObjectModel {

	const TABLE_NAME = 'newsletter';
	const TABLE_PRIMARY = 'id';

    const GROUP_NAME = "Newsletter";
    
	public $id = null;
	public $id_shop = null;
	public $id_shop_group = null;
	public $email = null;
	public $ip = null;
	public $date_add = null;
	
	public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => self::TABLE_PRIMARY,
        'fields' => array(
        	'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 1),
        	'id_shop_group' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 1),
        	'email' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
        	'ip' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 16),
        	'date_add' => array('type' => self::TYPE_DATE)
        )
    );

    public static function findByEmail($email, $active = false) {

    	$id = Db::getInstance()->getValue("SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE email = '$email'");
    	if($id) return new Newsletter($id);

    	return false;
    }

}