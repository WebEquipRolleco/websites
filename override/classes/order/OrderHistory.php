<?php

class OrderHistory extends OrderHistoryCore {

	/**
    * Override : application automatique des règles de redirection
    *
    * @param int $new_order_state
    * @param int/object $id_order
    * @param bool $use_existing_payment
    **/
    public function changeIdOrderState($new_order_state, $id_order, $use_existing_payment = false) {
    	parent::changeIdOrderState($new_order_state, $id_order, $use_existing_payment);

        // Vérification du Rollcash
        $state = new OrderState($new_order_state);
        if($state->rollcash) {

            if(is_int($id_order))
                $order = new Order($id_order);
            else
                $order = $id_order;

            foreach($order->getDetails() as $details) {

                $rate = Product::findRollcash($details->product_id, $details->product_attribute_id);
                if($rate) {
                    $rollcash = ($details->total_price_tax_excl * $rate) / 100;
                    $order->getCustomer()->rollcash += round($rollcash, 2);
                }
            }

            $order->getCustomer()->save();
        }
        
    	// Vérification des règles
    	foreach(OrderStateRule::getActiveRules() as $rule) {

    		// Si le nouvel état de la commande est une étape de la règle
    		if(in_array($new_order_state, $rule->ids)) {

    			// On vérifie l'ensemble des étapes pour la commande en cours
    			$nb = Db::getInstance()->getValue("SELECT COUNT(*) FROM ps_order_history WHERE id_order = ".$id_order->id." AND id_order_state IN (".implode(',', $rule->ids).")");
    			if($nb === count($rule->ids)) {

    				// Toutes les étapes sont franchies, on redirige la commande
    				$this->changeIdOrderState($rule->target_id, $id_order, $use_existing_payment);
    			}
    		}
    	}

    }

