<?php

class AdminOrdersController extends AdminOrdersControllerCore {

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
            $order = new Order((int)Tools::getValue('id_order'));
            $this->context->cart = new Cart($order->id_cart);
            $this->context->customer = new Customer($order->id_customer);
        }

        $this->bulk_actions = array(
            'updateOrderStatus' => array('text' => $this->trans('Change Order Status', array(), 'Admin.Orderscustomers.Feature'), 'icon' => 'icon-refresh'),
            'downloadPreparationSlips' => array('text'=>$this->trans('Télécharger les bons de préparation', array(), 'Admin.Orderscustomers.Feature'), 'icon'=>'icon-download')
        );
    }

    /**
    * Ajoute la modal import à la page liste
    **/
    public function renderList() {

        $tpl = $this->context->smarty->createTemplate(_PS_ROOT_DIR_."/override/controllers/admin/templates/orders/import.tpl");
        return parent::renderList().$tpl->fetch();
    }

    public function initContent() {

        // Import commande
        if(Tools::isSubmit('import')) {
            if(isset($_FILES['file'])) {

                $handle = fopen($_FILES['file']['tmp_name'], 'r');
                $not_found = array();

                if(Tools::getValue('skip'));
                    fgetcsv($handle, 0, ";");

                while($row = fgetcsv($handle, 0, ";")) {

                    if($order = Order::getIdByReference($row[0])) {

                        if($row[4]) $order->invoice_number = $row[4];
                        if($row[5]) $order->invoice_date = DateTime::createFromFormat('d/m/Y', $row[5]);
                        $order->save();

                        if($row[2])
                            OrderHistory::changeIdOrderState($row[2], $order->id);
                    }
                    else
                        $not_found[] = $row[0];
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

            $order = new Order((int)Tools::getValue('id_order'));
            $order->internal_reference = Tools::getValue('new_internal_reference');
            $order->save();
        }

        // Supprimer un OA
        if($id = Tools::getValue('remove_oa')) {
            $oa = new OA($id);
            $oa->delete();
        }

        // Enregistrer un nouvel OA
        if(Tools::isSubmit('save_new_oa')) {
            $form = Tools::getValue('new_oa');

            $oa = OA::find(Tools::getValue('id_order'), $form['id_supplier']);
            $oa->code = $form['code'];
            $oa->save();
        }

        // Modifier un OA existant
        if(Tools::getValue('save_oa')) {

            $oa = new OA(Tools::getValue('save_oa'));
            $oa->id_supplier = Tools::getValue('id_supplier');
            $oa->code = Tools::getValue('code');
            $oa->save();
        }
        
        // Supprimer un historique
        if($id = Tools::getValue('remove_history')) {
            $history = new OrderHistory($id);
            if($history->id) $history->delete();
        }

        // Supprimer un paiement 
        if($id = Tools::getValue('remove_payment')) {
            $payment = new OrderPayment($id);
            if($payment->id) $payment->delete();
        }

        // Enregistrement facturation 
        if(Tools::isSubmit('save_invoice')) {
            $order = new Order((int)Tools::getValue('id_order'));
            $order->invoice_date = Tools::getValue('invoice_date');
            $order->invoice_number = Tools::getValue('invoice_number');
            $order->no_recall = Tools::getValue('no_recall');
            $order->display_with_taxes = Tools::getValue('display_with_taxes');
            $order->invoice_comment = Tools::getValue('invoice_comment');
            $order->save();
        }
        // Enregistrement des infomations complémentaires
        foreach(array('supplier_information', 'delivery_information') as $name) {
            if(Tools::isSubmit("save_$name")) {
                $order = new Order((int)Tools::getValue('id_order'));
                $order->{$name} = Tools::getValue($name);
                $order->save();
            }
        }

        // Enregistrement des modifications produits
        if($rows = Tools::getValue('update')) {
            foreach($rows as $id => $row) {

                $detail = new OrderDetail($id);
                if($detail->id) {
                    
                    $detail->id_supplier = $row['id_supplier'];
                    $detail->product_reference = $row['product_reference'];
                    $detail->product_supplier_reference = $row['product_supplier_reference'];
                    $detail->purchase_supplier_price = $row['purchase_supplier_price'];
                    $detail->total_shipping_price_tax_excl = $row['total_shipping_price_tax_excl'];
                    
                    $detail->save();
                }
            }
        }

        // Envoi des documents
        if(Tools::isSubmit('send_documents')) {
            
            $documents = Tools::getValue('documents');
            $ids_supplier = Tools::getValue('ids_supplier');
            
            $object = Tools::getValue('object');
            $message = Tools::getValue('message');

            foreach(OA::findByOrder(Tools::getValue('id_order')) as $OA) {
                if(!$ids_supplier or in_array($OA->id_supplier, $ids_supplier)) {
                    if(!empty($OA->getSupplier()->getEmails())) {

                        $attachments = array();

                        // Bon de livraison
                        if(!$documents or in_array('BL', $documents)) {
                            $pdf = new PDF($OA, PDF::TEMPLATE_DELIVERY_SLIP, $this->context->smarty);
                            $attachments['BL']['content'] = $pdf->render(false);
                            $attachments['BL']['name'] = "bon_de_livraison.pdf";
                            $attachments['BL']['mime'] = 'application/pdf';

                            $OA->date_BL = date('Y-m-d H:i:s');
                        }

                        // Bon de commande
                        if(!$documents or in_array('BC', $documents)) {
                            $pdf = new PDF($OA, PDF::TEMPLATE_PURCHASE_ORDER, $this->context->smarty);
                            $attachments['BL']['content'] = $pdf->render(false);
                            $attachments['BL']['name'] = "bon_de_commande.pdf";
                            $attachments['BL']['mime'] = 'application/pdf';

                            $OA->date_BC = date('Y-m-d H:i:s');
                        }

                        // Envoi des e-mails
                        $emails = $OA->getSupplier()->getEmails();
                        if($email = Configuration::get('BLBC_HIDDEN_MAIL', null, $OA->getOrder()->id_shop)) $emails[] = $email;

                        foreach($emails as $email) {

                            $data['{message}'] = $message;

                            Mail::send(1, 'send_supplier', $object, $data, $email, null, null, Configuration::get('PS_SHOP_NAME', null, $OA->getOrder()->id_shop), $attachments);
                        }

                        // Mise à jour de la commande
                        if($id_state = Configuration::get('BLBC_ORDER_STATE', null, $OA->getOrder()->id_shop)) {

                            $history = new OrderHistory();
                            $history->changeIdOrderState($id_state, Tools::getValue('id_order'));
                        }

                        $OA->save();
                        $this->confirmations[] = "Les documents ont été envoyés";
                    }
                }
            }
        }

        $this->context->smarty->assign('suppliers', Supplier::getSuppliers(1));
        AdminController::initContent();
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

    public function ajaxProcessEditProductOnOrder()
    {
        // Return value
        $res = true;

        $order = new Order((int)Tools::getValue('id_order'));
        $order_detail = new OrderDetail((int)Tools::getValue('product_id_order_detail'));
        if (Tools::isSubmit('product_invoice')) {
            $order_invoice = new OrderInvoice((int)Tools::getValue('product_invoice'));
        }

        // Check fields validity
        $this->doEditProductValidation($order_detail, $order, isset($order_invoice) ? $order_invoice : null);

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
            $order = new Order($order_detail->id_order);
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

        // update new fileds
        $order_detail->day = Tools::getValue('day');
        $order_detail->week = Tools::getValue('week');
        $order_detail->comment = Tools::getValue('comment');
        
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
}