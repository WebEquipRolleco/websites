{extends file='page.tpl'}

{block name='page_title'}
	{l s="Devenir fournisseur" mod="webequip_partners"}
{/block}

{block name='page_content_container'}
	{if isset($validation) && $validation}
		<div class="alert alert-success">
			<b>{l s="Votre demande de partenariat a bien été enregistrée." mod="webequip_partners"}</b>
			<br />
			{l s="Nous vous remercions de votre intêret et reviendrons vers vous dès que possible." mod="webequip_partners"}
		</div>
	{else if isset($exists) && $exists}
		<div class="alert alert-info">
			<b>{l s="Une demande de partenariat pour la société suivante existe déjà : " mod="webequip_partners"} {$request->company}</b>
			<br />
			{l s="Votre demande sera traîtée dans les plus brefs délais." mod="webequip_partners"}
		</div>
	{else}
		{l s="Pour toute proposition commerciale, vous pouvez nous contacter en remplissant le formulaire ci-dessous."} <br />
		<b>{l s="Nous vous répondrons dans les plus brefs délais."}</b> <br />
		<br />
		{l s="Vous pouvez également nous joindre par téléphone au <b>%s</b> ou par fax au <b>%s</b>." sprintf=[Configuration::get('PS_SHOP_PHONE'), Configuration::get('PS_SHOP_FAX')]}
		<form method="post">
			<div class="row">
				<div class="col-xs-12 col-lg-6">
					<h3 class="section-subtitle top-space">
						{l s="Vos informations" mod="webequip_partners"}
					</h3>
					<div class="form-group">
						<label for="partner_firstname">
							{l s="Prénom" d='Shop.Forms.Labels'}
						</label>
						<input type="text" id="partner_firstname" class="form-control" name="partner[firstname]">
					</div>
					<div class="form-group">
						<label for="partner_lastname">
							{l s="Nom" d='Shop.Forms.Labels'} 
							<em class="text-danger bold">*</em>
						</label>
						<input type="text" id="partner_lastname" class="form-control" name="partner[lastname]" required>
					</div>
					<div class="form-group">
						<label for="partner_company">
							{l s="Société" d='Shop.Forms.Labels'}
							<em class="text-danger bold">*</em>
						</label>
						<input type="text" id="partner_company" class="form-control" name="partner[company]" required>
					</div>
					<div class="form-group">
						<label for="partner_phone">
							{l s="Téléphone" d='Shop.Forms.Labels'}
							<em class="text-danger bold">*</em>
						</label>
						<input type="text" id="partner_phone" class="form-control" name="partner[phone]" required>
					</div>
					<div class="form-group">
						<label for="partner_email">
							{l s="E-mail" d='Shop.Forms.Labels'}
							<em class="text-danger bold">*</em>
						</label>
						<input type="text" id="partner_email" class="form-control" name="partner[email]" required>
					</div>
				</div>
				<div class="col-xs-12 col-lg-6">
					<h3 class="section-subtitle top-space">
						{l s="Votre demande" mod="webequip_partners"}
					</h3>
					<div class="form-group">
						<label for="partner_message">
							{l s="Demande détaillée" d='Shop.Forms.Labels'}
							<em class="text-danger bold">*</em>
						</label>
						<textarea rows="18" id="partner_message" class="form-control" name="partner[content]" required></textarea>
					</div>
				</div>
			</div>
			<div class="well text-center">
				<button type="submit" class="btn btn-success bold">
					{l s="Envoyer" d='Shop.Forms.Labels'}
				</button>
			</div>
		</form>
	{/if}
{/block}