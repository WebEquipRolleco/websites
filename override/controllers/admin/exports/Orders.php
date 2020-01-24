<?php

class ExportOrders extends Export {

	/**
    * Retourne l'entête des colonnes
    * @return array
    **/
    private function getHeader() {

        $header[] = "Date";
        $header[] = "Commande";
        $header[] = "Référence Web";
        $header[] = "Famille";
        $header[] = "Fournisseur";
        $header[] = "Site Web";
        $header[] = "Référence fournisseur";
        $header[] = "Désignation";
        $header[] = "Quantité";
        $header[] = "Montant net";
        $header[] = "Prix d'achat";
        $header[] = "Marge";
        $header[] = "Taux de marge";
        $header[] = "Cumul CA";
        $header[] = "Nbr de commande";
        $header[] = "Num client Web";
        $header[] = "Date d'expédition";
        $header[] = "Num client M3";
        $header[] = "Client";
        $header[] = "Type";
        $header[] = "Mode de paiement";
        $header[] = "Avec SAV";

        return $header;
    }

    /**
    * Exporte une fichier CSV
    **/
    function export() {

        $csv = implode($this->separator, $this->getHeader()).parent::END_OF_LINE;

        $options['date_begin'] = Tools::getValue('date_begin');
        $options['date_end'] = Tools::getValue('date_end');
        
        $options['shops'] = array();
        foreach(Tools::getValue('shops') as $id => $value)
            if($value) $options['shops'][] = $id;

        foreach(Order::findIds($options) as $id) {
            $order = new Order($id);

            $new_order = true;
            $total = 0;

            foreach($order->getDetails() as $detail) {
                
                $total += $detail->total_price_tax_excl;
                $buying_price = $detail->getTotalBuyingPrice();
                $margin = $order->total_products - $buying_price;
                $margin_rate = ($order->total_products > 0) ? Tools::getMarginRate($margin, $order->total_products) : 0;

                $data = array();
                $data[] = $order->getDate('date_add')->format('d/m/Y');
                $data[] = $order->reference;
                $data[] = $detail->product_reference;
                $data[] = $detail->product_id ? $this->findFamily($detail->product_id) : '-';
                $data[] = $detail->getSupplier() ? $detail->getSupplier()->reference." - ".$detail->getSupplier()->name : '-';
                $data[] = $order->getShop()->name;
                $data[] = $order->product_supplier_reference;
                $data[] = $detail->product_name;
                $data[] = $detail->product_quantity;
                $data[] = round($detail->total_price_tax_excl, 2);
                $data[] = round($buying_price, 2);
                $data[] = round($margin, 2);
                $data[] = round($margin_rate, 2);
                $data[] = round($total, 2);
                $data[] = (int)$new_order;
                $data[] = $order->getCustomer()->id;
                $data[] = $order->getDate('delivery_date')->format('d/m/Y');
                $data[] = $order->getCustomer()->reference;
                $data[] = $order->getCustomer()->firstname." ".$order->getCustomer()->lastname;
                $data[] = $order->getCustomer()->getType() ? $order->getCustomer()->getType()->name : '-';
                $data[] = $order->payment;
                $data[] = AfterSale::hasAfterSales($order->id) ? "oui" : "non";

                $csv .= implode($this->separator, $data).parent::END_OF_LINE;
                $new_order = false;
            }
        }

        $this->RenderCSV("commandes_".date('d-m_H-i').".csv", $csv);
    }

    /**
    * Retourne la famille associée à un produit
    * @return string
    **/
    private function findFamily($id_product) {
        return Db::getInstance()->getValue("SELECT cl.name FROM ps_product p, ps_category_lang cl WHERE p.id_category_default = cl.id_category AND p.id_product = $id_product");
    }
    
}