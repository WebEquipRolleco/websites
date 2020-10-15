<?php 

class webequip_supplier extends Module {

	const RECALL_STATE = "PS_RECALL_STATE";
	const RECALL_NB_MIN_DAYS = "PS_RECALL_NB_MIN_DAYS";
	const RECALL_NB_MAX_DAYS = "PS_RECALL_NB_MAX_DAYS";
	const SECURE_KEY = "PS_RECALL_SECURE_KEY";
	const TABLE = "ps_employee_supplier";

	public function __construct() {

		$this->name = 'webequip_supplier';
		$this->tab = 'back_office_features';
		$this->version = '1.0';
		$this->author = 'Web-equip';
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('Rappel fournisseur');
		$this->description = $this->l('Envoi un mail de rappel à Web-equip');
	}

	/**
	* Installation
	**/
	public function install() {

		$this->createTable();
		Configuration::updateValue(self::SECURE_KEY, uniqid());

		return parent::install();
	}

	/**
	* Création de la table 
	**/
	public function createTable() {
		Db::getInstance()->execute("CREATE TABLE ".self::TABLE." (`id` int(11) NOT NULL, `id_employee` int(11) NOT NULL, `id_supplier` int(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1; ");
		Db::getInstance()->execute("ALTER TABLE ".self::TABLE." ADD PRIMARY KEY (`id`);");
		Db::getInstance()->execute("ALTER TABLE ".self::TABLE." MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
	}

	/**
	* Configuration du module
	**/
	public function getContent() {

		$this->saveData();

		$details = array();
		$id_rollplus = Configuration::get('EXPORT_ID_ROLLPLUS');

		$rows = $this->getOrdersIds();
		foreach($rows as $row) {

			$order = new Order($row['id_order']);
			foreach($order->getProducts() as $detail) {

				// Exclure les produits sans sous-traitant
				if(!$detail['id_supplier'])
					continue; 

				// Vérifier si les infos ont été renseignées pour ce produit
                if($detail['day'] != "0000-00-00" || $detail['week'] != "0" || $detail['comment'] != '')
                    continue;

				$detail['employees'] = $this->findEmployees($detail['id_supplier']);
				$detail['order'] = $order;
				$details[] = $detail;
			}
		}

		$this->context->smarty->assign('order_details', $details);
		$this->context->smarty->assign('cron_url', $this->getCronUrl());
		$this->context->smarty->assign('link', new Link());
		
		$this->context->smarty->assign('secure_key', Configuration::get(self::SECURE_KEY));
		$this->context->smarty->assign('selected_state', Configuration::get(self::RECALL_STATE));
		$this->context->smarty->assign('nb_min_days', Configuration::get(self::RECALL_NB_MIN_DAYS));
		$this->context->smarty->assign('nb_max_days', Configuration::get(self::RECALL_NB_MAX_DAYS));

		$this->context->smarty->assign('states', OrderState::getOrderStates(1));
		$this->context->smarty->assign('employees', Employee::getEmployees());
		$this->context->smarty->assign('suppliers', Supplier::getSuppliers(false, 1));
		$this->context->smarty->assign('associations', $this->findAll());

		return $this->display(__FILE__, 'content.tpl');
	}

	/**
	* Enregistrement de la configuration
	**/
	private function saveData() {

		if(Tools::isSubmit('save_configuration')) {
			Configuration::updateValue(self::SECURE_KEY, Tools::getValue(self::SECURE_KEY));
			Configuration::updateValue(self::RECALL_STATE, Tools::getValue(self::RECALL_STATE));
			Configuration::updateValue(self::RECALL_NB_MIN_DAYS, Tools::getValue(self::RECALL_NB_MIN_DAYS));
			Configuration::updateValue(self::RECALL_NB_MAX_DAYS, Tools::getValue(self::RECALL_NB_MAX_DAYS));
		}

		if(Tools::isSubmit('add_association')) {
			$id_employee = Tools::getValue('new_employee');
			$id_suppliers = Tools::getValue('new_suppliers');
			if($id_employee && $id_suppliers)
				foreach($id_suppliers as $id_supplier)
					$this->add($id_employee, $id_supplier);
		}

		if(Tools::isSubmit('remove_association')) {
			$this->remove(Tools::getValue('remove_association'));
		}
	}

	/**
	* DO THE ACTUAL THING
	**/
	public function cronTask() {

		$data = array();

		// Ne pas envoyer de mail le week end
		if(date('N') < 6) {

			$rows = $this->getOrdersIds();
			foreach($rows as $row) {
			
				$order = new Order($row['id_order']);
				foreach($order->getProducts() as $detail) {

					// Exclure le Roll+
					//if($detail['id_product'] == $id_rollplus)
					//	continue;

					// Exclure les produits sans sous-traitant
					if(!$detail['id_supplier'])
						continue; 

					// Vérifier si les infos ont été renseignées pour ce produit
					if($detail['day'] != "0000-00-00" || $detail['week'] != "0" || $detail['comment'] != '')
						continue;

					// Ajouter les infos de la commande dans le produit
					$detail['order'] = $order;

					// shortcut
					$id_supplier = $detail['id_supplier'];
					if($id_supplier) {

						// Charger l'employé pour préparation envoi mail
						$employees = $this->findEmployees($id_supplier);
						if($employees) {
							foreach($employees as $employee) {

								$data[$employee->id]['employee'] = $employee;
								if(!isset($data[$employee->id]['products']))
									$data[$employee->id]['products'] = array();
							}
						}

						// Ajouter les produits pour chaque employé concerné
						foreach($employees as $employee) {
							$data[$employee->id]['products'][] = $detail;
						}
					}
				}
			}

			// Envoyer les mails
			foreach($data as $row) {

				$tpl = $this->context->smarty->createTemplate(__DIR__.'/views/templates/hook/products.tpl');
				$tpl->assign('products', $row['products']);

				$tpl_vars['{products}'] = $tpl->fetch();

				$test = file_get_contents(__DIR__.'/mails/fr/recall.html');
				$test = str_replace("{products}", $tpl_vars['{products}'], $test);
				echo $test;

				if(Mail::Send(1, 'recall', "Date d'expédition non saisie dans le back-office", $tpl_vars, $row['employee']->email, $row['employee']->firstname." ".$row['employee']->lastname, Configuration::get('PS_SHOP_EMAIL'), "Web-equip", null, null, __DIR__."/mails/"))
					echo "<div>Mail envoyé vers ".$row['employee']->email."</div><hr />";
			}
		}
	}

	/**
	* Retourne l'url de la tâche CRON
	* @return string
	**/
	private function getCronUrl() {

		$ssl = Configuration::get('PS_SSL_ENABLED');
		if($ssl) $select = "domain_ssl";
		else $select = 'domain';

		$url = Db::getInstance()->getValue("SELECT $select FROM ps_shop_url WHERE id_shop = 1");
		$url .= "/modules/".$this->name."/cron.php?secure_key=".Configuration::get(self::SECURE_KEY)."&rnd=".uniqid();

        $url = "http://".$url;
		if ($ssl){
		    $url = "https://".$url;
        }

		return $url;
	}

	/**
	* Retourne les associations de fournisseurs
	* @return array
	**/
	private function findAll() {

		$data = array();
		$rows = Db::getInstance()->executeS("SELECT * FROM ".self::TABLE);
		foreach($rows as $row) {

			$id_employee = $row['id_employee'];

			if(!isset($data[$id_employee]))
				$data[$id_employee]['employee'] = new Employee($id_employee);

			$data[$id_employee]['suppliers'][$row['id']] = new Supplier($row['id_supplier'], 1);
		}

		return $data;
	}

	/**
	* Retourne la liste des commandes
	* @return array
	**/
	private function getOrdersIds() {

		$state_id = Configuration::get(self::RECALL_STATE);
		$nb_min_days = Configuration::get(self::RECALL_NB_MIN_DAYS);
		$nb_max_days = Configuration::get(self::RECALL_NB_MAX_DAYS);

		if(!$state_id or !$nb_min_days or !$nb_max_days)
			return array();

		$date_min = new DateTime('today');
		if($nb_min_days) $date_min->modify("-$nb_min_days days");

		$date_max = new DateTime('today');
		if($nb_max_days) $date_max->modify("-$nb_max_days days");

		$sql = "SELECT DISTINCT(o.id_order) 
		FROM ps_orders o
		WHERE o.current_state = $state_id 
		AND o.id_order IN (
			SELECT DISTINCT(d.id_order)
			FROM ps_order_detail d
			WHERE (d.week IS NOT NULL OR d.week <> 0)
		    OR (d.day IS NOT NULL AND d.day <> '0000-00-00 00:00:00')
		)
		AND o.id_order IN (
			SELECT h.id_order 
			FROM ps_order_history h 
			WHERE h.date_add < '".$date_min->format('Y-m-d 23:59:59')."' 
			AND h.date_add > '".$date_max->format('Y-m-d 00:00:00')."' 
			AND h.id_order_state = $state_id
		)";

		return Db::getInstance()->executeS($sql);
	}

	/**
	* Retourne la liste des employés
	* @param int $id_supplier
	* @return array
	**/
	private function findEmployees($id_supplier) {

		// Enregistrer la liste des employés associés à chaque fournisseur
		if(isset($this->supplier_employees[$id_supplier]))
			return $this->supplier_employees[$id_supplier];

		$data = array();
		$rows = Db::getInstance()->executeS("SELECT * FROM ".self::TABLE." WHERE id_supplier = $id_supplier");
		foreach($rows as $row) {
			$data[] = new Employee($row['id_employee']);
		}

		$this->supplier_employees[$id_supplier] = $data;
		return $data;
	}

	/**
	* Ajoute une association
	* @param int $id_employee
	* @param int $id_supplier
	* @return bool
	**/
	private function add($id_employee, $id_supplier) {
		return Db::getInstance()->execute("INSERT INTO ".self::TABLE." VALUES (NULL, $id_employee, $id_supplier)");
	}

	/**
	* @param int $id
	* @return bool
	**/
	private function remove($id) {
		return Db::getInstance()->execute("DELETE FROM ".self::TABLE." WHERE id = $id");
	}

}