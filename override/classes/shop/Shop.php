<?php

class Shop extends ShopCore {

    const CONDITIONS_DIR = "/documents/";

    const PDF_IDENTITY = "fichier_identite";
    const PDF_SALES = "conditions_ventes";
    const PDF_PAYMENT = "conditions_paiement";

	/** @var string Préfix de la référence commande **/
    public $reference_prefix;

    /** @var int Longueur de la référence **/
    public $reference_length;

    /** @var string Préfix de la référence des devis **/
    public $quotation_prefix;

    /** @var int Indice actuelle de la référence des devis **/
    public $quotation_number = 0;

    /** @var string Color **/
    public $color;

    /**
    * OVERRIDE : ajout de variables
    * @param int $id
    * @param int $id_lang
    * @param int $id_shop
    **/
    public function __construct($id = null, $id_lang = null, $id_shop = null) {
        
		self::$definition['fields']['reference_prefix'] = array('type'=>self::TYPE_STRING, 'validate' => 'isString');
		self::$definition['fields']['reference_length'] = array('type'=>self::TYPE_INT, 'validate' => 'isUnsignedId');
        self::$definition['fields']['quotation_prefix'] = array('type'=>self::TYPE_STRING, 'validate' => 'isString');
        self::$definition['fields']['quotation_number'] = array('type'=>self::TYPE_INT, 'validate' => 'isUnsignedId');
        self::$definition['fields']['color'] = array('type'=>self::TYPE_STRING, 'validate' => 'isString');

		parent::__construct($id, $id_lang, $id_shop);
	}

    /**
    * @return int
    **/
    public function getContextualShopId() {
        return (int)self::$context_id_shop ?? 1;
    }

    /**
    * Load list of groups and shops, and cache it
    * @param bool $refresh
    **/
    public static function cacheShops($refresh = false)
    {
        if (!is_null(self::$shops) && !$refresh) {
            return;
        }

        self::$shops = array();

        $from = '';
        $where = '';

        $employee = Context::getContext()->employee;

        // If the profile isn't a superAdmin
        if (Validate::isLoadedObject($employee) && $employee->id_profile != _PS_ADMIN_PROFILE_) {
            $from .= 'LEFT JOIN '._DB_PREFIX_.'employee_shop es ON es.id_shop = s.id_shop';
            $where .= 'AND es.id_employee = '.(int)$employee->id;
        }

        $sql = 'SELECT gs.*, s.*, gs.name AS group_name, s.name AS shop_name, s.active, su.domain, su.domain_ssl, su.physical_uri, su.virtual_uri
                FROM '._DB_PREFIX_.'shop_group gs
                LEFT JOIN '._DB_PREFIX_.'shop s
                    ON s.id_shop_group = gs.id_shop_group
                LEFT JOIN '._DB_PREFIX_.'shop_url su
                    ON s.id_shop = su.id_shop AND su.main = 1
                '.$from.'
                WHERE s.deleted = 0
                    AND gs.deleted = 0
                    '.$where.'
                ORDER BY gs.name, s.name';

        if ($results = Db::getInstance()->executeS($sql)) {
            foreach ($results as $row) {
                if (!isset(self::$shops[$row['id_shop_group']])) {
                    self::$shops[$row['id_shop_group']] = array(
                        'id'                => $row['id_shop_group'],
                        'name'              => $row['group_name'],
                        'color'             => $row['color'],
                        'share_customer'    => $row['share_customer'],
                        'share_order'       => $row['share_order'],
                        'share_stock'       => $row['share_stock'],
                        'shops'             => array(),
                    );
                }

                $row = $row + array('theme_name' => '');

                self::$shops[$row['id_shop_group']]['shops'][$row['id_shop']] = array(
                    'id_shop'       => $row['id_shop'],
                    'id_shop_group' => $row['id_shop_group'],
                    'name'          => $row['shop_name'],
                    'color'         => $row['color'],
                    'id_category'   => $row['id_category'],
                    'theme_name'    => $row['theme_name'],
                    'domain'        => $row['domain'],
                    'domain_ssl'    => $row['domain_ssl'],
                    'uri'           => $row['physical_uri'].$row['virtual_uri'],
                    'active'        => $row['active'],
                );
            }
        }
    }

    /**
    * Retourne le nom du fichier de conditions de ventes
    * @return string
    **/
    public function getFileName($name) {
        return $name."_".$this->id.".pdf";
    }

    /**
    * Vérifie l'existence d'un PDF de conditions de ventes
    * @return bool
    **/
    public function hasFile($name) {
        return is_file($this->getFilePath($name, true));
    }

    /**
    * Retourne le chemin du PDF des conditions de ventes
    * @param bool $full Chemin absolu ou relatif
    * @return string
    **/
    public function getFilePath($name, $full = false) {

        if($full)
            return _PS_ROOT_DIR_.self::CONDITIONS_DIR.$this->getFileName($name);
        else
            return self::CONDITIONS_DIR.$this->getFileName($name);
    }
    
    /**
    * Retourne la liste des documents
    * @return array
    **/
    public function getDocuments() {

        $data[] = array('label'=>"Fiche d'identité", 'name'=>self::PDF_IDENTITY, 'path'=>$this->getFilePath(self::PDF_IDENTITY), 'exists'=>$this->hasFile(self::PDF_IDENTITY));
        $data[] = array('label'=>"Conditions de vente", 'name'=>self::PDF_SALES, 'path'=>$this->getFilePath(self::PDF_SALES), 'exists'=>$this->hasFile(self::PDF_SALES));
        $data[] = array('label'=>"Conditions de paiement", 'name'=>self::PDF_PAYMENT, 'path'=>$this->getFilePath(self::PDF_PAYMENT), 'exists'=>$this->hasFile(self::PDF_PAYMENT));
    
        return $data;
    }
}