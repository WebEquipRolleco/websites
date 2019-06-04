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
    	}
    }

    public function initContent() {
    	
    	parent::initContent();

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

			$this->setTemplate("details.tpl");
    	}
		else {

			$this->context->smarty->assign('quotations', Quotation::find());
		}
    }

    private function addProduct() {

    	$line = new QuotationLine();
    	$line->id_quotation = Tools::getValue('id_quotation');
    	$line->position = QuotationLine::getNextPosition($line->id_quotation);
    	$line->save();

    	$product = new Product(Tools::getValue('id_product'), false, 1);
    	if($product->id) {
    		$product->id_product_attribute = Tools::getValue('id_combination');

    		$line->reference = $product->reference;
    		$line->name = $product->name;
    		$line->selling_price = $product->getPrice(false);
    		$line->save();
    	}

    	$tpl = $this->context->smarty->createTemplate(_PS_ROOT_DIR_."/override/admin/templates/quotations/helpers/view/product_line.tpl");
    	$this->context->smarty->assign('line', $line);
    	$data['view'] = $tpl->fetch();

    	die(json_encode($data));
    }

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

    	$tpl = $this->context->smarty->createTemplate(_PS_ROOT_DIR_."/override/admin/templates/quotations/helpers/view/product_details.tpl");
    	$this->context->smarty->assign('combinations', $combinations);
    	$data['view'] = $tpl->fetch();

    	die(json_encode($data));
    }

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
    			$line->quantity = $form['quantity'];
    			$line->comment = $form['comment'];
    			$line->save();
    		}
    }

    private function removeProduct() {

    	$id = Tools::getValue('remove_product');
    	if($id) {
    		$line = new QuotationLine($id);
    		if($line->id) $line->delete();
    	}
    }

}