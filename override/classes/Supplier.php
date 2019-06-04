<?php

class Supplier extends SupplierCore {

	const SEPARATOR = ",";

	/** @var string Emails */
    public $emails;

	/** @var bool BC */
    public $BC;

    /** @var bool BL */
    public $BL;

    /**
    * @see ObjectModel::$definition
    **/
    public function __construct($id = null, $id_lang = null, $id_shop = null) {

		self::$definition['fields']['emails'] = array('type' => self::TYPE_STRING);
		self::$definition['fields']['BC'] = array('type' => self::TYPE_BOOL);
		self::$definition['fields']['BL'] = array('type' => self::TYPE_BOOL);

		parent::__construct($id, $id_lang, $id_shop);
	}
	
    /**
    * Sépare et retourne les e-mails
    **/
    public function getEmails() {
    	return explode(self::SEPARATOR, $this->emails);
    }

}