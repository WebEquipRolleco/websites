{if isset($validation)}
	<div class="alert alert-success">
		<b>{$validation}</b>
	</div>
{/if}

<form method="post">
	<input type="hidden" name="id_quotation" value="{$quotation->id}">

	<div class="panel">
		<div class="row">
		<div class="col-lg-6">
			<span class="label label-default">
				<strong>{$quotation->getShop()->name}</strong>
			</span>
			&nbsp;
			<span class="label label-default">
				<strong>{$quotation->reference}</strong>
			</span>
			&nbsp;
			<span class="label label-{$quotation->getStatusClass()}">
				<b>{l s='Devis' d='Admin.Labels'} {$quotation->getStatusLabel()}</b>
			</span>
			{assign var=order value=$quotation->getOrder()}
			{if $order}
				&nbsp;
				<span class="label label-success">
					<b>{l s="Commande" d='Admin.Labels'} {$order->reference}</b>
				</span>
			{/if}
		</div>
		<div class="col-lg-6 text-right">
			<a href="{$link->getAdminLink('AdminQuotations')}">{l s='Retour' d='Admin.Labels'}</a>
			&nbsp;
			<button type="submit" class="btn btn-xs btn-success">
				<b>{l s='Enregistrer' d='Admin.Labels'}</b>
			</button>
		</div>
		</div>
	</div>

	<div class="row">

		<div class="col-lg-3">
			<div class="panel">
				<div class="panel-heading">
					{l s="Gestion"}
				</div>
				<div class="form-group">
					<label for="status">{l s="Etat"}</label>
					<select name="quotation[status]" class="form-control">
						{foreach $states as $id => $name}
							<option value="{$id}" {if $quotation->status == $id}selected{/if}>{$name}</option>
						{/foreach}
					</select>
				</div>
				<div class="form-group">
					<label for="id_employee">{l s="Créateur"}</label>
					<select name="quotation[id_employee]" class="form-control select2">
						{foreach $employees as $employee}
							<option value="{$employee.id_employee}" {if $quotation->id_employee == $employee.id_employee}selected{/if}>
								{$employee.firstname} {$employee.lastname}
							</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="panel">
				<div class="panel-heading">
					{l s="Validité"}
				</div>
				<span class="switch prestashop-switch fixed-width-lg" style="margin-left:auto; margin-right:auto; margin-bottom:20px">
					<input type="radio" name="quotation[active]" id="active_on" value="1" {if $quotation->active}checked{/if}>
					<label for="active_on">{l s='Active' d='Admin.Labels'}</label>
					<input type="radio" name="quotation[active]" id="active_off" value="0" {if !$quotation->active}checked{/if}>
					<label for="active_off">{l s='Inactive' d='Admin.Labels'}</label>
					<a class="slide-button btn"></a>
				</span>
				<div class="form-group">
					<label for="date_begin">{l s="Date de début"} <em class="text-danger">*</em></label>
					<input type="date" name="quotation[date_begin]" id="date_begin" class="form-control" {if $quotation->date_begin}value="{$quotation->date_begin|date_format:'Y-m-d'}"{/if} required>
				</div>
				<div class="form-group">
					<label for="date_end">{l s="Date de fin"} <em class="text-danger">*</em></label>
					<div class="pull-right">
						<button type="button" class="btn btn-xs btn-primary change-recall" data-source="#date_begin" data-target="#date_end" data-nb="30">
							<b>J+30</b>
						</button>
					</div>
					<input type="date" name="quotation[date_end]" id="date_end" class="form-control" {if $quotation->date_end}value="{$quotation->date_end|date_format:'Y-m-d'}"{/if} required>
				</div>
				<hr />
				<div class="form-group">
					<div>
						<label for="date_recall">{l s="Date de rappel"}</label>
						<div class="pull-right">
							<div class="btn-group">
								<button type="button" class="btn btn-xs btn-primary change-recall" data-source="#date_begin" data-target="#date_recall" data-nb="3">
									<b>J+3</b>
								</button>
								<button type="button" class="btn btn-xs btn-primary change-recall" data-source="#date_begin" data-target="#date_recall" data-nb="5">
									<b>J+5</b>
								</button>
								<button type="button" class="btn btn-xs btn-primary change-recall" data-source="#date_begin" data-target="#date_recall" data-nb="10">
									<b>J+10</b>
								</button>
							</div>
							&nbsp;
							<div class="btn-group">
								<button type="button" class="btn btn-xs btn-primary change-recall" data-source="#date_end" data-target="#date_recall" data-nb="-3">
									<b>J-3</b>
								</button>
								<button type="button" class="btn btn-xs btn-primary change-recall" data-source="#date_end" data-target="#date_recall" data-nb="-5">
									<b>J-5</b>
								</button>
								<button type="button" class="btn btn-xs btn-primary change-recall" data-source="#date_end" data-target="#date_recall" data-nb="-10">
									<b>J-10</b>
								</button>
							</div>
						</div>
					</div>
					<input type="date" name="quotation[date_recall]" id="date_recall" class="form-control" {if $quotation->date_recall}value="{$quotation->date_recall|date_format:'Y-m-d'}"{/if}>
				</div>
			</div>
			<div class="panel">
				<div class="panel-heading">
					{l s="Options"}
				</div>
				<div class="row">
					<div class="col-lg-12">
						<div class="text-center">
							<span class="label label-default" title="{l s='Le client verra sont devis en tant que nouveauté jusqu\'à son ouverture.'}" style="cursor:help">
								<b>{l s="Nouveau"}</b>
							</span>
						</div>
						<span class="switch prestashop-switch fixed-width-lg" style="margin-left:auto; margin-right:auto; margin-bottom:20px">
							<input type="radio" name="quotation[new]" id="new_on" value="1" {if $quotation->new}checked{/if}>
							<label for="new_on">{l s='Oui' d='Admin.Labels'}</label>
							<input type="radio" name="quotation[new]" id="new_off" value="0" {if !$quotation->new}checked{/if}>
							<label for="new_off">{l s='Non' d='Admin.Labels'}</label>
							<a class="slide-button btn"></a>
						</span>
					</div>
					<div class="col-lg-12">
						<div class="text-center">
							<span class="label label-default" title="{l s='Le devis sera mis en valeur pour attirer l\'attention du client.'}" style="cursor:help">
								<b>{l s="Valoriser"}</b>
							</span>
						</div>
						<span class="switch prestashop-switch fixed-width-lg" style="margin-left:auto; margin-right:auto; margin-bottom:20px">
							<input type="radio" name="quotation[highlight]" id="highlight_on" value="1" {if $quotation->highlight}checked{/if}>
							<label for="highlight_on">{l s='Oui' d='Admin.Labels'}</label>
							<input type="radio" name="quotation[highlight]" id="highlight_off" value="0" {if !$quotation->highlight}checked{/if}>
							<label for="highlight_off">{l s='Non' d='Admin.Labels'}</label>
							<a class="slide-button btn"></a>
						</span>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-9">
			<div class="panel">
				<div class="panel-heading">
					{l s="Affichage"}
				</div>
				{if $quotation->id}
					<div class="row">
						<div class="col-lg-12">
							<b>{l s="Lien du devis" d='Admin.Labels'}</b>
						</div>
						<div class="col-lg-12">
							<a href="{$quotation->getLink()}" class="quotation-link" target="_blank">
								{$quotation->getLink()}
							</a>
						</div>
					</div>
					<br />
				{/if}
				<div class="row">
					<div class="col-lg-6">
						<div class="form-group">
							<label for="origin">{l s="Provenance"} <em class="text-danger">*</em></label>
							<select name="quotation[origin]" class="form-control" required>
								<option value="">{l s='Choisir' d='Admin.Labels'}</option>
								{foreach $origins as $id => $origin}
									<option value="{$id}" {if $quotation->origin == $id}selected{/if}>
										{$origin}
									</option>
								{/foreach}
							</select>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="form-group">
							<label for="source">{l s="Source"} <em class="text-danger">*</em></label>
							<select name="quotation[source]" class="form-control" required>
								<option value="">{l s='Choisir' d='Admin.Labels'}</option>
								{foreach from=$sources key=id item=name}
									<option value="{$id}" {if $quotation->source == $id}selected{/if}>
										{$name}
									</option>
								{/foreach}
							</select>
						</div>
					</div>
				</div>
				<label>{l s="Client"}</label>
				<div class="well" style="padding:5px">
					<div class="row">
						<div class="col-lg-10">
							{if $quotation->getCustomer()}
								<b>{$quotation->getCustomer()->firstname} {$quotation->getCustomer()->lastname}</b>
								{if $quotation->getCustomer()->getType()}
									<em class="text-muted"> - {$quotation->getCustomer()->getType()->name}</em>
								{/if}
								<br /> 
								<a href="mailto:{$quotation->getCustomer()->email}">{$quotation->getCustomer()->email}</a>
							{else}
								<span class="label label-default" style="line-height:21px">
									<b>{l s="Le devis n'est rattaché a aucun compte client"}</b>
								</span>
							{/if}
						</div>
						<div class="col-lg-2 text-right">
							<button type="button" class="btn btn-xs btn-info" data-toggle="modal" data-target="#customer_modal">
								<b>{l s="Change" d='Admin.Actions'}</b>
							</button>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<div class="form-group">
							<label for="email">{l s="E-mails en copie"}</label>
							<span class="text-muted pull-right">{l s="Séparés par une virgule"}</span>
							<input type="text" name="quotation[email]" id="email" class="form-control" value="{$quotation->email}" tabindex="1">
						</div>

					</div>
					<div class="col-lg-6">
						<div class="form-group">
							<label for="phone">{l s="Téléphone"}</label>
							<input type="text" name="quotation[phone]" id="phone" class="form-control" value="{$quotation->phone}" tabindex="3">
						</div>
					</div>
					<div class="col-lg-6">
						<div class="form-group">
							<label for="fax">{l s="Fax"}</label>
							<input type="text" name="quotation[fax]" id="fax" class="form-control" value="{$quotation->fax}" tabindex="4">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="comment">{l s="Commentaire"}</label>
					<textarea rows="5" name="quotation[comment]" id="comment" class="form-control">{$quotation->comment}</textarea>
				</div>
				<div class="form-group">
					<label for="details">{l s="Information client"}</label>
					<span class="text-muted pull-right">{l s="Non visible par le client"}</span>
					<textarea rows="5" name="quotation[details]" id="details" class="form-control">{$quotation->details}</textarea>
				</div>
				<div class="row">
					{foreach from=OrderOption::getOrderOptions() item=option}
						{assign var=selected value=$option->id|in_array:$quotation->getOptions()}
						{assign var=empty value=empty($quotation->getOptions())}
						<div class="col-lg-3 text-center">
							<span class="label label-default" title="{l s='Autoriser cette option dans le panier'}"><b>{$option->name}</b></span>
							<span class="switch prestashop-switch fixed-width-lg" style="margin-left:auto; margin-right:auto; margin-bottom:20px">
								<input type="radio" name="quotation[options][{$option->id}]" id="option_{$option->id}_on" value="{$option->id}" {if $selected or $empty}checked{/if}>
								<label for="option_{$option->id}_on">{l s='Oui' d='Admin.Labels'}</label>
								<input type="radio" name="quotation[options][{$option->id}]" id="option_{$option->id}_off" value="" {if !$selected and !$empty}checked{/if}>
								<label for="option_{$option->id}_off">{l s='Non' d='Admin.Labels'}</label>
								<a class="slide-button btn"></a>
							</span>
						</div>
					{/foreach}
				</div>
			</div>
		</div>

	</div>

	<div class="modal fade" id="customer_modal">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<div class="form-group">
						<div class="alert bg-primary"><b>{l s="Compte client existant"}</b></div>
						<select name="quotation[id_customer]" class="form-control select2">
							<option value="">{l s='Choisir' d='Admin.Labels'}</option>
							{foreach $customers as $customer}
								<option value="{$customer.id_customer}" {if $quotation->id_customer == $customer.id_customer}selected{/if}>
									{$customer.firstname} {$customer.lastname} ({$customer.email})
								</option>
							{/foreach}
						</select>
					</div>
					<hr>
					<div class="alert bg-primary" style="margin-bottom:0px; border-radius:3px 3px 0px 0px">
						<div class="row">
							<div class="col-lg-6">
								<b>{l s="Création de compte"}</b>
							</div>
							<div class="col-lg-6 text-right">
								<span class="switch prestashop-switch fixed-width-lg" style="position:absolute; top:-8px; right:0px">
									<input type="radio" id="creation_on" name="creation" value="1">
									<label for="creation_on">{l s='Oui' d='Admin.Labels'}</label>
									<input type="radio" id="creation_off" name="creation" value="0" checked>
									<label for="creation_off">{l s='Non' d='Admin.Labels'}</label>
									<a class="slide-button btn"></a>
								</span>
							</div>
						</div>
					</div>
					<div id="account_box" style="display:none; border:1px solid #00aff0; padding:10px">
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<label for="new_email">{l s="E-mail" d='Admin.Labels'} <em class="text-danger">*</em></label>
									<input type="text" id="new_email" class="form-control" name="new_account[email]">
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label for="new_firstname">{l s="Prénom" d='Admin.Labels'} <em class="text-danger">*</em></label>
									<input type="text" id="new_firstname" class="form-control" name="new_account[firstname]">
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label for="new_lastname">{l s="Nom" d='Admin.Labels'} <em class="text-danger">*</em></label>
									<input type="text" id="new_lastname" class="form-control" name="new_account[lastname]">
								</div>
							</div>
							<div class="col-lg-12">
								<div class="form-group">
									<label>{l s="Type de compte" d='Admin.Labels'} <em class="text-danger">*</em></label>
									<select class="form-control" id="new_type" name="new_account[id_account_type]">
										{foreach from=AccountType::getAccountTypes() item=type}
											<option value="{$type->id}" {if $type->default_value}selected{/if}>{$type->name}</option>
										{/foreach}
									</select>
								</div>
							</div>
							<div class="col-lg-12">
								<div class="form-group">
									<label for="new_company">{l s="Société" d='Admin.Labels'}</label>
									<input type="text" id="new_company" class="form-control" name="new_account[company]">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
				    <button id="close_modal" type="button" class="btn btn-default" data-dismiss="modal">
				        <b><i class="icon-times"></i> &nbsp; {l s='Close' d='Admin.Actions'}</b>
				    </button>
				    <button type="submit" class="btn btn-success">
				        <b><i class="icon-check-square"></i> &nbsp; {l s='Enregistrer le devis' d='Admin.Labels'}</b>
				    </button>
				</div>
			</div>
		</div>
	</div>

