<?php

class Category extends CategoryCore {

	/** @var string Bottom Description */
	public $bottom_description;

	public function __construct($id_category = null, $id_lang = null, $id_shop = null) {

		self::$definition['fields']['bottom_description'] = array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml');
		parent::__construct($id_category, $id_lang, $id_shop);
	}

}