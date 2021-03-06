<?php

class AdminResultsController extends AdminController {

	const CONFIG_RESULTS_PERIOD_LIMIT = "CONFIG_RESULTS_PERIOD_LIMIT";

	const FULL_PERIOD = 1;
	const UP_TO_TODAY = 2;

	const PERIOD_MONTHLY = "MONTHLY";
	const PERIOD_ANNUAL = "ANNUAL";

	private $date_limit;

	public function __construct() {

		$this->bootstrap = true;
		parent::__construct();
	}

	public function initContent() {

		$methods = $this->getPaymentMethods();
		$shop_ids = Shop::getContextListShopID();

		/**** GESTION ARRET DES PERIODES ****/
		$result_period = Tools::getValue(self::CONFIG_RESULTS_PERIOD_LIMIT);
		if($result_period) Configuration::updateValue(self::CONFIG_RESULTS_PERIOD_LIMIT, $result_period);

		$this->date_limit = Configuration::get(self::CONFIG_RESULTS_PERIOD_LIMIT);

		/**********************************************************************************************
         * CHIFFRES DU JOUR ***
         ***********************************************************************************************/
        $options_today = array();
		$options_today['date_begin'] = date('Y-m-d 00:00:00');
		$options_today['date_end'] = date('Y-m-d 23:59:59');
		$options_today['shops'] = $shop_ids;
		$ids_today = Order::findIds($options_today);

		$today['nb_orders'] = count($ids_today);
		$today['turnover'] = Order::sumProducts($ids_today);
		$today['objective'] = DailyObjective::sumPeriod($options_today['date_begin'],
            $options_today['date_end']);

		/**** Déclaration des périodes ****/
		$periods[0]['title'] = "Objectifs mensuel";
		$periods[0]['dates'] = $this->getDates(0, self::PERIOD_MONTHLY);
		$periods[0]['index'] = 0;

		$periods[1]['title'] = "Objectifs annuel";
		$periods[1]['dates'] = $this->getDates(1, self::PERIOD_ANNUAL);
		$periods[1]['index'] = 1;

		foreach($periods as $index => $period) {

            /**********************************************************************************************
             * RESULTATS PERIODE COURANTE ***
             ***********************************************************************************************/

            // Initialisation des filtres de recherche
			$options_current = array();
			$options_current['date_begin'] = $period['dates']['begin']->format('Y-m-d 00:00:00');
			$options_current['date_end'] = $period['dates']['end']->format('Y-m-d 23:59:59');
			$options_current['shops'] = $shop_ids;
			$ids_current = Order::findIds($options_current);

			// Récupération ligne de commande objectif
			$periods[$index]['current']['nb_orders'] = count($ids_current);
			$periods[$index]['current']['turnover'] = Order::sumTurnover($ids_current);
			$periods[$index]['current']['objective'] = DailyObjective::sumPeriod($period['dates']['begin'], $period['dates']['end']);
			$periods[$index]['current']['difference'] = $periods[$index]['current']['turnover'] - $periods[$index]['current']['objective'];

			// Panier moyen condition
			if($periods[$index]['current']['nb_orders'] && $periods[$index]['current']['turnover'])
				$periods[$index]['current']['avg'] = $periods[$index]['current']['turnover'] / $periods[$index]['current']['nb_orders'];
			else
				$periods[$index]['current']['avg'] = 0;

			// Récupération prix d'achat et marge totales
			$buying_price_full = Order::sumBuyingPrice($ids_current);
			$margin_full = $periods[$index]['current']['turnover'] - $buying_price_full;

			// Mis en place des valeurs de marge totale
			$periods[$index]['current']['margin']['full'] = Tools::getMarginRate($margin_full, $periods[$index]['current']['turnover']);
			$periods[$index]['current']['margin_value']['full'] = $margin_full;

            // Mis à jour des champs de recherche pour les devis
            $options_current['quotations'] = true;
            $ids = Order::findIds($options_current);

            // Mise en place du nombre de devis
            $periods[$index]['current']['quotations']['nb_orders'] = count($ids);
            $periods[$index]['current']['quotations']['turnover'] = Order::sumProducts($ids, false, Order::ONLY_QUOTATIONS);

            // Condition panier moyen
            if($periods[$index]['current']['quotations']['nb_orders'] && $periods[$index]['current']['quotations']['turnover'])
                $periods[$index]['current']['quotations']['avg'] = $periods[$index]['current']['quotations']['turnover'] / $periods[$index]['current']['quotations']['nb_orders'];
            else
                $periods[$index]['current']['quotations']['avg'] = 0;

            // Récupération des prix d'achats, prix de ventes, marge
            $turnover_quotations = Order::sumTurnover($ids, false, Order::ONLY_QUOTATIONS);
            $buying_price_quotations = Order::sumBuyingPrice($ids, false, Order::ONLY_QUOTATIONS);
            $margin_quotations = $turnover_quotations - $buying_price_quotations;

            // Mise  en place des valeurs de marge des devis et naturelle
            $margin_products = $margin_full - $margin_quotations ;
            $periods[$index]['current']['margin_value']['quotations'] = $margin_quotations;
            $periods[$index]['current']['margin']['quotations'] = Tools::getMarginRate($margin_quotations, $turnover_quotations);
            $periods[$index]['current']['margin']['products'] = Tools::getMarginRate($margin_products, $periods[$index]['current']['turnover'] - $turnover_quotations);
            $periods[$index]['current']['margin_value']['products'] =  $periods[$index]['current']['margin_value']['full'] -
                $margin_quotations;

            /**********************************************************************************************
             * RESULTATS PERIODE PRECEDENTE ***
             ***********************************************************************************************/

            // Récupération de la période antérieur
			$date_begin = clone($period['dates']['begin']);
			$date_end = clone($period['dates']['end']);
			$date_begin->modify('-1 year');
			$date_end->modify('-1 year');

			// Initialisation des champs de recherche
			$options_last = array();
			$options_last['date_begin'] = $date_begin->format('Y-m-d 00:00:00');
			$options_last['date_end'] = $date_end->format('Y-m-d 23:59:59');
			$options_last['shops'] = $shop_ids;
			$ids = Order::findIds($options_last);

            // Récupération ligne de commande objectif
			$periods[$index]['last']['nb_orders'] = count($ids);
			$periods[$index]['last']['turnover'] = Order::sumProducts($ids);
			$periods[$index]['last']['objective'] = DailyObjective::sumPeriod($date_begin, $date_end);
			$periods[$index]['last']['difference'] = $periods[$index]['last']['turnover'] - $periods[$index]['last']['objective'];

			// Condition Panier moyen
			if($periods[$index]['last']['nb_orders'] && $periods[$index]['last']['turnover'])
				$periods[$index]['last']['avg'] = $periods[$index]['last']['turnover'] / $periods[$index]['last']['nb_orders'];
			else
				$periods[$index]['last']['avg'] = 0;

			// Récupération du prix d'achat et de la marge total
			$buying_price_full = Order::sumBuyingPrice($ids);
			$margin_full = $periods[$index]['last']['turnover'] - $buying_price_full;

            $periods[$index]['last']['margin']['full'] = Tools::getMarginRate($margin_full, $periods[$index]['last']['turnover']);
            $periods[$index]['last']['margin_value']['full'] = $margin_full;

            $options_last['quotations'] = true;
            $ids = Order::findIds($options_last);

            // Mise en place du nombre de devis
            $periods[$index]['last']['quotations']['nb_orders'] = count($ids);
            $periods[$index]['last']['quotations']['turnover'] = Order::sumProducts($ids, false, Order::ONLY_QUOTATIONS);

            // Condition panier moyen
            if($periods[$index]['last']['quotations']['nb_orders'] && $periods[$index]['last']['quotations']['turnover'])
                $periods[$index]['last']['quotations']['avg'] = $periods[$index]['last']['quotations']['turnover'] / $periods[$index]['last']['quotations']['nb_orders'];
            else
                $periods[$index]['last']['quotations']['avg'] = 0;

            // Récupération des prix d'achats, prix de ventes, marge
            $turnover_quotations = Order::sumTurnover($ids, false, Order::ONLY_QUOTATIONS);
            $buying_price_quotations = Order::sumBuyingPrice($ids, false, Order::ONLY_QUOTATIONS);
            $margin_quotations = $turnover_quotations - $buying_price_quotations;
			$margin_products = $margin_full - $margin_quotations;

            // Mise  en place des valeurs de marge des devis et naturelle
            $periods[$index]['last']['margin']['quotations'] = Tools::getMarginRate($margin_quotations, $periods[$index]['last']['quotations']['turnover']);
            $periods[$index]['last']['margin_value']['quotations'] = $margin_quotations;
			$periods[$index]['last']['margin']['products'] = Tools::getMarginRate($margin_products, $periods[$index]['last']['turnover'] - $turnover_quotations);
			$periods[$index]['last']['margin_value']['products'] = $margin_products;


			/**** COMPARAISONS ****/
			$periods[$index]['best']['nb_orders'] = $periods[$index]['current']['nb_orders'] >= $periods[$index]['last']['nb_orders'];
			$periods[$index]['rate']['nb_orders'] = Tools::getRate($periods[$index]['current']['nb_orders'], $periods[$index]['last']['nb_orders']);

			$periods[$index]['best']['turnover'] = $periods[$index]['current']['turnover'] >= $periods[$index]['last']['turnover'];
			$periods[$index]['rate']['turnover'] = Tools::getRate($periods[$index]['current']['turnover'], $periods[$index]['last']['turnover']);

			$periods[$index]['best']['objective'] = $periods[$index]['current']['objective'] >= $periods[$index]['last']['objective'];
			$periods[$index]['rate']['objective'] = Tools::getRate($periods[$index]['current']['objective'], $periods[$index]['last']['objective']);

			$periods[$index]['best']['difference'] = $periods[$index]['current']['difference'] >= $periods[$index]['last']['difference'];
			$periods[$index]['rate']['difference'] = Tools::getRate($periods[$index]['current']['difference'], $periods[$index]['last']['difference']);

			$periods[$index]['best']['avg'] = $periods[$index]['current']['avg'] >= $periods[$index]['last']['avg'];
			$periods[$index]['rate']['avg'] = Tools::getRate($periods[$index]['current']['avg'], $periods[$index]['last']['avg']);

			foreach(array('full', 'products', 'quotations') as $type) {
				$periods[$index]['best']['margin'][$type] = $periods[$index]['current']['margin'][$type] >= $periods[$index]['last']['margin'][$type];
				$periods[$index]['rate']['margin'][$type] = Tools::getRate($periods[$index]['current']['margin'][$type], $periods[$index]['last']['margin'][$type]);
			}

			/**** COMPARAISONS ****/
			$periods[$index]['best']['quotations']['nb_orders'] = $periods[$index]['current']['quotations']['nb_orders'] >= $periods[$index]['last']['quotations']['nb_orders'];
			$periods[$index]['rate']['quotations']['nb_orders'] = Tools::getRate($periods[$index]['current']['quotations']['nb_orders'], $periods[$index]['last']['quotations']['nb_orders']);

			$periods[$index]['best']['quotations']['turnover'] = $periods[$index]['current']['quotations']['turnover'] >= $periods[$index]['last']['quotations']['turnover'];
			$periods[$index]['rate']['quotations']['turnover'] = Tools::getRate($periods[$index]['current']['quotations']['turnover'], $periods[$index]['last']['quotations']['turnover']);

			$periods[$index]['best']['quotations']['avg'] = $periods[$index]['current']['quotations']['avg'] >= $periods[$index]['last']['quotations']['avg'];
			$periods[$index]['rate']['quotations']['avg'] = Tools::getRate($periods[$index]['current']['quotations']['avg'], $periods[$index]['last']['quotations']['avg']);
			
			/**** RESULTATS PAR TYPE DE CLIENT ****/
			foreach(AccountType::getAccountTypes() as $type) {

				$options = array();
				$options['date_begin'] = $period['dates']['begin']->format('Y-m-d 00:00:00');
				$options['date_end'] = $period['dates']['end']->format('Y-m-d 23:59:59');
				$options['customer_types'] = array($type->id);
				$options['shops'] = $shop_ids;
				$ids = Order::findIds($options);

				$periods[$index]['types'][$type->id]['name'] = $type->name;
				$periods[$index]['types'][$type->id]['nb_orders'] = count($ids);
				$periods[$index]['types'][$type->id]['turnover'] = Order::sumProducts($ids);

				if($periods[$index]['types'][$type->id]['nb_orders'] && $periods[$index]['types'][$type->id]['turnover'])
					$periods[$index]['types'][$type->id]['avg'] = $periods[$index]['types'][$type->id]['turnover'] / $periods[$index]['types'][$type->id]['nb_orders'];
				else
					$periods[$index]['types'][$type->id]['avg'] = 0;

			}		

			/**** RESULTATS PAR TYPE DE PAIEMENT ****/
			foreach($methods as $key => $method) {

				/**** RESULTATS PERIODE COURANTE ****/
				$options = array();
				$options['date_begin'] = $period['dates']['begin']->format('Y-m-d 00:00:00');
				$options['date_end'] = $period['dates']['end']->format('Y-m-d 23:59:59');
				$options['payment_methods'] = array("'".pSQL($method['name'])."'");
				$options['shops'] = $shop_ids;
				$ids = Order::findIds($options);

				$periods[$index]['methods'][$key]['name'] = $method['name'];
				$periods[$index]['methods'][$key]['current']['turnover'] = Order::sumProducts($ids);

				/**** RESULTATS PERIODE PRECEDENTE ****/
				$options['date_begin'] = $date_begin->format('Y-m-d 00:00:00');
				$options['date_end'] = $date_end->format('Y-m-d 23:59:59');
				$ids = Order::findIds($options);

				$periods[$index]['methods'][$key]['last']['turnover'] = Order::sumProducts($ids);

				/**** COMPARAISONS ****/
				$periods[$index]['methods'][$key]['best']['turnover'] = $periods[$index]['methods'][$key]['current']['turnover'] >= $periods[$index]['methods'][$key]['last']['turnover'];
				$periods[$index]['methods'][$key]['rate']['turnover'] = Tools::getRate($periods[$index]['methods'][$key]['current']['turnover'], $periods[$index]['methods'][$key]['last']['turnover']);
			}
		}

		foreach($periods as $period) {
			$tpl = $this->context->smarty->createTemplate(_PS_ROOT_DIR_."/override/controllers/admin/templates/results/table.tpl");
			$tpl->assign($period);
			$tables[] = $tpl->fetch();	
		}

		$this->context->smarty->assign('date_limit', $this->date_limit);
		$this->context->smarty->assign('today', $today);
		$this->context->smarty->assign('tables', $tables);
	}

	private function getDates($index, $default) {

		$dates = Tools::getValue('dates');
		if(is_array($dates) && isset($dates[$index]) && $dates[$index]['begin'] && $dates[$index]['end']) {

			$data['begin'] = new DateTime($dates[$index]['begin']);
			$data['end'] = new DateTime($dates[$index]['end']);
		}
		else {

			if($default == self::PERIOD_MONTHLY) {
				$data['begin'] = new DateTime('first day of this month');
				$data['end'] = new DateTime('last day of this month');
			}

			if($default == self::PERIOD_ANNUAL) {
				$data['begin'] = new DateTime('first day of this year');
				$data['end'] = new DateTime('last day of December this year');
			}
		}

		if($this->date_limit == self::UP_TO_TODAY) {
			$date = new DateTime('today');
			if($data['end'] > $date)
				$data['end'] = $date;
		}
		
		return $data;
	}

	private function getPaymentMethods() {
		return Db::getInstance()->executeS("SELECT DISTINCT(payment) AS name FROM ps_orders");
	}
}