</form>

{if $quotation->id}
	<form action="#save_products_form" enctype="multipart/form-data" method="post" id="save_products_form">
		<div class="row">
			<div class="col-lg-12">
				<div class="panel">
					<div class="panel-heading text-center">
						<span style="float:left">{l s="Liste des produits"}</span>
						<span class="label" style="font-size:14px; background-color:#59A8CA;">
							<b>Total HT :</b> {Tools::displayPrice($quotation->getPrice())}
						</span>
						<span class="label label-warning" style="margin-left:5px; font-size:14px; background-color:#59A8CA;">
							<b>TVA :</b> {Tools::displayPrice($quotation->getPrice(true) - $quotation->getPrice())}
						</span>
						<span class="label label-warning" style="margin-left:5px; font-size:14px; background-color:#59A8CA;">
							<b>Total TTC :</b> {Tools::displayPrice($quotation->getPrice(true))}
						</span>
						<span class="label label-warning" style="margin-left:5px; font-size:14px; background-color:#59A8CA;">
							<b>Eco-tax :</b> {Tools::displayPrice($quotation->getEcoTax())}
						</span>
						<span class="label label-warning" style="margin-left:25px; font-size:14px; background-color:#59A8CA;">
							<b>Marge :</b> {Tools::displayPrice($quotation->getMargin())}
						</span>
						<span class="label label-warning" style="margin-left:5px; font-size:14px; background-color:#59A8CA;">
							<b>Taux :</b> {$quotation->getMarginRate()|round:2}%
						</span>
						<span class="panel-heading-action">
							<a href="" id="new_product" class="list-toolbar-btn" data-toggle="modal" data-target="#new_product_modal" title="{l s='New' d='Shop.Theme.Actions'}">
								<i class="process-icon-new"></i>
							</a>
							<a href="" id="save_products" class="list-toolbar-btn" title="{l s='Save' d='Admin.Actions'}">
								<i class="process-icon-save"></i>
							</a>
						</span>
					</div>
					<table class="table">
						<thead>
							<th colspan="3" class="text-center"><b>{l s="Produit" d="Admin.Labels"}</b></th>
							<th class="text-center"><b>{l s="PA" d="Admin.Labels"}</b></th>
							<th class="text-center"><b>{l s="Port" d="Admin.Labels"}</b></th>
							<th class="text-center"><b>{l s="Total" d="Admin.Labels"}</b></th>
							<th class="text-center"><b>{l s="PV" d="Admin.Labels"}</b></th>
							<th width="10%" class="text-center"><b>{l s="PU final" d="Admin.Labels"}</b></th>
							<th width="5%" class="text-center"><b>{l s="Qté" d="Admin.Labels"}</b></th>
							<th class="text-center"><b>{l s="Total" d="Admin.Labels"}</b></th>
							<th class="text-center"><b>{l s="Marge" d="Admin.Labels"}</b></th>
							<th width="10%" class="text-center"><b>{l s="Fournisseur" d="Admin.Labels"}</b></th>
							<th class="text-center"><b>{l s="commentaire" d="Admin.Labels"}</b></th>
							<th width="5%" class="text-center"><b>{l s="Position" d="Admin.Labels"}</b></th>
							<th></th>
						</thead>
						<tbody id="quotation_products">
							{foreach from=$quotation->getProducts() item=line}
								{include file="./helpers/view/product_line.tpl"}
							{/foreach}
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</form>


	<form id="new_product_form" action="{$link->getAdminLink('AdminQuotations')}">
		<input type="hidden" name="action" value="add_product">
		<input type="hidden" name="ajax" value="1">
		<input type="hidden" name="id_quotation" value="{$quotation->id}">

		<div class="modal fade" id="new_product_modal">
			<div class="modal-dialog" role="document">
			  	<div class="modal-content">
			      	<div class="modal-header">
			        	<b>{l s="Nouveau produit"}</b>
			      	</div>
			      	<div class="modal-body">
			      		<div class="row">
			      			<div class="col-lg-12">
			      				<div class="form-group">
			      					<select class="form-control select2" name="product_infos">
			      						<option value="">{l s="Produit libre"}</option>
			      						{foreach $products as $product}
			      							<option value="{$product.id_product}_{$product.id_product_attribute}">
			      								{if $product.reference_attribute}
			      									{$product.reference_attribute} - 
			      								{elseif $product.reference}
			      									{$product.reference} - 
			      								{/if}
			      								{$product.name}
			      								{if $product.name_attribute}
			      									: {$product.name_attribute}
			      								{/if}
			      							</option>
			      						{/foreach}
			      					</select>
			      				</div>
			      				<div id="ajax_details_product"></div>
			      			</div>
			      		</div>
			      	</div>
			      	<div class="modal-footer">
			        	<button id="close_modal" type="button" class="btn btn-link" data-dismiss="modal">
			        		{l s='Annuler' d='Admin.Labels'}
			        	</button>
			        	<button type="submit" class="btn btn-success">
			        		<b>{l s='Ajouter' d='Admin.Labels'}</b>
			        	</button>
			      	</div>
			  	</div>
			</div>
		</div>
	</form>

	<form method="post">
		<div class="panel">
			<div class="panel-heading">
				{l s="Panier"}
			</div>
			<div class="row">
				<div class="col-lg-4">
					<div class="form-group">
						<select name="id_customer" class="form-control select2">
							<option value="">{l s='Choisir' d='Admin.Labels'}</option>
							{foreach $customers as $customer}
								<option value="{$customer.id_customer}" {if $employee->email == $customer.email}selected{/if}>
									{$customer.firstname} {$customer.lastname} ({$customer.email})
								</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="col-lg-8">
					<button type="submit" id="add_to_customer" class="btn btn-info" name="add_to_customer">
						<i class="icon icon-shopping-cart"></i> &nbsp; <b>{l s="Ajouter au panier du client"}</b>
					</button>
				</div>
			</div>
		</div>
	</form>

