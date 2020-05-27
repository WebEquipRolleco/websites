<?php

class Reassurance extends ObjectModel {

	const TABLE_NAME = 'webequip_reassurances';
	const TABLE_PRIMARY = 'id';

    const POSITION_TOP = 1;
    const POSITION_BOTTOM = 2;
    const POSITION_BOTH = 3;

	public $id = null;
	public $name = null;
	public $icon = null;
	public $text = null;
    public $link = null;
    public $location = null;
    public $position = null;
    public $active = true;
    
	public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => self::TABLE_PRIMARY,
        'fields' => array(
            'name' => array('type' => self::TYPE_STRING),
            'icon' => array('type' => self::TYPE_HTML),
            'text' => array('type' => self::TYPE_HTML),
            'link' => array('type' => self::TYPE_STRING),
            'location' => array('type' => self::TYPE_INT),
            'position' => array('type' => self::TYPE_INT),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool')
        )
    );

    public static function createTable() {
    	
        $check = Db::getInstance()->execute("CREATE TABLE "._DB_PREFIX_.self::TABLE_NAME." (`id` INT NOT NULL AUTO_INCREMENT, `name` VARCHAR(255) NULL, `icon` TEXT NULL, `text` TEXT NULL, `link` VARCHAR(255) NULL, `location` INT(1) NULL, `position` INT(2) NULL, `active` TINYINT(1) DEFAULT 1, PRIMARY KEY (`id`)) ENGINE = InnoDB;");
        return $check;
    }

    public static function removeTable() {
        
        $check = Db::getInstance()->execute("DROP TABLE "._DB_PREFIX_.self::TABLE_NAME);
        return $check;
    }

    public static function findByPosition($location) {

        $data = array();
        $rows = Db::getInstance()->executeS("SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE location IN($location, ".self::POSITION_BOTH.") AND active = 1 ORDER BY position");

        foreach($rows as $row)
            $data[] = new Reassurance($row['id']);

        return $data;
    }

    public static function findAll() {

    	$data = array();
    	$rows = Db::getInstance()->executeS("SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME." ORDER BY position");

    	foreach($rows as $row)
    		$data[] = new Reassurance($row['id']);

    	return $data;
    }

    public static function getLocations() {

        $data[self::POSITION_BOTH] = "Partout";
        $data[self::POSITION_TOP] = "Header";
        $data[self::POSITION_BOTTOM] = "Milieu de page";

        return $data;
    }

}