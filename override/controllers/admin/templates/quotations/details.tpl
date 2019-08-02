{if isset($validation)}
	<div class="alert alert-success">
		<b>{$validation}</b>
	</div>
{/if}

<form method="post">
	<input type="hidden" name="id" value="{$quotation->id}">

	<div class="panel">
		<div class="panel-content">
			<span class="label label-{$quotation->getStatusClass()} border-secondary">
				<b>{l s='Devis' d='Shop.Theme.Labels'} {$quotation->getStatusLabel()}</b>
			</span>
			<span class="pull-right">
				<a href="{$link->getAdminLink('AdminQuotations')}">{l s='Retour' d='Shop.Theme.Labels'}</a>
				&nbsp;
				<button type="submit" class="btn btn-xs btn-primary">
					<b>{l s='Enregistrer' d='Shop.Theme.Labels'}</b>
				</button>
			</span>
		</div>
	</div>

	<div class="row">

		<div class="col-lg-3">
			
			<div class="panel">
				<div class="panel-heading">
					{l s="Gestion" mod='webequip_quotation'}
				</div>
				<div class="panel-content">
					<div class="form-group">
						<label for="status">{l s="Etat" mod='webequip_quotation'}</label>
						<select name="quotation[status]" class="form-control">
							{foreach $states as $id => $name}
								<option value="{$id}" {if $quotation->status == $id}selected{/if}>{$name}</option>
							{/foreach}
						</select>
					</div>
					<div class="form-group">
						<label for="id_employee">{l s="Créateur" mod='webequip_quotation'}</label>
						<select name="quotation[id_employee]" class="form-control select2">
							{foreach $employees as $employee}
								<option value="{$employee.id_employee}" {if $quotation->id_employee == $employee.id_employee}selected{/if}>
									{$employee.firstname} {$employee.lastname}
								</option>
							{/foreach}
						</select>
					</div>
				</div>
			</div>

			<div class="panel">
				<div class="panel-heading">
					{l s="Validité" mod='webequip_quotation'}
				</div>
				<div class="panel-content">
					<span class="switch prestashop-switch fixed-width-lg" style="margin-left:auto; margin-right:auto; margin-bottom:20px">
						<input type="radio" name="quotation[active]" id="active_on" value="1" {if $quotation->active}checked{/if}>
						<label for="active_on">{l s='Active' d='Shop.Theme.Labels'}</label>
						<input type="radio" name="quotation[active]" id="active_off" value="0" {if !$quotation->active}checked{/if}>
						<label for="active_off">{l s='Inactive' d='Shop.Theme.Labels'}</label>
						<a class="slide-button btn"></a>
					</span>
					<div class="form-group">
						<label for="date_begin">{l s="Date de début" mod='webequip_quotation'} <em class="text-danger">*</em></label>
						<input type="date" name="quotation[date_begin]" id="date_begin" class="form-control" {if $quotation->date_begin}value="{$quotation->date_begin|date_format:'Y-m-d'}"{/if} required>
					</div>
					<div class="form-group">
						<label for="date_end">{l s="Date de fin" mod='webequip_quotation'} <em class="text-danger">*</em></label>
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
							<label for="date_recall">{l s="Date de rappel" mod='webequip_quotation'}</label>
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
		</div>

		<div class="col-lg-6">
			<div class="panel">
				<div class="panel-heading">
					{l s="Affichage" mod='webequip_quotation'}
				</div>
				<div class="panel-content">
					<div class="form-group">
						<label for="reference">{l s="Référence" mod='webequip_quotation'} <em class="text-danger">*</em></label>
						<div class="pull-right">
							<button type="button" class="btn btn-xs btn-primary change-reference">
								<i class="icon-refresh"></i>
							</button>
						</div>
						<input type="text" name="quotation[reference]" id="reference" class="form-control" value="{$quotation->reference}" required>
					</div>
					<div class="form-group">
						<label for="comment">{l s="Commentaire" mod='webequip_quotation'}</label>
						<textarea rows="5" name="quotation[comment]" id="comment" class="form-control">{$quotation->comment}</textarea>
					</div>
					<div class="form-group">
						<label for="details">{l s="Information client" mod='webequip_quotation'}</label>
						<textarea rows="5" name="quotation[details]" id="details" class="form-control">{$quotation->details}</textarea>
					</div>
					<div class="row">
						<div class="col-lg-6">
							<span class="switch prestashop-switch fixed-width-lg" style="margin-left:auto; margin-right:auto; margin-bottom:20px" title="{l s='Le client verra sont devis en tant que nouveauté jusqu\'à son ouverture.'}">
								<input type="radio" name="quotation[new]" id="new_on" value="1" {if $quotation->new}checked{/if}>
								<label for="new_on">{l s='Nouveau' d='Shop.Theme.Labels'}</label>
								<input type="radio" name="quotation[new]" id="new_off" value="0" {if !$quotation->new}checked{/if}>
								<label for="new_off">{l s='Non' d='Shop.Theme.Labels'}</label>
								<a class="slide-button btn"></a>
							</span>
						</div>
						<div class="col-lg-6">
							<span class="switch prestashop-switch fixed-width-lg" style="margin-left:auto; margin-right:auto; margin-bottom:20px" title="{l s='Le devis sera mis en valeur pour attirer l\'attention du client.'}">
								<input type="radio" name="quotation[highlight]" id="highlight_on" value="1" {if $quotation->highlight}checked{/if}>
								<label for="highlight_on">{l s='Valoriser' d='Shop.Theme.Labels'}</label>
								<input type="radio" name="quotation[highlight]" id="highlight_off" value="0" {if !$quotation->highlight}checked{/if}>
								<label for="highlight_off">{l s='Non' d='Shop.Theme.Labels'}</label>
								<a class="slide-button btn"></a>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-3">
			<div class="panel">
				<div class="panel-heading">
					{l s="Attribution" mod='webequip_quotation'}
				</div>
				<div class="panel-content">
					<div class="form-group">
						<label for="id_customer">{l s="Client" mod='webequip_quotation'}</label>
						<select name="quotation[id_customer]" class="form-control select2">
							<option value="">{l s='Choisir' d='Shop.Theme.Labels'}</option>
							{foreach $customers as $customer}
								<option value="{$customer.id_customer}" {if $quotation->id_customer == $customer.id_customer}selected{/if}>
									{$customer.firstname} {$customer.lastname} ({$customer.email})
								</option>
							{/foreach}
						</select>
					</div>
					<div class="form-group">
						<label for="origin">{l s="Provenance" mod='webequip_quotation'}</label>
						<select name="quotation[origin]" class="form-control">
							<option value="">{l s='Choisir' d='Shop.Theme.Labels'}</option>
							{foreach $origins as $id => $origin}
								<option value="{$id}" {if $quotation->origin == $id}selected{/if}>
									{$origin}
								</option>
							{/foreach}
						</select>
					</div>
				</div>
			</div>
			<div class="panel">
				<div class="panel-heading">
					{l s="Contact" mod='webequip_quotation'}
				</div>
				<div class="panel-content">
					<div class="form-group">
						<label for="email">{l s="E-mails" mod='webequip_quotation'}</label>
						<span class="text-muted pull-right">{l s="Séparés par une virgule" mod='webequip_quotation'}</span>
						<input type="text" name="quotation[email]" id="email" class="form-control" value="{$quotation->email}">
					</div>
					<div class="form-group">
						<label for="hidden_emails">{l s="E-mails (CC)" mod='webequip_quotation'}</label>
						<span class="text-muted pull-right">{l s="Séparés par une virgule" mod='webequip_quotation'}</span>
						<input type="text" name="quotation[hidden_emails]" id="hidden_emails" class="form-control" value="{$quotation->hidden_emails}">
					</div>
					<hr />
					<div class="form-group">
						<label for="phone">{l s="Téléphone" mod='webequip_quotation'}</label>
						<input type="text" name="quotation[phone]" id="phone" class="form-control" value="{$quotation->phone}">
					</div>
					<div class="form-group">
						<label for="fax">{l s="Fax" mod='webequip_quotation'}</label>
						<input type="text" name="quotation[fax]" id="fax" class="form-control" value="{$quotation->fax}">
					</div>
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
						{l s="Liste des produits" mod='webequip_quotation'}
						<span class="panel-heading-action">
							<a href="" id="new_product" class="list-toolbar-btn" data-toggle="modal" data-target="#new_product_modal" title="{l s='New' d='Shop.Theme.Actions'}">
								<i class="process-icon-new"></i>
							</a>
							<a href="" id="save_products" class="list-toolbar-btn" title="{l s='Save' d='Shop.Theme.Actions'}">
								<i class="process-icon-save"></i>
							</a>
						</span>
					</div>
					<div class="panel-content">
						<table class="table">
							<thead>
								<th width="85px"></th>
								<th width="10%" class="text-center"><b>{l s="Référence" d="Shop.Theme.Actions"}</b></th>
								<th class="text-center"><b>{l s="Produit" d="Shop.Theme.Actions"}</b></th>
								<th width="5%" class="text-center"><b>{l s="Quantité" d="Shop.Theme.Actions"}</b></th>
								<th width="5%" class="text-center"><b>{l s="PA" d="Shop.Theme.Actions"}</b></th>
								<th width="5%" class="text-center"><b>{l s="PV" d="Shop.Theme.Actions"}</b></th>
								<th width="5%" class="text-center"><b>{l s="Marge" d="Shop.Theme.Actions"}</b></th>
								<th class="text-center"><b>{l s="Commentaire" d="Shop.Theme.Actions"}</b></th>
								<th width="5%" class="text-center"><b>{l s="Position" d="Shop.Theme.Actions"}</b></th>
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
		</div>
	</form>

	<form id="new_product_form" action="{$link->getAdminLink('AdminQuotations')}">
		<input type="hidden" name="action" value="add_product">
		<input type="hidden" name="ajax" value="1">
		<input type="hidden" name="id_quotation" value="{$quotation->id}">

		<div class="modal fade" id="new_product_modal" tabindex="-1" role="dialog" aria-hidden="true">
		  	<div class="modal-dialog" role="document">
		    	<div class="modal-content">
		      		<div class="modal-header bg-secondary">
		        		<b>{l s="Nouveau produit" mod="webequip_quotation"}</b>
		      		</div>
		      		<div class="modal-body">
		      			<div class="row">
		      				<div class="col-lg-6">
		      					<div class="form-group">
		      						<select id="id_product" class="form-control select2" name="id_product">
		      							<option value="">{l s="Produit libre"}</option>
		      							{foreach $products as $product}
		      								<option value="{$product.id_product}">{$product.name}</option>
		      							{/foreach}
		      						</select>
		      					</div>
		      					<div id="ajax_details_product"></div>
		      				</div>
		      			</div>
		      		</div>
		      		<div class="modal-footer">
		        		<button id="close_modal" type="button" class="btn btn-xs btn-link" data-dismiss="modal">
		        			{l s='Annuler' d='Shop.Theme.Labels'}
		        		</button>
		        		<button type="submit" class="btn btn-xs btn-success">
		        			<b>{l s='Ajouter' d='Shop.Theme.Labels'}</b>
		        		</button>
		      		</div>
		    	</div>
		  	</div>
		</div>
	</form>
{/if}

<script>
	$(document).ready(function() {
		
		$('.select2').select2({
			width: 'auto'
		});

		$('.change-picture').on('click', function() {
			$($(this).data('id')).click();
		});

		$('.change-reference').on('click', function() {
			$('#reference').val(Date.now());
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

		$('#id_product').on('change', function() {
			
			var id_product = $(this).val();
			$('#ajax_details_product').html(null);

			if(id_product)
				$.ajax({
					url: $('#new_product_form').attr('action'),
					data: {
						'id_product' : id_product,
						'action' : 'product_details',
						'ajax' : 1
					},
					dataType: 'json',
					success: function(response) {
						$('#ajax_details_product').html(response.view);
					}
				});
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