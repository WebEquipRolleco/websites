<?php

class Modal extends ObjectModel {

	const TABLE_NAME = 'webequip_modals';
	const TABLE_PRIMARY = 'id';

	public $id = null;
    public $icon = null;
	public $title = null;
	public $subtitle = null;
	public $content = null;
    public $transition_in = null;
    public $transition_out = null;
    public $auto_open = 0;
    public $auto_close = 0;
    public $fullscreen = false;
    public $close_button = true;
    public $close_escape = true;
    public $close_overlay = true;
    public $header_color = null;
    public $overlay = true;
    public $width = 0;
    public $top = 0;
    public $bottom = 0;
    public $date_begin = null;
    public $date_end = null;
    public $active = true;
    public $browsing = 0;
    public $expiration = 0;
    public $validation = false;

    public $display_for_customers = true;
    public $display_for_guests = true;

    public $allow_pages = null;
    public $disable_pages = null;
    public $allow_customers = null;
    public $disable_customers = null;
    public $allow_groups = null;
    public $disable_groups = null;

    public $shops = null;

	public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => self::TABLE_PRIMARY,
        'fields' => array(
            'icon' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 50),
            'title' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'subtitle' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'content' => array('type' => self::TYPE_HTML),
            'transition_in' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'transition_out' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'auto_open' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 11),
            'auto_close' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 11),
            'fullscreen' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'close_button' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'close_escape' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'close_overlay' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'header_color' => array('type' => self::TYPE_STRING, 'size' => 20),
            'overlay' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'width' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 10),
            'top' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 10),
            'bottom' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 10),
            'date_begin' => array('type' => self::TYPE_DATE),
            'date_end' => array('type' => self::TYPE_DATE),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'browsing' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 11),
            'expiration' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 11),
            'validation' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),

            'display_for_customers' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'display_for_guests' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'allow_pages' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'disable_pages' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'allow_customers' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'disable_customers' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'allow_groups' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'disable_groups' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255)
        ),
    );

    public static function createTable() {
        $check = Db::getInstance()->execute("CREATE TABLE "._DB_PREFIX_.self::TABLE_NAME." (`id` INT NOT NULL AUTO_INCREMENT, `icon` VARCHAR(50) NULL, `title` VARCHAR(255) NULL, `subtitle` VARCHAR(255) NULL, `content` TEXT NULL, `transition_in` VARCHAR(255) NULL, `transition_out` VARCHAR(255) NULL, `auto_open` INTEGER(11) NULL, `auto_close` INTEGER(11) NULL, `fullscreen` TINYINT(1) DEFAULT 0, `close_button` TINYINT(1) DEFAULT 1, `close_escape` TINYINT(1) DEFAULT 1, `close_overlay` TINYINT(1) DEFAULT 1,  `header_color` VARCHAR(20) NULL, `overlay` TINYINT(1) DEFAULT 1, `width` VARCHAR(10) NULL, `top` VARCHAR(10) NULL, `bottom` VARCHAR(10) NULL, `date_begin` DATE NULL DEFAULT NULL, `date_end` DATE NULL DEFAULT NULL, `active` TINYINT(1) DEFAULT 1, `browsing` INTEGER(11) NULL, `expiration` INTEGER(11) NULL, `validation` TINYINT(1) DEFAULT 0, `display_for_customers` TINYINT NOT NULL DEFAULT '1', `display_for_guests` TINYINT NOT NULL DEFAULT '1', `allow_pages` VARCHAR(255) NULL DEFAULT NULL, `disable_pages` VARCHAR(255) NULL DEFAULT NULL, `allow_customers` VARCHAR(255) NULL DEFAULT NULL, `disable_customers` VARCHAR(255) NULL DEFAULT NULL, `allow_groups` VARCHAR(255) NULL DEFAULT NULL, `disable_groups` VARCHAR(255) NULL DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE = InnoDB;");

        return $check;
    }

    public static function removeTable() {
        $check = Db::getInstance()->execute("DROP TABLE IF EXISTS "._DB_PREFIX_.self::TABLE_NAME);
        return $check;
    }

    public static function findAll() {

    	$data = array();
    	$rows = Db::getInstance()->executeS("SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME);

    	foreach($rows as $row)
    		$data[] = new Modal($row['id']);

    	return $data;
    }

    public static function find($page_name = null, $id_customer = null, $id_group = null, $exclude = null) {

        if(!$page_name)
            $page_name = Context::getContext()->smarty->tpl_vars['page']->value['page_name'];

        if(!$id_customer)
            $id_customer = Context::getContext()->customer->id ?? 0;

        if(!$id_group)
            $id_group = Context::getContext()->customer->id_default_group ?? 0;
        
        if(!$exclude and isset($_COOKIE[self::TABLE_NAME]))
            $exclude = $_COOKIE[self::TABLE_NAME];

        $browsing = Tools::getBrowseTime();

        $date = date('Y-m-d');

        $data = array();
        $sql = "SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME." m WHERE m.active=1 AND (m.disable_pages IN('', NULL) OR m.disable_pages NOT LIKE '%$page_name%') AND (m.allow_pages IN('', NULL) OR m.allow_pages LIKE '%$page_name%') AND (m.date_begin IN('0000-00-00', NULL) OR m.date_begin <= '$date') AND (m.date_end IN('0000-00-00', NULL) OR date_end >= '$date') AND (m.allow_customers IN('', NULL) OR m.allow_customers LIKE '%@$id_customer@%') AND (m.disable_customers IN('', NULL) OR m.disable_customers NOT LIKE '%@$id_customer@%') AND (m.allow_groups IN ('', NULL) OR m.allow_groups LIKE '%@$id_group@%') AND (m.disable_groups IN ('', NULL) OR m.disable_groups NOT LIKE '%@$id_group@%') AND (m.browsing = 0 OR m.browsing <= $browsing)";

        if($id_customer)
            $sql .= " AND m.display_for_customers = 1";
        else
            $sql .= " AND m.display_for_guests = 1";

        if($exclude)
            $sql .=  " AND m.id NOT IN (".implode(",", $exclude).")";

        $rows = Db::getInstance()->executeS($sql);

        foreach($rows as $row)
            $data[] = new Modal($row['id']);

        return $data;
    }

    public function getAllowCustomersIds() {
        return array_filter(explode(",", str_replace("@", '', $this->allow_customers)));
    }
    public function getDisableCustomersIds() {
        return array_filter(explode(",", str_replace("@", '', $this->disable_customers)));
    }

    public function getAllowGroupsIds() {
        return array_filter(explode(",", str_replace("@", '', $this->allow_groups)));
    }
    public function getDisableGroupsIds() {
        return array_filter(explode(",", str_replace("@", '', $this->disable_groups)));
    }

    public function getOptions() {

        $data['closeOnEscape'] = (bool)$this->close_escape;
        $data['overlayClose'] = (bool)$this->close_overlay;
        $data['closeButton'] = (bool)$this->close_button;
        $data['overlay'] = (bool)$this->overlay;
        $data['fullscreen'] = (bool)$this->fullscreen;

        if($this->header_color != "#000000") $data['headerColor'] = $this->header_color;
        if($this->icon) $data['icon'] = $this->icon;
        if($this->title) $data['title'] = $this->title;
        if($this->subtitle) $data['subtitle'] = $this->subtitle;
        if($this->transition_in) $data['transitionIn'] = $this->transition_in;
        if($this->transition_out) $data['transitionOut'] = $this->transition_out;
        if($this->auto_open != null) $data['autoOpen'] = $this->auto_open * 1000;
        if($this->width) $data['width'] = $this->width;
        if($this->top != null) $data['top'] = $this->top;
        if($this->bottom != null) $data['bottom'] = $this->bottom;

        if($this->auto_close) {
            $data['timeout'] = $this->auto_close * 1000;
            $data['timeoutProgressbar'] = true;
            $data['pauseOnHover'] = true;
        }

        if($this->content)
            $data['padding'] = 15;
        else
            $data['borderBottom'] = false;
        
        return $data;
    }

    public static function getAnimationsIn() {
        return array('comingIn', 'bounceInDown', 'bounceInUp', 'fadeInDown', 'fadeInUp', 'fadeInLeft', 'fadeInRight', 'flipInX');
    }
    public static function getAnimationsOut() {
        return array('comingOut', 'bounceOutDown', 'bounceOutUp', 'fadeOutDown', 'fadeOutUp', 'fadeOutLeft', 'fadeOutRight', 'flipOutX');
    }

}