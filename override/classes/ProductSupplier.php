<?php

class ProductSupplier extends ProductSupplierCore {

	/**
	* Supprimer un fournisseur pour une déclinaison
	**/
	public static function removeCombination($id_product_attribute) {
		Db::getInstance()->execute("DELETE FROM ps_product_supplier WHERE id_product_attribute = ".$id_product_attribute);
	}
	
}