<?php

class Webequip_reviewsAccountModuleFrontController extends ModuleFrontController {

	public function initContent() {
		
		parent::initContent();

		if($form = Tools::getValue('review')) {
            $review = new Review(Tools::getValue('id_review'));

            $review->id_customer = $this->context->customer->id;
            $review->id_product = $form['id_product'];
            $review->id_shop = $form['id_shop'];

            $review->name = $form['name'];
            $review->comment = $form['comment'];
            $review->rating = $form['rating'];

            $review->save();
        }

		$reviews = array();
		$product_ids = Order::findOrderedProducts($this->context->customer->id);
		foreach($product_ids as $row) {

			$review = Review::find($this->context->customer->id, $row['id_product']);
			if(!$review->name) $review->name = $row['name'];

			$reviews[] = $review;
		}

		$this->context->smarty->assign('reviews', $reviews);
		$this->setTemplate('module:webequip_reviews/views/templates/hook/account.tpl');
	}

}