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

                                /*$history = new OrderHistory();
                                $history->changeIdOrderState($row[1], $order->id);

                                $history->id_order = $order->id;
                                $history->id_order_state = $row[1];
                                $order->setCurrentState($history->id_order_state);

                                $history->id_employee = $this->context->employee->id;
                                $history->date_add = date('Y-m-d H:i:s');
                                $history->save();
                                $history->sendEmail($order);*/


                                $history = new OrderHistory();
                                $history->id_order = (int)$order->id;
                                $history->id_order_state = $row[1];
                                $history->changeIdOrderState((int)$row[1], $order);
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

        if(!$this->getCurrentOrder()->invoice_date || empty($this->getCurrentOrder()->invoice_date) || $this->getCurrentOrder()->invoice_date == "0000-00-00 00:00:00"){
            $this->getCurrentOrder()->invoice_date = new DateTime();
            $this->getCurrentOrder()->save();
        }

        if(!$this->getCurrentOrder()->payment_date || empty($this->getCurrentOrder()->payment_date) || $this->getCurrentOrder()->payment_date == "0000-00-00 00:00:00"){
            $this->getCurrentOrder()->payment_date = new DateTime();
            if ($this->getCurrentOrder()->getDatePaid()){
                $this->getCurrentOrder()->payment_date =  new DateTime($this->getCurrentOrder()->getDatePaid());
            }
            $this->getCurrentOrder()->save();
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
        $data['{date_payment}'] = $this->getCurrentOrder()->payment_date->format('d/m/Y');

        // Proforma
        if($this->getCurrentOrder()->isProforma()) {
            $object = $this->trans("%shop% :  Proforma de votre commande n° %reference%", array('%shop%'=>$shop_name, '%reference%'=>$this->getCurrentOrder()->reference));
            
            foreach($this->getCurrentOrder()->getCustomer()->getInvoiceEmails() as $email)
                Mail::send(1, 'invoice_proforma', $object, $data, $email, null, null, $shop_name, $attachments, null, _PS_MAIL_DIR_, false, $this->getCurrentOrder()->getShop()->id, Configuration::get("PS_SHOP_EMAIL"));
        }

        // Classique
        else {
            $object = $this->trans("%shop% :  Facture de votre commande n° %reference%", array('%shop%'=>$shop_name, '%reference%'=>$this->getCurrentOrder()->reference));
            $data['{deadline}'] = $this->getCurrentOrder()->getPaymentDeadline()->format('d/m/Y');
            
            foreach($this->getCurrentOrder()->getCustomer()->getInvoiceEmails() as $email)
                Mail::send(1, 'invoice', $object, $data, $email, null, null, $shop_name, $attachments, null, _PS_MAIL_DIR_, false, $this->getCurrentOrder()->getShop()->id, Configuration::get("PS_SHOP_EMAIL"));
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


    public function postProcess()
    {
        // If id_order is sent, we instanciate a new Order object
        if (Tools::isSubmit('id_order') && Tools::getValue('id_order') > 0) {
            $order = new Order(Tools::getValue('id_order'));
            if (!Validate::isLoadedObject($order)) {
                $this->errors[] = $this->trans('The order cannot be found within your database.', array(), 'Admin.Orderscustomers.Notification');
            }
            ShopUrl::cacheMainDomainForShop((int)$order->id_shop);
        }

        /* Update shipping number and carrier */
        if (Tools::isSubmit('submitShippingNumber') && isset($order)) {
            if ($this->access('edit')) {
                $tracking_number = Tools::getValue('shipping_tracking_number');
                $id_carrier = Tools::getValue('shipping_carrier');
                $old_tracking_number = $order->shipping_number;

                $order_carrier = new OrderCarrier(Tools::getValue('id_order_carrier'));
                if (!Validate::isLoadedObject($order_carrier)) {
                    $this->errors[] = $this->trans('The order carrier ID is invalid.', array(), 'Admin.Orderscustomers.Notification');
                } elseif (!empty($tracking_number) && !Validate::isTrackingNumber($tracking_number)) {
                    $this->errors[] = $this->trans('The tracking number is incorrect.', array(), 'Admin.Orderscustomers.Notification');
                } else {
                    //update carrier - ONLY if changed - then refresh shipping cost
                    $old_id_carrier = $order_carrier->id_carrier;
                    if (!empty($id_carrier) && $old_id_carrier != $id_carrier) {
                        $order->id_carrier = (int) $id_carrier;
                        $order_carrier->id_carrier = (int) $id_carrier;
                        $order_carrier->update();
                        $order->refreshShippingCost();
                    }

                    //load fresh order carrier because updated just before
                    $order_carrier = new OrderCarrier((int) Tools::getValue('id_order_carrier'));

                    // update shipping number
                    // Keep these two following lines for backward compatibility, remove on 1.6 version
                    $order->shipping_number = $tracking_number;
                    $order->update();

                    // Update order_carrier
                    $order_carrier->tracking_number = pSQL($tracking_number);
                    if ($order_carrier->update()) {
                        //send mail only if tracking number is different AND not empty
                        if (!empty($tracking_number) && $old_tracking_number != $tracking_number) {
                            if ($order_carrier->sendInTransitEmail($order)) {
                                $customer = new Customer((int)$order->id_customer);
                                $carrier = new Carrier((int)$order->id_carrier, $order->id_lang);

                                Hook::exec('actionAdminOrdersTrackingNumberUpdate', array(
                                    'order' => $order,
                                    'customer' => $customer,
                                    'carrier' => $carrier
                                ), null, false, true, false, $order->id_shop);

                                Tools::redirectAdmin(self::$currentIndex.'&id_order='.$order->id.'&vieworder&conf=4&token='.$this->token);
                            } else {
                                $this->errors[] = $this->trans('An error occurred while sending an email to the customer.', array(), 'Admin.Orderscustomers.Notification');
                            }
                        }
                    } else {
                        $this->errors[] = $this->trans('The order carrier cannot be updated.', array(), 'Admin.Orderscustomers.Notification');
                    }
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('submitState') && isset($order)) {
            /* Change order status, add a new entry in order history and send an e-mail to the customer if needed */
            if ($this->access('edit')) {
                $order_state = new OrderState(Tools::getValue('id_order_state'));

                if (!Validate::isLoadedObject($order_state)) {
                    $this->errors[] = $this->trans('The new order status is invalid.', array(), 'Admin.Orderscustomers.Notification');
                } else {
                    $current_order_state = $order->getCurrentOrderState();
                    if ($current_order_state->id != $order_state->id) {
                        // Create new OrderHistory
                        $history = new OrderHistory();
                        $history->id_order = $order->id;
                        $history->id_employee = (int)$this->context->employee->id;

                        $use_existings_payment = false;
                        if (!$order->hasInvoice()) {
                            $use_existings_payment = true;
                        }
                        $history->changeIdOrderState((int)$order_state->id, $order, $use_existings_payment);

                        if ($order_state->paid){
                            $order->payment_date = new DateTime();
                        }


                        $carrier = new Carrier($order->id_carrier, $order->id_lang);
                        $templateVars = array();
                        if ($history->id_order_state == Configuration::get('PS_OS_SHIPPING') && $order->shipping_number) {
                            $templateVars = array('{followup}' => str_replace('@', $order->shipping_number, $carrier->url));
                        }

                        // Save all changes
                        if ($history->addWithemail(true, $templateVars)) {
                            // synchronizes quantities if needed..
                            if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                                foreach ($order->getProducts() as $product) {
                                    if (StockAvailable::dependsOnStock($product['product_id'])) {
                                        StockAvailable::synchronize($product['product_id'], (int)$product['id_shop']);
                                    }
                                }
                            }

                            Tools::redirectAdmin(self::$currentIndex.'&id_order='.(int)$order->id.'&vieworder&token='.$this->token);
                        }
                        $this->errors[] = $this->trans('An error occurred while changing order status, or we were unable to send an email to the customer.', array(), 'Admin.Orderscustomers.Notification');
                    } else {
                        $this->errors[] = $this->trans('The order has already been assigned this status.', array(), 'Admin.Orderscustomers.Notification');
                    }
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('submitMessage') && isset($order)) {
            // Add a new message for the current order and send an e-mail to the customer if needed
            if ($this->access('edit')) {
                $customer = new Customer(Tools::getValue('id_customer'));
                if (!Validate::isLoadedObject($customer)) {
                    $this->errors[] = $this->trans('The customer is invalid.', array(), 'Admin.Orderscustomers.Notification');
                } elseif (!Tools::getValue('message')) {
                    $this->errors[] = $this->trans('The message cannot be blank.', array(), 'Admin.Orderscustomers.Notification');
                } else {
                    /* Get message rules and and check fields validity */
                    $rules = call_user_func(array('Message', 'getValidationRules'), 'Message');
                    foreach ($rules['required'] as $field) {
                        if (($value = Tools::getValue($field)) == false && (string)$value != '0') {
                            if (!Tools::getValue('id_'.$this->table) || $field != 'passwd') {
                                $this->errors[] = $this->trans('field %s is required.', array('%s' => $field), 'Admin.Orderscustomers.Notification');
                            }
                        }
                    }
                    foreach ($rules['size'] as $field => $maxLength) {
                        if (Tools::getValue($field) && Tools::strlen(Tools::getValue($field)) > $maxLength) {
                            $this->errors[] = $this->trans(
                                'The %1$s field is too long (%2$d chars max).',
                                array(
                                    '%1$s' => $field,
                                    '%2$d' => $maxLength,
                                ),
                                'Admin.Notifications.Error'
                            );
                        }
                    }
                    foreach ($rules['validate'] as $field => $function) {
                        if (Tools::getValue($field)) {
                            if (!Validate::$function(htmlentities(Tools::getValue($field), ENT_COMPAT, 'UTF-8'))) {
                                $this->errors[] = $this->trans('The %s field is invalid.', array('%s' => $field), 'Admin.Notifications.Error');
                            }
                        }
                    }

                    if (!count($this->errors)) {
                        //check if a thread already exist
                        $id_customer_thread = CustomerThread::getIdCustomerThreadByEmailAndIdOrder($customer->email, $order->id);
                        if (!$id_customer_thread) {
                            $customer_thread = new CustomerThread();
                            $customer_thread->id_contact = 0;
                            $customer_thread->id_customer = (int)$order->id_customer;
                            $customer_thread->id_shop = (int)$this->context->shop->id;
                            $customer_thread->id_order = (int)$order->id;
                            $customer_thread->id_lang = (int)$this->context->language->id;
                            $customer_thread->email = $customer->email;
                            $customer_thread->status = 'open';
                            $customer_thread->token = Tools::passwdGen(12);
                            $customer_thread->add();
                        } else {
                            $customer_thread = new CustomerThread((int)$id_customer_thread);
                        }

                        $customer_message = new CustomerMessage();
                        $customer_message->id_customer_thread = $customer_thread->id;
                        $customer_message->id_employee = (int)$this->context->employee->id;
                        $customer_message->message = Tools::getValue('message');
                        $customer_message->private = Tools::getValue('visibility');

                        if (!$customer_message->add()) {
                            $this->errors[] = $this->trans('An error occurred while saving the message.', array(), 'Admin.Notifications.Error');
                        } elseif ($customer_message->private) {
                            Tools::redirectAdmin(self::$currentIndex.'&id_order='.(int)$order->id.'&vieworder&conf=11&token='.$this->token);
                        } else {
                            $message = $customer_message->message;
                            if (Configuration::get('PS_MAIL_TYPE', null, null, $order->id_shop) != Mail::TYPE_TEXT) {
                                $message = Tools::nl2br($customer_message->message);
                            }

                            $orderLanguage = new Language((int) $order->id_lang);
                            $varsTpl = array(
                                '{lastname}' => $customer->lastname,
                                '{firstname}' => $customer->firstname,
                                '{id_order}' => $order->id,
                                '{order_name}' => $order->getUniqReference(),
                                '{message}' => $message
                            );

                            if (
                            @Mail::Send(
                                (int)$order->id_lang,
                                'order_merchant_comment',
                                $this->trans(
                                    'New message regarding your order',
                                    array(),
                                    'Emails.Subject',
                                    $orderLanguage->locale
                                ),
                                $varsTpl, $customer->email,
                                $customer->firstname.' '.$customer->lastname,
                                null,
                                null,
                                null,
                                null,
                                _PS_MAIL_DIR_,
                                true,
                                (int)$order->id_shop)
                            ) {
                                Tools::redirectAdmin(self::$currentIndex.'&id_order='.$order->id.'&vieworder&conf=11'.'&token='.$this->token);
                            }
                        }
                        $this->errors[] = $this->trans('An error occurred while sending an email to the customer.', array(), 'Admin.Orderscustomers.Notification');
                    }
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to delete this.', array(), 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('partialRefund') && isset($order)) {
            // Partial refund from order
            if ($this->access('edit')) {
                if (Tools::isSubmit('partialRefundProduct') && ($refunds = Tools::getValue('partialRefundProduct')) && is_array($refunds)) {
                    $amount = 0;
                    $order_detail_list = array();
                    $full_quantity_list = array();
                    foreach ($refunds as $id_order_detail => $amount_detail) {
                        $quantity = Tools::getValue('partialRefundProductQuantity');
                        if (!$quantity[$id_order_detail]) {
                            continue;
                        }

                        $full_quantity_list[$id_order_detail] = (int)$quantity[$id_order_detail];

                        $order_detail_list[$id_order_detail] = array(
                            'quantity' => (int)$quantity[$id_order_detail],
                            'id_order_detail' => (int)$id_order_detail
                        );

                        $order_detail = new OrderDetail((int)$id_order_detail);
                        if (empty($amount_detail)) {
                            $order_detail_list[$id_order_detail]['unit_price'] = (!Tools::getValue('TaxMethod') ? $order_detail->unit_price_tax_excl : $order_detail->unit_price_tax_incl);
                            $order_detail_list[$id_order_detail]['amount'] = $order_detail->unit_price_tax_incl * $order_detail_list[$id_order_detail]['quantity'];
                        } else {
                            $order_detail_list[$id_order_detail]['amount'] = (float)str_replace(',', '.', $amount_detail);
                            $order_detail_list[$id_order_detail]['unit_price'] = $order_detail_list[$id_order_detail]['amount'] / $order_detail_list[$id_order_detail]['quantity'];
                        }
                        $amount += $order_detail_list[$id_order_detail]['amount'];
                        if (!$order->hasBeenDelivered() || ($order->hasBeenDelivered() && Tools::isSubmit('reinjectQuantities')) && $order_detail_list[$id_order_detail]['quantity'] > 0) {
                            $this->reinjectQuantity($order_detail, $order_detail_list[$id_order_detail]['quantity']);
                        }
                    }

                    $shipping_cost_amount = (float)str_replace(',', '.', Tools::getValue('partialRefundShippingCost')) ? (float)str_replace(',', '.', Tools::getValue('partialRefundShippingCost')) : false;

                    if ($amount == 0 && $shipping_cost_amount == 0) {
                        if (!empty($refunds)) {
                            $this->errors[] = $this->trans('Please enter a quantity to proceed with your refund.', array(), 'Admin.Orderscustomers.Notification');
                        } else {
                            $this->errors[] = $this->trans('Please enter an amount to proceed with your refund.', array(), 'Admin.Orderscustomers.Notification');
                        }
                        return false;
                    }

                    $choosen = false;
                    $voucher = 0;

                    if ((int)Tools::getValue('refund_voucher_off') == 1) {
                        $amount -= $voucher = (float)Tools::getValue('order_discount_price');
                    } elseif ((int)Tools::getValue('refund_voucher_off') == 2) {
                        $choosen = true;
                        $amount = $voucher = (float)Tools::getValue('refund_voucher_choose');
                    }

                    if ($shipping_cost_amount > 0) {
                        if (!Tools::getValue('TaxMethod')) {
                            $tax = new Tax();
                            $tax->rate = $order->carrier_tax_rate;
                            $tax_calculator = new TaxCalculator(array($tax));
                            $amount += $tax_calculator->addTaxes($shipping_cost_amount);
                        } else {
                            $amount += $shipping_cost_amount;
                        }
                    }

                    $order_carrier = new OrderCarrier((int)$order->getIdOrderCarrier());
                    if (Validate::isLoadedObject($order_carrier)) {
                        $order_carrier->weight = (float)$order->getTotalWeight();
                        if ($order_carrier->update()) {
                            $order->weight = sprintf("%.3f ".Configuration::get('PS_WEIGHT_UNIT'), $order_carrier->weight);
                        }
                    }

                    if ($amount >= 0) {
                        if (!OrderSlip::create($order, $order_detail_list, $shipping_cost_amount, $voucher, $choosen,
                            (Tools::getValue('TaxMethod') ? false : true))) {
                            $this->errors[] = $this->trans('You cannot generate a partial credit slip.', array(), 'Admin.Orderscustomers.Notification');
                        } else {
                            Hook::exec('actionOrderSlipAdd', array('order' => $order, 'productList' => $order_detail_list, 'qtyList' => $full_quantity_list), null, false, true, false, $order->id_shop);
                            $customer = new Customer((int)($order->id_customer));
                            $params['{lastname}'] = $customer->lastname;
                            $params['{firstname}'] = $customer->firstname;
                            $params['{id_order}'] = $order->id;
                            $params['{order_name}'] = $order->getUniqReference();
                            $orderLanguage = new Language((int) $order->id_lang);
                            @Mail::Send(
                                (int)$order->id_lang,
                                'credit_slip',
                                $this->trans(
                                    'New credit slip regarding your order',
                                    array(),
                                    'Emails.Subject',
                                    $orderLanguage->locale
                                ),
                                $params,
                                $customer->email,
                                $customer->firstname.' '.$customer->lastname,
                                null,
                                null,
                                null,
                                null,
                                _PS_MAIL_DIR_,
                                true,
                                (int)$order->id_shop
                            );
                        }

                        foreach ($order_detail_list as &$product) {
                            $order_detail = new OrderDetail((int)$product['id_order_detail']);
                            if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                                StockAvailable::synchronize($order_detail->product_id);
                            }
                        }

                        // Generate voucher
                        if (Tools::isSubmit('generateDiscountRefund') && !count($this->errors) && $amount > 0) {
                            $cart_rule = new CartRule();
                            $cart_rule->description = $this->trans('Credit slip for order #%d', array('#%d' => $order->id), 'Admin.Orderscustomers.Feature');
                            $language_ids = Language::getIDs(false);
                            foreach ($language_ids as $id_lang) {
                                // Define a temporary name
                                $cart_rule->name[$id_lang] = sprintf('V0C%1$dO%2$d', $order->id_customer, $order->id);
                            }

                            // Define a temporary code
                            $cart_rule->code = sprintf('V0C%1$dO%2$d', $order->id_customer, $order->id);
                            $cart_rule->quantity = 1;
                            $cart_rule->quantity_per_user = 1;

                            // Specific to the customer
                            $cart_rule->id_customer = $order->id_customer;
                            $now = time();
                            $cart_rule->date_from = date('Y-m-d H:i:s', $now);
                            $cart_rule->date_to = date('Y-m-d H:i:s', strtotime('+1 year'));
                            $cart_rule->partial_use = 1;
                            $cart_rule->active = 1;

                            $cart_rule->reduction_amount = $amount;
                            $cart_rule->reduction_tax = $order->getTaxCalculationMethod() != PS_TAX_EXC;
                            $cart_rule->minimum_amount_currency = $order->id_currency;
                            $cart_rule->reduction_currency = $order->id_currency;

                            if (!$cart_rule->add()) {
                                $this->errors[] = $this->trans('You cannot generate a voucher.', array(), 'Admin.Orderscustomers.Notification');
                            } else {
                                // Update the voucher code and name
                                foreach ($language_ids as $id_lang) {
                                    $cart_rule->name[$id_lang] = sprintf('V%1$dC%2$dO%3$d', $cart_rule->id, $order->id_customer, $order->id);
                                }
                                $cart_rule->code = sprintf('V%1$dC%2$dO%3$d', $cart_rule->id, $order->id_customer, $order->id);

                                if (!$cart_rule->update()) {
                                    $this->errors[] = $this->trans('You cannot generate a voucher.', array(), 'Admin.Orderscustomers.Notification');
                                } else {
                                    $currency = $this->context->currency;
                                    $customer = new Customer((int)($order->id_customer));
                                    $params['{lastname}'] = $customer->lastname;
                                    $params['{firstname}'] = $customer->firstname;
                                    $params['{id_order}'] = $order->id;
                                    $params['{order_name}'] = $order->getUniqReference();
                                    $params['{voucher_amount}'] = Tools::displayPrice($cart_rule->reduction_amount, $currency, false);
                                    $params['{voucher_num}'] = $cart_rule->code;
                                    $orderLanguage = new Language((int) $order->id_lang);
                                    @Mail::Send(
                                        (int)$order->id_lang,
                                        'voucher',
                                        $this->trans(
                                            'New voucher for your order #%s',
                                            array($order->reference),
                                            'Emails.Subject',
                                            $orderLanguage->locale
                                        ),
                                        $params,
                                        $customer->email,
                                        $customer->firstname.' '.$customer->lastname,
                                        null,
                                        null,
                                        null,
                                        null,
                                        _PS_MAIL_DIR_,
                                        true,
                                        (int)$order->id_shop
                                    );
                                }
                            }
                        }
                    } else {
                        if (!empty($refunds)) {
                            $this->errors[] = $this->trans('Please enter a quantity to proceed with your refund.', array(), 'Admin.Orderscustomers.Notification');
                        } else {
                            $this->errors[] = $this->trans('Please enter an amount to proceed with your refund.', array(), 'Admin.Orderscustomers.Notification');
                        }
                    }

                    // Redirect if no errors
                    if (!count($this->errors)) {
                        Tools::redirectAdmin(self::$currentIndex.'&id_order='.$order->id.'&vieworder&conf=30&token='.$this->token);
                    }
                } else {
                    $this->errors[] = $this->trans('The partial refund data is incorrect.', array(), 'Admin.Orderscustomers.Notification');
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to delete this.', array(), 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('cancelProduct') && isset($order)) {
            // Cancel product from order
            if ($this->access('delete')) {
                if (!Tools::isSubmit('id_order_detail') && !Tools::isSubmit('id_customization')) {
                    $this->errors[] = $this->trans('You must select a product.', array(), 'Admin.Orderscustomers.Notification');
                } elseif (!Tools::isSubmit('cancelQuantity') && !Tools::isSubmit('cancelCustomizationQuantity')) {
                    $this->errors[] = $this->trans('You must enter a quantity.', array(), 'Admin.Orderscustomers.Notification');
                } else {
                    $productList = Tools::getValue('id_order_detail');
                    if ($productList) {
                        $productList = array_map('intval', $productList);
                    }

                    $customizationList = Tools::getValue('id_customization');
                    if ($customizationList) {
                        $customizationList = array_map('intval', $customizationList);
                    }

                    $qtyList = Tools::getValue('cancelQuantity');
                    if ($qtyList) {
                        $qtyList = array_map('intval', $qtyList);
                    }

                    $customizationQtyList = Tools::getValue('cancelCustomizationQuantity');
                    if ($customizationQtyList) {
                        $customizationQtyList = array_map('intval', $customizationQtyList);
                    }

                    $full_product_list = $productList;
                    $full_quantity_list = $qtyList;

                    if ($customizationList) {
                        foreach ($customizationList as $key => $id_order_detail) {
                            $full_product_list[(int)$id_order_detail] = $id_order_detail;
                            if (isset($customizationQtyList[$key])) {
                                $full_quantity_list[(int)$id_order_detail] += $customizationQtyList[$key];
                            }
                        }
                    }

                    if ($productList || $customizationList) {
                        if ($productList) {
                            $id_cart = Cart::getCartIdByOrderId($order->id);
                            $customization_quantities = Customization::countQuantityByCart($id_cart);

                            foreach ($productList as $key => $id_order_detail) {
                                $qtyCancelProduct = abs($qtyList[$key]);
                                if (!$qtyCancelProduct) {
                                    $this->errors[] = $this->trans('No quantity has been selected for this product.', array(), 'Admin.Orderscustomers.Notification');
                                }

                                $order_detail = new OrderDetail($id_order_detail);
                                $customization_quantity = 0;
                                if (array_key_exists($order_detail->product_id, $customization_quantities) && array_key_exists($order_detail->product_attribute_id, $customization_quantities[$order_detail->product_id])) {
                                    $customization_quantity = (int)$customization_quantities[$order_detail->product_id][$order_detail->product_attribute_id];
                                }

                                if (($order_detail->product_quantity - $customization_quantity - $order_detail->product_quantity_refunded - $order_detail->product_quantity_return) < $qtyCancelProduct) {
                                    $this->errors[] = $this->trans('An invalid quantity was selected for this product.', array(), 'Admin.Orderscustomers.Notification');
                                }
                            }
                        }
                        if ($customizationList) {
                            $customization_quantities = Customization::retrieveQuantitiesFromIds(array_keys($customizationList));

                            foreach ($customizationList as $id_customization => $id_order_detail) {
                                $qtyCancelProduct = abs($customizationQtyList[$id_customization]);
                                $customization_quantity = $customization_quantities[$id_customization];

                                if (!$qtyCancelProduct) {
                                    $this->errors[] = $this->trans('No quantity has been selected for this product.', array(), 'Admin.Orderscustomers.Notification');
                                }

                                if ($qtyCancelProduct > ($customization_quantity['quantity'] - ($customization_quantity['quantity_refunded'] + $customization_quantity['quantity_returned']))) {
                                    $this->errors[] = $this->trans('An invalid quantity was selected for this product.', array(), 'Admin.Orderscustomers.Notification');
                                }
                            }
                        }

                        if (!count($this->errors) && $productList) {
                            foreach ($productList as $key => $id_order_detail) {
                                $qty_cancel_product = abs($qtyList[$key]);
                                $order_detail = new OrderDetail((int)($id_order_detail));

                                if (!$order->hasBeenDelivered() || ($order->hasBeenDelivered() && Tools::isSubmit('reinjectQuantities')) && $qty_cancel_product > 0) {
                                    $this->reinjectQuantity($order_detail, $qty_cancel_product);
                                }

                                // Delete product
                                $order_detail = new OrderDetail((int)$id_order_detail);
                                if (!$order->deleteProduct($order, $order_detail, $qty_cancel_product)) {
                                    $this->errors[] = $this->trans('An error occurred while attempting to delete the product.', array(), 'Admin.Orderscustomers.Notification').' <span class="bold">'.$order_detail->product_name.'</span>';
                                }
                                // Update weight SUM
                                $order_carrier = new OrderCarrier((int)$order->getIdOrderCarrier());
                                if (Validate::isLoadedObject($order_carrier)) {
                                    $order_carrier->weight = (float)$order->getTotalWeight();
                                    if ($order_carrier->update()) {
                                        $order->weight = sprintf("%.3f ".Configuration::get('PS_WEIGHT_UNIT'), $order_carrier->weight);
                                    }
                                }

                                if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && StockAvailable::dependsOnStock($order_detail->product_id)) {
                                    StockAvailable::synchronize($order_detail->product_id);
                                }
                                Hook::exec('actionProductCancel', array('order' => $order, 'id_order_detail' => (int)$id_order_detail), null, false, true, false, $order->id_shop);
                            }
                        }
                        if (!count($this->errors) && $customizationList) {
                            foreach ($customizationList as $id_customization => $id_order_detail) {
                                $order_detail = new OrderDetail((int)($id_order_detail));
                                $qtyCancelProduct = abs($customizationQtyList[$id_customization]);
                                if (!$order->deleteCustomization($id_customization, $qtyCancelProduct, $order_detail)) {
                                    $this->errors[] = $this->trans('An error occurred while attempting to delete product customization.', array(), 'Admin.Orderscustomers.Notification').' '.$id_customization;
                                }
                            }
                        }
                        // E-mail params
                        if ((Tools::isSubmit('generateCreditSlip') || Tools::isSubmit('generateDiscount')) && !count($this->errors)) {
                            $customer = new Customer((int)($order->id_customer));
                            $params['{lastname}'] = $customer->lastname;
                            $params['{firstname}'] = $customer->firstname;
                            $params['{id_order}'] = $order->id;
                            $params['{order_name}'] = $order->getUniqReference();
                        }

                        // Generate credit slip
                        if (Tools::isSubmit('generateCreditSlip') && !count($this->errors)) {
                            $product_list = array();
                            $amount = $order_detail->unit_price_tax_incl * $full_quantity_list[$id_order_detail];

                            $choosen = false;
                            if ((int)Tools::getValue('refund_total_voucher_off') == 1) {
                                $amount -= $voucher = (float)Tools::getValue('order_discount_price');
                            } elseif ((int)Tools::getValue('refund_total_voucher_off') == 2) {
                                $choosen = true;
                                $amount = $voucher = (float)Tools::getValue('refund_total_voucher_choose');
                            }
                            foreach ($full_product_list as $id_order_detail) {
                                $order_detail = new OrderDetail((int)$id_order_detail);
                                $product_list[$id_order_detail] = array(
                                    'id_order_detail' => $id_order_detail,
                                    'quantity' => $full_quantity_list[$id_order_detail],
                                    'unit_price' => $order_detail->unit_price_tax_excl,
                                    'amount' => isset($amount) ? $amount : $order_detail->unit_price_tax_incl * $full_quantity_list[$id_order_detail],
                                );
                            }

                            $shipping = Tools::isSubmit('shippingBack') ? null : false;

                            if (!OrderSlip::create($order, $product_list, $shipping, $voucher, $choosen)) {
                                $this->errors[] = $this->trans('A credit slip cannot be generated.', array(), 'Admin.Orderscustomers.Notification');
                            } else {
                                Hook::exec('actionOrderSlipAdd', array('order' => $order, 'productList' => $full_product_list, 'qtyList' => $full_quantity_list), null, false, true, false, $order->id_shop);
                                $orderLanguage = new Language((int) $order->id_lang);
                                @Mail::Send(
                                    (int)$order->id_lang,
                                    'credit_slip',
                                    $this->trans(
                                        'New credit slip regarding your order',
                                        array(),
                                        'Emails.Subject',
                                        $orderLanguage->locale
                                    ),
                                    $params,
                                    $customer->email,
                                    $customer->firstname.' '.$customer->lastname,
                                    null,
                                    null,
                                    null,
                                    null,
                                    _PS_MAIL_DIR_,
                                    true,
                                    (int)$order->id_shop
                                );
                            }
                        }

                        // Generate voucher
                        if (Tools::isSubmit('generateDiscount') && !count($this->errors)) {
                            $cartrule = new CartRule();
                            $language_ids = Language::getIDs((bool)$order);
                            $cartrule->description = $this->trans('Credit card slip for order #%d', array('#%d' => $order->id), 'Admin.Orderscustomers.Feature');
                            foreach ($language_ids as $id_lang) {
                                // Define a temporary name
                                $cartrule->name[$id_lang] = 'V0C'.(int)($order->id_customer).'O'.(int)($order->id);
                            }
                            // Define a temporary code
                            $cartrule->code = 'V0C'.(int)($order->id_customer).'O'.(int)($order->id);

                            $cartrule->quantity = 1;
                            $cartrule->quantity_per_user = 1;
                            // Specific to the customer
                            $cartrule->id_customer = $order->id_customer;
                            $now = time();
                            $cartrule->date_from = date('Y-m-d H:i:s', $now);
                            $cartrule->date_to = date('Y-m-d H:i:s', $now + (3600 * 24 * 365.25)); /* 1 year */
                            $cartrule->active = 1;

                            $products = $order->getProducts(false, $full_product_list, $full_quantity_list);

                            $total = 0;
                            foreach ($products as $product) {
                                $total += $product['unit_price_tax_incl'] * $product['product_quantity'];
                            }

                            if (Tools::isSubmit('shippingBack')) {
                                $total += $order->total_shipping;
                            }

                            if ((int)Tools::getValue('refund_total_voucher_off') == 1) {
                                $total -= (float)Tools::getValue('order_discount_price');
                            } elseif ((int)Tools::getValue('refund_total_voucher_off') == 2) {
                                $total = (float)Tools::getValue('refund_total_voucher_choose');
                            }

                            $cartrule->reduction_amount = $total;
                            $cartrule->reduction_tax = true;
                            $cartrule->minimum_amount_currency = $order->id_currency;
                            $cartrule->reduction_currency = $order->id_currency;

                            if (!$cartrule->add()) {
                                $this->errors[] = $this->trans('You cannot generate a voucher.', array(), 'Admin.Orderscustomers.Notification');
                            } else {
                                // Update the voucher code and name
                                foreach ($language_ids as $id_lang) {
                                    $cartrule->name[$id_lang] = 'V'.(int)($cartrule->id).'C'.(int)($order->id_customer).'O'.$order->id;
                                }
                                $cartrule->code = 'V'.(int)($cartrule->id).'C'.(int)($order->id_customer).'O'.$order->id;
                                if (!$cartrule->update()) {
                                    $this->errors[] = $this->trans('You cannot generate a voucher.', array(), 'Admin.Orderscustomers.Notification');
                                } else {
                                    $currency = $this->context->currency;
                                    $params['{voucher_amount}'] = Tools::displayPrice($cartrule->reduction_amount, $currency, false);
                                    $params['{voucher_num}'] = $cartrule->code;
                                    $orderLanguage = new Language((int) $order->id_lang);
                                    @Mail::Send(
                                        (int)$order->id_lang,
                                        'voucher',
                                        $this->trans(
                                            'New voucher for your order #%s',
                                            array($order->reference),
                                            'Emails.Subject',
                                            $orderLanguage->locale
                                        ),
                                        $params,
                                        $customer->email,
                                        $customer->firstname.' '.$customer->lastname,
                                        null,
                                        null,
                                        null,
                                        null,
                                        _PS_MAIL_DIR_,
                                        true,
                                        (int)$order->id_shop
                                    );
                                }
                            }
                        }
                    } else {
                        $this->errors[] = $this->trans('No product or quantity has been selected.', array(), 'Admin.Orderscustomers.Notification');
                    }

                    // Redirect if no errors
                    if (!count($this->errors)) {
                        Tools::redirectAdmin(self::$currentIndex.'&id_order='.$order->id.'&vieworder&conf=31&token='.$this->token);
                    }
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to delete this.', array(), 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('messageReaded')) {
            Message::markAsReaded(Tools::getValue('messageReaded'), $this->context->employee->id);
        } elseif (Tools::isSubmit('submitAddPayment') && isset($order)) {
            if ($this->access('edit')) {
                $amount = str_replace(',', '.', Tools::getValue('payment_amount'));
                $currency = new Currency(Tools::getValue('payment_currency'));
                $order_has_invoice = $order->hasInvoice();
                if ($order_has_invoice) {
                    $order_invoice = new OrderInvoice(Tools::getValue('payment_invoice'));
                } else {
                    $order_invoice = null;
                }

                if (!Validate::isLoadedObject($order)) {
                    $this->errors[] = $this->trans('The order cannot be found', array(), 'Admin.Orderscustomers.Notification');
                } elseif (!Validate::isNegativePrice($amount) || !(float)$amount) {
                    $this->errors[] = $this->trans('The amount is invalid.', array(), 'Admin.Orderscustomers.Notification');
                } elseif (!Validate::isGenericName(Tools::getValue('payment_method'))) {
                    $this->errors[] = $this->trans('The selected payment method is invalid.', array(), 'Admin.Orderscustomers.Notification');
                } elseif (!Validate::isString(Tools::getValue('payment_transaction_id'))) {
                    $this->errors[] = $this->trans('The transaction ID is invalid.', array(), 'Admin.Orderscustomers.Notification');
                } elseif (!Validate::isLoadedObject($currency)) {
                    $this->errors[] = $this->trans('The selected currency is invalid.', array(), 'Admin.Orderscustomers.Notification');
                } elseif ($order_has_invoice && !Validate::isLoadedObject($order_invoice)) {
                    $this->errors[] = $this->trans('The invoice is invalid.', array(), 'Admin.Orderscustomers.Notification');
                } elseif (!Validate::isDate(Tools::getValue('payment_date'))) {
                    $this->errors[] = $this->trans('The date is invalid', array(), 'Admin.Orderscustomers.Notification');
                } else {
                    if (!$order->addOrderPayment($amount, Tools::getValue('payment_method'), Tools::getValue('payment_transaction_id'), $currency, Tools::getValue('payment_date'), $order_invoice)) {
                        $this->errors[] = $this->trans('An error occurred during payment.', array(), 'Admin.Orderscustomers.Notification');
                    } else {
                        Tools::redirectAdmin(self::$currentIndex.'&id_order='.$order->id.'&vieworder&conf=4&token='.$this->token);
                    }
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('submitEditNote')) {
            $note = Tools::getValue('note');
            $order_invoice = new OrderInvoice((int)Tools::getValue('id_order_invoice'));
            if (Validate::isLoadedObject($order_invoice) && Validate::isCleanHtml($note)) {
                if ($this->access('edit')) {
                    $order_invoice->note = $note;
                    if ($order_invoice->save()) {
                        Tools::redirectAdmin(self::$currentIndex.'&id_order='.$order_invoice->id_order.'&vieworder&conf=4&token='.$this->token);
                    } else {
                        $this->errors[] = $this->trans('The invoice note was not saved.', array(), 'Admin.Orderscustomers.Notification');
                    }
                } else {
                    $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
                }
            } else {
                $this->errors[] = $this->trans('Failed to upload the invoice and edit its note.', array(), 'Admin.Orderscustomers.Notification');
            }
        } elseif (Tools::isSubmit('submitAddOrder') && ($id_cart = Tools::getValue('id_cart')) &&
            ($module_name = Tools::getValue('payment_module_name')) &&
            ($id_order_state = Tools::getValue('id_order_state')) && Validate::isModuleName($module_name)) {
            if ($this->access('edit')) {
                if (!Configuration::get('PS_CATALOG_MODE')) {
                    $payment_module = Module::getInstanceByName($module_name);
                } else {
                    $payment_module = new BoOrder();
                }

                $cart = new Cart((int)$id_cart);
                Context::getContext()->currency = new Currency((int)$cart->id_currency);
                Context::getContext()->customer = new Customer((int)$cart->id_customer);

                $bad_delivery = false;
                if (($bad_delivery = (bool)!Address::isCountryActiveById((int)$cart->id_address_delivery))
                    || !Address::isCountryActiveById((int)$cart->id_address_invoice)) {
                    if ($bad_delivery) {
                        $this->errors[] = $this->trans('This delivery address country is not active.', array(), 'Admin.Orderscustomers.Notification');
                    } else {
                        $this->errors[] = $this->trans('This invoice address country is not active.', array(), 'Admin.Orderscustomers.Notification');
                    }
                } else {
                    $employee = new Employee((int)Context::getContext()->cookie->id_employee);
                    $payment_module->validateOrder(
                        (int)$cart->id, (int)$id_order_state,
                        $cart->getOrderTotal(true, Cart::BOTH), $payment_module->displayName, $this->trans('Manual order -- Employee:', array(), 'Admin.Orderscustomers.Feature').' '.
                        substr($employee->firstname, 0, 1).'. '.$employee->lastname, array(), null, false, $cart->secure_key
                    );
                    if ($payment_module->currentOrder) {
                        Tools::redirectAdmin(self::$currentIndex.'&id_order='.$payment_module->currentOrder.'&vieworder'.'&token='.$this->token);
                    }
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to add this.', array(), 'Admin.Notifications.Error');
            }
        } elseif ((Tools::isSubmit('submitAddressShipping') || Tools::isSubmit('submitAddressInvoice')) && isset($order)) {
            if ($this->access('edit')) {
                $address = new Address(Tools::getValue('id_address'));
                if (Validate::isLoadedObject($address)) {
                    // Update the address on order
                    if (Tools::isSubmit('submitAddressShipping')) {
                        $order->id_address_delivery = $address->id;
                    } elseif (Tools::isSubmit('submitAddressInvoice')) {
                        $order->id_address_invoice = $address->id;
                    }
                    $order->update();
                    $order->refreshShippingCost();

                    Tools::redirectAdmin(self::$currentIndex.'&id_order='.$order->id.'&vieworder&conf=4&token='.$this->token);
                } else {
                    $this->errors[] = $this->trans('This address can\'t be loaded', array(), 'Admin.Orderscustomers.Notification');
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('submitChangeCurrency') && isset($order)) {
            if ($this->access('edit')) {
                if (Tools::getValue('new_currency') != $order->id_currency && !$order->valid) {
                    $old_currency = new Currency($order->id_currency);
                    $currency = new Currency(Tools::getValue('new_currency'));
                    if (!Validate::isLoadedObject($currency)) {
                        throw new PrestaShopException('Can\'t load Currency object');
                    }

                    // Update order detail amount
                    foreach ($order->getOrderDetailList() as $row) {
                        $order_detail = new OrderDetail($row['id_order_detail']);
                        $fields = array(
                            'ecotax',
                            'product_price',
                            'reduction_amount',
                            'total_shipping_price_tax_excl',
                            'total_shipping_price_tax_incl',
                            'total_price_tax_incl',
                            'total_price_tax_excl',
                            'product_quantity_discount',
                            'purchase_supplier_price',
                            'reduction_amount',
                            'reduction_amount_tax_incl',
                            'reduction_amount_tax_excl',
                            'unit_price_tax_incl',
                            'unit_price_tax_excl',
                            'original_product_price'

                        );
                        foreach ($fields as $field) {
                            $order_detail->{$field} = Tools::convertPriceFull($order_detail->{$field}, $old_currency, $currency);
                        }

                        $order_detail->update();
                        $order_detail->updateTaxAmount($order);
                    }

                    $id_order_carrier = (int)$order->getIdOrderCarrier();
                    if ($id_order_carrier) {
                        $order_carrier = $order_carrier = new OrderCarrier((int)$order->getIdOrderCarrier());
                        $order_carrier->shipping_cost_tax_excl = (float)Tools::convertPriceFull($order_carrier->shipping_cost_tax_excl, $old_currency, $currency);
                        $order_carrier->shipping_cost_tax_incl = (float)Tools::convertPriceFull($order_carrier->shipping_cost_tax_incl, $old_currency, $currency);
                        $order_carrier->update();
                    }

                    // Update order && order_invoice amount
                    $fields = array(
                        'total_discounts',
                        'total_discounts_tax_incl',
                        'total_discounts_tax_excl',
                        'total_discount_tax_excl',
                        'total_discount_tax_incl',
                        'total_paid',
                        'total_paid_tax_incl',
                        'total_paid_tax_excl',
                        'total_paid_real',
                        'total_products',
                        'total_products_wt',
                        'total_shipping',
                        'total_shipping_tax_incl',
                        'total_shipping_tax_excl',
                        'total_wrapping',
                        'total_wrapping_tax_incl',
                        'total_wrapping_tax_excl',
                    );

                    $invoices = $order->getInvoicesCollection();
                    if ($invoices) {
                        foreach ($invoices as $invoice) {
                            foreach ($fields as $field) {
                                if (isset($invoice->$field)) {
                                    $invoice->{$field} = Tools::convertPriceFull($invoice->{$field}, $old_currency, $currency);
                                }
                            }
                            $invoice->save();
                        }
                    }

                    foreach ($fields as $field) {
                        if (isset($order->$field)) {
                            $order->{$field} = Tools::convertPriceFull($order->{$field}, $old_currency, $currency);
                        }
                    }

                    // Update currency in order
                    $order->id_currency = $currency->id;
                    // Update exchange rate
                    $order->conversion_rate = (float)$currency->conversion_rate;
                    $order->update();
                } else {
                    $this->errors[] = $this->trans('You cannot change the currency.', array(), 'Admin.Orderscustomers.Notification');
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('submitGenerateInvoice') && isset($order)) {
            if (!Configuration::get('PS_INVOICE', null, null, $order->id_shop)) {
                $this->errors[] = $this->trans('Invoice management has been disabled.', array(), 'Admin.Orderscustomers.Notification');
            } elseif ($order->hasInvoice()) {
                $this->errors[] = $this->trans('This order already has an invoice.', array(), 'Admin.Orderscustomers.Notification');
            } else {
                $order->setInvoice(true);
                Tools::redirectAdmin(self::$currentIndex.'&id_order='.$order->id.'&vieworder&conf=4&token='.$this->token);
            }
        } elseif (Tools::isSubmit('submitDeleteVoucher') && isset($order)) {
            if ($this->access('edit')) {
                $order_cart_rule = new OrderCartRule(Tools::getValue('id_order_cart_rule'));
                if (Validate::isLoadedObject($order_cart_rule) && $order_cart_rule->id_order == $order->id) {
                    if ($order_cart_rule->id_order_invoice) {
                        $order_invoice = new OrderInvoice($order_cart_rule->id_order_invoice);
                        if (!Validate::isLoadedObject($order_invoice)) {
                            throw new PrestaShopException('Can\'t load Order Invoice object');
                        }

                        // Update amounts of Order Invoice
                        $order_invoice->total_discount_tax_excl -= $order_cart_rule->value_tax_excl;
                        $order_invoice->total_discount_tax_incl -= $order_cart_rule->value;

                        $order_invoice->total_paid_tax_excl += $order_cart_rule->value_tax_excl;
                        $order_invoice->total_paid_tax_incl += $order_cart_rule->value;

                        // Update Order Invoice
                        $order_invoice->update();
                    }

                    // Update amounts of order
                    $order->total_discounts -= $order_cart_rule->value;
                    $order->total_discounts_tax_incl -= $order_cart_rule->value;
                    $order->total_discounts_tax_excl -= $order_cart_rule->value_tax_excl;

                    $order->total_paid += $order_cart_rule->value;
                    $order->total_paid_tax_incl += $order_cart_rule->value;
                    $order->total_paid_tax_excl += $order_cart_rule->value_tax_excl;

                    // Delete Order Cart Rule and update Order
                    $order_cart_rule->delete();
                    $order->update();
                    Tools::redirectAdmin(self::$currentIndex.'&id_order='.$order->id.'&vieworder&conf=4&token='.$this->token);
                } else {
                    $this->errors[] = $this->trans('You cannot edit this cart rule.', array(), 'Admin.Orderscustomers.Notification');
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('submitNewVoucher') && isset($order)) {
            if ($this->access('edit')) {
                if (!Tools::getValue('discount_name')) {
                    $this->errors[] = $this->trans('You must specify a name in order to create a new discount.', array(), 'Admin.Orderscustomers.Notification');
                } else {
                    if ($order->hasInvoice()) {
                        // If the discount is for only one invoice
                        if (!Tools::isSubmit('discount_all_invoices')) {
                            $order_invoice = new OrderInvoice(Tools::getValue('discount_invoice'));
                            if (!Validate::isLoadedObject($order_invoice)) {
                                throw new PrestaShopException('Can\'t load Order Invoice object');
                            }
                        }
                    }

                    $cart_rules = array();
                    $discount_value = (float)str_replace(',', '.', Tools::getValue('discount_value'));
                    switch (Tools::getValue('discount_type')) {
                        // Percent type
                        case 1:
                            if ($discount_value < 100) {
                                if (isset($order_invoice)) {
                                    $cart_rules[$order_invoice->id]['value_tax_incl'] = Tools::ps_round($order_invoice->total_paid_tax_incl * $discount_value / 100, 2);
                                    $cart_rules[$order_invoice->id]['value_tax_excl'] = Tools::ps_round($order_invoice->total_paid_tax_excl * $discount_value / 100, 2);

                                    // Update OrderInvoice
                                    $this->applyDiscountOnInvoice($order_invoice, $cart_rules[$order_invoice->id]['value_tax_incl'], $cart_rules[$order_invoice->id]['value_tax_excl']);
                                } elseif ($order->hasInvoice()) {
                                    $order_invoices_collection = $order->getInvoicesCollection();
                                    foreach ($order_invoices_collection as $order_invoice) {
                                        /** @var OrderInvoice $order_invoice */
                                        $cart_rules[$order_invoice->id]['value_tax_incl'] = Tools::ps_round($order_invoice->total_paid_tax_incl * $discount_value / 100, 2);
                                        $cart_rules[$order_invoice->id]['value_tax_excl'] = Tools::ps_round($order_invoice->total_paid_tax_excl * $discount_value / 100, 2);

                                        // Update OrderInvoice
                                        $this->applyDiscountOnInvoice($order_invoice, $cart_rules[$order_invoice->id]['value_tax_incl'], $cart_rules[$order_invoice->id]['value_tax_excl']);
                                    }
                                } else {
                                    $cart_rules[0]['value_tax_incl'] = Tools::ps_round($order->total_paid_tax_incl * $discount_value / 100, 2);
                                    $cart_rules[0]['value_tax_excl'] = Tools::ps_round($order->total_paid_tax_excl * $discount_value / 100, 2);
                                }
                            } else {
                                $this->errors[] = $this->trans('The discount value is invalid.', array(), 'Admin.Orderscustomers.Notification');
                            }
                            break;
                        // Amount type
                        case 2:
                            if (isset($order_invoice)) {
                                if ($discount_value > $order_invoice->total_paid_tax_incl) {
                                    $this->errors[] = $this->trans('The discount value is greater than the order invoice total.', array(), 'Admin.Orderscustomers.Notification');
                                } else {
                                    $cart_rules[$order_invoice->id]['value_tax_incl'] = Tools::ps_round($discount_value, 2);
                                    $cart_rules[$order_invoice->id]['value_tax_excl'] = Tools::ps_round($discount_value / (1 + ($order->getTaxesAverageUsed() / 100)), 2);

                                    // Update OrderInvoice
                                    $this->applyDiscountOnInvoice($order_invoice, $cart_rules[$order_invoice->id]['value_tax_incl'], $cart_rules[$order_invoice->id]['value_tax_excl']);
                                }
                            } elseif ($order->hasInvoice()) {
                                $order_invoices_collection = $order->getInvoicesCollection();
                                foreach ($order_invoices_collection as $order_invoice) {
                                    /** @var OrderInvoice $order_invoice */
                                    if ($discount_value > $order_invoice->total_paid_tax_incl) {
                                        $this->errors[] = $this->trans('The discount value is greater than the order invoice total.', array(), 'Admin.Orderscustomers.Notification').$order_invoice->getInvoiceNumberFormatted(Context::getContext()->language->id, (int)$order->id_shop).')';
                                    } else {
                                        $cart_rules[$order_invoice->id]['value_tax_incl'] = Tools::ps_round($discount_value, 2);
                                        $cart_rules[$order_invoice->id]['value_tax_excl'] = Tools::ps_round($discount_value / (1 + ($order->getTaxesAverageUsed() / 100)), 2);

                                        // Update OrderInvoice
                                        $this->applyDiscountOnInvoice($order_invoice, $cart_rules[$order_invoice->id]['value_tax_incl'], $cart_rules[$order_invoice->id]['value_tax_excl']);
                                    }
                                }
                            } else {
                                if ($discount_value > $order->total_paid_tax_incl) {
                                    $this->errors[] = $this->trans('The discount value is greater than the order total.', array(), 'Admin.Orderscustomers.Notification');
                                } else {
                                    $cart_rules[0]['value_tax_incl'] = Tools::ps_round($discount_value, 2);
                                    $cart_rules[0]['value_tax_excl'] = Tools::ps_round($discount_value / (1 + ($order->getTaxesAverageUsed() / 100)), 2);
                                }
                            }
                            break;
                        // Free shipping type
                        case 3:
                            if (isset($order_invoice)) {
                                if ($order_invoice->total_shipping_tax_incl > 0) {
                                    $cart_rules[$order_invoice->id]['value_tax_incl'] = $order_invoice->total_shipping_tax_incl;
                                    $cart_rules[$order_invoice->id]['value_tax_excl'] = $order_invoice->total_shipping_tax_excl;

                                    // Update OrderInvoice
                                    $this->applyDiscountOnInvoice($order_invoice, $cart_rules[$order_invoice->id]['value_tax_incl'], $cart_rules[$order_invoice->id]['value_tax_excl']);
                                }
                            } elseif ($order->hasInvoice()) {
                                $order_invoices_collection = $order->getInvoicesCollection();
                                foreach ($order_invoices_collection as $order_invoice) {
                                    /** @var OrderInvoice $order_invoice */
                                    if ($order_invoice->total_shipping_tax_incl <= 0) {
                                        continue;
                                    }
                                    $cart_rules[$order_invoice->id]['value_tax_incl'] = $order_invoice->total_shipping_tax_incl;
                                    $cart_rules[$order_invoice->id]['value_tax_excl'] = $order_invoice->total_shipping_tax_excl;

                                    // Update OrderInvoice
                                    $this->applyDiscountOnInvoice($order_invoice, $cart_rules[$order_invoice->id]['value_tax_incl'], $cart_rules[$order_invoice->id]['value_tax_excl']);
                                }
                            } else {
                                $cart_rules[0]['value_tax_incl'] = $order->total_shipping_tax_incl;
                                $cart_rules[0]['value_tax_excl'] = $order->total_shipping_tax_excl;
                            }
                            break;
                        default:
                            $this->errors[] = $this->trans('The discount type is invalid.', array(), 'Admin.Orderscustomers.Notification');
                    }

                    $res = true;
                    foreach ($cart_rules as &$cart_rule) {
                        $cartRuleObj = new CartRule();
                        $cartRuleObj->date_from = date('Y-m-d H:i:s', strtotime('-1 hour', strtotime($order->date_add)));
                        $cartRuleObj->date_to = date('Y-m-d H:i:s', strtotime('+1 hour'));
                        $cartRuleObj->name[Configuration::get('PS_LANG_DEFAULT')] = Tools::getValue('discount_name');
                        $cartRuleObj->quantity = 0;
                        $cartRuleObj->quantity_per_user = 1;
                        if (Tools::getValue('discount_type') == 1) {
                            $cartRuleObj->reduction_percent = $discount_value;
                        } elseif (Tools::getValue('discount_type') == 2) {
                            $cartRuleObj->reduction_amount = $cart_rule['value_tax_excl'];
                        } elseif (Tools::getValue('discount_type') == 3) {
                            $cartRuleObj->free_shipping = 1;
                        }
                        $cartRuleObj->active = 0;
                        if ($res = $cartRuleObj->add()) {
                            $cart_rule['id'] = $cartRuleObj->id;
                        } else {
                            break;
                        }
                    }

                    if ($res) {
                        foreach ($cart_rules as $id_order_invoice => $cart_rule) {
                            // Create OrderCartRule
                            $order_cart_rule = new OrderCartRule();
                            $order_cart_rule->id_order = $order->id;
                            $order_cart_rule->id_cart_rule = $cart_rule['id'];
                            $order_cart_rule->id_order_invoice = $id_order_invoice;
                            $order_cart_rule->name = Tools::getValue('discount_name');
                            $order_cart_rule->value = $cart_rule['value_tax_incl'];
                            $order_cart_rule->value_tax_excl = $cart_rule['value_tax_excl'];
                            $res &= $order_cart_rule->add();

                            $order->total_discounts += $order_cart_rule->value;
                            $order->total_discounts_tax_incl += $order_cart_rule->value;
                            $order->total_discounts_tax_excl += $order_cart_rule->value_tax_excl;
                            $order->total_paid -= $order_cart_rule->value;
                            $order->total_paid_tax_incl -= $order_cart_rule->value;
                            $order->total_paid_tax_excl -= $order_cart_rule->value_tax_excl;
                        }

                        // Update Order
                        $res &= $order->update();
                    }

                    if ($res) {
                        Tools::redirectAdmin(self::$currentIndex.'&id_order='.$order->id.'&vieworder&conf=4&token='.$this->token);
                    } else {
                        $this->errors[] = $this->trans('An error occurred during the OrderCartRule creation', array(), 'Admin.Orderscustomers.Notification');
                    }
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('sendStateEmail') && Tools::getValue('sendStateEmail') > 0 && Tools::getValue('id_order') > 0) {
            if ($this->access('edit')) {
                $order_state = new OrderState((int)Tools::getValue('sendStateEmail'));

                if (!Validate::isLoadedObject($order_state)) {
                    $this->errors[] = $this->trans('An error occurred while loading order status.', array(), 'Admin.Orderscustomers.Notification');
                } else {
                    $history = new OrderHistory((int)Tools::getValue('id_order_history'));

                    $carrier = new Carrier($order->id_carrier, $order->id_lang);
                    $templateVars = array();
                    if ($order_state->id == Configuration::get('PS_OS_SHIPPING') && $order->shipping_number) {
                        $templateVars = array('{followup}' => str_replace('@', $order->shipping_number, $carrier->url));
                    }

                    if ($history->sendEmail($order, $templateVars)) {
                        Tools::redirectAdmin(self::$currentIndex.'&id_order='.$order->id.'&vieworder&conf=10&token='.$this->token);
                    } else {
                        $this->errors[] = $this->trans('An error occurred while sending the e-mail to the customer.', array(), 'Admin.Orderscustomers.Notification');
                    }
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
            }
        }

        parent::postProcess();
    }


}