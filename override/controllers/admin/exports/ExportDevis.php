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

        return $header;
    }

    public function export() {
        $csv = implode($this->separator, $this->getHeader()).parent::END_OF_LINE;

        $date_begin = "'".date('Y-m-d H:i:s', strtotime(Tools::getValue('date_begin')))."'";

        $date_end = "'".date('Y-m-d H:i:s', strtotime(Tools::getValue('date_end') . ' +1 day'))."'";

        foreach (Db::getInstance()->executeS("Select id_quotation from ps_quotation WHERE date_add >=".$date_begin." and date_add <= ".$date_end) as $row){

            $quotation = new Quotation($row["id_quotation"]);
            $employee = new Employee($quotation->id_employee);
            $data = array();
            $data[] = $quotation->reference;
            $data[] = $employee->firstname." ".($employee)->lastname;
            $data[] = $quotation->date_add;
            $data[] = $quotation->getStatusLabel();
            $data[] = $quotation->getOrder() ? $quotation->getOrder()->getUniqReference() : "";
            $data[] = $quotation->getOrder() ? $quotation->getOrder()->getDatePaid() : "";
            $data[] = $quotation->getPrice();

            $csv .= implode($this->separator, $data).parent::END_OF_LINE;
        }
        $this->renderCSV("devis_".date('d-m_H-i').".csv", $csv);
    }
}