{/if}

<script>
	$(document).ready(function() {
		
		/** SELECT 2 **/
		$('.select2').select2({
			width: 'auto'
		});

		/** Calcul initial des prix **/
		updateAllPrices();

		/** Re-calcul des prix **/
		$(document).on('change keyup', '.update-price', function() {
			updatePrices($(this).data('id'));
		});
		$(document).on('change keyup', '.update-pa', function() {
			updateBuyingPrices($(this).data('id'));
		});

		$('.remove_product').on('click', function(e) {
			if(!confirm('Etes-vous sûr de vouloir supprimer ce produit du devis ?'))
				e.preventDefault();
		});

		$('#add_to_customer').on('click', function(e) {
			if(!confirm("Etes-vous sûr de vouloir ajouter les produits au panier de ce client ?"))
				e.preventDefault();
		});

		$('.change-picture').on('click', function() {
			$($(this).data('id')).click();
		});

		$('#save_products').on('click', function(e) {
			e.preventDefault();

			$('#save_products_form').submit();
		});

		$('#creation_on').on('change', function() {
			$('#account_box').slideDown('fast');
			changeFieldsRequirements(true);
		});

		$('#creation_off').on('change', function() {
			$('#account_box').slideUp('fast');
			changeFieldsRequirements(false);
		});

		$('.change-recall').on('click', function() {
			
			var source = $(this).data('source');
			var target = $(this).data('target');
			var nb = $(this).data('nb');

			var date = $(source).val();
			if(date) {

				date = new Date(date);
				date.setDate(date.getDate() + nb);

				var month = String(date.getMonth()+1);
				if(month.length == 1) month = "0"+month;

				var day = String(date.getDate()); 
				if(day.length == 1) day = "0"+day;

				$(target).val(date.getFullYear()+"-"+month+"-"+day);
			}
			else
				$(target).val(null);

		});

		$('#new_product_form').on('submit', function(e) {
			e.preventDefault();

			$.ajax({
				url: $(this).attr('action'),
				data: $(this).serialize(),
				dataType: "json",
				success : function(response) {

					$('#quotation_products').append(response.view);
					$('#new_product_modal').modal('hide');
					updateAllPrices();
				}
			});
		});

	});

	function changeFieldsRequirements(bool) {
		$('#new_email').prop('required', bool);
		$('#new_firstname').prop('required', bool);
		$('#new_lastname').prop('required', bool);
		$('#new_type').prop('required', bool);
	}

	function updateAllPrices() {
		$(document).find('.line').each(function() {
			var id = $(this).val();

			updatePrices(id);
			updateBuyingPrices(id);
		});
	}

	function updateBuyingPrices(line_id) {
		var pa = parseFloat($('#pa_'+line_id).val());
		var fees = parseFloat($('#fees_'+line_id).val());
		var total = pa + fees;

		$('#pa_with_fees_'+line_id).html(total.toFixed(2)+" €");
	}

	function updatePrices(line_id) {
		var quantity = parseInt($('#quantity_'+line_id).val());
		var pv = parseFloat($('#pv_'+line_id).val());
		var ecotax = parseFloat($('#ecotax_'+line_id).val());

		var unit = pv + ecotax;
		var total_without_ec = pv * quantity;
		var total_with_ec = unit * quantity;

		$('#unit_'+line_id).html(unit.toFixed(2)+" €");
		$('#total_without_ec_'+line_id).html(total_without_ec.toFixed(2)+" €");
		$('#total_with_ec_'+line_id).html(total_with_ec.toFixed(2)+" €");
	}

</script>