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

        $this->_select = "a.*, CONCAT_WS('. ', c.email, a.email) AS emails, a.id_quotation AS id, a.id_quotation AS id_2, a.id_quotation AS id_3, CONCAT(c.firstname, ' ', c.lastname) AS customer, CONCAT(e.firstname, ' ', e.lastname) AS employee, (SELECT SUM(l.selling_price * l.quantity) FROM "._DB_PREFIX_.QuotationLine::TABLE_NAME." l WHERE l.id_quotation = a.id_quotation) AS price, c.company";
        $this->_join = ' LEFT JOIN '._DB_PREFIX_.'customer c ON (a.id_customer = c.id_customer)';
        $this->_join .= ' LEFT JOIN '._DB_PREFIX_.'employee e ON (a.id_employee = e.id_employee)';
        //$this->_where = " AND a.id_shop = ".$this->context->shop->id;

        $this->_orderBy = 'date_add';
        $this->_orderWay = 'desc';
        $this->_use_found_rows = true;

        $this->fields_list = array(
            'reference' => array(
                'title' => $this->trans('Référence', array(), 'Admin.Global'),
                'filter_key' => 'a!reference'
            ),
            'id_2' => array(
                'title' => $this->trans('Commande', array(), 'Admin.Global'),
                'callback' => 'formatOrder',
                'filter_key' => 'a!reference',
                'search' => false
            ),
            'id_3' => array(
                'title' => $this->trans('Date commande', array(), 'Admin.Global'),
                'callback' => 'formatOrderDate',
                'type' => 'datetime',
                'search' => false
            ),
            'customer' => array(
                'title' => $this->trans('Client', array(), 'Admin.Global'),
                'align' => 'text-center',
                'havingFilter' => true
            ),
            'emails' => array(
                'title' => $this->trans('E-mails', array(), 'Admin.Global'),
                'align' => 'text-center',
                'havingFilter' => true
            ),
            'company' => array(
                'title' => $this->trans('Société', array(), 'Admin.Global'),
                'align' => 'text-center',
                'filter_key' => 'c!company'
            ),
            'employee' => array(
                'title' => $this->trans('Créateur', array(), 'Admin.Global'),
                'align' => 'text-center',
                'filter_key' => 'e!email'
            ),
            'price' => array(
                'title' => $this->trans('Montant HT', array(), 'Admin.Global'),
                'align' => 'text-center',
                'callback' => 'formatPrice',
                'search' => false
            ),
            'status' => array(
                'title' => $this->trans('Etat', array(), 'Admin.Global'),
                'align' => 'text-center',
                'callback' => 'formatStatus',
                'type' => 'select',
                'list' => Quotation::getStates(),
                'filter_key' => 'a!status'
            ),
            'mail_sent' => array(
                'title' => $this->trans('Mail', array(), 'Admin.Global'),
                'align' => 'text-center',
                'callback' => 'formatMail',
                'search' => false
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
                'remove_onclick' => true,
                'search' => false
            ),
        );

        if($order_by = Tools::getValue('quotationOrderby'))
            $this->_orderBy = $order_by;

        if($order_way = Tools::getValue('quotationOrderway'))
        $this->_orderWay = $order_way;
    }

    public function formatOrder($value) {
        $quotation = new Quotation($value);
        $order = $quotation->getOrder();

        if($order) return $order->reference;
        return null;
    }

    public function formatOrderDate($value) {
        $quotation = new Quotation($value);
        $order = $quotation->getOrder();

        if($order) return $this->formatDate($order->date_add);
        return null;
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

    public function formatMail($value) {
        
        if($value)
            return "<span class='label label-success' title='E-mail envoyé'><i class='icon-envelope'></i></span>";
        else
           return "<span class='label label-danger' title='E-mail non envoyé'><i class='icon-envelope'></i></span>"; 
    }

    public function formatStatus($value) {
        $quotation = new Quotation();
        $quotation->status = $value;

        return "<span class='label label-".$quotation->getStatusClass()."'><b>".$quotation->getStatusLabel()."</b></span>";
    }

    public function formatActions($value) {
        $tpl = $this->context->smarty->createTemplate(_PS_ROOT_DIR_."/override/controllers/admin/templates/quotations/actions.tpl");
        $this->context->smarty->assign('quotation', new Quotation($value));
        
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

            case 'search_customer':
                die(json_encode(Customer::search(Tools::getValue('term'), Tools::getValue('id_shop'))));
            break;
    	}
    }

    public function postProcess() {
        
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

        // Fonctionnement normal
        parent::postProcess();
    }

    public function initContent() {

    	parent::initContent();

        if(Tools::getIsset('deletequotation'))
            QuotationAssociation::erazeDeletedQuotations();
        
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

            // Suppression d'un document
            if($id = Tools::getValue('remove_document')) {
                $line = new QuotationLine($id);
                @unlink($line->getDocumentLink(true));
            }
            
            // Enregistrement des informations
    		if(Tools::getIsset('quotation')) {
    			$form = Tools::getValue('quotation');

    			$quotation->status = $form['status'];
    			$quotation->id_customer = $form['id_customer'] ?? $quotation->id_customer;
                $quotation->origin = $form['origin'];
    			$quotation->source = $form['source'];
    			$quotation->email = $form['email'];
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
                $quotation->document_ids = implode(Quotation::DELIMITER, $form['documents']);

                if(!$quotation->id)
                    $quotation->generateReference(true);
                //TODO
                if(Tools::getValue('creation')) {
                    $form = Tools::getValue('new_account');

                    $customer = new Customer();
                    if (Validate::isEmail($form['email'])) {
                        $customer->getByEmail($form['email']);
                    }
                    if (!$customer->id){
                        $customer->email = $form['email'];
                        $customer->firstname = $form['firstname'];
                        $customer->lastname = $form['lastname'];
                        $customer->id_account_type = $form['id_account_type'];
                        $customer->company = $form['company'];

                        $customer->id_shop = $quotation->id_shop;
                        $customer->quotation = Customer::QUOTATION_NEW;
                        $customer->passwd = $this->crypto->hash($quotation->reference);

                        $customer->save();
                    }
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
    		$this->context->smarty->assign('products', Product::getSimpleActiveProducts(1, true, $quotation->id_shop));
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
            $data['{shop_name}'] = Configuration::get('PS_SHOP_NAME');
            $data['{shop_email}'] = Configuration::get('PS_SHOP_EMAIL');
            $data['{shop_phone}'] = Configuration::get('PS_SHOP_PHONE');
            $data['{shop_fax}'] = Configuration::get('PS_SHOP_FAX');
            $data['{shop_url}'] = ShopUrl::getMainShopDomain($quotation->id_shop);

            
            // Gestion pièces jointes : PDF
            if(Tools::getValue('pdf')) {
                $pdf = new PDF(array('quotation'=>$quotation), PDF::TEMPLATE_QUOTATION, $this->context->smarty);
                $attachments['pdf']['content'] = $pdf->render(false);
                $attachments['pdf']['name'] = "devis.pdf";
                $attachments['pdf']['mime'] = 'application/pdf';
            }

            // Gestion des pièces jointes : documents de la boutique
            foreach($quotation->getShop()->getDocuments() as $document)
                if(in_array($document['name'], $quotation->getDocuments()) and $document['exists']) {
                    $attachments[$document['name']]['content'] = file_get_contents($quotation->getShop()->getFilePath($document['name'], true));
                    $attachments[$document['name']]['name'] = $document['name'].".pdf";
                    $attachments[$document['name']]['mime'] = 'application/pdf';  
                }
                
            // Envoi des e-mails
            Mail::send(1, 'quotation', Tools::getValue('object'), $data, explode(',', Tools::getValue('emails')), null, null, Configuration::get('PS_SHOP_NAME'), $attachments, null, null, null, null, Configuration::get('PS_SHOP_EMAIL'));

            $quotation->mail_sent = true;
            $quotation->save();

            $this->confirmations[] = "Le devis a été envoyé au client";
        }
    }

    /**
    * Export des devis
    **/
    public function export() {
        if(Tools::getIsset('exportquotation')) {

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

            $quotation = new Quotation($this->id_quotation);
            if($quotation->id) {

                $products = $quotation->getProducts();
                $quotation->id = null;
                $quotation->reference = null;
                $quotation->status = null;
                $quotation->date_begin = date("Y-m-d");
                $quotation->date_add = date("Y-m-d");
                $quotation->date_end = (new DateTime())->add(new DateInterval('P30D'))->format("Y-m-d");
                $quotation->date_recall = null;
                $quotation->status = Quotation::STATUS_WAITING;
                $quotation->id_employee = $this->context->cookie->id_employee;
                $quotation->generateReference(true);
                $quotation->save();

                foreach($products as $product) {
                    $img_path = $product->getImageLink(true);

                    $product->id = null;
                    $product->id_quotation = $quotation->id;
                    $product->save();

                    // Copie de l'image
                    if($img_path)
                        copy($img_path, $quotation->getDirectory(true).$product->getFileName());
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

    	$product = new Product(($infos[0] ?? null), true, 1, $line->getQuotation()->id_shop);
    	if($product->id) {

            $line->id_product = $product->id;
            $line->id_supplier = $product->id_supplier;
    		$line->reference = $product->reference;
    		$line->name = $product->name;
            $line->eco_tax = $product->custom_ecotax;

            $line->min_quantity = $product->minimal_quantity;
            if($line->min_quantity) $line->quantity = $line->min_quantity;

            if($product->comment_1) $line->name .= " | ".$product->comment_1;
            if($product->comment_2) $line->name .= " | ".$product->comment_2;

            $dim = array();
            foreach(Product::loadColumn($product->id, 1) as $row)
                $dim[] = $row['name'].". ".$row['value'];
            $line->properties = implode(" x ", $dim);

            // Gestion déclinaison
            $product->id_product_attribute = $infos[1] ?? null;
            if($product->id_product_attribute and $combination = new Combination($product->id_product_attribute)) {

                $information = Product::getCombinationName($product->id_product_attribute);
                if($information) $line->properties = $information;

                $line->id_combination = $combination->id;
                $line->reference = $combination->reference;
                $line->eco_tax = $combination->custom_ecotax;
                
                $line->min_quantity = $combination->minimal_quantity;
                $line->quantity = $line->min_quantity;

                if($combination->comment_1) $line->name .= " | ".$combination->comment_1;
                if($combination->comment_2) $line->name .= " | ".$combination->comment_2;

                $dim = array();
                foreach(Combination::loadColumn($combination->id, 1) as $row)
                    $dim[] = $row['name'].". ".$row['value'];
                $line->properties = implode(" x ", $dim);
            }
    		
            // Référence du fournisseur
            $line->reference_supplier = Product::getSupplierReference($product->id, $product->id_product_attribute);
    	}

        // Gestion des prix
        $prices = SpecificPrice::getDefaultPrices($line->id_product, $line->id_combination);
        if(empty($prices)) $prices = SpecificPrice::getDefaultPrices($line->id_product, 0);

        $line->buying_price = round($prices['buying_price'], 2);
        $line->buying_fees = round($prices['delivery_fees'], 2);
        $line->selling_price = round($prices['price'], 2);

        // Enregistrement
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
    			$line->properties = $form['properties'];
    			$line->information = $form['information'];
                $line->buying_price = $form['buying_price'];
                $line->buying_fees = $form['buying_fees'];
    			$line->eco_tax = $form['eco_tax'];
    			$line->selling_price = $form['selling_price'];
                $line->id_supplier = $form['id_supplier'];
    			$line->quantity = $form['quantity'];
    			$line->comment = $form['comment'];
                $line->position = $form['position'];
    			$line->save();

                if(isset($_FILES['lines']['name'][$form['id']]['image'])) {
                    move_uploaded_file($_FILES['lines']['tmp_name'][$form['id']]['image'], $line->getDirectory(true).$line->getFileName());
                }

                if(isset($_FILES['lines']['name'][$form['id']]['document'])) {
                    if($_FILES['lines']['type'][$form['id']]['document'] == 'application/pdf') {
                        move_uploaded_file($_FILES['lines']['tmp_name'][$form['id']]['document'], $line->getDirectory(true).$line->getDocumentName());   
                    }
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