<?php

/**
* Utilisation des options dans le pani
**/
class OrderOptionCartCore extends ObjectModel {

    const TABLE_NAME = 'order_option_cart';
    const TABLE_PRIMARY = 'id';

    public $id; 
    public $id_option; 
	public $id_cart; 

	/**
    * @see ObjectModel::$definition
    **/
    public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => self::TABLE_PRIMARY,
        'fields' => array(
            'id_option' => array('type'=>self::TYPE_INT, 'validate'=>'isInt', 'required'=>true),
            'id_cart' => array('type'=>self::TYPE_INT, 'validate'=>'isInt', 'required'=>true)
        )
    );

    /**
    * Vérifie si une option est présente dans un panier
    **/
    public static function hasAssociation($id_cart, $id_option) {
        Return Db::getInstance()->getValue("SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE id_cart = $id_cart AND id_option = $id_option");
    }

    /**
    * Retourne l'option associée
    **/
    public function getOption() {
        return new OrderOption($this->id_option);
    }

    /**
    * Retourne la liste des options présentes dans un panier
    **/
    public static function findByCart($id_cart = null) {

        if(!$id_cart)
            $id_cart = Context::getContext()->cart->id;

        $data = array();

        if($id_cart)
            foreach(Db::getInstance()->executeS("SELECT id_option FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE id_cart = $id_cart") as $row)
                $data[] = new OrderOption($row['id_option']);

        return $data;
    }

    /**
    * Retourne le prix total des options d'un panier
    **/
    public static function getCartTotal($id_cart = null) {

        if(!$id_cart)
            $id_cart = Context::getContext()->cart->id;
        
        $total = 0;
        foreach(self::findByCart($id_cart) as $option)
            $total += $option->getPrice();

        return $total;
    }

    /**
    * Supprime l'option de tous les paniers en cours
    **/
    public static function purge($id_option) {
        Db::getInstance()->execute("DELETE FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE id_option = $id_option AND id_cart NOT IN (SELECT id_cart FROM ps_orders)");
    }

}