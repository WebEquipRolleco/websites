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
}