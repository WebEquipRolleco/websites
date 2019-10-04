<?php

class CMS extends CMSCore {

	/** @var bool Display Raw */
    public $display_raw = false;

    public function __construct($id_cms = null, $id_lang = null, $id_shop = null) {

		self::$definition['fields']['display_raw'] = array('type' => self::TYPE_BOOL);
		parent::__construct($id_cms, $id_lang, $id_shop);
	}
	
}