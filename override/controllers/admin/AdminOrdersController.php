<?php

class AdminOrdersController extends AdminOrdersControllerCore {

    private $current_id;
    private $order;

    /**
    * Récupère la commande en cours
    * @return Order
    **/
    private function getCurrentOrder() {

        if($this->current_id and !$this->order)
            $this->order = new Order($this->current_id);

        return $this->order;
    }

    /**
    * Override : filtre sur la référence commande (conflit SQL)
    **/
    public function __construct() {

        $this->bootstrap = true;
        $this->table = 'order';
        $this->className = 'Order';
        $this->lang = false;
        $this->addRowAction('view');
        $this->explicitSelect = true;
        $this->allow_export = true;
        $this->deleted = false;

        $this->current_id = (int)Tools::getValue('id_order');
        AdminController::__construct();

        $this->toolbar_btn['import'] = array(
            'href' => '#import',
            'desc' => $this->l('Import')
        );

        $this->_select = '
        a.id_currency,
        a.id_order AS id_pdf,
        CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customer`,
        osl.`name` AS `osname`,
        os.`color`,
        IF((SELECT so.id_order FROM `'._DB_PREFIX_.'orders` so WHERE so.id_customer = a.id_customer AND so.id_order < a.id_order LIMIT 1) > 0, 0, 1) as new,
        country_lang.name as cname,
        IF(a.valid, 1, 0) badge_success';

        $this->_join = '
        LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = a.`id_customer`)
        INNER JOIN `'._DB_PREFIX_.'address` address ON address.id_address = a.id_address_delivery
        INNER JOIN `'._DB_PREFIX_.'country` country ON address.id_country = country.id_country
        INNER JOIN `'._DB_PREFIX_.'country_lang` country_lang ON (country.`id_country` = country_lang.`id_country` AND country_lang.`id_lang` = '.(int)$this->context->language->id.')
        LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = a.`current_state`)
        LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int)$this->context->language->id.')';
        $this->_orderBy = 'id_order';
        $this->_orderWay = 'DESC';
        $this->_use_found_rows = true;

        $statuses = OrderState::getOrderStates((int)$this->context->language->id);
        foreach ($statuses as $status) {
            $this->statuses_array[$status['id_order_state']] = $status['name'];
        }

        $payments = array_column(Order::getPaymentList(), 'payment');
        $payments = array_combine($payments, $payments);

        $this->fields_list = array(
            'id_order' => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'reference' => array(
                'title' => $this->trans('Reference', array(), 'Admin.Global'),
                'filter_key' => 'a!reference'
            ),
            'reference_edeal' => array(
                'title' => $this->trans('Code E-deal', array(), 'Admin.Global'),
                'filter_key' => 'c!reference'
            ),
            'customer' => array(
                'title' => $this->trans('Customer', array(), 'Admin.Global'),
                'havingFilter' => true,
            ),
        );

        if (Configuration::get('PS_B2B_ENABLE')) {
            $this->fields_list = array_merge($this->fields_list, array(
                'company' => array(
                    'title' => $this->trans('Company', array(), 'Admin.Global'),
                    'filter_key' => 'c!company'
                ),
            ));
        }

        $this->fields_list = array_merge($this->fields_list, array(
            'total_paid_tax_incl' => array(
                'title' => $this->trans('Total', array(), 'Admin.Global'),
                'align' => 'text-right',
                'type' => 'price',
                'currency' => true,
                'callback' => 'setOrderCurrency',
                'badge_success' => true
            ),
            'payment' => array(
                'title' => $this->trans('Payment', array(), 'Admin.Global'),
                'type' => 'select',
                'list' => $payments,
                'filter_key' => 'a!payment'
            ),
            'osname' => array(
                'title' => $this->trans('Status', array(), 'Admin.Global'),
                'type' => 'select',
                'color' => 'color',
                'list' => $this->statuses_array,
                'filter_key' => 'os!id_order_state',
                'filter_type' => 'int',
                'order_key' => 'osname'
            ),
            'date_add' => array(
                'title' => $this->trans('Date', array(), 'Admin.Global'),
                'align' => 'text-right',
                'type' => 'datetime',
                'filter_key' => 'a!date_add'
            ),
            'id_pdf' => array(
                'title' => $this->trans('PDF', array(), 'Admin.Global'),
                'align' => 'text-center',
                'callback' => 'printPDFIcons',
                'orderby' => false,
                'search' => false,
                'remove_onclick' => true
            )
        ));

        $this->shopLinkType = 'shop';
        $this->shopShareDatas = Shop::SHARE_ORDER;

        if (Tools::isSubmit('id_order')) {
            // Save context (in order to apply cart rule)
            $this->context->cart = new Cart($this->getCurrentOrder()->id_cart);
            $this->context->customer = new Customer($this->getCurrentOrder()->id_customer);
        }

        $this->bulk_actions = array(
            'updateOrderStatus' => array('text' => $this->trans('Change Order Status', array(), 'Admin.Orderscustomers.Feature'), 'icon' => 'icon-refresh'),
            'downloadPreparationSlips' => array('text'=>$this->trans('Télécharger les bons de préparation', array(), 'Admin.Orderscustomers.Feature'), 'icon'=>'icon-download')
        );
    }

    /**
    * Gestion AJAX
    **/
    public function displayAjax() {
        switch (Tools::getValue('action')) {
            
            case 'load_oa':
                $this->loadOA();
            break;

            case 'new_oa':
                $this->addOA();
            break;

            case 'save_oa':
                $this->saveOA();
            break;

            case 'delete_oa':
                $this->deleteOA();
            break;
        }
    }

    /**
    * Gestion des OA
    **/
    public function loadOA() {

        // Vérification des OA
        $rows = Db::getInstance()->executeS("SELECT id_product_supplier FROM ps_order_detail WHERE id_order = ".$this->current_id);
        foreach($rows as $row) {

            if($row['id_product_supplier']) {
                $oa = OA::find($this->current_id, $row['id_product_supplier']);
                if(!$oa->id)
                    $oa->save();
            }
        }

        $this->context->smarty->assign('order', $this->getCurrentOrder());
        $this->context->smarty->assign('suppliers', Supplier::getSuppliers(1));
        $this->context->smarty->assign('BLBC_state_id', Configuration::getForOrder('BLBC_ORDER_STATE', $this->getCurrentOrder()));

        $tpl = $this->context->smarty->createTemplate(_PS_ROOT_DIR_."/override/controllers/admin/templates/orders/obligations_content.tpl");
        $data['view'] = $tpl->fetch();

        die(json_encode($data));
    }

    /**
    * Ajout d'un OA
    **/
    public function addOA() {
        if($this->current_id and $id_supplier = Tools::getValue('id_supplier') and $code = Tools::getValue('code')) {
        
            $oa = OA::find($this->current_id, $id_supplier);
            $oa->code = $code;
            $oa->save();

            $this->context->smarty->assign('confirmation', "OA ajouté");
        }

        $this->loadOA();
    }

    /**
    * Modifie un OA
    **/
    public function saveOA() {
        if($id_oa = Tools::getValue('id_oa') and $id_supplier = Tools::getValue('id_supplier')) {
            $code = Tools::getValue('code');

            $oa = new OA($id_oa);
            if($oa->id) {
                $oa->id_supplier = $id_supplier;
                $oa->code = $code;
                $oa->save();

                $this->context->smarty->assign('confirmation', "OA enregistré");
            }
        }

        $this->loadOA();
    }

    /**
    * Suppression OA
    **/
    public function deleteOA() {
        if($this->current_id and $id_oa = Tools::getValue('id_oa')) {

            $oa = new OA($id_oa);
            if($oa->id){
                $oa->delete();
                $this->context->smarty->assign('confirmation', "OA supprimé");
            }
        }

        $this->loadOA();
    }

    /**
    * Ajoute la modal import à la page liste
    **/
    public function renderList() {

        $tpl = $this->context->smarty->createTemplate(_PS_ROOT_DIR_."/override/controllers/admin/templates/orders/import.tpl");
        return parent::renderList().$tpl->fetch();
    }

    /**
     * Methode pour l'import de masse des status de commande
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function initContent() {

        // Import commande
        if(Tools::isSubmit('import')) {
            if(isset($_FILES['file'])) {

                $handle = fopen($_FILES['file']['tmp_name'], 'r');
                $not_found = array();

                if(Tools::getValue('skip'))
                    fgetcsv($handle, 0, ";");

                while($row = fgetcsv($handle, 0, ";")) {

                    if($row[0]) {
                        if($id = Order::getIdByReference($row[0])) {
                            $date = DateTime::createFromFormat('d/m/Y', $row[3]);

                            $order = new Order($id);
                            if($row[2]) $order->invoice_number = $row[2];
                            if($row[3]) $order->invoice_date = $date->format('Y-m-d 00:00:00');
                            $order->save();

                            if($row[1]) {

                                $history = new OrderHistory();
                                $history->changeIdOrderState($row[1], $order->id);

                                $history->id_order = $order->id;
                                $history->id_order_state = $row[1];
                                $history->id_employee = $this->context->employee->id;
                                $history->date_add = date('Y-m-d H:i:s');
                                $history->save();
                                $history->sendEmail($order);
                                
                            }
                        }
                        else
                            $not_found[] = $row[0];
                    }
                }

                fclose($handle);

                if(!empty($not_found))
                    $this->errors[] = "L'import est terminé mais les commandes suivantes n'ont pas été trouvées : ".implode(', ', $not_found);
                else
                    $this->confirmations[] = "Import terminé";
            }
            
        }

        // Modification référence interne
        if(Tools::getIsset('new_internal_reference')) {

            $this->getCurrentOrder()->internal_reference = Tools::getValue('new_internal_reference');
            $this->getCurrentOrder()->save();
        }
        
        // Supprimer un historique
        if($id = Tools::getValue('remove_history')) {

            $history = new OrderHistory($id);
            if($history->id) $history->delete();

            if($this->getCurrentOrder()->current_state == $history->id_order_state) {
                $this->getCurrentOrder()->current_state = Db::getInstance()->getValue("SELECT id_order_state FROM ps_order_history WHERE id_order = ".$order->id." ORDER BY date_add DESC");
                $this->getCurrentOrder()->save();
            }
        }

        // Supprimer un paiement 
        if($id = Tools::getValue('remove_payment')) {

            $payment = new OrderPayment($id);
            if($payment->id) $payment->delete();
        }

        // Enregistrement facturation 
        if(Tools::isSubmit('save_invoice')) {
            $this->getCurrentOrder()->invoice_date = Tools::getValue('invoice_date');
            $this->getCurrentOrder()->invoice_number = Tools::getValue('invoice_number');
            $this->getCurrentOrder()->no_recall = Tools::getValue('no_recall');
            $this->getCurrentOrder()->display_with_taxes = Tools::getValue('display_with_taxes');
            $this->getCurrentOrder()->invoice_comment = Tools::getValue('invoice_comment');
            $this->getCurrentOrder()->order_slip_number = Tools::getValue('order_slip_number');
            $this->getCurrentOrder()->save();
        }
        // Enregistrement des infomations complémentaires
        foreach(array('supplier_information', 'delivery_information') as $name) {
            if(Tools::isSubmit("save_$name")) {
                $this->getCurrentOrder()->{$name} = Tools::getValue($name);
                $this->getCurrentOrder()->save();
            }
        }

        // Enregistrement des modifications produits
        if($rows = Tools::getValue('update')) {

            $update_order = false;
            foreach($rows as $id => $row) {

                $detail = new OrderDetail($id);
                $update = false;

                if($detail->id) {
                    $update = ($detail->product_quantity != $row['product_quantity']);

                    $detail->id_product_supplier = $row['id_supplier'];
                    $detail->product_reference = $row['product_reference'];
                    $detail->product_supplier_reference = $row['product_supplier_reference'];
                    $detail->purchase_supplier_price = $row['purchase_supplier_price'];
                    $detail->delivery_fees = $row['delivery_fees'];

                    if($update) {
                        $update_order = true;
                        $detail->product_quantity = $row['product_quantity'];
                        $detail->total_price_tax_incl = $detail->unit_price_tax_incl * $detail->product_quantity;
                        $detail->total_price_tax_excl = $detail->unit_price_tax_excl * $detail->product_quantity;
                    }

                    $detail->save();
                }
            }

            if($update_order) {

                $this->getCurrentOrder()->updateCosts();
                OrderInvoice::synchronizeOrder($this->getCurrentOrder());
            }

        }

        // Envoi de la facture
        if(Tools::isSubmit('send_invoice')) {
            $this->sendInvoice();
        }

        // Envoi des documents
        if(Tools::isSubmit('send_documents')) {
            $this->sendDocuments();
        }

        if($this->getCurrentOrder()) {
            $this->context->smarty->assign('suppliers', Supplier::getSuppliers(1));
            $this->context->smarty->assign('BLBC_state_id', Configuration::getForOrder('BLBC_ORDER_STATE', $this->getCurrentOrder()));
        }
        
        AdminController::initContent();
    }

    private function sendInvoice() {

        
        // PDF
        foreach($this->getCurrentOrder()->getInvoicesCollection() as $invoice) {
            $pdf = new PDF($invoice, PDF::TEMPLATE_INVOICE, $this->context->smarty);
        }
        
        $attachments['invoice']['content'] = $pdf->render(false);
        $attachments['invoice']['name'] = "facture.pdf";
        $attachments['invoice']['mime'] = 'application/pdf';

        $shop_name = $this->getCurrentOrder()->getShop()->name;
        $date = new DateTime($this->getCurrentOrder()->date_add);

        $data['{order_reference}'] = $this->getCurrentOrder()->reference;
        $data['{order_date}'] = $date->format('d/m/Y');
        $data['{firstname}'] = $this->getCurrentOrder()->getCustomer()->firstname;
        $data['{lastname}'] = $this->getCurrentOrder()->getCustomer()->lastname;
        $data['{shop_phone'] = Configuration::getForOrder('PS_SHOP_PHONE', $this->getCurrentOrder());
        $data['{devis}'] = $this->$order.getQuotation() ? " via le devis : " + $this->$order.getQuotation()  : ".";

        // Proforma
        if($this->getCurrentOrder()->isProforma()) {
            $object = $this->trans("%shop% :  Proforma de votre commande n° %reference%", array('%shop%'=>$shop_name, '%reference%'=>$this->getCurrentOrder()->reference));
            
            foreach($this->getCurrentOrder()->getCustomer()->getInvoiceEmails() as $email)
                Mail::send(1, 'invoice_proforma', $object, $data, $email, null, null, $shop_name, $attachments, null, _PS_MAIL_DIR_, false, $this->getCurrentOrder()->getShop()->id);
        }

        // Classique
        else {
            $object = $this->trans("%shop% :  Facture de votre commande n° %reference%", array('%shop%'=>$shop_name, '%reference%'=>$this->getCurrentOrder()->reference));
            $data['{deadline}'] = $this->getCurrentOrder()->getPaymentDeadline()->format('d/m/Y');
            
            foreach($this->getCurrentOrder()->getCustomer()->getInvoiceEmails() as $email)
                Mail::send(1, 'invoice', $object, $data, $email, null, null, $shop_name, $attachments, null, _PS_MAIL_DIR_, false, $this->getCurrentOrder()->getShop()->id);
        }
    }
    /**
    * Gestion de l'envoi des documents aux fournisseurs
    **/
    private function sendDocuments() {

        $ids_supplier = Tools::getValue('ids_supplier');
        $documents = Tools::getValue('documents');
        $custom_send = Tools::getValue('custom_send');
        $id_change_state = Tools::getValue('id_change_state');

        $default["send_supplier_BL"] = $this->trans('Nouveau bon de livraison Web Equip pour commande {order_reference} à {supplier_reference} - {supplier_name}', array(), 'Admin.Orderscustomers.Feature');
        $default["send_supplier_BC"] = $this->trans('Nouveau bon de commande Web Equip {order_reference} pour {supplier_reference} - {supplier_name}', array(), 'Admin.Orderscustomers.Feature');
        $default["send_supplier_BLBC"] = $this->trans('Nouvelle commande {order_reference} de Web Equip à {supplier_reference} - {supplier_name}', array(), 'Admin.Orderscustomers.Feature');

        foreach(OA::findByOrder($this->current_id) as $OA) { 
            if(!$ids_supplier or in_array($OA->id_supplier, $ids_supplier)) { 
                if(!empty($OA->getSupplier()->getEmails())) {

                    // Gestion du template
                    $template = 'send_supplier_';

                    // Bon de livraison
                    if($documents[$OA->id_supplier]['BL']) {
                        $pdf = new PDF($OA, PDF::TEMPLATE_DELIVERY_SLIP, $this->context->smarty);
                        $attachments['BL']['content'] = $pdf->render(false);
                        $attachments['BL']['name'] = "bon_de_livraison.pdf";
                        $attachments['BL']['mime'] = 'application/pdf';

                        $OA->date_BL = date('Y-m-d H:i:s');
                        $template .= "BL";
                    }

                    // Bon de commande
                    if($documents[$OA->id_supplier]['BC']) {
                        $pdf = new PDF($OA, PDF::TEMPLATE_PURCHASE_ORDER, $this->context->smarty);
                        $attachments['BC']['content'] = $pdf->render(false);
                        $attachments['BC']['name'] = "bon_de_commande.pdf";
                        $attachments['BC']['mime'] = 'application/pdf';

                        $OA->date_BC = date('Y-m-d H:i:s');
                        $template .= "BC";
                    }

                    // Envoi des e-mails
                    $emails = $OA->getSupplier()->getEmails();
                    if($email = Configuration::get('BLBC_HIDDEN_MAIL', null, $OA->getOrder()->id_shop)) $emails[] = $email;

                    foreach($emails as $email) {

                        $message = Tools::getValue('message');
                        if($custom_send)
                            $object = Tools::getValue('object');
                        else
                            $object = $default[$template];

                        $object = str_replace('{order_reference}', $OA->getOrder()->reference, $object);
                        $message = str_replace('{order_reference}', $OA->getOrder()->reference, $message);

                        $object = str_replace('{supplier_reference}', $OA->getSupplier()->reference, $object);
                        $message = str_replace('{supplier_reference}', $OA->getSupplier()->reference, $message);

                        $object = str_replace('{supplier_name}', $OA->getSupplier()->name, $object);
                        $message  = str_replace('{supplier_name}', $OA->getSupplier()->name, $message);

                        $data['{message}'] = $message;
                        $data['{reference}'] = $OA->getOrder()->reference;
                        $data['{shop_title}'] = Configuration::getForOrder('PS_SHOP_TITLE', $OA->getOrder());
                        $data['{shop_phone}'] = Configuration::getForOrder('PS_SHOP_PHONE', $OA->getOrder());
                        $data['{shop_addr1}'] = Configuration::getForOrder('PS_SHOP_ADDR1', $OA->getOrder());
                        $data['{shop_addr2}'] = Configuration::getForOrder('PS_SHOP_ADDR2', $OA->getOrder());
                        $data['{shop_code}'] = Configuration::getForOrder('PS_SHOP_CODE', $OA->getOrder());
                        $data['{shop_city}'] = Configuration::getForOrder('PS_SHOP_CITY', $OA->getOrder());

                        Mail::send(1, $template, $object, $data, $email, null, null, Configuration::get('PS_SHOP_NAME', null, $OA->getOrder()->id_shop), $attachments, null, _PS_MAIL_DIR_, false, $OA->getOrder()->getShop()->id);

                    }

                    $OA->save();
                    $this->confirmations[] = "Les documents ont été envoyés";
                }
            }
        }

        // Mise à jour de la commande
        if($id_change_state) {
            if($this->getCurrentOrder()->current_state != $id_change_state) {

                $history = new OrderHistory();
                $history->changeIdOrderState($id_change_state, $this->current_id);

                $history->id_order = $this->current_id;
                $history->id_order_state = $id_change_state;
                $history->id_employee = $this->context->employee->id;
                $history->date_add = date('Y-m-d H:i:s');
                $history->save();
            }
        }
    }

    public function processBulkDownloadPreparationSlips() {
        if($ids = Tools::getValue('orderBox') and !empty($ids)) {

            $orders = array();
            foreach($ids as $id)
                $orders[] = new Order($id);

            $pdf = new PDF(array($orders), PDF::TEMPLATE_PREPARATION_SLIPS, $this->context->smarty);

            header('Content-Disposition: attachment; filename="préparations.pdf";');
            die($pdf->render(false));
        }
    }

    public function ajaxProcessEditProductOnOrder() {

        // Return value
        $res = true;

        /* Recuperation de la ligne de produit */
        $order_detail = new OrderDetail((int)Tools::getValue('product_id_order_detail'));
        $order = new Order($order_detail->id_order);
        if (Tools::isSubmit('product_invoice')) {
            $order_invoice = new OrderInvoice((int)Tools::getValue('product_invoice'));
        }

        /* Verification de la mise a jour ou de la semaine d'expedition */
        if (Tools::getValue('day') != $order_detail->day or Tools::getValue('week') != $order_detail->week or
            Tools::getValue('comment') != $order_detail->comment)
            $sendEmail = true;

        /* Mise en place des valeurs d'expedition */
        $order_detail->day = Tools::getValue('day');
        $order_detail->week = Tools::getValue('week');
        $order_detail->comment = Tools::getValue('comment');
        $order_detail->notification_sent = Tools::getValue('notification_sent');
        $order_detail->prevent_notification = Tools::getValue('prevent_notification');
        $order_detail->save();

        /* Ajout dans la table d'envoi si modification de la date et pas de blocage de notification */
        if ($sendEmail && $order_detail -> prevent_notification != "1"){

            /* Suppression de l'ancienne version si il y en a une */
            if (SendOrderDate::findByOrderDetailId($order_detail -> id_order_detail)) {
                SendOrderDate::deleteToIdOrderDetail($order_detail -> id_order_detail);
            }

            /* Sauvegarde en cas d'absence dans la base de donnes */
            $sendOrderDate = new SendOrderDate();
            $sendOrderDate -> id_order_detail = $order_detail -> id_order_detail;
            $sendOrderDate -> date = date("Y-m-d H:i:s");
            $sendOrderDate -> save();
        }

        // Check fields validity
        $this->doEditProductValidation($order_detail, $this->getCurrentOrder(), isset($order_invoice) ? $order_invoice : null);

        // If multiple product_quantity, the order details concern a product customized
        $product_quantity = 0;
        if (is_array(Tools::getValue('product_quantity'))) {
            foreach (Tools::getValue('product_quantity') as $id_customization => $qty) {
                // Update quantity of each customization
                Db::getInstance()->update('customization', array('quantity' => (int)$qty), 'id_customization = '.(int)$id_customization);
                // Calculate the real quantity of the product
                $product_quantity += $qty;
            }
        } else {
            $product_quantity = Tools::getValue('product_quantity');
        }

        $product_price_tax_incl = Tools::ps_round(Tools::getValue('product_price_tax_incl'), 2);
        $product_price_tax_excl = Tools::ps_round(Tools::getValue('product_price_tax_excl'), 2);
        $total_products_tax_incl = $product_price_tax_incl * $product_quantity;
        $total_products_tax_excl = $product_price_tax_excl * $product_quantity;

        // Calculate differences of price (Before / After)
        $diff_price_tax_incl = $total_products_tax_incl - $order_detail->total_price_tax_incl;
        $diff_price_tax_excl = $total_products_tax_excl - $order_detail->total_price_tax_excl;

        // Apply change on OrderInvoice
        if (isset($order_invoice)) {
            // If OrderInvoice to use is different, we update the old invoice and new invoice
            if ($order_detail->id_order_invoice != $order_invoice->id) {
                $old_order_invoice = new OrderInvoice($order_detail->id_order_invoice);
                // We remove cost of products
                $old_order_invoice->total_products -= $order_detail->total_price_tax_excl;
                $old_order_invoice->total_products_wt -= $order_detail->total_price_tax_incl;

                $old_order_invoice->total_paid_tax_excl -= $order_detail->total_price_tax_excl;
                $old_order_invoice->total_paid_tax_incl -= $order_detail->total_price_tax_incl;

                $res &= $old_order_invoice->update();

                $order_invoice->total_products += $order_detail->total_price_tax_excl;
                $order_invoice->total_products_wt += $order_detail->total_price_tax_incl;

                $order_invoice->total_paid_tax_excl += $order_detail->total_price_tax_excl;
                $order_invoice->total_paid_tax_incl += $order_detail->total_price_tax_incl;

                $order_detail->id_order_invoice = $order_invoice->id;
            }
        }

        if ($diff_price_tax_incl != 0 && $diff_price_tax_excl != 0) {
            $order_detail->unit_price_tax_excl = $product_price_tax_excl;
            $order_detail->unit_price_tax_incl = $product_price_tax_incl;

            $order_detail->total_price_tax_incl += $diff_price_tax_incl;
            $order_detail->total_price_tax_excl += $diff_price_tax_excl;

            if (isset($order_invoice)) {
                // Apply changes on OrderInvoice
                $order_invoice->total_products += $diff_price_tax_excl;
                $order_invoice->total_products_wt += $diff_price_tax_incl;

                $order_invoice->total_paid_tax_excl += $diff_price_tax_excl;
                $order_invoice->total_paid_tax_incl += $diff_price_tax_incl;
            }

            // Apply changes on Order

            $order->total_products += $diff_price_tax_excl;
            $order->total_products_wt += $diff_price_tax_incl;

            $order->total_paid += $diff_price_tax_incl;
            $order->total_paid_tax_excl += $diff_price_tax_excl;
            $order->total_paid_tax_incl += $diff_price_tax_incl;

            $res &= $order->update();
        }

        $old_quantity = $order_detail->product_quantity;

        $order_detail->product_quantity = $product_quantity;
        $order_detail->reduction_percent = 0;

        // update taxes
        $res &= $order_detail->updateTaxAmount($order);

        // Save order detail
        $res &= $order_detail->update();

        // Update weight SUM
        $order_carrier = new OrderCarrier((int)$order->getIdOrderCarrier());
        if (Validate::isLoadedObject($order_carrier)) {
            $order_carrier->weight = (float)$order->getTotalWeight();
            $res &= $order_carrier->update();
            if ($res) {
                $order->weight = sprintf("%.3f ".Configuration::get('PS_WEIGHT_UNIT'), $order_carrier->weight);
            }
        }

        // Save order invoice
        if (isset($order_invoice)) {
            $res &= $order_invoice->update();
        }

        // Update product available quantity
        StockAvailable::updateQuantity($order_detail->product_id, $order_detail->product_attribute_id, ($old_quantity - $order_detail->product_quantity), $order->id_shop);

        $products = $this->getProducts($order);
        // Get the last product
        $product = $products[$order_detail->id];
        $resume = OrderSlip::getProductSlipResume($order_detail->id);
        $product['quantity_refundable'] = $product['product_quantity'] - $resume['product_quantity'];
        $product['amount_refundable'] = $product['total_price_tax_excl'] - $resume['amount_tax_excl'];
        $product['amount_refund'] = Tools::displayPrice($resume['amount_tax_incl']);
        $product['refund_history'] = OrderSlip::getProductSlipDetail($order_detail->id);
        if ($product['id_warehouse'] != 0) {
            $warehouse = new Warehouse((int)$product['id_warehouse']);
            $product['warehouse_name'] = $warehouse->name;
            $warehouse_location = WarehouseProductLocation::getProductLocation($product['product_id'], $product['product_attribute_id'], $product['id_warehouse']);
            if (!empty($warehouse_location)) {
                $product['warehouse_location'] = $warehouse_location;
            } else {
                $product['warehouse_location'] = false;
            }
        } else {
            $product['warehouse_name'] = '--';
            $product['warehouse_location'] = false;
        }

        // Get invoices collection
        $invoice_collection = $order->getInvoicesCollection();

        $invoice_array = array();
        foreach ($invoice_collection as $invoice) {
            /** @var OrderInvoice $invoice */
            $invoice->name = $invoice->getInvoiceNumberFormatted(Context::getContext()->language->id, (int)$order->id_shop);
            $invoice_array[] = $invoice;
        }

        $order = $order->refreshShippingCost();

        // Assign to smarty informations in order to show the new product line
        $this->context->smarty->assign(array(
            'product' => $product,
            'order' => $order,
            'currency' => new Currency($order->id_currency),
            'can_edit' => $this->access('edit'),
            'invoices_collection' => $invoice_collection,
            'current_id_lang' => Context::getContext()->language->id,
            'link' => Context::getContext()->link,
            'current_index' => self::$currentIndex,
            'display_warehouse' => (int)Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')
        ));

        if (!$res) {
            die(json_encode(array(
                'result' => $res,
                'error' => $this->trans('An error occurred while editing the product line.', array(), 'Admin.Orderscustomers.Notification')
            )));
        }


        if (is_array(Tools::getValue('product_quantity'))) {
            $view = $this->createTemplate('_customized_data.tpl')->fetch();
        } else {
            $view = $this->createTemplate('_product_line.tpl')->fetch();
        }

        $this->sendChangedNotification($order);

        die(json_encode(array(
            'result' => $res,
            'view' => $view,
            'can_edit' => $this->access('add'),
            'invoices_collection' => $invoice_collection,
            'order' => $order,
            'invoices' => $invoice_array,
            'documents_html' => $this->createTemplate('_documents.tpl')->fetch(),
            'shipping_html' => $this->createTemplate('_shipping.tpl')->fetch(),
            'customized_product' => is_array(Tools::getValue('product_quantity'))
        )));
    }

    public function ajaxProcessSearchProducts() {

        /* Information du client et de la devise */
        Context::getContext() -> customer = new Customer((int)Tools::getValue('id_customer'));
        $currency = new Currency((int)Tools::getValue('id_currency'));

        if ($products = Product::searchByName((int)$this -> context -> language -> id, pSQL(Tools::getValue('product_search')))) {
            foreach ($products as &$product) {
                // Formatted price
                $product['formatted_price'] = Tools::displayPrice(Tools::convertPrice($product['price_tax_incl'], $currency), $currency);
                // Concret price
                $product['price_tax_incl'] = Tools::ps_round(Tools::convertPrice($product['price_tax_incl'], $currency), 2);
                $product['price_tax_excl'] = Tools::ps_round(Tools::convertPrice($product['price_tax_excl'], $currency), 2);
                $productObj = new Product((int)$product['id_product'], false, (int)$this->context->language->id);
                $combinations = array();
                $attributes = $productObj->getAttributesGroups((int)$this->context->language->id);

                // Tax rate for this customer
                if (Tools::isSubmit('id_address')) {
                    $product['tax_rate'] = $productObj->getTaxesRate(new Address(Tools::getValue('id_address')));
                }

                $product['warehouse_list'] = array();

                foreach ($attributes as $attribute) {
                    if (!isset($combinations[$attribute['id_product_attribute']]['attributes'])) {
                        $combinations[$attribute['id_product_attribute']]['attributes'] = '';
                    }
                    $combinations[$attribute['id_product_attribute']]['attributes'] .= $attribute['attribute_name'].' - ';
                    $combinations[$attribute['id_product_attribute']]['id_product_attribute'] = $attribute['id_product_attribute'];
                    $combinations[$attribute['id_product_attribute']]['default_on'] = $attribute['default_on'];
                    if (!isset($combinations[$attribute['id_product_attribute']]['price'])) {
                        $price_tax_incl = Product::getPriceStatic((int)$product['id_product'], true, $attribute['id_product_attribute']);
                        $price_tax_excl = Product::getPriceStatic((int)$product['id_product'], false, $attribute['id_product_attribute']);
                        $combinations[$attribute['id_product_attribute']]['price_tax_incl'] = Tools::ps_round(Tools::convertPrice($price_tax_incl, $currency), 2);
                        $combinations[$attribute['id_product_attribute']]['price_tax_excl'] = Tools::ps_round(Tools::convertPrice($price_tax_excl, $currency), 2);
                        $combinations[$attribute['id_product_attribute']]['formatted_price'] = Tools::displayPrice(Tools::convertPrice($price_tax_excl, $currency), $currency);
                    }
                    if (!isset($combinations[$attribute['id_product_attribute']]['qty_in_stock'])) {
                        $combinations[$attribute['id_product_attribute']]['qty_in_stock'] = StockAvailable::getQuantityAvailableByProduct((int)$product['id_product'], $attribute['id_product_attribute'], (int)$this->context->shop->id);
                    }

                    if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && (int)$product['advanced_stock_management'] == 1) {
                        $product['warehouse_list'][$attribute['id_product_attribute']] = Warehouse::getProductWarehouseList($product['id_product'], $attribute['id_product_attribute']);
                    } else {
                        $product['warehouse_list'][$attribute['id_product_attribute']] = array();
                    }

                    $product['stock'][$attribute['id_product_attribute']] = Product::getRealQuantity($product['id_product'], $attribute['id_product_attribute']);
                }

                if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && (int)$product['advanced_stock_management'] == 1) {
                    $product['warehouse_list'][0] = Warehouse::getProductWarehouseList($product['id_product']);
                } else {
                    $product['warehouse_list'][0] = array();
                }

                $product['stock'][0] = StockAvailable::getQuantityAvailableByProduct((int)$product['id_product'], 0, (int)$this->context->shop->id);

                foreach ($combinations as &$combination) {
                    $combination['attributes'] = rtrim($combination['attributes'], ' - ');
                }
                $product['combinations'] = $combinations;

                if ($product['customizable']) {
                    $product_instance = new Product((int)$product['id_product']);
                    $product['customization_fields'] = $product_instance->getCustomizationFields($this->context->language->id);
                }
            }

            $to_return = array(
                'products' => $products,
                'found' => true
            );
        } else {
            $to_return = array('found' => false);
        }

        $this->content = json_encode($to_return);
        die($this->content);
    }

    
}