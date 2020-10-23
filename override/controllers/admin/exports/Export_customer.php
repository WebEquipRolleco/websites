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
        $header[] = "Difference de jours";
        $header[] = "Différence de jours depuis dernière commande";
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
            $date = $order->date_add;
            $date = date_create($date);
            $order = $customer->getLastOrder();
            $date_order = date_create($order->date_add);
            $data = array();
            $data[] = $customer->id;
            $data[] = $customer->getCustomerType();
            $data[] = $customer->company;
            $data[] = $customer->firstname;
            $data[] = $customer->lastname;
            $data[] = $customer->email;
            $data[] = $customer->getPhone();
            $data[] = $date->format('d/m/y');
            $data[] = $order->getUniqReference();
            $data[] = round($order->getTotalPrice(), 2);
            $data[] = (date_diff($date_order, $date_now))->format('%R%a');
            if ($customer->getPreLastOrder()) {
                $data[] = (date_diff(date_create($customer->getPreLastOrder()->date_add), $date_order))->format('%R%a');
            } else {
                $data[] = "";
            }
            $data[] = sizeof($customer->getOrders());
            $data[] = sizeof($customer->getOrders()) / (date_diff(date_create($customer->date_add), $date_now))->format('%R%y');
            $data[] = $total_price_order;
            $data[] = round($total_price_order / sizeof($customer->getOrders()), 2);
            $data[] = $customer->note;
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
