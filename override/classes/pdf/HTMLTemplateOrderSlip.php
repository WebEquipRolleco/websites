<?php
 class HTMLTemplateOrderSlip extends HTMLTemplateOrderSlipCore {

     public function __construct(OrderSlip $order_slip, $smarty)
     {
         $this->order_slip = $order_slip;
         $this->order = new Order((int)$order_slip->id_order);
         $this->id_cart = $this->order->id_cart;

         $products = OrderSlip::getOrdersSlipProducts($this->order_slip->id, $this->order);

         foreach ($products as $product) {
             $customized_datas = Product::getAllCustomizedDatas($this->id_cart, null, true, null, (int)$product['id_customization']);
             Product::addProductCustomizationPrice($product, $customized_datas);
         }

         $this->order->products = $products;
         $this->smarty = $smarty;

         // header informations
         $this->date = Tools::displayDate($this->order_slip->date_add);
         $prefix = Configuration::get('PS_CREDIT_SLIP_PREFIX', Context::getContext()->language->id);
         $this->title = sprintf(HTMLTemplateOrderSlip::l('%1$s%2$06d'), $prefix, (int)$this->order_slip->id);
         if($this->order->order_slip_number){
             $this->title = $this->order->order_slip_number;
         }

         $this->shop = new Shop((int)$this->order->id_shop);
     }

 }