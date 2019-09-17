<?php

class QuotationAssociation extends ObjectModel {

	const TABLE_NAME = 'quotation_association';
	const TABLE_PRIMARY = 'id_quotation_association';

	public $id_quotation;
	public $id_cart;

	public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => self::TABLE_PRIMARY,
        'fields' => array(
            'id_quotation' => array('type' => self::TYPE_INT),
            'id_cart' => array('type' => self::TYPE_INT)
        )
    );

    /**
    * Vérifie si un devis existe dans le panier d'un client
    * @param int $id_quotation
    * @param int $id_cart
    * @return boolean
    **/
    public static function has($id_quotation, $id_cart = null) {

        if(!$id_cart)
            $id_cart = Context::getContext()->cart->id;

    	return (bool) Db::getInstance()->getValue("SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE id_cart = $id_cart AND id_quotation = $id_quotation");
    }

    /**
    * Retourne les devis d'un panier client
    * @param int $id_cart
    * @return array
    **/
    public static function find($id_cart = null) {

        if(!$id_cart)
            $id_cart = Context::getContext()->cart->id;

        $data = array();

        if($id_cart)
            foreach(Db::getInstance()->executeS("SELECT id_quotation FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE id_cart = $id_cart") as $row)
                $data[] = new Quotation($row['id_quotation']);

        return $data;
    }

    /**
    * Ajoute un devis dans un panier
    * @param int $id_quotation
    * @param int $id_cart
    * @return bool
    **/
    public static function addToCart($id_quotation, $id_cart = null) {

        if(!$id_cart)
            $id_cart = Context::getContext()->cart->id;

    	if(self::has($id_cart, $id_quotation))
    		return false;

    	$association = new QuotationAssociation();
    	$association->id_cart = $id_cart;
    	$association->id_quotation = $id_quotation;
    	return $association->save();
    }

    /**
    * Supprime un devis du panier
    * @param int $id_quotation
    * @param int $id_cart
    **/
    public static function removeFromCart($id_quotation, $id_cart = null) {

        if(!$id_cart)
            $id_cart = Context::getContext()->cart->id;

        Db::getInstance()->execute("DELETE FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE id_quotation = $id_quotation AND id_cart = $id_cart");
    }
    
    /**
    * Compte le nombre de produits dans un panier
    * @param int $id_cart
    * @return int
    **/
    public static function countProducts($id_cart = null) : int {

       if(!$id_cart)
            $id_cart = Context::getContext()->cart->id;

        return (int)Db::getInstance()->getValue("SELECT COUNT(*) FROM "._DB_PREFIX_.self::TABLE_NAME." a, "._DB_PREFIX_.QuotationLine::TABLE_NAME." l WHERE a.id_quotation = l.id_quotation AND a.id_quotation = $id_cart");  
    }

}