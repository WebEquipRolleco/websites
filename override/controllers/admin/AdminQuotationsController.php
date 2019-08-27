<?php

class AdminQuotationsController extends AdminController {

    const DELIMITER = ";";
    const END_OF_LINE = "\n";

    private $id_quotation;

    public function __construct() {

        $this->bootstrap = true;
        $this->show_toolbar = false;

        parent::__construct();

        $this->id_quotation = Tools::getValue('id');
    }

    public function displayAjax() {
    	switch(Tools::getValue('action')) {
    		
    		case 'add_product':
    			$this->addProduct();
    		break;

            case 'load_quotations':
                $this->loadQuotations();
            break;
    	}
    }

    public function initContent() {

    	parent::initContent();

        // Export des devis
        $this->export();

        // Ajouter au panier
        $this->addToCart();

        // Gestion de la liste des devis
        $this->downloadQuotation();
        $this->removeQuotation();
        $this->copyQuotation();

        // Gestion des produits d'un devis
    	$this->saveProducts();
    	$this->removeProduct();

    	if(Tools::getIsset('details')) {

    		$quotation = new Quotation($this->id_quotation);

            // Initialisation du devis
            if(!$quotation->id) {
    
                $quotation->id_shop = $this->context->shop->id;
                $quotation->id_employee = $this->context->cookie->id_employee;
                $quotation->date_begin = new DateTime('today');
                $quotation->date_add = date('Y-m-d H:i:s');

                $quotation->date_end = clone($quotation->date_begin);
                $quotation->date_end->modify('+30 days');
                $quotation->date_end = $quotation->date_end->format('Y-m-d H:i:s');
            }

            // Enregistrement des informations
    		if(Tools::getIsset('quotation')) {

    			$form = Tools::getValue('quotation');

    			$quotation->reference = $form['reference'];
    			$quotation->status = $form['status'];
    			$quotation->id_customer = $form['id_customer'];
                $quotation->origin = $form['origin'];
    			$quotation->source = $form['source'];
    			$quotation->email = $form['email'];
    			$quotation->hidden_emails = $form['hidden_emails'];
    			$quotation->date_begin = $form['date_begin'];
			    $quotation->date_end = $form['date_end'];
			    $quotation->date_recall = $form['date_recall'];
			    $quotation->phone = $form['phone'];
			    $quotation->fax = $form['fax'];
			    $quotation->comment = $form['comment'];
			    $quotation->details = $form['details'];
			    $quotation->id_employee = $form['id_employee'];
			    $quotation->active = $form['active'];
                $quotation->new = $form['new'];
                $quotation->highlight = $form['highlight'];
                $quotation->option_ids = implode(Quotation::DELIMITER, $form['options']);
                //$quotation->id_shop = $form['id_shop'];

			    $quotation->save();
			    $this->context->smarty->assign('validation', "Devis enregistré");
    		}
    		
    		$this->context->controller->addjQueryPlugin('select2');

    		$this->context->smarty->assign('quotation', $quotation);
            $this->context->smarty->assign('employee', $this->context->employee);
    		$this->context->smarty->assign('states', Quotation::getStates());
            $this->context->smarty->assign('origins', Quotation::getOrigins());
    		$this->context->smarty->assign('sources', Quotation::getSources());
    		$this->context->smarty->assign('employees', Employee::getEmployees());
    		$this->context->smarty->assign('customers', Customer::getCustomers());
    		$this->context->smarty->assign('products', Product::getSimpleActiveProducts());
            $this->context->smarty->assign('suppliers', Supplier::getSuppliers());
            $this->context->smarty->assign('shops', Shop::getShops());

			$this->setTemplate("details.tpl");
    	}
    }

    /**
    * Récupère les options de filtre
    **/ 
    private function getOptions() {

        $options['reference'] = Tools::getValue('reference');
        $options['date_add'] = Tools::getValue('date');
        $options['id_customer'] = Tools::getValue('customer');
        $options['id_employee'] = Tools::getValue('employee');
        if($id = Tools::getValue('state')) $options['states'] = array($id);

        return $options;
    }

    /**
    * Export des devis
    **/
    public function export() {
        if(Tools::isSubmit('export')) {

            $header = array("Numéro de devis", "Boutique", "Créateur", "Date de création", "Statut", "Référence commande", "Date commande", "Montant HT", "Montant marge", "taux de marge", "Type client", "Méthode de contact", "Source");

            $csv = implode(self::DELIMITER, $header).self::END_OF_LINE;

            foreach(Quotation::find($this->getOptions()) as $quotation) {

                $row['reference'] = $quotation->reference;
                $row['shop'] = $quotation->getShop() ? $quotation->getShop()->name : null;
                $row['employee'] = $quotation->getEmployee() ? $quotation->getEmployee()->firstname." ".$quotation->getEmployee()->lastname : null; 
                $row['date_add'] = $quotation->date_add;
                $row['status'] = $quotation->getStatusLabel();
                $row['order_reference'] = null; 
                $row['order_date'] = null;
                $row['price'] = $quotation->getPrice();
                $row['margin'] = $quotation->getMargin();
                $row['margin_rate'] = Tools::getMarginRate($row['margin'], $row['price']);
                $row['customer_type'] = ($quotation->getCustomer() and $quotation->getCustomer()->getType()) ? $quotation->getCustomer()->getType()->name : null;
                $row['customer_method'] = $quotation->getOriginLabel();
                $row['source'] = $quotation->getSourceLabel();

                $csv .= implode(self::DELIMITER, $row).self::END_OF_LINE;
            }

            header('Content-Disposition: attachment; filename="export.csv";');
            die($csv);
        }
    }

