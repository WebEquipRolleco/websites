<?php

class ExportBeezup extends Export {

	/**
    * Retourne l'entête des colonnes
    * @return array
    **/
    private function getHeader() {

        $header[] = "Référence produit";
		$header[] = "Marque Rolleco";
		$header[] = "Titre";
		$header[] = "Description";
		$header[] = "Prix HT min";
		$header[] = "Prix HT max";
		$header[] = "Prix HT barré";
		$header[] = "Prix TTC min";
		$header[] = "Prix Barré TTC min";
		$header[] = "Prix TTC max";
		$header[] = "Prix Barré TTC max";	
		$header[] = "Etat";
		$header[] = "URL produit";
		$header[] = "URL image";
		$header[] = "URL Image Google";
		$header[] = "Frais de port Rolleco";
		$header[] = "Stock";
		$header[] = "Délai de livraison";
		$header[] = "Catégorie 1";
		$header[] = "Catégorie 2";
		$header[] = "Catégorie 3";
		$header[] = "Texte Promo Rolleco";
		$header[] = "Adwords grouping";
		$header[] = "Garantie";
		$header[] = "Devise";
		$header[] = "Adwords labels";
		$header[] = "ID";

        return $header;
    }

    /**
    * Retourne le contenu du fichier CSV
    **/
    public function getLines() {

    	$lines[] = $this->getHeader();
    	$format = Configuration::get(AdminBeezupControllerCore::CONFIG_IMG_FORMAT);

    	foreach(Db::getInstance()->executeS("SELECT id_product FROM ps_product WHERE active = 1") as $row) {

    		$product = new Product($row['id_product'], true, 1);
    		$combinations = Combination::getCombinations($product->id);

    		$link = new Link();
    		$link = $link->getProductLink($product);

    		$category_3 = new Category($product->id_category_default, true);
    		$category_2 = $this->getParentOrSameCategory($category_3);
    		$category_1 = $this->getParentOrSameCategory($category_2);

			// Export produit simple
			if(empty($combinations)) {
				if($product->reference) {

					$picture = Product::getCoverPicture($product->id);
					$picture = $picture->getFileUrl($format);

					$infos = Product::loadColumn($product->id, 2);

					$min_price_ht = SpecificPrice::getMinimumPrice($product->id);
					$max_price_ht = SpecificPrice::getMaximumPrice($product->id);

					$min_crossed_ht = SpecificPrice::getMinimumPrice($product->id, null, false, true);

					$data = array();
			    	$data[] = $product->reference;
					$data[] = "Rolléco";
					$data[] = trim($product->name.' '.$product->comment_1);
					$data[] = str_replace(array(";", "\n", "\r"), ".", strip_tags($product->description_short));
					$data[] = $min_price_ht;
					$data[] = $max_price_ht;
					$data[] = (($min_price_ht == $max_price_ht and $min_crossed_ht > 0 and $min_crossed_ht > $min_price_ht) ? $min_crossed_ht : "");
					$data[] = SpecificPrice::getMinimumPrice($product->id, null, true);
					$data[] = SpecificPrice::getMinimumPrice($product->id, null, true, true);
					$data[] = SpecificPrice::getMaximumPrice($product->id, null, true);
					$data[] = SpecificPrice::getMaximumPrice($product->id, null, true, true);
					$data[] = "Neuf";
					$data[] = $link;
					$data[] = $picture;
					$data[] = $picture;
					$data[] = 0;
					$data[] = "En stock";
					$data[] = (isset($infos['reference']) ? $infos['reference'] : 0);
					$data[] = $category_1->name;
					$data[] = $category_2->name;
					$data[] = $category_3->name;
					$data[] = "Livraison gratuite dès 1€ !";
					$data[] = $category_3->name;
					$data[] = 1;
					$data[] = "EUR";
					$data[] = $category_3->name;
					$data[] = $product->id;

					$lines[] = $data;
				}
			}
			// Export déclinaison
			else {
				foreach($combinations as $combination) {
					if($combination->reference) {

						$picture = Product::getCoverPicture($product->id, $combination->id);
						$picture = $picture->getFileUrl($format);

						$infos = Combination::loadColumn($combination->id, 2);

						$min_price_ht = SpecificPrice::getMinimumPrice($product->id, $combination->id);
						$max_price_ht = SpecificPrice::getMaximumPrice($product->id, $combination->id);

						$min_crossed_ht = SpecificPrice::getMinimumPrice($product->id, $combination->id, false, true);

						$data = array();
				    	$data[] = $combination->reference;
						$data[] = "Rolléco";
						$data[] = trim($product->name.' '.$combination->comment_1);
						$data[] = str_replace(array(";", "\n"), ".", strip_tags($product->description_short));
						$data[] = $min_price_ht;
						$data[] = $max_price_ht;
						$data[] = (($min_price_ht == $max_price_ht and $min_crossed_ht and $min_crossed_ht > $min_price_ht) ? $min_crossed_ht : "");
						$data[] = SpecificPrice::getMinimumPrice($product->id, $combination->id, true);
						$data[] = SpecificPrice::getMinimumPrice($product->id, $combination->id, true, true);;
						$data[] = SpecificPrice::getMaximumPrice($product->id, $combination->id, true);
						$data[] = SpecificPrice::getMaximumPrice($product->id, $combination->id, true, true);
						$data[] = "Neuf";
						$data[] = $link;
						$data[] = $picture;
						$data[] = $picture;
						$data[] = 0;
						$data[] = "En stock";
						$data[] = (isset($infos['reference']) ? $infos['reference'] : 0);
						$data[] = $category_1->name;
						$data[] = $category_2->name;
						$data[] = $category_3->name;
						$data[] = "Livraison gratuite dès 1€ !";
						$data[] = $category_3->name;
						$data[] = 1;
						$data[] = "EUR";
						$data[] = $category_3->name;
						$data[] = $combination->id;

						$lines[] = $data;
					}
				}
			}
		}

    	return $lines;	
    }

    /**
    * Retourne la catégorie parente ou la catégorie actuelle si pas de parent
    * @param Category $category
    * @return Category
    **/
    public function getParentOrSameCategory($category) {

    	if($category->id_parent)
    		return new Category($category->id_parent, 1);

    	return $category;
    }

}