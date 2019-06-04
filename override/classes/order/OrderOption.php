<?php

/**
* Options modifiant le coût total de la commande
**/
class OrderOptionCore extends ObjectModel {

    const TABLE_NAME = 'order_option';
    const TABLE_PRIMARY = 'id_order_option';
    const DELIMITER = ",";

    const TYPE_PERCENT = 1;
    const TYPE_FLAT = 2;

    /** @var string Day **/
	public $name;

    /** @var string Description **/
    public $description;

    /** @var tnt Type **/
	public $type; 

    /** @var float Value **/
	public $value;

    /** @var string White list **/
    public $white_list;

    /** @var string Black list **/
    public $black_list;

    /** @var bool Active **/
	public $active = true;

	/**
    * @see ObjectModel::$definition
    **/
    public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => self::TABLE_PRIMARY,
        'fields' => array(
            'name' => array('type'=>self::TYPE_STRING, 'validate'=>'isGenericName', 'required' => true),
        	'description' => array('type'=>self::TYPE_STRING),
        	'type' => array('type'=>self::TYPE_INT, 'validate'=>'isInt', 'required' => true),
        	'value' => array('type'=>self::TYPE_FLOAT, 'validate'=>'isFloat', 'required'=>true),
            'white_list' => array('type'=>self::TYPE_STRING),
            'black_list' => array('type'=>self::TYPE_STRING),
        	'active' => array('type'=>self::TYPE_BOOL, 'validate'=>'isBool')
        )
    );

    /**
    * Retourne le label du statut
    **/
    public function getTypeLabel() {
        return self::getTypes()[$this->type];
    }

    /**
    * Retourne la liste des options de commandes
    **/
    public static function getOrderOptions($active = true) {

        $sql = "SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME;
        if($active) $sql .= " WHERE active = 1";

        $data = array();
        $ids = Db::getInstance()->executeS($sql);

        foreach($ids as $row)
            $data[] = new self($row[self::TABLE_PRIMARY]);

        return $data;
    }

    /**
    * Retourne la liste des types de calcul de prix
    **/
    public static function getTypes() {

        $data[self::TYPE_PERCENT] = "Pourcentage (%)";
        $data[self::TYPE_FLAT] = "Montant (€)";

        return $data;
    }

    /**
    * Retourne le prix de l'option
    **/
    public function getPrice() {

        if($this->type == self::TYPE_FLAT)
            return $this->value;


        $total = Context::getContext()->cart->getOrderTotal(true, CART::ONLY_PRODUCTS);
        if($total and $this->type == self::TYPE_PERCENT)
            return ($total * $this->value) / 100;

        return 0;
    }

    /**
    * Retourne la liste blanche des produits
    **/
    public function getWhiteList($full = false) {
        
        if(!$full)
            return array_filter(explode(self::DELIMITER, $this->white_list));
        elseif($this->white_list)
            return Db::getInstance()->executeS("SELECT id_product, name FROM ps_product_lang WHERE id_lang = 1 AND id_product IN (".$this->white_list.") ORDER BY id_product ASC");
        else
            return array();
    }

    /**
    * Retourne la liste noire des produits
    **/
    public function getBlackList($full = false) {
        
        if(!$full)
            return array_filter(explode(self::DELIMITER, $this->black_list));
        elseif($this->black_list)
            return Db::getInstance()->executeS("SELECT id_product, name FROM ps_product_lang WHERE id_lang = 1 AND id_product IN (".$this->black_list.") ORDER BY id_product ASC");
        else
            return array();
    }

    /**
    * Vérifie si l'option doit être disponible dans le panier en fonction des produits
    **/
    public function display() {

        if($this->white_list or $this->black_list) {

            $ids = Db::getInstance()->executeS("SELECT id_product FROM ps_cart_product WHERE id_cart = ".Context::getContext()->cart->id);
            $ids = array_map(function($e) { return $e['id_product']; }, $ids);

            // Vérification liste noire
            if($this->black_list) {
                $bl = $this->getBlackList();
                
                foreach($ids as $id)
                    if(in_array($id, $bl))
                        return false;
            }

            // Vérification liste blanche
            if($this->white_list){
                
                foreach($this->getWhiteList() as $id)
                    if(!in_array($id, $ids))
                        return false;
            }
        }

        return $this->active;
    }

}