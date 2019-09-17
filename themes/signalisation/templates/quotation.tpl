{extends file='page.tpl'}

{block name='page_title'}
	{l s="Demande de devis"}
{/block}

{block name='page_content_container'}
	<table>
		<tr>
			<td class="icon-cell"><i class="far fa-4x fa-comment-dots"></i></td>
			<td class="description-cell">
				<b>{l s="Pro Signalisation est à votre écoute pour toutes vos demandes spécifiques :"}</b>
				<ul>
					<li>Produits sur-mesure</li>
					<li>Dimensions particulières</li>
					<li>Grande quantité</li>
					<li>Produits non présents sur le site</li>
					<li>...</li>
				</ul>
				<p class="bold">{l s="Remplissez le formulaire ci-dessous et nous vous contacterons dans les plus brefs délais. Nous sommes également à votre disposition au %s." sprintf=[Configuration::get('PS_SHOP_PHONE')]}</p>
			</td>
		</tr>
	</table>

	{if isset($validation)}
		<div class="alert alert-success mt-3">
			<table>
				<tr>
					<td>
						<span class="fa fa-4x fa-user-check"></span>
					</td>
					<td class="pl-1">
						<div class="bold">{l s="Votre demande de devis a été enregistrée."}</div>
						<div>
							{l s="Nous reviendrons vers vous dès que possible afin de satisfaire au mieux votre demande."}
						</div>
					</td>
				</tr>
			</table>
		</div>
	{else}
		<form method="post">
			<div class="row">
				<div class="col-xs-12 col-lg-6">
					<h3 class="section-title margin-top-sm">
						{l s="Vos informations"}
					</h3>
					<div class="form-group">
						<label for="quotation_firstname">
							{l s="Prénom" d='Shop.Forms.Labels'}
						</label>
						<input type="text" id="quotation_firstname" class="form-control" name="quotation[firstname]">
					</div>
					<div class="form-group">
						<label for="quotation_lastname">
							{l s="Nom" d='Shop.Forms.Labels'} 
							<em class="text-danger bold">*</em>
						</label>
						<input type="text" id="quotation_lastname" class="form-control" name="quotation[lastname]" required>
					</div>
					<div class="form-group">
						<label for="quotation_company">
							{l s="Société" d='Shop.Forms.Labels'}
							<em class="text-danger bold">*</em>
						</label>
						<input type="text" id="quotation_company" class="form-control" name="quotation[company]" required>
					</div>
					<div class="form-group">
						<label for="quotation_phone">
							{l s="Téléphone" d='Shop.Forms.Labels'}
							<em class="text-danger bold">*</em>
						</label>
						<input type="text" id="quotation_phone" class="form-control" name="quotation[phone]" required>
					</div>
					<div class="form-group">
						<label for="quotation_email">
							{l s="E-mail" d='Shop.Forms.Labels'}
							<em class="text-danger bold">*</em>
						</label>
						<input type="text" id="quotation_email" class="form-control" name="quotation[email]" required>
					</div>
					<div class="form-group">
						<label for="quotation_delivery">
							{l s="Département de livraison" d='Shop.Forms.Labels'}
							<em class="text-danger bold">*</em>
						</label>
						<input type="text" id="quotation_email" class="form-control" name="quotation[delivery]" required>
					</div>
				</div>
				<div class="col-xs-12 col-lg-6">
					<h3 class="section-title margin-top-sm">
						{l s="Votre demande"}
					</h3>
					<div class="form-group">
						<label for="quotation_message">
							{l s="Demande détaillée" d='Shop.Forms.Labels'}
							<em class="text-danger bold">*</em>
						</label>
						<textarea rows="22" id="quotation_message" class="form-control" name="quotation[message]" required></textarea>
						<div class="text-right text-muted">
							<i>{l s="Merci d'indiquer les références et les quantités qui vous interessent."}</i>
						</div>
					</div>
				</div>
			</div>
			<div class="well">
				<table width="100%">
					<tr class="hidden-lg-up">
						<td class="text-center text-muted">
							{l s="* Délai moyen habituellement constaté. 24h (jour ouvrable)."}
						</td>
					</tr>
					<tr class="hidden-lg-up">
						<td class="text-center">
							<button type="submit" class="btn btn-info bold margin-top-10">
								{l s="Envoyer ma demande de devis" d='Shop.Forms.Labels'}
							</button>
						</td>
					</tr>
					<tr class="hidden-md-down">
						<td class="text-muted">
							{l s="* Délai moyen habituellement constaté. 24h (jour ouvrable)."}
						</td>
						<td class="text-right">
							<button type="submit" class="btn btn-info bold">
								{l s="Envoyer ma demande de devis" d='Shop.Forms.Labels'}
							</button>
						</td>
					</tr>
				</table>
			</div>
		</form>
	{/if}
	
{/block}