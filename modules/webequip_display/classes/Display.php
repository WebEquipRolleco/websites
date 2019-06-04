<?php

class Display extends ObjectModel {

	const TABLE_NAME = 'webequip_displays';
	const TABLE_PRIMARY = 'id';

	public $id = null;
	public $name = null;
	public $link = null;
	public $picture = null;
    public $position = 1;
    public $active = true;

	public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => self::TABLE_PRIMARY,
        'fields' => array(
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'link' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'picture' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool')
        ),
    );

    public static function createTable() {
    	$check = Db::getInstance()->execute("CREATE TABLE "._DB_PREFIX_.self::TABLE_NAME." (`id` INT NOT NULL AUTO_INCREMENT, `name` VARCHAR(255) NULL, `link` VARCHAR(255) NULL, `picture` VARCHAR(255) NULL, `position` INTEGER(11) NOT NULL DEFAULT 1, `active` TINYINT(1) DEFAULT 1, PRIMARY KEY (`id`)) ENGINE = InnoDB;");
        $check .= Db::getInstance()->execute("CREATE TABLE "._DB_PREFIX_.self::TABLE_NAME."_shop (`id_display` INT NOT NULL, `id_shop` INT NOT NULL, `active` TINYINT(1) DEFAULT 0, PRIMARY KEY (`id_display`, `id_shop`)) ENGINE = InnoDB;");
    }

    public static function removeTable() {
        $check = Db::getInstance()->execute("DROP TABLE "._DB_PREFIX_.self::TABLE_NAME);
    	$check .= Db::getInstance()->execute("DROP TABLE "._DB_PREFIX_.self::TABLE_NAME."_shop");
        return $check;
    }

    public function getImgDirectory() {
    	return "/modules/webequip_display/img/";
    }

    public function getUrl() {
    	return $this->getImgDirectory().$this->picture;
    }

    public static function find($id_shop = null) {

        if(!$id_shop)
            $id_shop = Context::getContext()->shop->id;

        $data = array();
        $rows = Db::getInstance()->executeS("SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME." d, "._DB_PREFIX_.self::TABLE_NAME."_shop dp WHERE d.id = dp.id_display AND d.active = 1 AND dp.active = 1 ORDER BY d.position");

        foreach($rows as $row)
            $data[] = new Display($row['id']);

        return $data;
    }

    public static function findAll() {

    	$data = array();
    	$rows = Db::getInstance()->executeS("SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME." ORDER BY position");

    	foreach($rows as $row)
    		$data[] = new Display($row['id']);

    	return $data;
    }

    public function getShops() {
        
        if(!$this->id)
            return array();

        if(!$this->shops)
            $this->shops = Db::getInstance()->executeS("SELECT * FROM "._DB_PREFIX_.self::TABLE_NAME."_shop WHERE id_display = ".$this->id);
    
        return $this->shops;
    }

    public function erazeShops() {
        return Db::getInstance()->execute("DELETE FROM "._DB_PREFIX_.self::TABLE_NAME."_shop WHERE id_display = ".$this->id);
    }

    public function addShop($id_shop, $status) {
        return Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_.self::TABLE_NAME."_shop VALUES(".$this->id.", $id_shop, $status)");
    }

    public function countShops() {
        
        $nb = 0;
        foreach($this->getShops() as $shop)
            if($shop['active']) $nb++;

        return $nb;
    }

    public function hasShop($id_shop) {

        foreach($this->getShops() as $row)
            if($row['id_shop'] == $id_shop)
                return $row['active'];

        return false;
    }
}