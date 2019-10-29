<?php

use PrestaShop\PrestaShop\Adapter\ServiceLocator;

class AdminQuotationsController extends AdminController {

    const DELIMITER = ";";
    const END_OF_LINE = "\n";

    private $id_quotation;
    private $crypto;

    public function __construct() {

        $this->bootstrap = true;
        $this->show_toolbar = false;
        $this->table = Quotation::TABLE_NAME;
        $this->className = 'Quotation';
        $this->id_quotation = Tools::getValue('id_quotation');
        $this->crypto = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Core\\Crypto\\Hashing');

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

        $this->_select = "a.*, a.id_quotation AS id, CONCAT(c.firstname, ' ', c.lastname) AS customer, CONCAT(e.firstname, ' ', e.lastname) AS employee, (SELECT SUM(l.selling_price * l.quantity) FROM "._DB_PREFIX_.QuotationLine::TABLE_NAME." l WHERE l.id_quotation = a.id_quotation) AS price";
        $this->_join = ' LEFT JOIN '._DB_PREFIX_.'customer c ON (a.id_customer = c.id_customer)';
        $this->_join .= ' LEFT JOIN '._DB_PREFIX_.'employee e ON (a.id_employee = e.id_employee)';
        $this->_where = " AND a.id_shop = ".$this->context->shop->id;

        $this->_orderBy = 'reference';
        $this->_orderWay = 'desc';
        $this->_use_found_rows = true;

        $this->fields_list = array(
            'reference' => array(
                'title' => $this->trans('Référence', array(), 'Admin.Global'),
                'filter_key' => 'a!reference'
            ),
            'customer' => array(
                'title' => $this->trans('Client', array(), 'Admin.Global'),
                'align' => 'text-center',
            ),
            'email' => array(
                'title' => $this->trans('E-mails', array(), 'Admin.Global'),
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
            'price' => array(
                'title' => $this->trans('Montant HT', array(), 'Admin.Global'),
                'align' => 'text-center',
                'callback' => 'formatPrice',
            ),
            'status' => array(
                'title' => $this->trans('Etat', array(), 'Admin.Global'),
                'align' => 'text-center',
                'callback' => 'formatStatus',
                'type' => 'select',
                'list' => Quotation::getStates(),
                'filter_key' => 'a!status'
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
                'class' => 'fixed-width-lg',
                'remove_onclick' => true,
                'search' => false
            ),
        );

        if($order_by = Tools::getValue('quotationOrderby'))
            $this->_orderBy = $order_by;

        if($order_way = Tools::getValue('quotationOrderway'))
        $this->_orderWay = $order_way;
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

    public function formatPrice($value) {
        return Tools::displayPrice($value);
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

    /**
    * Ajoute la modal import à la page liste
    **/
    public function renderList() {

        $tpl = $this->context->smarty->createTemplate(_PS_ROOT_DIR_."/override/controllers/admin/templates/quotations/ajax.tpl");
        return parent::renderList().$tpl->fetch();
    }

    /**
    * Gestion appels AJAX
    **/
    public function displayAjax() {
    	switch(Tools::getValue('action')) {
    		
    		case 'add_product':
    			$this->addProduct();
    		break;

            case 'contact_modal':
                $this->renderContactModal();
            break;
    	}
    }

    public function postProcess() {
        parent::postProcess();

        // Export des devis
        $this->export();

        // Ajouter au panier
        $this->addToCart();

        // Téléchargement du PDF
        $this->downloadQuotation();

        // Réplication d'un devis
        $this->copyQuotation();

        // Enregistrement de la liste des produits
        $this->saveProducts();

        // Suppression d'un produit
        $this->removeProduct();

        // Envoi du mail au client
        $this->sendMail();

        // Actions groupées
        $this->handleBulkActions();
    }

    public function initContent() {

    	parent::initContent();

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

                $quotation->generateReference();
            }

            // Enregistrement des informations
    		if(Tools::getIsset('quotation')) {
    			$form = Tools::getValue('quotation');

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

                if(!$quotation->id)
                    $quotation->generateReference(true);

                if(Tools::getValue('creation')) {
                    $form = Tools::getValue('new_account'); 

                    $customer = new Customer();
                    $customer->email = $form['email'];
                    $customer->firstname = $form['firstname'];
                    $customer->lastname = $form['lastname'];
                    $customer->id_account_type = $form['id_account_type'];

                    $customer->id_shop = $quotation->id_shop;
                    $customer->quotation = Customer::QUOTATION_NEW;
                    $customer->passwd = $this->crypto->hash($quotation->reference);

                    $customer->save();
                    $quotation->id_customer = $customer->id;
                }

			    $quotation->save();

                $url = $this->context->link->getAdminLink('AdminQuotations&updatequotation&id_quotation='.$quotation->id."&token=".$this->token, false);
                Tools::redirect($url);
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
    * Envoi du mail au client
    **/
    public function sendMail() {
        if(Tools::isSubmit('send') and $this->id_quotation) {

            $quotation = new Quotation($this->id_quotation);
            $attachments = array();
            
            // Gestion du bloc "nouvel utilisateur"
            $tpl = $this->context->smarty->createTemplate(_PS_ROOT_DIR_."/override/controllers/admin/templates/quotations/account.tpl");
            $tpl->assign('quotation', $quotation);

            $data['{user}'] = $tpl->fetch();
            $data['{link}'] = $quotation->getLink();
            $data['{reference}'] = $quotation->reference;
            $data['{message}'] = str_replace("\r\n", "<br />", Tools::getValue('content'));
            $data['{shop_name}'] = Configuration::get('PS_SHOP_NAME', null, $quotation->id_shop);
            $data['{shop_email}'] = Configuration::get('PS_SHOP_EMAIL', null, $quotation->id_shop);
            $data['{shop_phone}'] = Configuration::get('PS_SHOP_PHONE', null, $quotation->id_shop);
            $data['{shop_fax}'] = Configuration::get('PS_SHOP_FAX', null, $quotation->id_shop);
            $data['{shop_url}'] = ShopUrl::getMainShopDomain($quotation->id_shop);

            // Gestion des destinataires
            $emails = explode(',', Tools::getValue('emails'));
            $emails[] = Configuration::get('PS_SHOP_EMAIL', null, $quotation->id_shop);
            
            // Gestion pièces jointes : PDF
            if(Tools::getValue('pdf')) {
                $pdf = new PDF(array('quotation'=>$quotation), PDF::TEMPLATE_QUOTATION, $this->context->smarty);
                $attachments['pdf']['content'] = $pdf->render(false);
                $attachments['pdf']['name'] = "devis.pdf";
                $attachments['pdf']['mime'] = 'application/pdf';
            }

            // Gestion pièces jointes : CGV
            if(Tools::getValue('cgv')) {
                $attachments['cgv']['content'] = file_get_contents($quotation->getShop()->getConditionsFilePath(true));
                $attachments['cgv']['name'] = "cgv.pdf";
                $attachments['cgv']['mime'] = 'application/pdf';
            }

            // Envoi des e-mails
            foreach($emails as $email)
                Mail::send(1, 'quotation', Tools::getValue('object'), $data, $email, null, null, Configuration::get('PS_SHOP_NAME', null, $quotation->id_shop), $attachments);

            $this->confirmations[] = "Le devis a été envoyé au client";
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
    * Ajoute un devis dans un client
    **/
    private function addToCart() {
        if(Tools::isSubmit('add_to_customer') and $id_customer = Tools::getValue('id_customer')) {

            $cart = Customer::getLastCart($id_customer);
            QuotationAssociation::addToCart($this->id_quotation, $cart->id);

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
    		$line->name = $product->name;

            $line->buying_price = $product->wholesale_price;
            $line->eco_tax = $product->ecotax;

            $line->min_quantity = $product->minimal_quantity;
            if($line->min_quantity) $line->quantity = $line->min_quantity;

            // Gestion déclinaison
            $product->id_product_attribute = $infos[1] ?? null;
            if($product->id_product_attribute and $combination = new Combination($product->id_product_attribute)) {

                $information = Product::getCombinationName($product->id_product_attribute);
                if($information) $line->name = implode(" | ", array($line->name, $information));

                $line->reference = $combination->reference;
                $line->buying_price = round($combination->wholesale_price, 2);
                $line->eco_tax = $combination->ecotax;

                $line->min_quantity = $combination->minimal_quantity;
                if($line->min_quantity) $line->quantity = $line->min_quantity;
            }
    		
            $line->reference_supplier = Product::getSupplierReference($product->id, $product->id_product_attribute);
            $line->selling_price = $product->getPrice(false, $product->id_product_attribute, 2) - $line->eco_tax;
    	}

        $line->save();

        // Gestion de l'image
        if($product->id) {
            $image = Product::getCoverPicture($product->id, $product->id_product_attribute);
            if($img_path = $image->getProductFilePath('cart'))
                @copy($img_path, $line->getDirectory(true).$line->getFileName());
        }

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

    /**
    * Renvoie la modal d'envoi au client
    **/
    public function renderContactModal() {
        
        //$this->addJS(_PS_JS_DIR_.'tiny_mce/tiny_mce.js');

        $tpl = $this->context->smarty->createTemplate(_PS_ROOT_DIR_."/override/controllers/admin/templates/quotations/contact.tpl");
        $tpl->assign('quotation', new Quotation($this->id_quotation));

        die($tpl->fetch());
    }

    /**
    * Gestion des actions groupées
    **/
    public function handleBulkActions() {
        if($ids = Tools::getValue('quotationBox')) {
            $ids = implode(',', $ids);

            // Suppression
            if(Tools::getIsset('submitBulkdeletequotation')) {
                Db::getInstance()->execute("DELETE FROM "._DB_PREFIX_.Quotation::TABLE_NAME." WHERE ".Quotation::TABLE_PRIMARY." IN ($ids)");
                Db::getInstance()->execute("DELETE FROM "._DB_PREFIX_.QuotationAssociation::TABLE_NAME." WHERE ".Quotation::TABLE_PRIMARY." IN ($ids)");
                Db::getInstance()->execute("DELETE FROM "._DB_PREFIX_.QuotationLine::TABLE_NAME." WHERE ".Quotation::TABLE_PRIMARY." IN ($ids)");
                $this->confirmations[] = "Les devis ont été supprimés";
            }

            // Activation
            if(Tools::getIsset('submitBulkenableSelectionquotation')) {
                Db::getInstance()->execute("UPDATE "._DB_PREFIX_.Quotation::TABLE_NAME." SET active = 1 WHERE ".Quotation::TABLE_PRIMARY." IN ($ids)");
                $this->confirmations[] = "Les devis ont été activés";
            }

            // Desactivation
            if(Tools::getIsset('submitBulkdisableSelectionquotation')) {
                Db::getInstance()->execute("UPDATE "._DB_PREFIX_.Quotation::TABLE_NAME." SET active = 0 WHERE ".Quotation::TABLE_PRIMARY." IN ($ids)");
                $this->confirmations[] = "Les devis ont été désactivés";
            }

        }
    }

}