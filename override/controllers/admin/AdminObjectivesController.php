<?php

class AdminObjectivesControllerCore extends AdminController {

	const SEPARATOR = ";";

	private $date_current;
    private $date_compare;
	private $date_begin;
	private $date_end;

	public function __construct() {
        
        $this->bootstrap = true;
        parent::__construct();

    	// Filtrage manuel
    	if($this->date_current = Tools::getValue('date_current'))
    		Tools::save('objective_date_current', $this->date_current);
	    if($this->date_begin = Tools::getValue('date_begin'))
	    	Tools::save('objective_date_begin', $this->date_begin);
	    if($this->date_end = Tools::getValue('date_end'))
	    	Tools::save('objective_date_end', $this->date_end);

    	// Dates par défaut
    	if(!$this->date_current = Tools::load('objective_date_current')) {
    		Tools::save('objective_date_current', date('Y-m-d'));
            $this->date_current = date('Y-m-d');
        }

    	if(!$this->date_begin = Tools::load('objective_date_begin') or !$this->date_end = Tools::load('objective_date_end')) {

    		$date = new DateTime('this week');
	    	Tools::save('objective_date_begin', $date->format('Y-m-d'));
            $this->date_begin = $date->format('Y-m-d');

	    	$date->modify('+6 days');
	    	Tools::save('objective_date_end', $date->format('Y-m-d'));
            $this->date_end = $date->format('Y-m-d');
	    }

        // Date de comparaison
        $this->date_compare = DateTime::createFromFormat('Y-m-d', $this->date_current);
        $this->date_compare->modify('-1 day');
    }

    /**
    * Récupération des données
    **/
    public function initContent() {

    	$this->context->smarty->assign('date_current', $this->date_current);
    	$this->context->smarty->assign('date_begin', $this->date_begin);
    	$this->context->smarty->assign('date_end', $this->date_end);

        $objective = DailyObjective::findOneByDate($this->date_current);
        $turnover = Order::sumTurnover(false, $this->date_current, $this->date_current);
        $nb_orders = Order::count($this->date_current, $this->date_current);
        $avg = ($turnover and $nb_orders) ? $turnover / $nb_orders : 0;
        $balance = $turnover - $objective->value;

        $this->context->smarty->assign('turnover', $turnover);
        $this->context->smarty->assign('nb_orders', $nb_orders);
        $this->context->smarty->assign('avg', $avg);
        $this->context->smarty->assign('balance', $balance);

        $last_turnover = Order::sumTurnover(false, $this->date_compare, $this->date_compare);
        $last_nb_orders = Order::count($this->date_compare, $this->date_compare);
        $last_avg = ($last_turnover and $last_nb_orders) ? $last_turnover / $last_nb_orders : 0;

        $this->context->smarty->assign('rate_turnover', Tools::getRate($turnover, $last_turnover));
        $this->context->smarty->assign('rate_nb_orders', Tools::getRate($nb_orders, $last_nb_orders));
        $this->context->smarty->assign('rate_avg', Tools::getRate($avg, $last_avg));

    	$this->context->smarty->assign('display_tab', Tools::getValue('display_tab', 1));
    	$this->context->smarty->assign('objective', $objective);
    	$this->context->smarty->assign('objectives', DailyObjective::findForPeriod($this->date_begin, $this->date_end));

        $shops = array();
        foreach(Shop::getShops() as $shop) {

            $row['name'] = $shop['name'];
            $row['turnover'] = Order::sumTurnover(false, $this->date_current, $this->date_current, $shop['id_shop']);
            $row['nb_orders'] = Order::count($this->date_current, $this->date_current, $shop['id_shop']);
            $row['avg'] = ($row['turnover'] and $row['nb_orders']) ? $row['turnover'] / $row['nb_orders'] : 0;

            $last_turnover = Order::sumTurnover(false, $this->date_compare, $this->date_compare, $shop['id_shop']);
            $last_nb_orders = Order::count($this->date_compare, $this->date_compare, $shop['id_shop']);
            $last_avg = ($last_turnover and $last_nb_orders) ? $last_turnover / $last_nb_orders : 0;

            $row['rate_turnover'] = Tools::getRate($row['turnover'], $last_turnover);
            $row['rate_nb_orders'] = Tools::getRate($row['nb_orders'], $last_nb_orders);
            $row['rate_avg'] = Tools::getRate($row['avg'], $last_avg);

            $shops[] = $row;
        }

        $this->context->smarty->assign('shops', $shops);
    }

    /** 
    * Enregistrement des données
    **/
    public function postProcess() {

    	// Création d'un nouvel objectif
    	if(Tools::isSubmit('save_new_objective')) {
    		$form = Tools::getValue('new');

    		$daily = DailyObjective::findOneByDate($form['date']);
    		$daily->date = $form['date'];
    		$daily->value = $form['objective'];
    		$daily->save();
    	}

    	// Suppression d'un objectif
    	if($id = Tools::getValue('remove_objective')) {
    		$daily = new DailyObjective($id);
    		if($daily->id) $daily->delete();
    	}

    	// Import des objectifs par fichier CSV
    	if(isset($_FILES['objective_file'])) {

			$handle = fopen($_FILES['objective_file']['tmp_name'], 'r+');
			while($row = fgetcsv($handle, 10000, self::SEPARATOR)) {

				$date = DateTime::createFromFormat('d/m/Y', $row[0]);

				$objective = DailyObjective::findOneByDate($date->format('Y-m-d'));
				$objective->date = $date->format('Y-m-d');
				$objective->value = $row[1];
				$objective->save();
			}
		}
    }

}