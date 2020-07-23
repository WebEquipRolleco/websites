<?php

class SendOrderDate extends ObjectModel
{

    /** @var int Id detail */
    public $id_order_detail;

    /** @var DateTime date */
    public $date;

    private $orderDetail;

    public static $definition = array(
        'table' => 'send_order_date',
        'primary' => 'id',
        'fields' => array(
            /* Classic fields */
            'id' =>                 array('type' => self::TYPE_INT),
            'id_order_detail' =>    array('type' => self::TYPE_INT, 'required' => true),
            'date' =>               array('type' => self::TYPE_DATE, 'required' => true),
        )
    );

    public static function needToRecall($date_search) {
        return Db::getInstance()->executeS("select * from ps_send_order_date where date <= '" . $date_search -> format("Y-m-d H:i:s") ."'");
    }

    public static function deleteToDate($date_search) {
        Db::getInstance() -> execute("delete from ps_send_order_date where date <= '" . $date_search -> format("Y-m-d H:i:s") ."'") ;
    }

    /**
     * Retourne la commande
     **/
    public function getOrderDetail() {

        if(!$this->orderDetail and $this->id_order_detail)
            $this->orderDetail = new OrderDetail($this->id_order_detail);

        return $this->orderDetail;
    }





/*            Mail::send(1,"date_expedition",$this->l("En cours de livraison"),
                $tabArgs,  $order_detail->getOrder()->getCustomer()->email, null, Configuration::get("PS_SHOP_EMAIL"),
                Configuration::get("PS_SHOP_NAME"));*/
}
