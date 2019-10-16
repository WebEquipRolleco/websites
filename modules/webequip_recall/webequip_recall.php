<?php

class Webequip_recall extends Module {

	const CONFIG_RECALL_OBJECT_1 = "RECALL_OBJECT_1";
	const CONFIG_RECALL_OBJECT_2 = "RECALL_OBJECT_2";
	const CONFIG_RECALL_OBJECT_3 = "RECALL_OBJECT_3";
	const CONFIG_RECALL_OBJECT_4 = "RECALL_OBJECT_4";
	const CONFIG_RECALL_HIDDEN_MAIL = "RECALL_HIDDEN_MAIL";
	const CONFIG_RECALL_MAILS_1 = "RECALL_MAILS_1";
	const CONFIG_RECALL_MAILS_2 = "RECALL_MAILS_2";
	const CONFIG_CHECK_INVOICE_DAYS = "CHECK_INVOICE_DAYS";
	const CONFIG_CHECK_INVOICE_STATE = "CHECK_INVOICE_STATE";
	const CONFIG_CHECK_INVOICE_EMPLOYEE = "CHECK_INVOICE_EMPLOYEE";
	const CONFIG_RECALL_SAV_NB_DAYS = "RECALL_SAV_NB_DAYS";

	const ACTION_RECALL = "recall";
	const ACTION_PAYMENT = "payment";
	const ACTION_INVOICE = "invoice";
	const ACTION_SAV = "sav";
	const ACTION_QUOTATION = "quotation";

	const LIST_DELIMITER = ',';
	const ACTIONS_DELIMITER = '|';

	private $from;
	private $from_name;
	private $cc;
	private $mail_dir;

	private $recall_team_1 = array();
	private $recall_team_2 = array();

	/**
	* Infos module
	**/
	public function __construct() {

		$this->name = 'webequip_recall';
		$this->tab = 'others';
		$this->version = '1.0';
		$this->author = 'Web-equip';
		$this->bootstrap = true;
		
		parent::__construct();
		
		$this->displayName = $this->l('Webequip Rappels commandes');
		$this->description = $this->l('Rappels automatiques des commandes');

		$this->from = Configuration::get('PS_SHOP_EMAIL');
		$this->from_name = Configuration::get('PS_SHOP_NAME');
		$this->cc = Configuration::get(self::CONFIG_RECALL_HIDDEN_MAIL);
		$this->mail_dir = __DIR__."/mails/";
	}

	/**
	* Configuration du module
	**/
	public function getContent() {

		// Configuration des variables simples
		$configs = array(self::CONFIG_RECALL_OBJECT_1, self::CONFIG_RECALL_OBJECT_2, self::CONFIG_RECALL_OBJECT_3, self::CONFIG_RECALL_OBJECT_4, self::CONFIG_RECALL_HIDDEN_MAIL, self::CONFIG_CHECK_INVOICE_DAYS, self::CONFIG_CHECK_INVOICE_STATE, self::CONFIG_CHECK_INVOICE_EMPLOYEE, self::CONFIG_RECALL_SAV_NB_DAYS);
		foreach($configs as $config) {

			if(Tools::isSubmit($config))
				Configuration::updateValue($config, Tools::getValue($config));

			$this->context->smarty->assign($config, Configuration::get($config));
		}

		// Configuration des variables listes
		$configs = array(self::CONFIG_RECALL_MAILS_1, self::CONFIG_RECALL_MAILS_2);
		foreach($configs as $config) {

			if(Tools::isSubmit($config))
				Configuration::updateValue($config, implode(self::LIST_DELIMITER, Tools::getValue($config)));

			$this->context->smarty->assign($config, explode(self::LIST_DELIMITER, Configuration::get($config)));
		}

		$this->context->smarty->assign('cron_url', $this->getCronUrl());
		$this->context->smarty->assign('cron_informations', $this->getCronActions());
		$this->context->smarty->assign('states', OrderState::getOrderStates(1));
		$this->context->smarty->assign('employees', Employee::getEmployees());
		$this->context->smarty->assign('nb_customers_to_update', count($this->getCustomersToUpdate()));
		$this->context->smarty->assign('nb_orders_recall_1', count($this->getOrders(35)));
		$this->context->smarty->assign('date_invoice_recall_1', $this->getInvoiceDate(35));
		$this->context->smarty->assign('nb_orders_recall_2', count($this->getOrders(45)));
		$this->context->smarty->assign('date_invoice_recall_2', $this->getInvoiceDate(45));
		$this->context->smarty->assign('nb_orders_recall_3', count($this->getOrders(52)));
		$this->context->smarty->assign('date_invoice_recall_3', $this->getInvoiceDate(52));
		$this->context->smarty->assign('nb_orders_recall_4', count($this->getOrders(62)));
		$this->context->smarty->assign('date_invoice_recall_4', $this->getInvoiceDate(62));
		$this->context->smarty->assign('nb_orders_recall_5', count($this->getOrders(70)));
		$this->context->smarty->assign('date_invoice_recall_5', $this->getInvoiceDate(70));
		$this->context->smarty->assign('nb_orders_recall_6', count($this->getOrders(75)));
		$this->context->smarty->assign('date_invoice_recall_6', $this->getInvoiceDate(75));
		$this->context->smarty->assign('nb_orders_recall_7', count($this->getOrders(90)));
		$this->context->smarty->assign('date_invoice_recall_7', $this->getInvoiceDate(90));
		$this->context->smarty->assign('nb_no_facturation', count($this->getNoFacturationIds()));
		$this->context->smarty->assign('nb_recall_quotation', count(Quotation::needToRecall()));

		return $this->display(__FILE__, 'views/templates/admin/content.tpl');
	}

