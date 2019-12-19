<?php

class CartRule extends CartRuleCore {

	public static function findUsed($date_begin, $date_end, $use_taxes = fale) {

		$options['date_begin'] = $date_begin;
        $options['date_end'] = $date_end;
        $ids = Order::findIds($options);

        if(!$ids)
        	return array();
        
        if($use_taxes) $field = "ocr.value";
        else $field = "ocr.value_tax_excl";

		return Db::getInstance()->executeS("SELECT ocr.name, SUM($field) AS value FROM `ps_order_cart_rule` ocr WHERE ocr.id_order IN (".implode(',', $ids).") GROUP BY ocr.id_cart_rule");
	}

}