<?php

class AfterSale extends ObjectModel {

	const TABLE_NAME = 'after_sale';
	const TABLE_PRIMARY = 'id';

	public $id = null;
    public $number = null;
	public $firstname = null;
	public $lastname = null;
    public $company = null;
    public $phone = null;
    public $email = null;
    public $city = null;
	public $content = null;
    public $id_customer = null;
    public $date_add = null;

	public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => self::TABLE_PRIMARY,
        'fields' => array(
            'number' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'firstname' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'lastname' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'company' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'phone' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'email' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'city' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'content' => array('type' => self::TYPE_HTML),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 11),
            'date_add' => array('type' => self::TYPE_DATE)
        ),
    );

    public static function findByCustomer($id_customer) {

        $data = array();
        $rows = Db::getInstance()->executeS("SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE id_customer = $id_customer");
        foreach($rows as $row)
            $data[] = new self($row['id']);

        return $data;
    }

    public static function findAll() {

    	$data = array();
    	$rows = Db::getInstance()->executeS("SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME);

    	foreach($rows as $row)
    		$data[] = new self($row['id']);

    	return $data;
    }

}