	/**
	* Calcule la date supposée de facturation à partir d'un délai de retard
	**/
	private function getInvoiceDate($nb_days) {
		
		$date = new DateTime('today');
		$date->modify("-$nb_days days");

		return $date;
	}

	/**
	* Retourne l'url de la tache CRON
	**/
	private function getCronUrl() {

		$ssl = Configuration::get('PS_SSL_ENABLED');
		if($ssl) $select = "domain_ssl";
		else $select = 'domain';

		$url = Db::getInstance()->getValue("SELECT $select FROM ps_shop_url WHERE id_shop = 1");
		$url .= "/modules/".$this->name."/cron.php?rnd=".uniqid();

		return $url;
	}

	/**
	* Tache CRON
	**/
	public function cronTask() {	

		$actions = explode(self::ACTIONS_DELIMITER, Tools::getValue('actions'));

		if(empty($actions))
			die('Aucune action effectuée');

		if(in_array(self::ACTION_RECALL, $actions))
			$this->sendRecalls();

		if(in_array(self::ACTION_PAYMENT, $actions))
			$this->checkForPayments();

		if(in_array(self::ACTION_INVOICE, $actions))
			$this->checkInvoices();

		if(in_array(self::ACTION_SAV, $actions))
			$this->checkSAV();

		if(in_array(self::ACTION_QUOTATION, $actions))
			$this->checkQuotations();
	}

	public function getCronActions() {

		$data[self::ACTION_RECALL] = "Envoyer les mails de rappels de demande de paiement aux clients";
		$data[self::ACTION_PAYMENT] = "Vérifie les paiements des clients à problèmes (et les replace en clients OK)";
		$data[self::ACTION_INVOICE] = "Envoyer un mail en cas d'informations facture non renseignés";
		$data[self::ACTION_QUOTATION] = "Envoyer les mails de rappel de relance devis";

		return $data;
	}

