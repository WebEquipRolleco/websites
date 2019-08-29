<?php

class AdminQuotationsController extends AdminController {

    const DELIMITER = ";";
    const END_OF_LINE = "\n";

    private $id_quotation;

    public function __construct() {

        $this->bootstrap = true;
        $this->show_toolbar = false;
        $this->table = Quotation::TABLE_NAME;
        $this->className = 'Quotation';
        $this->id_quotation = Tools::getValue('id_quotation');

        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->allow_export = true;

        parent::__construct();

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->trans('Delete selected', array(), 'Admin.Notifications.Info'),
                'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Info'),
                'icon' => 'icon-trash'
            )
        );
        
        //$this->processResetFilters();

        $this->_select = "a.*, a.id_quotation AS id, CONCAT(c.firstname, ' ', c.lastname) AS customer, CONCAT(e.firstname, ' ', e.lastname) AS employee";
        $this->_join = ' LEFT JOIN '._DB_PREFIX_.'customer c ON (a.id_customer = c.id_customer)';
        $this->_join .= ' LEFT JOIN '._DB_PREFIX_.'employee e ON (a.id_employee = e.id_employee)';

        $this->fields_list = array(
            'reference' => array(
                'title' => $this->trans('Référence', array(), 'Admin.Global'),
                'filter_key' => 'a!reference'
            ),
            'customer' => array(
                'title' => $this->trans('Client', array(), 'Admin.Global'),
                'align' => 'text-center',
            ),
            'employee' => array(
                'title' => $this->trans('Créateur', array(), 'Admin.Global'),
                'align' => 'text-center',
            ),
            'origin' => array(
                'title' => $this->trans('Origine', array(), 'Admin.Global'),
                'align' => 'text-center',
                'callback' => 'formatOrigin',
            ),
            'source' => array(
                'title' => $this->trans('Source', array(), 'Admin.Global'),
                'align' => 'text-center',
                'callback' => 'formatSource',
            ),
            'status' => array(
                'title' => $this->trans('Etat', array(), 'Admin.Global'),
                'align' => 'text-center',
                'callback' => 'formatStatus',
            ),
            'active' => array(
                'title' => $this->trans('Actif', array(), 'Admin.Global'),
                'align' => 'text-center',
                'active' => 'status',
                'type' => 'bool',
                'search' => false
            ),
            'date_add' => array(
                'title' => $this->trans('Création', array(), 'Admin.Global'),
                'align' => 'text-center',
                'callback' => 'formatDate',
                'type' => 'date',
            ),
            'id' => array(
                'title' => $this->trans('Actions', array(), 'Admin.Global'),
                'align' => 'text-center',
                'callback' => 'formatActions',
                'search' => false
            ),
        );
    }

    public function formatDate($value) {
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $value);
        return $date->format('d/m/Y');
    }

    public function formatOrigin($value) {
       $origins = Quotation::getOrigins();
       return $origins[$value] ?? '-';
    }

    public function formatSource($value) {
        $sources = Quotation::getSources();
        return $sources[$value] ?? '-';
    }

    public function formatStatus($value) {
        $quotation = new Quotation();
        $quotation->status = $value;

        return "<span class='label label-".$quotation->getStatusClass()."'><b>".$quotation->getStatusLabel()."</b></span>";
    }

    public function formatActions($value) {
        $tpl = $this->context->smarty->createTemplate(_PS_ROOT_DIR_."/override/controllers/admin/templates/quotations/actions.tpl");
        $this->context->smarty->assign('id', $value);
        
        return $tpl->fetch();
    }

    public function displayAjax() {
    	switch(Tools::getValue('action')) {
    		
    		case 'add_product':
    			$this->addProduct();
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
        $this->copyQuotation();

        // Gestion des produits d'un devis
    	$this->saveProducts();
    	$this->removeProduct();

    	if(Tools::getIsset('updatequotation') or Tools::getIsset('addquotation')) {

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
			    $this->confirmations[] = "Devis enregistré";
    		}
    		
    		$this->context->controller->addjQueryPlugin('select2');

    		$this->context->smarty->assign('quotation', $quotation);
            $this->context->smarty->assign('employee', $this->context->employee);
    		$this->context->smarty->assign('states', Quotation::getStates());
            $this->context->smarty->assign('origins', Quotation::getOrigins());
    		$this->context->smarty->assign('sources', Quotation::getSources());
    		$this->context->smarty->assign('employees', Employee::getEmployees());
    		$this->context->smarty->assign('customers', Customer::getCustomers());
    		$this->context->smarty->assign('products', Product::getSimpleActiveProducts(1, true));
            $this->context->smarty->assign('suppliers', Supplier::getSuppliers());
            $this->context->smarty->assign('shops', Shop::getShops());

			$this->setTemplate("details.tpl");
    	}
    }

    /**
    * Export des devis
    **/
    public function export() {
        if(Tools::isSubmit('export')) {

            $header = array("Numéro de devis", "Boutique", "Créateur", "Date de création", "Statut", "Référence commande", "Date commande", "Montant HT", "Montant marge", "taux de marge", "Type client", "Méthode de contact", "Source");

            $csv = implode(self::DELIMITER, $header).self::END_OF_LINE;

            foreach(Quotation::find() as $quotation) {

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

            $this->confirmations[] = "Les produits ont été ajoutés au panier client";
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
    * Copie un devis
    **/
    private function copyQuotation() {
        if(Tools::getIsset('dupplicate') and $this->id_quotation) {

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

                $this->confirmations[] = 'Une copie du devis a été créée.';
            }
        }
    }
    /**
    * Ajoute un produit à un devis
    **/
    private function addProduct() {

    	$line = new QuotationLine();
    	$line->id_quotation = $this->id_quotation;
    	$line->position = QuotationLine::getNextPosition($line->id_quotation);

        $infos = Tools::getValue('product_infos');
        $infos = explode('_', $infos);

    	$product = new Product(($infos[0] ?? null), false, 1);
    	if($product->id) {

            $line->id_supplier = $product->id_supplier;
    		$line->reference = $product->reference;
            $line->reference_supplier = $product->supplier_reference;
    		$line->name = $product->name;

            $line->buying_price = $product->wholesale_price;
    		$line->selling_price = $product->getPrice(false);
            $line->eco_tax = $product->ecotax;

            $line->min_quantity = $product->minimal_quantity;
            if($line->min_quantity) $line->quantity = $line->min_quantity;

            // Gestion déclinaison
            $product->id_product_attribute = $infos[1] ?? null;
            if($product->id_product_attribute and $combination = new Combination($product->id_product_attribute)) {

                $line->information = Product::getCombinationName($product->id_product_attribute);
                $line->reference = $combination->reference;
                $line->reference_supplier = Product::getSupplierReference($product->id, $product->id_product_attribute);
                
                $line->buying_price = round($combination->wholesale_price, 2);
                $line->selling_price = Combination::getPrice($combination->id);
                $line->eco_tax = $combination->ecotax;

                $line->min_quantity = $combination->minimal_quantity;
                if($line->min_quantity) $line->quantity = $line->min_quantity;
            }
    		
    	}

        $line->save();

        // Gestion de l'image
        $image = Product::getCoverPicture($product->id, $product->id_product_attribute);
        if($img_path = $image->getProductFilePath('cart'))
            @copy($img_path, $line->getDirectory(true).$line->getFileName());

    	$tpl = $this->context->smarty->createTemplate(_PS_ROOT_DIR_."/override/controllers/admin/templates/quotations/helpers/view/product_line.tpl");
    	$this->context->smarty->assign('line', $line);
        $this->context->smarty->assign('suppliers', Supplier::getSuppliers());
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
                $line->reference_supplier = $form['reference_supplier'];
    			$line->name = $form['name'];
    			$line->information = $form['information'];
                $line->buying_price = $form['buying_price'];
    			$line->buying_fees = $form['buying_fees'];
    			$line->selling_price = $form['selling_price'];
                $line->id_supplier = $form['id_supplier'];
    			$line->quantity = $form['quantity'];
    			$line->comment = $form['comment'];
                $line->position = $form['position'];
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