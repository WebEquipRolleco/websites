<?php

class ProductSupplier extends ProductSupplierCore {

	/**
	* Supprimer un fournisseur pour une déclinaison
	**/
	public static function removeProduct($id_product) {
		Db::getInstance()->execute("DELETE FROM ps_product_supplier WHERE id_product_attribute = 0 AND id_product = ".$id_product);
	}

	/**
	* Supprimer un fournisseur pour une déclinaison
	**/
	public static function removeCombination($id_product_attribute) {
		Db::getInstance()->execute("DELETE FROM ps_product_supplier WHERE id_product_attribute = ".$id_product_attribute);
	}
	
}