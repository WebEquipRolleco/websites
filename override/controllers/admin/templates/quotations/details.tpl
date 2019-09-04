{if isset($validation)}
	<div class="alert alert-success">
		<b>{$validation}</b>
	</div>
{/if}

<form method="post">
	<input type="hidden" name="id" value="{$quotation->id}">

	<div class="panel">
		<div class="row">
		<div class="col-lg-6">
			<span class="label label-default">
				<strong>{$quotation->reference}</strong>
			</span>
			&nbsp;
			<span class="label label-{$quotation->getStatusClass()} border-secondary">
				<b>{l s='Devis' d='Admin.Labels'} {$quotation->getStatusLabel()}</b>
			</span>
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
					<label for="id_shop">{l s="Boutique"}</label>
					<input type="text" class="form-control" value="{$quotation->getShop()->name}" disabled>
					{*<select name="quotation[id_shop]" class="form-control" disabled>
						{foreach $shops as $shop}
							<option value="{$shop.id_shop}" {if $quotation->id_shop == $shop.id_shop}selected{/if}>{$shop.name}</option>
						{/foreach}
					</select>*}
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
		</div>

		<div class="col-lg-6">
			<div class="panel">
				<div class="panel-heading">
					{l s="Affichage"}
				</div>
				<div class="form-group">
					<label for="comment">{l s="Commentaire"}</label>
					<textarea rows="5" name="quotation[comment]" id="comment" class="form-control">{$quotation->comment}</textarea>
				</div>
				<div class="form-group">
					<label for="details">{l s="Information client"}</label>
					<textarea rows="5" name="quotation[details]" id="details" class="form-control">{$quotation->details}</textarea>
				</div>
				<div class="row">
					<div class="col-lg-6">
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
					<div class="col-lg-6">
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
			<div class="panel">
				<div class="panel-heading">
					{l s="Options"}
				</div>
				{assign var=options value=OrderOption::getOrderOptions()}
				{if !empty($options)}
					<table class="table" width="100%">
						<tbody>
							{foreach from=$options item=option}
								{assign var=selected value=$option->id|in_array:$quotation->getOptions()}
								<tr>
									<td>
										{$option->name}
									</td>
									<td class="text-right">
										<span class="switch prestashop-switch fixed-width-lg" style="float:right">
											<input type="radio" name="quotation[options][{$option->id}]" id="option_{$option->id}_on" value="{$option->id}" {if $selected}checked{/if}>
											<label for="option_{$option->id}_on">{l s='Oui' d='Admin.Labels'}</label>
											<input type="radio" name="quotation[options][{$option->id}]" id="option_{$option->id}_off" value="" {if !$selected}checked{/if}>
											<label for="option_{$option->id}_off">{l s='Non' d='Admin.Labels'}</label>
											<a class="slide-button btn"></a>
										</span>
									</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				{else}
					<div class="alert alert-info">
						{l s="Aucune option de commande disponible"}
					</div>
				{/if}
			</div>
		</div>

		<div class="col-lg-3">
			<div class="panel">
				<div class="panel-heading">
					{l s="Attribution"}
				</div>
				<div class="form-group">
					<label for="id_customer">{l s="Client"}</label>
					<select name="quotation[id_customer]" class="form-control select2">
						<option value="">{l s='Choisir' d='Admin.Labels'}</option>
						{foreach $customers as $customer}
							<option value="{$customer.id_customer}" {if $quotation->id_customer == $customer.id_customer}selected{/if}>
								{$customer.firstname} {$customer.lastname} ({$customer.email})
							</option>
						{/foreach}
					</select>
				</div>
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
			<div class="panel">
				<div class="panel-heading">
					{l s="Contact"}
				</div>
				<div class="form-group">
					<label for="email">{l s="E-mails"}</label>
					<span class="text-muted pull-right">{l s="Séparés par une virgule"}</span>
					<input type="text" name="quotation[email]" id="email" class="form-control" value="{$quotation->email}">
				</div>
				<div class="form-group">
					<label for="hidden_emails">{l s="E-mails (CC)"}</label>
					<span class="text-muted pull-right">{l s="Séparés par une virgule"}</span>
					<input type="text" name="quotation[hidden_emails]" id="hidden_emails" class="form-control" value="{$quotation->hidden_emails}">
				</div>
				<hr />
				<div class="form-group">
					<label for="phone">{l s="Téléphone"}</label>
					<input type="text" name="quotation[phone]" id="phone" class="form-control" value="{$quotation->phone}">
				</div>
				<div class="form-group">
					<label for="fax">{l s="Fax"}</label>
					<input type="text" name="quotation[fax]" id="fax" class="form-control" value="{$quotation->fax}">
				</div>
			</div>
		</div>

	</div>
</form>

{if $quotation->id}
	<form enctype="multipart/form-data" method="post" id="save_products_form">
		<div class="row">
			<div class="col-lg-12">
				<div class="panel">
					<div class="panel-heading">
						{l s="Liste des produits"}
						<span class="label label-warning" style="margin-left:25px; font-size:14px">
							<b>Total HT :</b> {Tools::displayPrice($quotation->getPrice())}
						</span>
						<span class="label label-warning" style="margin-left:5px; font-size:14px">
							<b>TVA :</b> {Tools::displayPrice($quotation->getPrice(true) - $quotation->getPrice())}
						</span>
						<span class="label label-warning" style="margin-left:5px; font-size:14px">
							<b>Total TTC :</b> {Tools::displayPrice($quotation->getPrice(true))}
						</span>
						<span class="label label-warning" style="margin-left:5px; font-size:14px">
							<b>Eco-tax :</b> {Tools::displayPrice($quotation->getEcoTax())}
						</span>
						<span class="label label-warning" style="margin-left:25px; font-size:14px">
							<b>Marge :</b> {Tools::displayPrice($quotation->getMargin())}
						</span>
						<span class="label label-warning" style="margin-left:5px; font-size:14px">
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
							<th width="85px"></th>
							<th class="text-center"><b>{l s="Produit" d="Admin.Labels"}</b></th>
							<th width="10%" class="text-center"><b>{l s="Fournisseur" d="Admin.Labels"}</b></th>
							<th width="10%" class="text-center"><b>{l s="Prix" d="Admin.Labels"}</b></th>
							<th width="5%" class="text-center"><b>{l s="Total" d="Admin.Labels"}</b></th>
							<th width="5%" class="text-center"><b>{l s="Marge" d="Admin.Labels"}</b></th>
							<th class="text-center"><b>{l s="Commentaire" d="Admin.Labels"}</b></th>
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
			{if $quotation->id}
				<hr />
				<b>{l s="Lien du devis : " d='Admin.Labels'} </b>
				<a href="{$quotation->getLink()}" class="quotation-link" target="_blank">
					{$quotation->getLink()}
				</a>
			{/if}
		</div>
	</form>

{/if}

<script>
	$(document).ready(function() {
		
		$('.select2').select2({
			width: 'auto'
		});

		$('.quotation-link').on('click', function(e) {
			if(!confirm('Etes-vous sûr de vouloir ajouter le devis a votre panier ?'))
				e.preventDefault();
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
					$('#close_modal').click();
				}
			});
		});

	});
</script>