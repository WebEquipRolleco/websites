<?php

class AdminSearchController extends AdminSearchControllerCore { 

	/**
	* OVERRIDE : ajout recherche des SAV
	**/
	public function postProcess() {

        $this->context = Context::getContext();
        $this->query = trim(Tools::getValue('bo_query'));
        $searchType = (int)Tools::getValue('bo_search_type');

        /* 1.6 code compatibility, as we use HelperList, we need to handle click to go to product */
        $action = Tools::getValue('action');
        if ($action == 'redirectToProduct') {
            $id_product = (int)Tools::getValue('id_product');
            $link = $this->context->link->getAdminLink('AdminProducts', false, array('id_product' => $id_product));
            Tools::redirectAdmin($link);
        }

        /* Handle empty search field */
        if (!empty($this->query)) {
            if (!$searchType && strlen($this->query) > 1) {
                $this->searchFeatures();
            }

			/** SAV **/
			if(!$searchType) {

				$after_sales = AfterSale::find(array('search'=>$this->query, 'orderBy'=>'date_add', 'orderWay'=>'DESC'));
				$this->context->smarty->assign('after_sales', $after_sales);
				$this->_list['after_sales'] = $after_sales;
			}   
			         
            /* Product research */
            if (!$searchType || $searchType == 1) {
                /* Handle product ID */
                if ($searchType == 1 && (int)$this->query && Validate::isUnsignedInt((int)$this->query)) {
                    if (($product = new Product($this->query)) && Validate::isLoadedObject($product)) {
                        Tools::redirectAdmin('index.php?tab=AdminProducts&id_product='.(int)($product->id).'&token='.Tools::getAdminTokenLite('AdminProducts'));
                    }
                }

                /* Normal catalog search */
                $this->searchCatalog();
            }

            /* Customer */
            if (!$searchType || $searchType == 2 || $searchType == 6) {
                if (!$searchType || $searchType == 2) {
                    /* Handle customer ID */
                    if ($searchType && (int)$this->query && Validate::isUnsignedInt((int)$this->query)) {
                        if (($customer = new Customer($this->query)) && Validate::isLoadedObject($customer)) {
                            Tools::redirectAdmin('index.php?tab=AdminCustomers&id_customer='.(int)$customer->id.'&viewcustomer'.'&token='.Tools::getAdminToken('AdminCustomers'.(int)Tab::getIdFromClassName('AdminCustomers').(int)$this->context->employee->id));
                        }
                    }

                    /* Normal customer search */
                    $this->searchCustomer();
                }

                if ($searchType == 6) {
                    $this->searchIP();
                }
            }

            /* Order */
            if (!$searchType || $searchType == 3) {
                if (Validate::isUnsignedInt(trim($this->query)) && (int)$this->query && ($order = new Order((int)$this->query)) && Validate::isLoadedObject($order)) {
                    if ($searchType == 3) {
                        Tools::redirectAdmin('index.php?tab=AdminOrders&id_order='.(int)$order->id.'&vieworder'.'&token='.Tools::getAdminTokenLite('AdminOrders'));
                    } else {
                        $row = get_object_vars($order);
                        $row['id_order'] = $row['id'];
                        $customer = $order->getCustomer();
                        $row['customer'] = $customer->firstname.' '.$customer->lastname;
                        $order_state = $order->getCurrentOrderState();
                        $row['osname'] = $order_state->name[$this->context->language->id];
                        $this->_list['orders'] = array($row);
                    }
                } else {
                    $orders_1 = Order::getByReference($this->query);
                    $orders_2 = Order::getByInvoiceReference($this->query);

                    $orders = array();
                    foreach($orders_1 as $order) $orders[$order->id] = $order;
                    foreach($orders_2 as $order) $orders[$order->id] = $order;

                    $nb_orders = count($orders);
                    if ($nb_orders == 1 && $searchType == 3) {
                        Tools::redirectAdmin('index.php?tab=AdminOrders&id_order='.(int)$orders[0]->id.'&vieworder'.'&token='.Tools::getAdminTokenLite('AdminOrders'));
                    } elseif ($nb_orders) {
                        $this->_list['orders'] = array();
                        foreach ($orders as $order) {
                            /** @var Order $order */
                            $row = get_object_vars($order);
                            $row['id_order'] = $row['id'];
                            $customer = $order->getCustomer();
                            $row['customer'] = $customer->firstname.' '.$customer->lastname;
                            $order_state = $order->getCurrentOrderState();
                            $row['osname'] = $order_state ? $order_state->name[$this->context->language->id] : '-';
                            $this->_list['orders'][] = $row;
                        }
                    } elseif ($searchType == 3) {
                        $this->errors[] = $this->trans('No order was found with this ID:', array(), 'Admin.Orderscustomers.Notification').' '.Tools::htmlentitiesUTF8($this->query);
                    }
                }
            }

            /* Invoices */
            if ($searchType == 4) {
                if (Validate::isOrderInvoiceNumber($this->query) && ($invoice = OrderInvoice::getInvoiceByNumber($this->query))) {
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminPdf').'&submitAction=generateInvoicePDF&id_order='.(int)($invoice->id_order));
                }
                $this->errors[] = $this->trans('No invoice was found with this ID:', array(), 'Admin.Orderscustomers.Notification').' '.Tools::htmlentitiesUTF8($this->query);
            }

            /* Cart */
            if ($searchType == 5) {
                if ((int)$this->query && Validate::isUnsignedInt((int)$this->query) && ($cart = new Cart($this->query)) && Validate::isLoadedObject($cart)) {
                    Tools::redirectAdmin('index.php?tab=AdminCarts&id_cart='.(int)($cart->id).'&viewcart'.'&token='.Tools::getAdminToken('AdminCarts'.(int)(Tab::getIdFromClassName('AdminCarts')).(int)$this->context->employee->id));
                }
                $this->errors[] = $this->trans('No cart was found with this ID:', array(), 'Admin.Orderscustomers.Notification').' '.Tools::htmlentitiesUTF8($this->query);
            }
            /* IP */
            // 6 - but it is included in the customer block

            /* Module search */
            if (!$searchType || $searchType == 7) {
                /* Handle module name */
                if ($searchType == 7 && Validate::isModuleName($this->query) && ($module = Module::getInstanceByName($this->query)) && Validate::isLoadedObject($module)) {
                    Tools::redirectAdmin('index.php?tab=AdminModules&tab_module='.$module->tab.'&module_name='.$module->name.'&anchor='.ucfirst($module->name).'&token='.Tools::getAdminTokenLite('AdminModules'));
                }

                /* Normal catalog search */
                $this->searchModule();
            }
        }
        $this->display = 'view';
    }

}