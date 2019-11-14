<?php

class OrderInvoice extends OrderInvoiceCore {

	/**
	* Met à jour les prix d'une commande (après changement des quantités)
	* Utilisation : page commande (BO)
	* @param Order $order
	**/
	public static function synchronizeOrder($order) {

		$id = Db::getInstance()->getValue("SELECT id_order_invoice FROM ps_order_invoice WHERE id_order = ".$order->id);
		if($id) {
			$invoice = new OrderInvoice($id);
			$invoice->total_paid_tax_excl = $order->total_paid_tax_excl;
			$invoice->total_paid_tax_incl = $order->total_paid_tax_incl;
			$invoice->total_products = $order->total_products;
			$invoice->total_products_wt = $order->total_products_wt;
			$invoice->total_shipping_tax_excl = $order->total_shipping_tax_excl;
			$invoice->total_shipping_tax_incl = $order->total_shipping_tax_incl;
			$invoice->save();
		}
	}

}