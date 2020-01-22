<?php

class CMS extends CMSCore {

	const DIR = "cms-logo/";

	/** @var string Descrition **/
	public $description;

	/** @var bool Display Raw **/
    public $display_raw = false;

    public function __construct($id_cms = null, $id_lang = null, $id_shop = null) {

		self::$definition['fields']['meta_description'] = array('type' => self::TYPE_STRING, 'lang'=>true, 'validate'=>'isGenericName');
		self::$definition['fields']['meta_keywords'] = array('type' => self::TYPE_STRING, 'lang'=>true, 'validate'=>'isGenericName');
		self::$definition['fields']['description'] = array('type' => self::TYPE_STRING, 'lang'=>true);
		self::$definition['fields']['display_raw'] = array('type' => self::TYPE_BOOL);

		parent::__construct($id_cms, $id_lang, $id_shop);
	}
	
}