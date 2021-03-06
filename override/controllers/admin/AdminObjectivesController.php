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

        // Format
        $this->date_current = DateTime::createFromFormat('Y-m-d', $this->date_current);
        $this->date_begin = DateTime::createFromFormat('Y-m-d', $this->date_begin);
        $this->date_end = DateTime::createFromFormat('Y-m-d', $this->date_end);
    }

    /**
    * Récupération des données
    **/
    public function initContent() {

        /* Recuperation des donnees sur les dates selectionnees */
        $this->context->smarty->assign('date_current', $this->date_current->format('Y-m-d'));
        $this->context->smarty->assign('date_begin', $this->date_begin->format('Y-m-d'));
        $this->context->smarty->assign('date_end', $this->date_end->format('Y-m-d'));

        /* Recuperation des commandes pour une journee selectionne */
        $options = array();
        $options['date_begin'] = $this->date_current->format('Y-m-d 00:00:00');
        $options['date_end'] = $this->date_current->format('Y-m-d 23:59:59');
        $options['shops'] = Shop::getContextListShopID();;
        $ids = Order::findIds($options);

        $objective = DailyObjective::findOneByDate($this->date_current);
//        $turnover = Order::sumTurnover(false,$this -> date_current, $this -> date_current);
        $turnover = Order::sumProducts($ids);
        $nb_orders = Order::count($this->date_current, $this->date_current);
        $avg = ($turnover and $nb_orders) ? $turnover / $nb_orders : 0;
        $balance = $turnover - $objective->value;

        $this->context->smarty->assign('turnover', $turnover);
        $this->context->smarty->assign('nb_orders', $nb_orders);
        $this->context->smarty->assign('avg', $avg);
        $this->context->smarty->assign('balance', $balance);
        $this->context->smarty->assign('balance_rate', Tools::getRate($turnover, $objective->value));
        
        $last_turnover = Order::sumTurnover(false, $this->date_compare, $this->date_compare);
        $last_nb_orders = Order::count($this->date_compare, $this->date_compare);
        $last_avg = ($last_turnover and $last_nb_orders) ? $last_turnover / $last_nb_orders : 0;

        $this->context->smarty->assign('rate_turnover', Tools::getRate($turnover, $last_turnover));
        $this->context->smarty->assign('rate_nb_orders', Tools::getRate($nb_orders, $last_nb_orders));
        $this->context->smarty->assign('rate_avg', Tools::getRate($avg, $last_avg));

    	$this->context->smarty->assign('display_tab', Tools::getValue('display_tab', 1));
    	$this->context->smarty->assign('objective', $objective);
    	$this->context->smarty->assign('objectives', DailyObjective::findForPeriod($this->date_begin, $this->date_end));

        $evolution = array();
        $date = clone($this->date_current);

        for($x=0; $x<=23; $x++) {
            $evolution[] = array('date' => $date->format("Y-m-d $x:00:00"), 'turnover'=>Order::sumTurnover(false, $date->format('Y-m-d 00:00:00'), $date->format("Y-m-d $x:59:59")));
        }

        $this->context->smarty->assign('evolution', $evolution);
        
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