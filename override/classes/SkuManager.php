<?php

class SkuManager {

	const TYPE_PRODUCT = "PRODUCT";
	const TYPE_COMBINATION = "COMBINATION";

	private $separator;
	private $prefix_product;
	private $prefix_combination;

	private $current_sku;
	private $type;
	private $properties;

	/**
	* Initialiser le manager avec un SKU
	**/
	public function __construct($sku = null) {

		$this->prefix_product = Configuration::get('SKU_PRODUCT_PREFIX');
		$this->prefix_combination = Configuration::get('SKU_COMBINATION_PREFIX');
		$this->separator = Configuration::get('SKU_SEPARATOR');

		$this->setSku($sku);
	}

	/**
	* Changer le SKU courant
	* @param string $sku
	**/
	public function setSku($sku) {
		$this->current_sku = $sku;

		$this->properties = explode($this->separator, $sku);
		if(!empty($this->properties)) $this->type = array_shift($this->properties);
	}

	/**
	* Retourne le type du SKU
	* @return string
	**/
	public function getType() {

		if($this->type == $this->prefix_product) return self::TYPE_PRODUCT;
		if($this->type == $this->prefix_combination) return self::TYPE_COMBINATION;
		return null;
	}

	/**
	* Retourne le type du SKU
	* @return string
	**/
	public function getTypeLabel() {

		$data[self::TYPE_PRODUCT] = "Produit";
		$data[self::TYPE_COMBINATION] = "Déclinaison";

		return (isset($data[$this->getType()]) ? $data[$this->getType()] : null);
	}

	/**
	* Retourne les propriétés du SKU
	**/
	public function getProperties() {

		$data = array();
		foreach($this->properties as $property) {

			$group = substr($property, 0, 3);
			$value = substr($property, 3);

			if($this->getType() == self::TYPE_PRODUCT) {
				if($result = Db::getInstance()->getRow("SELECT f.id_feature, fl.name, fv.id_feature_value, fvl.value FROM ps_feature f, ps_feature_lang fl, ps_feature_value fv, ps_feature_value_lang fvl WHERE f.id_feature = fl.id_feature AND f.id_feature = fv.id_feature AND fv.id_feature_value = fvl.id_feature_value AND f.reference = '$group' AND fv.reference = '$value'"))
					$data[] = $result;
			}

			if($this->getType() == self::TYPE_COMBINATION) {
				if($result = Db::getInstance()->getRow("SELECT ag.id_attribute_group, agl.name, a.id_attribute, al.name AS value FROM ps_attribute a, ps_attribute_lang al, ps_attribute_group ag, ps_attribute_group_lang agl WHERE a.id_attribute_group = ag.id_attribute_group AND a.id_attribute = al.id_attribute AND ag.id_attribute_group = agl.id_attribute_group AND ag.reference = '$group' AND a.reference = '$value'"))
					$data[] = $result;
			}

		}

		return $data;
	}

	/**
	* Retourne le SKU d'un produit
	* @param int $id
	* @return string
	**/
	public function getProductSku($id) {

		$sku = $this->prefix_product.$this->separator;

		$rows = Db::getInstance()->executeS("SELECT CONCAT(f.reference, fv.reference) AS sku FROM ps_feature_product fp, ps_feature f, ps_feature_value fv WHERE fp.id_feature = f.id_feature AND fp.id_feature_value = fv.id_feature_value AND fp.id_product = $id AND f.reference IS NOT NULL, fv.reference IS NOT NULL ORDER BY f.position");
		if($rows){
			$rows = array_map(function($e) { return $e['sku']; }, $rows);
			$sku .= implode($this->separator, $rows);
		}

		return $sku;
	}

	/**
	* Retourne le SKU d'une déclinaison
	* @param int $id
	* @return string
	**/
	public function getCombinationSku($id) {

		$sku = $this->prefix_combination.$this->separator;

		$rows = Db::getInstance()->executeS("SELECT CONCAT(ag.reference, a.reference) AS sku FROM ps_product_attribute_combination pac, ps_attribute a, ps_attribute_group ag WHERE pac.id_attribute = a.id_attribute AND a.id_attribute_group = ag.id_attribute_group AND pac.id_product_attribute = $id AND ag.reference IS NOT NULL AND a.reference IS NOT NULL ORDER BY ag.position");
		if($rows){
			$rows = array_map(function($e) { return $e['sku']; }, $rows);
			$sku .= implode($this->separator, $rows);
		}

		return $sku;
	}
}