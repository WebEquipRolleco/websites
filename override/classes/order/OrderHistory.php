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

            $topic = $result['osname'];
            $data = array(
                '{lastname}' => $result['lastname'],
                '{firstname}' => $result['firstname'],
                '{id_order}' => (int)$this->id_order,
                '{order_name}' => $order->getUniqReference()
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
                // Attach invoice and / or delivery-slip if they exists and status is set to attach them
                if (($result['pdf_invoice'] || $result['pdf_delivery'])) {
                    $context = Context::getContext();
                    $invoice = $order->getInvoicesCollection();
                    $file_attachement = array();

                    if ($result['pdf_invoice'] && (int)Configuration::get('PS_INVOICE') && $order->invoice_number) {
                        Hook::exec('actionPDFInvoiceRender', array('order_invoice_list' => $invoice));
                        $pdf = new PDF($invoice, PDF::TEMPLATE_INVOICE, $context->smarty);
                        $file_attachement['invoice']['content'] = $pdf->render(false);
                        $file_attachement['invoice']['name'] = Configuration::get('PS_INVOICE_PREFIX', (int)$order->id_lang, null, $order->id_shop).sprintf('%06d', $order->invoice_number).'.pdf';
                        $file_attachement['invoice']['mime'] = 'application/pdf';
                    }
                    if ($result['pdf_delivery'] && $order->delivery_number) {
                        $pdf = new PDF($invoice, PDF::TEMPLATE_DELIVERY_SLIP, $context->smarty);
                        $file_attachement['delivery']['content'] = $pdf->render(false);
                        $file_attachement['delivery']['name'] = Configuration::get('PS_DELIVERY_PREFIX', Context::getContext()->language->id, null, $order->id_shop).sprintf('%06d', $order->delivery_number).'.pdf';
                        $file_attachement['delivery']['mime'] = 'application/pdf';
                    }
                    if($result['term_of_use'] and $order->getShop() and $order->getShop()->hasConditionsFile()) {
                        $file_attachement['term_of_use']['content'] = file_get_contents($order->getShop()->getConditionsFilePath(true));
                        $file_attachement['term_of_use']['name'] = "conditions_de_ventes.pdf";
                        $file_attachement['term_of_use']['mime'] = 'application/pdf';
                    }
                } else {
                    $file_attachement = null;
                }

                if (!Mail::Send(
                    (int)$order->id_lang,
                    $result['template'],
                    $topic,
                    $data,
                    $result['email'],
                    $result['firstname'].' '.$result['lastname'],
                    null,
                    null,
                    $file_attachement,
                    null,
                    _PS_MAIL_DIR_,
                    false,
                    (int)$order->id_shop
                )) {
                    return false;
                }
            }

            ShopUrl::resetMainDomainCache();
        }

        return true;
    }
}