    /**
    * Ajoute les produits du devis à un client
    **/
    private function addToCart() {
        if(Tools::isSubmit('add_to_customer') and $id_customer = Tools::getValue('id_customer')) {

            $cart = Customer::getLastCart($id_customer);
            $quotation = new Quotation($this->id_quotation);

            foreach($quotation->getProducts() as $line) {
                if(!QuotationAssociation::hasLine($cart->id, $line->id))
                    QuotationAssociation::addLine($cart->id, $line->id);
            }

            $this->context->smarty->assign('validation', "Les produits ont été ajoutés au panier client");
        }
    }

    /**
    * Télécharge la version PDF d'un devis
    **/
    public function downloadQuotation() {

        if(Tools::getIsset('dl_pdf') and $this->id_quotation) {
            $pdf = new PDF(array('quotation'=>new Quotation($this->id_quotation)), PDF::TEMPLATE_QUOTATION, $this->context->smarty);
            die($pdf->render());
        }
    }

    /**
    * Charge la liste des devis
    **/
    private function loadQuotations() {

        $tpl = $this->context->smarty->createTemplate(_PS_ROOT_DIR_."/override/controllers/admin/templates/quotations/helpers/quotation_lines.tpl");
        $this->context->smarty->assign('quotations', Quotation::find($this->getOptions()));
        die($tpl->fetch());
    }

    /**
    * Gère la suppression des devis
    **/
    private function removeQuotation() {
        if($id = Tools::getValue('remove_quotation')) {

            $quotation = new Quotation($id);
            if($quotation->id) {
                $quotation->delete();
                $this->context->smarty->assign('alert', array('type'=>'success', 'message'=>'Le devis a été supprimé.'));
            }
        }
    }

    /**
    * Copie un devis
    **/
    private function copyQuotation() {
        if($id = Tools::getValue('copy_quotation')) {

            $quotation = new Quotation($id);
            if($quotation->id) {

                $products = $quotation->getProducts();
                $quotation->id = null;
                $quotation->save();

                foreach($products as $product) {
                    $product->id = null;
                    $product->id_quotation = $quotation->id;
                    $product->save();
                }

                $this->context->smarty->assign('alert', array('type'=>'success', 'message'=>'Une copie du devis a été créée.'));
            }
        }
    }
    /**
    * Ajoute un produit à un devis
    **/
    private function addProduct() {

    	$line = new QuotationLine();
    	$line->id_quotation = Tools::getValue('id_quotation');
    	$line->position = QuotationLine::getNextPosition($line->id_quotation);
    	$line->save();

        $infos = Tools::getValue('product_infos');
        $infos = explode('_', $infos);

    	$product = new Product(($infos[0] ?? null), false, 1);
    	if($product->id) {
    		$product->id_product_attribute = $infos[1] ?? null;

            $line->id_supplier = $product->id_supplier;
    		$line->reference = $product->reference;
    		$line->name = $product->name;
    		$line->selling_price = $product->getPrice(false);
    		$line->save();
    	}

    	$tpl = $this->context->smarty->createTemplate(_PS_ROOT_DIR_."/override/controllers/admin/templates/quotations/helpers/view/product_line.tpl");
    	$this->context->smarty->assign('line', $line);
    	$data['view'] = $tpl->fetch();

    	die(json_encode($data));
    }

    /**
    * Enregistre la liste des produits d'un devis
    **/
    private function saveProducts() {

    	$lines = Tools::getValue('lines');
    	if(is_array($lines))
    		foreach($lines as $form) {

    			$line = new QuotationLine($form['id']);
    			$line->reference = $form['reference'];
    			$line->name = $form['name'];
    			$line->information = $form['information'];
    			$line->buying_price = $form['buying_price'];
    			$line->selling_price = $form['selling_price'];
                $line->id_supplier = $form['id_supplier'];
    			$line->quantity = $form['quantity'];
    			$line->comment = $form['comment'];
    			$line->save();

                if(isset($_FILES['lines']['name'][$form['id']])) {
                    move_uploaded_file($_FILES['lines']['tmp_name'][$form['id']], $line->getDirectory(true).$line->getFileName());
                }
    		}
    }

    /**
    * Supprime un produit d'un devis
    **/
    private function removeProduct() {

    	$id = Tools::getValue('remove_product');
    	if($id) {
    		$line = new QuotationLine($id);
    		if($line->id) $line->delete();
    	}
    }

}