<?php

class CartRule extends CartRuleCore {

	public static function findUsed($date_begin, $date_end, $use_taxes = fale) {

		if(!is_string($date_begin)) $date_begin = $date_begin->format('Y-m-d 00:00:00');
        if(!is_string($date_end)) $date_end = $date_end->format('Y-m-d 23:59:59');

        if($use_taxes) $field = "ocr.value";
        else $field = "ocr.value_tax_excl";

		return Db::getInstance()->executeS("SELECT ocr.name, SUM($field) AS value FROM `ps_order_cart_rule` ocr, ps_orders o WHERE ocr.id_order = o.id_order AND o.date_add >= '$date_begin' AND o.date_add <= '$date_end' GROUP BY ocr.id_cart_rule");
	}

}