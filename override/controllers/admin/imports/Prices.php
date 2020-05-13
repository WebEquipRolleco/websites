<?php

class ImportPrices extends Import {

	/**
    * Retourne l'alias des colonnes
    * @return array
    **/
    private function getColumns() {

        $data[] = 'id_specific_price';
        $data[] = 'id_product';
        $data[] = 'id_combination';
        $data[] = '_product_reference';
        $data[] = '_combination_reference';
        $data[] = '_name';
        $data[] = 'min_quantity';
        $data[] = "full_price";
        $data[] = "price";
        $data[] = "buying_price";
        $data[] = 'delivery_fees';
        $data[] = '_margin';
        $data[] = 'rollcash';
        $data[] = 'comment_1';
        $data[] = 'comment_2';
        $data[] = '_name_supplier';
        $data[] = '_supplier_reference';
        $data[] = "batch";
        $data[] = "ecotax";
        $data[] = '_active';
        $data[] = 'from';
        $data[] = 'to';
        $data[] = 'id_group';
        $data[] = 'id_customer';
        $data[] = 'id_shop';
        $data[] = "delete";

        return $data;
    }

    /**
    * Import des prix
    **/
    public function import() {

    	$this->openFile();  
        while($row = $this->getNextRow()) {
            if(is_array($row) and count($row) == count($this->getColumns())) {

                $row = array_combine($this->getColumns(), $row);

                // Mise à jour du prix spécifique
                $price = new SpecificPrice($row['id_specific_price']);
                $update = !empty($price->id);

                if($price->id and $row['delete']) {
                    $price->delete();
                    $this->nb_lines++;
                }
                else {

                    $price->id = $row['id_specific_price'];
                    $price->id_product = $row['id_product'];
                    $price->id_product_attribute = $row['id_combination'];
                    $price->from_quantity = $row['min_quantity'];
                    $price->comment_1 = $row['comment_1'];
                    $price->comment_2 = $row['comment_2'];
                    $price->from = Tools::isEmptyDate($row['from']) ? date('Y-01-01 00:00:00') : $row['from'];
                    $price->to = Tools::isEmptyDate($row['to']) ? date('2100-01-01 00:00:00') : $row['to'];
                    $price->id_shop = $row['id_shop'] ?? 0;
                    $price->id_group = (int)$row['id_group'];
                    $price->id_customer = (int)$row['id_customer'];
                    $price->full_price = str_replace(',', '.', $row['full_price']);
                    $price->price = str_replace(',', '.', $row['price']);
                    $price->buying_price = str_replace(',', '.', $row['buying_price']);
                    $price->delivery_fees = str_replace(',', '.', $row['delivery_fees']);
                    $price->id_currency = 0;
                    $price->id_country = 0;
                    $price->reduction = 0;
                    $price->reduction_type = "amount";

                    $price->record($update);
                    $this->nb_lines++;

                    // Mise à jour du produit ou de la déclinaison
                    if($price->getTarget()) {

                        $price->getTarget()->rollcash = str_replace(',', '.', $row['rollcash']);
                        $price->getTarget()->batch = (int)$row['batch'];
                        $price->getTarget()->custom_ecotax = str_replace(',', '.', $row['ecotax']);

                        $price->getTarget()->save();
                    }
                }
                    
            }
            else
                $this->has_errors = true;
        }

        return true;
    }

}