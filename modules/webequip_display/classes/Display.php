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
    }

    public static function removeTable() {
        $check = Db::getInstance()->execute("DROP TABLE "._DB_PREFIX_.self::TABLE_NAME);
        return $check;
    }

    public function getImgDirectory() {
    	return "/modules/webequip_display/img/";
    }

    public function getUrl() {
    	return $this->getImgDirectory().$this->picture;
    }

    /**
    * Retourne les blocs de publicité
    * @param bool $active
    * @return array
    **/
    public static function find($active = true) {

        $sql = "SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME;
        if($active) $sql .= " WHERE active = 1";
        $sql .= " ORDER BY position";

        $data = array();
        $rows = Db::getInstance()->executeS($sql);

        foreach($rows as $row)
            $data[] = new Display($row['id']);

        return $data;
    }

    /**
    * Retourne tous les blocs de publicité
    * @deprecated
    **/
    public static function findAll() {
        return self::find(false);
    }

}