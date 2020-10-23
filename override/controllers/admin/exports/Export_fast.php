<?php

class Export_fast extends Export {

    /**
     * Retourne l'entête des colonnes
     * @return array
     **/

    private function getHeader_fast_export() {

        $header[] = "Date";
        $header[] = "Commande";
        $header[] = "Site Web";
        $header[] = "Montant net";
        $header[] = "Prix d'achat";
        $header[] = "Marge";
        $header[] = "Taux de marge";
        $header[] = "Num client Web";
        $header[] = "Date d'expédition";
        $header[] = "Num client M3";
        $header[] = "Client";
        $header[] = "Type";
        $header[] = "Mode de paiement";
        $header[] = "Status de commande";

        return $header;
    }

    /**
     * Exporte une fichier CSV
     **/

    function export() {

        $csv = implode($this->separator, $this->getHeader_fast_export()).parent::END_OF_LINE;
        $options['date_begin'] = Tools::getValue('date_begin');
        $options['date_end'] = date('Y-m-d H:i:s', strtotime(Tools::getValue('date_end') . ' +1 day'));

        foreach(Order::findIds($options) as $id) {
            $order = new Order($id);

            $new_order = true;
            $total = 0;
            foreach($order->getOrder() as $detail) {

                $data = array();
                $data[] = $order->getDate('date_add')->format('d/m/Y');
                $data[] = $order->reference;
                $data[] = $order->getShop()->name;
                $data[] = round($order->getTotalPrice(), 2);
                $data[] = round($order->getBuyingPrice(), 2);
                $data[] = round($order->getTotalPrice() - $order->getBuyingPrice(), 2);
                $data[] = round((($order->getTotalPrice() - $order->getBuyingPrice()) / $order->getTotalPrice()) * 100, 2);
                $data[] = $order->getCustomer()->id;
                $data[] = $order->getDate('delivery_date')->format('d/m/Y');
                $data[] = $order->getCustomer()->reference;
                $data[] = $order->getCustomer()->firstname." ".$order->getCustomer()->lastname;
                $data[] = $order->getCustomer()->getType() ? $order->getCustomer()->getType()->name : '-';
                $data[] = $order->payment;
                $data[] = $order->getState()->name;
                $csv .= implode($this->separator, $data).parent::END_OF_LINE;
                $new_order = false;
            }
            //ajout des remise dans le csv
            if ($order->total_discounts > 0){

                if ($order->total_discounts_tax_excl == 0){
                    $margin = 0;
                } else{
                    $margin = $order->total_discounts_tax_excl * (-1);
                }
                $margin_rate = ($order->total_products > 0) ? Tools::getMarginRate($margin, $order->total_products) : 0;

                $data = array();
                $data[] = $order->getDate('date_add')->format('d/m/Y');
                $data[] = $order->reference;
                $data[] = $order->getShop()->name;
                $data[] = $this->clean($order->product_supplier_reference);
                $data[] = $this->clean('Remise');
                $data[] = $this->clean('0');
                $data[] = round($order-> total_discounts_tax_excl * -1, 2);
                $data[] =  round($order-> total_discounts_tax_excl * -1, 2);
                $data[] = round($margin, 2);
                $data[] = round($margin_rate, 2);
                $data[] = (int)$new_order;
                $data[] = $order->getCustomer()->id;
                $data[] = $order->getDate('delivery_date')->format('d/m/Y');
                $data[] = $order->getCustomer()->reference;
                $data[] = $order->getCustomer()->firstname." ".$order->getCustomer()->lastname;
                $data[] = $order->getCustomer()->getType() ? $order->getCustomer()->getType()->name : '-';
                $data[] = $order->payment;
                $data[] = $order->getState()->name;

                $csv .= implode($this->separator, $data).parent::END_OF_LINE;

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

    function clean($string) {
        $string=str_replace("\r\n","",$string); // Removes special chars.

        return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
    }

}