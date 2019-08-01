<?php

class AdminQuotationsController extends AdminController {

    public function __construct() {

        $this->bootstrap = true;
        $this->show_toolbar = false;

        parent::__construct();
    }

    public function displayAjax() {
    	switch(Tools::getValue('action')) {
    		
    		case 'add_product':
    			$this->addProduct();
    		break;

    		case 'product_details':
    			$this->productDetails();
    		break;

            case 'load_quotations':
                $this->loadQuotations();
            break;
    	}
    }

    public function initContent() {

    	parent::initContent();

        // Gestion de la liste des devis
        $this->downloadQuotation();
        $this->removeQuotation();
        $this->copyQuotation();

        // Gestion des produits d'un devis
    	$this->saveProducts();
    	$this->removeProduct();

    	if(Tools::getIsset('details')) {

    		$quotation = new Quotation(Tools::getValue('id'));

    		if(Tools::getIsset('quotation')) {

    			$form = Tools::getValue('quotation');

    			$quotation->reference = $form['reference'];
    			$quotation->status = $form['status'];
    			$quotation->id_customer = $form['id_customer'];
    			$quotation->origin = $form['origin'];
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
                
                if(!$quotation->id)
                    $quotation->date_add = date('Y-m-d H:i:s');

			    $quotation->save();
			    $this->context->smarty->assign('validation', "Devis enregistré");
    		}

    		$quotation->id_employee = $this->context->cookie->id_employee;
    		$quotation->date_begin = new DateTime('today');
    		
    		$this->context->controller->addjQueryPlugin('select2');

    		$this->context->smarty->assign('quotation', $quotation);
    		$this->context->smarty->assign('states', Quotation::getStates());
    		$this->context->smarty->assign('origins', Quotation::getOrigins());
    		$this->context->smarty->assign('employees', Employee::getEmployees());
    		$this->context->smarty->assign('customers', Customer::getCustomers());
    		$this->context->smarty->assign('products', Product::getSimpleProducts(1));
            $this->context->smarty->assign('suppliers', Supplier::getSuppliers());

			$this->setTemplate("details.tpl");
    	}
    }

    /**
    * Télécharge la version PDF d'un devis
    **/
    public function downloadQuotation() {

        if(Tools::getIsset('dl_pdf') and $id = Tools::getValue('id')) {
            $pdf = new PDF(array('quotation'=>new Quotation($id)), PDF::TEMPLATE_QUOTATION, $this->context->smarty);
            die($pdf->render());
        }
    }

    /**
    * Charge la liste des devis
    **/
    private function loadQuotations() {

        $options['reference'] = Tools::getValue('reference');
        $options['date_add'] = Tools::getValue('date');
        $options['id_customer'] = Tools::getValue('customer');
        $options['id_employee'] = Tools::getValue('employee');
        if($id = Tools::getValue('state')) $options['states'] = array($id);

        $tpl = $this->context->smarty->createTemplate(_PS_ROOT_DIR_."/override/controllers/admin/templates/quotations/helpers/quotation_lines.tpl");
        $this->context->smarty->assign('quotations', Quotation::find($options));
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

    	$product = new Product(Tools::getValue('id_product'), false, 1);
    	if($product->id) {
    		$product->id_product_attribute = Tools::getValue('id_combination');

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
    * Récupère les informations d'un produit
    **/
    private function productDetails() {

    	$id_product = Tools::getValue('id_product');
    	$attributes = Product::getAttributesInformationsByProduct($id_product);

    	$combinations = array();
    	foreach($attributes as $attribute) {

    		$combinations[$attribute['reference']]['id'] = $attribute['id_attribute'];
    		$combinations[$attribute['reference']]['name'][] = $attribute['group']." : ".$attribute['attribute'];
    	}

    	foreach($combinations as $key => $combination)
    		$combinations[$key]['name'] = implode(" - ", $combination['name']);

    	$tpl = $this->context->smarty->createTemplate(_PS_ROOT_DIR_."/override/controllers/admin/templates/quotations/helpers/view/product_details.tpl");
    	$this->context->smarty->assign('combinations', $combinations);
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