<?php

/**
* Gestion des informations commandes à la semaine
**/
class AdminReportControllerCore extends AdminController {

	private $selected_week;
	private $date_begin;
	private $date_end;
	private $use_taxes;

	public function __construct() {
        
        $this->bootstrap = true;
        parent::__construct();

        // Formulaire
        $this->selected_week = Tools::getValue('selected_week', date('W'));
        $this->use_taxes = Tools::getValue('selected_taxes', true);

        // Récupérer les dates
        $date = new DateTime();
        $date->setISODate(date('Y'), $this->selected_week);
        $this->date_begin = clone($date);

        $date->modify('+6 days');
        $this->date_end = clone($date);
    }

    private function getDates() {

    	$date = new DateTime();
    	$data = array();

    	for($x=1; $x<=date('W'); $x++) {
    		$date->setISODate(date('Y'), $x);
    		$data[$x]['begin'] = $date->format('d/m/Y');
    		$date->modify('+6 days');
    		$data[$x]['end'] = $date->format('d/m/Y');
    	}

    	return $data;
    }

    public function initContent() {

    	$nb_orders = Order::count($this->date_begin, $this->date_end);
    	$nb_references = OrderDetail::countReferences($this->date_begin, $this->date_end);

    	$turnover_products = OrderDetail::sumTurnover($this->date_begin, $this->date_end, $this->use_taxes, Order::ONLY_PRODUCTS);
    	$turnover_quotations = OrderDetail::sumTurnover($this->date_begin, $this->date_end, $this->use_taxes, Order::ONLY_QUOTATIONS);
    	$turnover_total = $turnover_products + $turnover_quotations;
    	$turnover_avg = $nb_orders ? ($turnover_total / $nb_orders) : 0;

    	$options = array();
    	foreach(OrderOption::getOrderOptions() as $index => $option) {
    		$options[$index]['option'] = $option;
    		$options[$index]['turnover'] = OrderDetail::sumProductTurnover($this->date_begin, $this->date_end, $option->reference, $this->use_taxes);
    	}

    	$this->context->smarty->assign('selected_week', $this->selected_week);
    	$this->context->smarty->assign('date_begin', $this->date_begin);
    	$this->context->smarty->assign('date_end', $this->date_end);

    	$this->context->smarty->assign('nb_orders', $nb_orders);
    	$this->context->smarty->assign('nb_references', $nb_references);

    	$this->context->smarty->assign('turnover_products', $turnover_products);
    	$this->context->smarty->assign('turnover_quotations', $turnover_quotations);
    	$this->context->smarty->assign('turnover_total', $turnover_total);
    	$this->context->smarty->assign('turnover_avg', $turnover_avg);

    	$this->context->smarty->assign('total_objective', DailyObjective::sumPeriod($this->date_begin, $this->date_end));
    	$this->context->smarty->assign('objectives', DailyObjective::findForPeriod($this->date_begin, $this->date_end));

		$this->context->smarty->assign('margin_natural', Tools::getMarginRate($turnover_products, $turnover_total));
		$this->context->smarty->assign('margin_quotation', Tools::getMarginRate($turnover_quotations, $turnover_total));

		$this->context->smarty->assign('options', $options);
		$this->context->smarty->assign('cart_rules', CartRule::findUsed($this->date_begin, $this->date_end, $this->use_taxes));

		$this->context->smarty->assign('dates', $this->getDates());
    	$this->context->smarty->assign('use_taxes', $this->use_taxes);

    	parent::initContent();
    }

}