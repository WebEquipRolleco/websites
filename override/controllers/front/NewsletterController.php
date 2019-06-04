<?php

class NewsletterControllerCore extends FrontController {

	public function displayAjax() {

		$action = Tools::getValue('action');
		$email = Tools::getValue('email');
		$data['disable_form'] = true;

		$customer = new Customer();
		$customer->getByEmail($email);

		try {
			if($action == 'registration') {
				if($email) {
					Db::getInstance()->execute("UPDATE ps_customer SET newsletter = 1, ip_registration_newsletter = '".$_SERVER['REMOTE_ADDR']."', newsletter_date_add = '".date('Y-m-d H:i:s')."' WHERE email = '".$email."'");

					if($newsletter = Newsletter::findByEmail($email, true)) {

						$data['headerColor'] = "#1e4688";
						$data['icon'] = "far fa-grin-beam";
						$data['subtitle'] = "Votre inscription est terminée !";
						$data['content'] = $this->context->smarty->fetch('_partials/newsletter_modal_already_registered.tpl');
					}
					else {

						$newsletter = new Newsletter();
						$newsletter->id_shop = $this->context->cart->id_shop;
						$newsletter->id_shop_group = $this->context->cart->id_shop_group;
						$newsletter->email = $email;
						$newsletter->ip = $_SERVER['REMOTE_ADDR'];
						$newsletter->date_add = date('Y-m-d H:i:s');
						$newsletter->save();

						if($customer->id) {

							if($row = Group::searchByName(Newsletter::GROUP_NAME)) {
								$customer->addGroups(array($row['id_group']));

								if($id = CartRule::getIdByCode(strtoupper(Newsletter::GROUP_NAME))) {
									$reduction = new CartRule($id, 1);
									$this->context->smarty->assign('reduction', $reduction);
								}
							}
						}

						$data['headerColor'] = "#4cbb6c";
						$data['icon'] = "fa fa-check-square";
						$data['subtitle'] = "Votre inscription est terminée !";
						$data['content'] = $this->context->smarty->fetch('_partials/newsletter_modal_validation.tpl');
					}
				}
			}
		}
		catch(Exception $e) {

			$data['headerColor'] = "#d5121d";
			$data['icon'] = "far fa-sad-tear";
			$data['subtitle'] = "Votre inscription n'a pas pu aboutir !";
			$data['content'] = $this->context->smarty->fetch('_partials/newsletter_modal_error.tpl');
			$data['disable_form'] = false;
		}

		die(json_encode($data));
	}
}