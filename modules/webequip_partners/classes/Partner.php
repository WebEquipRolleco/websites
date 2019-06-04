<?php

class Partner extends ObjectModel {

	const TABLE_NAME = 'webequip_partners';
	const TABLE_PRIMARY = 'id';

	public $id = null;
	public $name = null;
	public $link = null;
	public $picture = null;

	public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => self::TABLE_PRIMARY,
        'fields' => array(
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'link' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'picture' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255)
        ),
    );

    public static function createTable() {
    	return Db::getInstance()->execute("CREATE TABLE "._DB_PREFIX_.self::TABLE_NAME." (`id` INT NOT NULL AUTO_INCREMENT, `name` VARCHAR(255) NULL, `link` VARCHAR(255) NULL, `picture` VARCHAR(255) NULL, PRIMARY KEY (`id`)) ENGINE = InnoDB;");
    }

    public static function removeTable() {
    	return Db::getInstance()->execute("DROP TABLE "._DB_PREFIX_.self::TABLE_NAME);
    }

    public function getImgDirectory() {
    	return "/modules/webequip_partners/img/";
    }

    public function getUrl() {
    	return $this->getImgDirectory().$this->picture;
    }

    public static function findAll() {

    	$data = array();
    	$rows = Db::getInstance()->executeS("SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME);

    	foreach($rows as $row)
    		$data[] = new Partner($row['id']);

    	return $data;
    }

}