    /**
    * Override : ajout de la gestion du PDF de conditions de ventes
    **/
    public function sendEmail($order, $template_vars = false) {

        $result = Db::getInstance()->getRow('
            SELECT osl.`template`, c.`lastname`, c.`firstname`, osl.`name` AS osname, c.`email`, os.`module_name`, os.`id_order_state`, os.`pdf_invoice`, os.`pdf_delivery`, os.`term_of_use`
            FROM `'._DB_PREFIX_.'order_history` oh
                LEFT JOIN `'._DB_PREFIX_.'orders` o ON oh.`id_order` = o.`id_order`
                LEFT JOIN `'._DB_PREFIX_.'customer` c ON o.`id_customer` = c.`id_customer`
                LEFT JOIN `'._DB_PREFIX_.'order_state` os ON oh.`id_order_state` = os.`id_order_state`
                LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = o.`id_lang`)
            WHERE oh.`id_order_history` = '.(int)$this->id.' AND os.`send_email` = 1');

        if (isset($result['template']) && Validate::isEmail($result['email'])) {
            ShopUrl::cacheMainDomainForShop($order->id_shop);

            $date = DateTime::createFromFormat('Y-m-d H:i:s', $order->date_add);

            $topic = $result['osname'];
            $data = array(
                '{lastname}' => $result['lastname'],
                '{firstname}' => $result['firstname'],
                '{id_order}' => (int)$this->id_order,
                '{order_name}' => $order->getUniqReference(),
                '{order_reference}' => $order->reference,
                '{deadline}' => $order->getPaymentDeadline() ? $order->getPaymentDeadline()->format('d/m/Y') : '',
                '{shop_phone}' => Configuration::get('PS_SHOP_PHONE'),
                '{order_date}' => $date ? $date->format('d/m/Y') : ''
            );

            if ($result['module_name']) {
                $module = Module::getInstanceByName($result['module_name']);
                if (Validate::isLoadedObject($module) && isset($module->extra_mail_vars) && is_array($module->extra_mail_vars)) {
                    $data = array_merge($data, $module->extra_mail_vars);
                }
            }

            if (is_array($template_vars)) {
                $data = array_merge($data, $template_vars);
            }

            $data['{total_paid}'] = Tools::displayPrice((float)$order->total_paid, new Currency((int)$order->id_currency), false);

            if (Validate::isLoadedObject($order)) {

                /* Condition en cas de fichier a ajouter en tant que piece jointe */
                if (($result['pdf_invoice'] || $result['pdf_delivery'])) {
                    $context = Context::getContext();
                    $invoice = $order->getInvoicesCollection();

                    $file_attachement = array();
                    /* Condition pour la generation de la facture */

                    if ($result['pdf_invoice'] && (int)Configuration::get('PS_INVOICE')) {
                        //Hook::exec('actionPDFInvoiceRender', array('order_invoice_list' => $invoice));
                        $pdf = new PDF($invoice, PDF::TEMPLATE_INVOICE, $context->smarty);

                        foreach($order->getInvoicesCollection() as $invoice) {
                            $pdf = new PDF($invoice, PDF::TEMPLATE_INVOICE, $this->context->smarty);
                        }

                        $file_attachement['invoice']['content'] = $pdf->render(false);
                        $file_attachement['invoice']['name'] = Configuration::get('PS_INVOICE_PREFIX', (int)$order->id_lang, null, $order->id_shop) . sprintf('%06d', $order->invoice_number) . '.pdf';
                        $file_attachement['invoice']['mime'] = 'application/pdf';
                    }
                    /* Condition pour la generation du bordereau de livraison */
                    if ($result['pdf_delivery'] && $order->delivery_number) {
                        $pdf = new PDF($invoice, PDF::TEMPLATE_DELIVERY_SLIP, $context->smarty);
                        $file_attachement['delivery']['content'] = $pdf->render(false);
                        $file_attachement['delivery']['name'] = Configuration::get('PS_DELIVERY_PREFIX', Context::getContext()->language->id, null, $order->id_shop).sprintf('%06d', $order->delivery_number).'.pdf';
                        $file_attachement['delivery']['mime'] = 'application/pdf';
                    }

                    /* Condition pour la generation des conditions de ventes */
                    if($result['term_of_use'] and $order->getShop() and $order->getShop()->hasConditionsFile()) {
                        $file_attachement['term_of_use']['content'] = file_get_contents($order->getShop()->getConditionsFilePath(true));
                        $file_attachement['term_of_use']['name'] = "conditions_de_ventes.pdf";
                        $file_attachement['term_of_use']['mime'] = 'application/pdf';
                    }

                } else {
                    $file_attachement = null;
                }
                /*  */
                $emails[] = $result['email'];
                $emails[] = Configuration::get('PS_SHOP_EMAIL');



                /* Boucle pour l'envoi des emails pour chaque mail renseigne */
                foreach($emails as $email)
                    if(!Mail::Send((int)$order->id_lang, $result['template'], $topic, $data, $email, $result['firstname'].' '.$result['lastname'],
                        null, null, $file_attachement, null, _PS_MAIL_DIR_, false, (int)$order->id_shop)) {
                           return false;
                    }
            }

            ShopUrl::resetMainDomainCache();
        }

        return true;
    }


    public function processGenerateInvoicePdf()
    {
        if (Tools::isSubmit('id_order_invoice')) {
            return $this->generateInvoicePDFByIdOrderInvoice(Tools::getValue('id_order_invoice'));
        } elseif (Tools::isSubmit('id_order')) {
            return $this->generateInvoicePDFByIdOrder(Tools::getValue('id_order'));
        }
        else {
            die($this->trans('The order ID -- or the invoice order ID -- is missing.', array(), 'Admin.Orderscustomers.Notification'));
        }
    }


    public function generateInvoicePDFByIdOrderInvoice($id_order_invoice)
    {
        $order_invoice = new OrderInvoice((int)$id_order_invoice);
        Hook::exec('actionPDFInvoiceRender', array('order_invoice_list' => array($order_invoice)));
        return  $this->generatePDF($order_invoice, PDF::TEMPLATE_INVOICE);
    }


    public function generateInvoicePDFByIdOrder($id_order)
    {
        $order = new Order((int)$id_order);
        $order_invoice_list = $order->getInvoicesCollection();
        Hook::exec('actionPDFInvoiceRender', array('order_invoice_list' => $order_invoice_list));
        return $this->generatePDF($order_invoice_list, PDF::TEMPLATE_INVOICE);
    }



    public function generatePDF($object, $template)
    {
        $pdf = new PDF($object, $template, Context::getContext()->smarty);
        return $pdf->render(false);
    }
}