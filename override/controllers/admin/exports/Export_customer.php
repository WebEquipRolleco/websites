<?php


class Export_customer extends Export
{

    /**
     * Retourne l'entête des colonnes
     * @return array
     **/

    private function getHeader_export()
    {

        $header[] = "ID";
        $header[] = "Type client";
        $header[] = "Société";
        $header[] = "Prénom client";
        $header[] = "Nom";
        $header[] = "Mail";
        $header[] = "Telephone";
        $header[] = "Date derniere commande";
        $header[] = "Numero derniere commande";
        $header[] = "Montant derniere commande";
        $header[] = "Différence entre date de dernière commande et date du jour";
        $header[] = "Différence en nbre de jours entre date dernière commande et date avant dernière commande";
        $header[] = "Nombre de commande";
        $header[] = "Moyenne de commande depuis création compte";
        $header[] = "Montant total";
        $header[] = "Panier Moyen";
        $header[] = "Commentaire";



        return $header;
    }

    /**
     * Exporte une fichier CSV
     **/

    function export()
    {
        $csv = implode($this->separator, $this->getHeader_export()) . parent::END_OF_LINE;
        $options['date_begin'] = Tools::getValue('date_begin');
        $options['date_end'] = date('Y-m-d H:i:s', strtotime(Tools::getValue('date_end') . ' +1 day'));
        $sql = "SELECT id_customer FROM ps_customer";
        $date_now = date_create("now");

        foreach (Db::getInstance()->executeS($sql) as $customer_id) {
            $customer = new Customer($customer_id['id_customer']);
            $total_price_order = 0;
            foreach($customer->getOrders() as $order) {
                $total_price_order += $order->getTotalPrice();
            }
            $data = array();
            $order = $customer->getLastOrder();
            $date = $order->invoice_date;
            $date_order = date_create($order->date_add);
            $data[] = $customer->id;
            $data[] = $customer->getCustomerType();
            $data[] = $customer->company;
            $data[] = $customer->firstname;
            $data[] = $customer->lastname;
            $data[] = $customer->email;
            $data[] = $customer->getPhone();
            $data[] = $date_order->format('d/m/y');
            $data[] = $order->getUniqReference();
            $data[] = round($order->getTotalPrice(), 2);
            $data[] = (date_diff($date_order, $date_now))->format('%R%a');
            if ($customer->getPreLastOrder()) {
                $data[] = (date_diff(date_create($customer->getPreLastOrder()->date_add), $date_order))->format('%R%a');
            } else {
                $data[] = "";
            }
            $sizeOfOrder = sizeof($customer->getOrders());
            $data[] = $sizeOfOrder;
            $data[] = $sizeOfOrder / (date_diff(date_create($customer->date_add), $date_now))->format('%R%y');
            $data[] = $total_price_order;
            $data[] = round($total_price_order / $sizeOfOrder, 2);
            $data[] = preg_replace('/\s+/', ' ', $customer->note);
            $csv .= implode($this->separator, $data) . parent::END_OF_LINE;
        }
        $this->RenderCSV("historique_client_" . date('d-m_H-i') . ".csv", $csv);
    }

    /**
     * Retourne la famille associée à un produit
     * @return string
     **/

    function clean($string)
    {
        $string = str_replace("\r\n", "", $string); // Removes special chars.

        return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
    }

}
