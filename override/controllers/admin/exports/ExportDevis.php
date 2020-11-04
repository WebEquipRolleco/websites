<?php


class ExportDevis extends Export
{
    private function getHeader() {

        $header[] = 'Numéro du devis';
        $header[] = 'Créateur';
        $header[] = 'Date création';
        $header[] = 'Statut';
        $header[] = 'Référence commande';
        $header[] = 'Date commande';
        $header[] = 'Montant HT';
        $header[] = 'Montant marge';
        $header[] = '% marge';
        $header[] = 'Type client';
        $header[] = 'Provenance';
        $header[] = 'Source';

        return $header;
    }

    public function export() {
        $csv = implode($this->separator, $this->getHeader()).parent::END_OF_LINE;

        $date_begin = "'".date('Y-m-d H:i:s', strtotime(Tools::getValue('date_begin')))."'";

        $date_end = "'".date('Y-m-d H:i:s', strtotime(Tools::getValue('date_end') . ' +1 day'))."'";

        foreach (Db::getInstance()->executeS("Select id_quotation from ps_quotation WHERE date_add >=".$date_begin." and date_add <= ".$date_end) as $row){

            $quotation = new Quotation($row["id_quotation"]);
            $employee = new Employee($quotation->id_employee);
            $order = $quotation->getOrder();
            $selling_price = 0;
            $buying_price = 0;
            foreach ($quotation->getProducts() as $detail) {
                $delivery_fees = $detail->getDeliveryFees();
                $selling_price += $detail->selling_price * $detail->quantity;
                $buying_price += ($detail->buying_price + $delivery_fees) * $detail->quantity;
            }
            $margin_total = $selling_price - $buying_price;
            $margin_rate = $margin_total  / $quotation->getPrice() * 100;
            $data = array();
            $data[] = $quotation->reference;
            $data[] = $employee->firstname." ".($employee)->lastname;
            $data[] = $quotation->date_add;
            $data[] = $quotation->getStatusLabel();
            $data[] = $order ? $order->getUniqReference() : "";
            $data[] = $order ? $order->date_add : "";
            $data[] = $quotation->getPrice();
            $data[] = $margin_total;
            $data[] = $margin_rate ?  round((($selling_price - $buying_price)  / $selling_price) * 100, 2) : 0;
            $data[] = $quotation->getCustomer() ? $quotation->getCustomer()->getCustomerType() :  '';
            $data[] = $quotation->getOriginLabel();
            $data[] = $quotation->getSourceLabel();

            $csv .= implode($this->separator, $data).parent::END_OF_LINE;
        }
        $this->renderCSV("devis_".date('d-m_H-i').".csv", $csv);
    }
}