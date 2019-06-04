{extends file='page.tpl'}

{block name='page_title'}
	{l s="Nous contacter"}
{/block}

{block name='page_content_container'}
	{if isset($validation) && $validation}
		<div class="alert alert-success">
			<b>{l s="Votre message a bien été enregistré."}</b>
			<br />
			{l s="Nous vous remercions de votre intêret et reviendrons vers vous dès que possible."}
		</div>
	{else}

		<table>
			<tbody>
				<tr>
					<td class="text-info">
						<span class="fa fa-4x fa-comments"></span>
					</td>
					<td class="padding-left-20">
						<b>{l s="Pour toute demande ou information, vous pouvez nous contacter en remplissant le formulaire ci-dessous, nous vous répondrons dans les plus brefs délais."}</b>
						<br />
						<br />
						{l s="Vous pouvez également nous joindre directement"}
						<br />
						{assign var=contact_phone value=Configuration::get('PS_SHOP_PHONE')}
						{if $contact_phone}
							&nbsp; <i class="fa fa-phone"></i> &nbsp; {$contact_phone}
						{/if}
						{assign var=contact_fax value=Configuration::get('PS_SHOP_FAX')}
						{if $contact_fax}
							&nbsp; <i class="fa fa-fax"></i> &nbsp; {$contact_fax}
						{/if}
					</td>
				</tr>
			</tbody>
		</table>

		<form method="post">
			<div class="row">
				<div class="col-xs-12 col-lg-6">
					<h3 class="section-title margin-top-sm">
						{l s="Vos informations" d='Shop.Forms.Labels'}
					</h3>
					<div class="form-group">
						<label for="contact_number">
							{l s="Code client" d='Shop.Forms.Labels'}
						</label>
						<input type="text" id="contact_number" class="form-control" name="contact[number]">
					</div>
					<div class="form-group">
						<label for="contact_firstname">
							{l s="Prénom" d='Shop.Forms.Labels'}
						</label>
						<input type="text" id="contact_firstname" class="form-control" name="contact[firstname]">
					</div>
					<div class="form-group">
						<label for="contact_lastname">
							{l s="Nom" d='Shop.Forms.Labels'} 
							<em class="text-danger bold">*</em>
						</label>
						<input type="text" id="contact_lastname" class="form-control" name="contact[lastname]" required>
					</div>
					<div class="form-group">
						<label for="contact_company">
							{l s="Société" d='Shop.Forms.Labels'}
							<em class="text-danger bold">*</em>
						</label>
						<input type="text" id="contact_company" class="form-control" name="contact[company]" required>
					</div>
					<div class="form-group">
						<label for="contact_phone">
							{l s="Téléphone" d='Shop.Forms.Labels'}
							<em class="text-danger bold">*</em>
						</label>
						<input type="text" id="contact_phone" class="form-control" name="contact[phone]" required>
					</div>
					<div class="form-group">
						<label for="contact_email">
							{l s="E-mail" d='Shop.Forms.Labels'}
							<em class="text-danger bold">*</em>
						</label>
						<input type="text" id="contact_email" class="form-control" name="contact[email]" required>
					</div>
					<div class="form-group">
						<label for="contact_city">
							{l s="CP / Ville" d='Shop.Forms.Labels'}
						</label>
						<input type="text" id="contact_city" class="form-control" name="contact[city]">
					</div>
				</div>
				<div class="col-xs-12 col-lg-6">
					<h3 class="section-title margin-top-sm">
						{l s="Votre demande" d='Shop.Forms.Labels'}
					</h3>
					<div class="form-group">
						<label for="contact_message">
							{l s="Demande détaillée" d='Shop.Forms.Labels'}
							<em class="text-danger bold">*</em>
						</label>
						<textarea rows="26" id="contact_message" class="form-control" name="contact[content]" required></textarea>
					</div>
				</div>
			</div>
			<div class="well text-center">
				<button type="submit" class="btn btn-info bold">
					{l s="Envoyer votre demande" d='Shop.Forms.Labels'}
				</button>
			</div>
		</form>
	{/if}
{/block}