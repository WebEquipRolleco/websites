<?php

class Webequip_m3 extends Module {

	public function __construct() {

        $this->name = 'webequip_m3';
        $this->tab = 'administration';
        $this->version = '1.0';
        $this->author = 'Web-equip';

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Web-equip M3');
        $this->description = $this->l('Transfert des commandes vers M3.');
    }

    public function getContent() {
    	
        foreach(array('M3_API_URL', 'M3_API_ORDER_SUFFIX', 'M3_API_ADDRESS_LIST_SUFFIX', 'M3_API_ADDRESS_ADD_SUFFIX', 'DEFAULT_STATE_SUCCESS', 'ID_ORDER_STATE_STANDBY', 'ID_ORDER_STATE_FINAL', 'M3_API_ORDER_NUMBER', 'M3_API_ORDER_STATE') as $name) {
            
            if(Tools::getIsset($name))
                Configuration::updateValue($name, Tools::getValue($name));

            $this->context->smarty->assign($name, Configuration::get($name));
        }

        if(Tools::getIsset('DEFAULT_STATE_SUCCESS')) {
            foreach(OrderState::getDefaultStateNames() as $name)
                Configuration::updateValue($name, Tools::getValue('DEFAULT_STATE_SUCCESS'));
        }

        $this->context->smarty->assign('states', OrderState::getOrderStates(1));
    	return $this->display(__FILE__, 'config.tpl');
    }

    /**
    * Execute un appel CURL
    * @param string $url URL de l'appel
    * @param array $data Données à envoyer
    * @param bool $decode Décode le retour JSON
    * @param bool $post Effectue un appel POST, sinon un appel GET
    * @param bool $return Récupère le résultat de la requête
    * @return array|null
    **/
    private function executeCurl($url, $json = null, $decode = true, $post = true, $return = true) {

        $ch = curl_init();
        echo "<div><b>CURL : </b> ".$url."</div>";

        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

        if($post) curl_setopt($ch,CURLOPT_POST, true);
        if($return) curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
        if($json) curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

        $result = curl_exec($ch);
        curl_close($ch);

        if($result && $decode)
            $result = json_decode($result);

        return $result;
    }

    /**
    * TEST URL
    **/
    public function testUrl($url) {
        die(var_dump($this->executeCurl($url)));
    }

    /**
    * Décode le json mal branlé de Provost
    **/
    private function decodeShit($string) {
        return explode('=', str_replace(array('{', '}'), '', $string));
    }

    /**
    * Envoie les nouvelles commandes vers M3
    **/
    public function sendOrders() {

    	// Commandes à envoyer vers M3
    	foreach($this->findOrdersToExport() as $order) {

            $json = $order->getJson();
            echo "<div>".$json."</div>";

            $url = Configuration::get('M3_API_URL').Configuration::get('M3_API_ORDER_SUFFIX');
            $result = $this->executeCurl($url, $json);
            var_dump($result);

            if($result && isset($result->body) && isset($result->body->numPrecommande)) {

                $order->exported = Order::STANDBY_EXPORT;
                $order->reference_m3 = $result->body->numPrecommande;
                $order->save();

                if($id_state = Configuration::get('ID_ORDER_STATE_STANDBY')) {
                    $history = new OrderHistory();
                    $history->changeIdOrderState($id_state, $order->id);
                }
            }

    	}
    }

    /**
    * Retourne la liste des commandes à exporter vers M3
    **/
    private function findOrdersToExport() {

        $data = array();
        foreach(Db::getInstance()->executeS("SELECT id_order FROM ps_orders WHERE exported = ".Order::NOT_EXPORTED) as $row)
            $data[] = new Order($row['id_order']);

        return $data;
    }

    /**
    * Récupère les numéros de commandes M3
    **/
    public function getOrderNumbers() {

        foreach($this->findOrdersToValidate() as $order) {

            $url = Configuration::get('M3_API_URL').Configuration::get('M3_API_ORDER_NUMBER').$order->reference_m3;
            $result = $this->executeCurl($url);

            if($result && isset($result->body) && $result->body->data) {
                $values = $this->decodeShit($result->body->data);

                if(count($values == 3) && $values[0] == $order->reference_m3) {
                    echo "<div>Numéro importé : ".$values[0]." => ".$values[2]."</div>";

                    $order->exported = Order::EXPORTED;
                    $order->reference_m3 = $values[2];
                    $order->save();
                }
            }
        }
    }

    /**
    * Retourne la liste des commandes en attente de validation M3
    **/
    private function findOrdersToValidate() {

        $data = array();
        foreach(Db::getInstance()->executeS("SELECT id_order FROM ps_orders WHERE exported = ".Order::STANDBY_EXPORT) as $row)
            $data[] = new Order($row['id_order']);

        return $data;
    }

    /**
    * Vérifie les changements d'états sur M3
    **/
    public function getNewStates() {

        foreach($this->findOrdersToUpdate() as $order) {

            $url = Configuration::get('M3_API_URL').Configuration::get('M3_API_ORDER_STATE').$order->reference_m3;
            $result = $this->executeCurl($url);

            if($result && isset($result->body) && $result->body->data) {
                $values = $this->decodeShit($result->body->data);

                if(count($values == 3) && $values[0] == $order->reference_m3) {
                    echo "<div>Statut récupéré : ".$values[0]." => ".$values[2]."</div>";

                    $state = OrderState::findStateM3($values[2]);
                    if($state->id && $order->current_state != $state->id) {

                        $history = new OrderHistory();
                        $history->changeIdOrderState($state->id, $order->id);

                        $history->id_order = $order->id;
                        $history->id_order_state = $state->id;
                        $history->id_employee = 0;
                        $history->date_add = date('Y-m-d H:i:s');
                        $history->save();
                    }
                }
            }
        }
    }

    /**
    * Retourne la liste des commandes à mettre à jour (statut M3)
    **/
    private function findOrdersToUpdate() {

        $data = array();
        if(!$id_state = Configuration::get('ID_ORDER_STATE_FINAL'))
            return $data;

        $subSQL = "SELECT DISTINCT(id_order) FROM ps_order_history WHERE id_order_state = $id_state";
        $sql = "SELECT id_order FROM ps_orders WHERE exported = ".self::Order." AND id_order NOT IN ($subSQL)";
        
        foreach(Db::getInstance()->executeS($sql) as $row)
            $data[] = new Order($row['id_order']);

        return $data;
    }

    /**
    * Récupère une liste d'addresse 
    * @param id_customer
    **/
    public function listAddresses($id_customer) {

        $customer = new Customer($id_customer);
        if($customer && $customer->reference) {

            $url = Configuration::get('M3_API_URL').Configuration::get('M3_API_ADDRESS_LIST_SUFFIX').$customer->reference;
            die($this->executeCurl($url));
        }
    }

}