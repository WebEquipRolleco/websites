<?php

class QuotationAssociation extends ObjectModel {

	const TABLE_NAME = 'webequip_quotation_associations';
	const TABLE_PRIMARY = 'id';

	public $id_line;
	public $id_cart;

	public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => self::TABLE_PRIMARY,
        'fields' => array(
            'id_line' => array('type' => self::TYPE_INT),
            'id_cart' => array('type' => self::TYPE_INT)
        )
    );

    /**
    * Vérifie si une ligne devis existe dans le panier d'un client
    * @return boolean
    **/
    public static function hasLine($id_cart, $id_line) {
    	return (bool) Db::getInstance()->getValue("SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE id_cart = $id_cart AND id_line = $id_line");
    }

    /**
    * Ajoute une ligne devis dans un panier
    **/
    public static function addLine($id_cart, $id_line) {

    	if(!$id_cart or !$id_line) 
    		return false;

    	if(self::hasLine($id_cart, $id_line))
    		return false;

    	$association = new QuotationAssociation();
    	$association->id_cart = $id_cart;
    	$association->id_line = $id_line;
    	$association->save();

    	return $association;
    }

    /**
    * Récupère les lignes devis associées à un panier
    **/
    public static function getCartLines($id_cart = null) {

        if(!$id_cart)
            $id_cart = Context::getContext()->cart->id;

    	$data = array();

        if($id_cart) {
            $rows = Db::getInstance()->executeS("SELECT * FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE id_cart = ".$id_cart);
            foreach($rows as $row)
        	   $data[] = new QuotationLine($row[self::TABLE_PRIMARY]);
        }
        
    	return $data;
    }
}