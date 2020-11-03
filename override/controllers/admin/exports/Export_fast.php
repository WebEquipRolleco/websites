<?php

class Export_fast extends Export {

    /**
     * Retourne l'entête des colonnes
     * @return array
     **/

    private function getHeader_fast_export() {

        $header[] = "Date";
        $header[] = "Commande";
        $header[] = "Montant net";
        $header[] = "Prix d'achat";
        $header[] = "Marge";
        $header[] = "Taux de marge";
        $header[] = "Num client Web";
        $header[] = "Limite de paiement";
        $header[] = "Num de facture";
        $header[] = "Num client M3";
        $header[] = "Siret";
        $header[] = "Nom de société";
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
            $buying_price = 0;
            $selling_price = 0;
            foreach ($order->getDetails() as $detail) {
                $buying_price += $detail->purchase_supplier_price * $detail->product_quantity;
                $selling_price += $detail->unit_price_tax_excl * $detail->product_quantity;
            }
            if ($order->total_discounts > 0) {
                continue;
            }
            if ($order->invoice_number == 0) {
                $order->invoice_number = "";
            }
            $data = array();
            $data[] = $order->getDate('date_add')->format('d/m/Y');
            $data[] = $order->reference;
            $data[] = round($selling_price, 2);
            $data[] = round($buying_price, 2);
            $data[] = round($order->getTotalPrice() - $buying_price, 2);
            $data[] = round((($order->getTotalPrice() - $buying_price)  / $order->getTotalPrice()) * 100, 2);
            $data[] = $order->getCustomer()->id;
            $data[] = $order->getPaymentDeadline()->format('d/m/Y') == '14/01/0000' ? "" : $order->getPaymentDeadline()->format('d/m/Y');
            $data[] = $order->invoice_number;
            $data[] = $order->getCustomer()->reference;
            $data[] = $order->getCustomer()->siret;
            $data[] = $order->getCustomer()->company;
            $data[] = $order->getCustomer()->firstname." ".$order->getCustomer()->lastname;
            $data[] = $order->getCustomer()->getType() ? $order->getCustomer()->getType()->name : '-';
            $data[] = $order->payment;
            $data[] = $order->getState()->name;
            $csv .= implode($this->separator, $data).parent::END_OF_LINE;
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