	/**
	* Logo boutique
	**/
	private function getLogo($order) {
		
		$logo = '';
		$physical_uri = Context::getContext()->shop->physical_uri.'img/';

		if (Configuration::get('PS_LOGO_INVOICE', null, null, (int)$order->id_shop) != false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO_INVOICE', null, null, (int)$order->id_shop)))
			$logo = _PS_IMG_DIR_.Configuration::get('PS_LOGO_INVOICE', null, null, (int)$order->id_shop);
		elseif (Configuration::get('PS_LOGO', null, null, (int)$order->id_shop) != false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO', null, null, (int)$order->id_shop)))
			$logo = _PS_IMG_DIR_.Configuration::get('PS_LOGO', null, null, (int)$order->id_shop);
		return $logo;
	}

	/**
	* Envoyer les mails de rappel
	**/
	public function sendRecalls() {

		$nb_orders = 0;

		// 10 jours avant échéance (45 - 10 = 35 jours)
		foreach($this->getOrders(35) as $order) {

			$customer = $order->getCustomer();
			$object = $order->renderString(Configuration::get(self::CONFIG_RECALL_OBJECT_1));

			$data['{order_reference}'] = $order->reference;
			$data['{firstname}'] = $order->getCustomer()->firstname;
			$data['{lastname}'] = $order->getCustomer()->lastname;
			$data['{order_date}'] = $order->getDate()->format('d/m/Y');
			$data['{shop_name}'] = $order->getShop()->name;
			$data['{shop_phone}'] = Configuration::getForOrder('PS_SHOP_PHONE', $order);
			$data['{shop_mail}'] = Configuration::getForOrder('PS_SHOP_EMAIL', $order);
			$data['{shop_logo}'] = $this->getLogo($order);
			$data['{deadline}'] = $order->getDeadline()->format('d/m/Y');

			Mail::send(1, "recall_customer_1", $object, $data, $customer->email, $customer->firstname." ".$customer->lastname, $this->from, $order->getShop()->name, null, null, $this->mail_dir);

			$content = file_get_contents($this->mail_dir."recall_customer_1.html");
			MailHistory::record($object, $content, $order->id_customer, $order->id);
			$nb_orders++;
		}

		// Jour de l'échéance (45 jours)
		foreach($this->getOrders(45) as $order) {

			$customer = new Customer($order->id_customer);
			$customer->status_information = Customer::STATUS_LATE;
			$customer->save();
		}

		// 7 jours après l'échéance (45 + 7 = 52 jours)
		foreach($this->getOrders(52) as $order) {

			$customer = $order->getCustomer();
			$object = $order->renderString(Configuration::get(self::CONFIG_RECALL_OBJECT_2));

			$data['{order_reference}'] = $order->reference;
			$data['{firstname}'] = $order->getCustomer()->firstname;
			$data['{lastname}'] = $order->getCustomer()->lastname;
			$data['{order_date}'] = $order->getDate()->format('d/m/Y');
			$data['{invoice_date}'] = $order->getCustomInvoiceDate()->format('d/m/Y');
			$data['{shop_name}'] = $order->getShop()->name;
			$data['{shop_phone}'] = Configuration::getForOrder('PS_SHOP_PHONE', $order);
			$data['{shop_mail}'] = Configuration::getForOrder('PS_SHOP_EMAIL', $order);
			$data['{shop_logo}'] = $this->getLogo($order);

			$pdf = new PDF($order->getInvoicesCollection(), PDF::TEMPLATE_INVOICE, $this->context->smarty);
			$file_attachement['content'] = $pdf->render(false);
			$file_attachement['name'] = 'facture.pdf';
			$file_attachement['mime'] = 'application/pdf';
			$files_attachements = array($file_attachement);

			$emails = array_merge($customer->getInvoiceEmails(), array($this->cc));
			foreach($emails as $email)
				if($email) Mail::send(1, "recall_customer_2", $object, $data, $email, $customer->firstname." ".$customer->lastname, $this->from, $order->getShop()->name, $files_attachements, null, $this->mail_dir);

			$content = file_get_contents($this->mail_dir."recall_customer_2.html");
			MailHistory::record($object, $content, $order->id_customer, $order->id);
			$nb_orders++;
			
		}

		// 17 jours après l'échéance (45 + 17 = 62 jours)
		foreach($this->getOrders(62) as $order) {

			$customer = $order->getCustomer();
			$object = $order->renderString(Configuration::get(self::CONFIG_RECALL_OBJECT_3));

			$date = new DateTime('today');
			$date->modify('-10 days');

			$data['{order_reference}'] = $order->reference;
			$data['{firstname}'] = $order->getCustomer()->firstname;
			$data['{lastname}'] = $order->getCustomer()->lastname;
			$data['{order_date}'] = $order->getDate()->format('d/m/Y');
			$data['{shop_name}'] = $order->getShop()->name;
			$data['{shop_phone}'] = Configuration::getForOrder('PS_SHOP_PHONE', $order);
			$data['{shop_mail}'] = Configuration::getForOrder('PS_SHOP_EMAIL', $order);
			$data['{relance_1}'] = $date->format('d/m/Y');
			$data['{shop_logo}'] = $this->getLogo($order);

			$pdf = new PDF($order->getInvoicesCollection(), PDF::TEMPLATE_INVOICE, $this->context->smarty);
			$file_attachement['content'] = $pdf->render(false);
			$file_attachement['name'] = 'facture.pdf';
			$file_attachement['mime'] = 'application/pdf';
			$files_attachements = array($file_attachement);

			$emails = array_merge($customer->getInvoiceEmails(), array($this->cc));
			foreach($emails as $email)
				if($email) Mail::send(1, "recall_customer_3", $object, $data, $email, $customer->firstname." ".$customer->lastname, $this->from, $order->getShop()->name, $files_attachements, null, $this->mail_dir);

			$content = file_get_contents($this->mail_dir."recall_customer_3.html");
			MailHistory::record($object, $content, $order->id_customer, $order->id);
			$nb_orders++;
		}

		// 25 jours après l'échéance (45 + 25 = 70 jours)
		foreach($this->getOrders(70) as $order) {

			$customer = $order->getCustomer();
			$object = $order->renderString(Configuration::get(self::CONFIG_RECALL_OBJECT_4));

			$date = new DateTime('today');
			$date->modify('-8 days');

			$data['{firstname}'] = $order->getCustomer()->firstname;
			$data['{lastname}'] = $order->getCustomer()->lastname;
			$data['{order_reference}'] = $order->reference;
			$data['{shop_name}'] = $order->getShop()->name;
			$data['{shop_phone}'] = Configuration::getForOrder('PS_SHOP_PHONE', $order);
			$data['{shop_mail}'] = Configuration::getForOrder('PS_SHOP_EMAIL', $order);
			$data['{relance_1}'] = $date->format('d/m/Y');
			$date->modify('-10 days');
			$data['{relance_2}'] = $date->format('d/m/Y');
			$data['{shop_logo}'] = $this->getLogo($order);
			
			$pdf = new PDF($order->getInvoicesCollection(), PDF::TEMPLATE_INVOICE, $this->context->smarty);
			$file_attachement['content'] = $pdf->render(false);
			$file_attachement['name'] = 'facture.pdf';
			$file_attachement['mime'] = 'application/pdf';
			$files_attachements = array($file_attachement);

			$emails = array_merge($customer->getInvoiceEmails(), array($this->cc));
			foreach($emails as $email)
				if($email) Mail::send(1, $this->l("recall_customer_4"), $object, $data, $email, $customer->firstname." ".$customer->lastname, $this->from, $order->getShop()->name, $files_attachements, null, $this->mail_dir);

			$content = file_get_contents($this->mail_dir."recall_customer_4.html");
			MailHistory::record($object, $content, $order->id_customer, $order->id);
			$nb_orders++;
		}

		// 30 jours après l'échéance (45 + 30 = 75 jours)
		foreach($this->getOrders(75) as $order) {
			$recall_team_1[] = $order;
		}

		// 45 jours après l'échance (45 + 45 = 90 jours)
		foreach($this->getOrders(90) as $order) {

			$recall_team_2[] = $order;

			$customer = new Customer($order->id_customer);
			$customer->status_information = Customer::STATUS_PROBLEM;
			$customer->save();
		}

		// Envoi des mails de rappel à l'équipe (30 jours)
		if(!empty($recall_team_1)) {

			$customer = $order->getCustomer();

			$tpl = $this->context->smarty->createTemplate(__DIR__.'/views/templates/mails/recall_lines.tpl');
			$tpl->assign('orders', $recall_team_1);

			$data['{$lines}'] = $tpl->fetch();

			$ids = explode(self::LIST_DELIMITER, Configuration::get(self::CONFIG_RECALL_MAILS_1));
			foreach($ids as $id) {

				$employee = new Employee(trim($id));
				Mail::send(1, "recall_team_1", $this->l("Recommandés à envoyer"), $data, $employee->email, $employee->firstname." ".$employee->lastname, $this->from, $this->from_name, null, null, $this->mail_dir);
			}

		}

		// Envoi des mails de rappel à l'équipe (45 jours)
		if(!empty($recall_team_2)) {

			$customer = $order->getCustomer();

			$tpl = $this->context->smarty->createTemplate(__DIR__.'/views/templates/mails/recall_lines.tpl');
			$tpl->assign('orders', $recall_team_2);

			$data['{$lines}'] = $tpl->fetch();

			$ids = explode(self::LIST_DELIMITER, Configuration::get(self::CONFIG_RECALL_MAILS_2));
			foreach($ids as $id) {

				$employee = new Employee(trim($id));
				Mail::send(1, "recall_team_2", $this->l("Clients à déclarer en contentieux"), $data, $employee->email, $employee->firstname." ".$employee->lastname, $this->from, $this->from_name, null, null, $this->mail_dir);
			}
		}

		Configuration::updateValue('RECALL_CRON_NB_ORDERS', $nb_orders);
	}

	/**
	* Vérifie les paiments récents des clients à problèmes
	**/
	public function checkForPayments() {

		foreach($this->getCustomersToUpdate() as $id) {
			
			$customer = new Customer($id);
			$customer->status_order = Customer::STATUS_OK;
			$customer->save();
		}
	}

	/**
	* Envoyer un mail en cas d'informations facture non renseignés
	**/
	public function checkInvoices() {

		$nb_days = Configuration::get(self::CONFIG_CHECK_INVOICE_DAYS);
		if(!$nb_days)
			return false;

		$id_state_check = Configuration::get(self::CONFIG_CHECK_INVOICE_STATE);
		if(!$id_state_check)
			return false;

		$id_employee = Configuration::get(self::CONFIG_CHECK_INVOICE_EMPLOYEE);
		if(!$id_employee)
			return false;

		$ids = $this->getNoFacturationIds();
		if(!empty($ids)) {

			$employee = new Employee($id_employee);
			$state = new OrderState($id_state_check, 1);

			$tpl = $this->context->smarty->createTemplate(__DIR__.'/views/templates/mails/reference_lines.tpl');
			$tpl->assign('ids', $ids);

			$data['{$lines}'] = $tpl->fetch();
			$data['{$nb}'] = $nb_days;
			$data['{$state}'] = $state->name;

			Mail::send(1, "recall_invoice", $this->l("Rappel des commandes sans facturation"), $data, $employee->email, $employee->firstname." ".$employee->lastname, $this->from, $this->from_name, null, null, $this->mail_dir);
		}

	}

	/**
	* Envoi les mails de rappel des SAV non traités
	**/
	public function checkSAV() {

		$nb_days = Configuration::get(self::CONFIG_RECALL_SAV_NB_DAYS);
		if(!$nb_days)
			return false;

		$employees = Employee::findAfterSaleAccountants();
		if(empty($employees))
			return false;

		// SAV en retard de traitement
		$lines = AfterSale::findLateTreatment($nb_days);
		if(!empty($lines)) {

			$tpl = $this->context->smarty->createTemplate(__DIR__.'/views/templates/mails/sav_lines.tpl');
			$tpl->assign('rows', $lines);

			$data['{$lines}'] = $tpl->fetch();

			foreach($employees as $employee)
				Mail::send(1, "recall_sav_1", $this->l("SAV non traités"), $data, $employee->email, $employee->firstname." ".$employee->lastname, $this->from, $this->from_name, null, null, $this->mail_dir);
		}

		// SAV "abandonnés"
		$lines = AfterSale::findLateUpdate($nb_days);
		if(!empty($lines)) {

			$tpl = $this->context->smarty->createTemplate(__DIR__.'/views/templates/mails/sav_lines.tpl');
			$tpl->assign('rows', $lines);

			$data['{$lines}'] = $tpl->fetch();

			foreach($employees as $employee)
				Mail::send(1, "recall_sav_2", $this->l("SAV à relancer"), $data, $employee->email, $employee->firstname." ".$employee->lastname, $this->from, $this->from_name, null, null, $this->mail_dir);
		}
	}


	/**
	* Envoie les mails de rappel de relance de devis
	**/
	public function checkQuotations() {

		$data = array();
		foreach(Quotation::needToRecall() as $quotation) {

			if(!isset($employees[$quotation->id_employee])) {
				$data[$quotation->id_employee]['employee'] = $quotation->getEmployee();
				$data[$quotations->id_employee]['quotations'] = array();
			}
			
			$data[$quotations->id_employee]['quotations'][] = $quotation;
		}

		if(!empty($data)) {
			foreach($data as $row) {

				$tpl = $this->context->smarty->createTemplate(__DIR__.'/views/templates/mails/quotation_lines.tpl');
				$tpl->assign('quotations', $row['quotations']);	

				$data['{$lines}'] = $tpl->fetch();

				Mail::send(1, "recall_quotation", $this->l("Devis à relancer"), $data, $row['employee']->email, $row['employee']->firstname." ".$row['employee']->lastname, $this->from, $this->from_name, null, null, $this->mail_dir);
			}
		}
	}

	/**
	* Retourne une liste de clients problématiques ayant effectué leur paiement hier
	**/
	public function getCustomersToUpdate() {

		$data = array();
		$date = new DateTime('yesterday');

		// Retrouver les clients "mauvais payeurs ayant payé une commande depuis hier"
		$sql = "SELECT c.id_customer
				FROM ps_customer c, ps_customer_state cs, ps_orders o, ps_order_history h, ps_order_state s
				WHERE o.id_customer = c.id_customer
				AND c.id_customer_state = cs.id_customer_state
				AND o.id_order = h.id_order
				AND h.id_order_state = s.id_order_state
				AND cs.risk_level > 0
				AND h.date_add >= '".$date->format('Y-m-d 00:00:00')."' 
				AND s.paid = 1";

		foreach(Db::getInstance()->executeS($sql) as $row) {

			// Récupérer les commandes du client
			$nb_orders = (int)Db::getInstance()->getValue("SELECT COUNT(id_order FROM ps_orders WHERE id_customer = ".$row['id_customer']);

			// Récupérer les commandes du client passées par l'état payé
			$nb_paid = (int)Db::getInstance()->getValue("SELECT COUNT(DISTINCT(o.id_order)) FROM ps_orders o, ps_order_history h, ps_order_state s WHERE o.id_order = h.id_order AND h.id_order_state = s.id_order_state = s.paid = 1 AND o.id_customer = ".$row['id_customer']);
			
			// Si NB commandes = NB commandes payées = mettre à jour
			if($nb_orders == $nb_paid)
				$data[] = $row['id_customer'];
		}

		return $data;
	}

	/**
	* Retourne une liste de références commandes n'ayant aucune information de facturation renseignée
	**/
	public function getNoFacturationIds() {

		$nb_days = Configuration::get(self::CONFIG_CHECK_INVOICE_DAYS);
		if(!$nb_days)
			return array();

		$id_state_check = Configuration::get(self::CONFIG_CHECK_INVOICE_STATE);
		if(!$id_state_check)
			return array();

		$date = new DateTime('today');
		$date->modify("-$nb_days days");

		$sql = "SELECT DISTINCT(o.internal_reference) as reference
				FROM ps_orders o, ps_order_history h
				WHERE o.id_order = h.id_order 
				AND h.date_add < '".$date->format('Y-m-d 23:59:59')."'
				AND h.id_order_state = $id_state_check
				AND (o.invoice_number IS NULL OR o.invoice_number = '')
				AND (o.invoice_date IS NULL OR o.invoice_date = '000-00-00')";

		return Db::getInstance()->executeS($sql);
	}

	/**
	* Retourne une liste de commandes en fonction du nombre de jours sans paiement
	**/
	public function getOrders($nb_days, $full = true) {

		$date = new DateTime('today');
		$date->modify("-$nb_days days");

		$sql = "SELECT o.id_order 
				FROM ps_orders o
				WHERE o.no_recall = 0
				AND o.invoice_date = '".$date->format('Y-m-d 00:00:00')."'
				AND NOT EXISTS (
				    SELECT * 
				    FROM ps_order_history h, ps_order_state s
				    WHERE h.id_order_state = s.id_order_state
				    AND s.paid = 1
				    AND h.id_order = o.id_order
				)";

		$data = array();
		$rows = Db::getInstance()->executeS($sql);
		foreach($rows as $row) {

			if($full)
				$data[] = new Order($row['id_order']);
			else 
				$data[] = $row['id_order'];
		}

		return $data;
	}

}