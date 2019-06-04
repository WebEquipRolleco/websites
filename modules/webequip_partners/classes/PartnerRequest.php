<?php

class PartnerRequest extends ObjectModel {

	const TABLE_NAME = 'webequip_partner_request';
	const TABLE_PRIMARY = 'id';

	public $id = null;
	public $firstname = null;
	public $lastname = null;
    public $company = null;
    public $phone = null;
    public $email = null;
	public $content = null;

	public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => self::TABLE_PRIMARY,
        'fields' => array(
            'firstname' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'lastname' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'company' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'phone' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'email' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'content' => array('type' => self::TYPE_HTML)
        ),
    );

    public static function createTable() {
    	return Db::getInstance()->execute("CREATE TABLE "._DB_PREFIX_.self::TABLE_NAME." (`id` INT NOT NULL AUTO_INCREMENT, `firstname` VARCHAR(255) NULL, `lastname` VARCHAR(255) NULL, `company` VARCHAR(255) NULL, `phone` VARCHAR(255) NULL, `email` VARCHAR(255) NULL, `content` TEXT NULL, PRIMARY KEY (`id`)) ENGINE = InnoDB;");
    }

    public static function removeTable() {
    	return Db::getInstance()->execute("DROP TABLE "._DB_PREFIX_.self::TABLE_NAME);
    }

    public static function findOneByCompany($name) {

        $id = Db::getInstance()->getValue("SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE company = '$name'");
        if(!$id) return false;

        return new PartnerRequest($id);
    }

    public static function findAll() {

    	$data = array();
    	$rows = Db::getInstance()->executeS("SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME);

    	foreach($rows as $row)
    		$data[] = new PartnerRequest($row['id']);

    	return $data;
    }

}