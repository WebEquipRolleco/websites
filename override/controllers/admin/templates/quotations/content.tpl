{if isset($alert)}
	<div class="alert alert-{$alert.type}">
		<b>{$alert.message}</b>
	</div>
{/if}

<div class="panel">
	<div class="panel-heading">
		{l s="Devis en cours" mod='webequip_partners'}
		<span class="panel-heading-action">
			<a href="{$link->getAdminLink('AdminQuotations')}&details" id="new_quotation" class="list-toolbar-btn" title="{l s='New' d='Shop.Theme.Actions'}">
				<i class="process-icon-new"></i>
			</a>
		</span>
	</div>
	<div class="panel-content">
		<form method="post">
			<table id="data_table" class="table table-striped table-hover">
				<thead>
					<tr>
						<th><b>{l s='Référence' d='Shop.Theme.Labels'}</b></th>
						<th class="text-center"><b>{l s='Etat' d='Shop.Theme.Labels'}</b></th>
						<th class="text-center"><b>{l s='Client' d='Shop.Theme.Labels'}</b></th>
						<th class="text-center"><b>{l s='Créateur' d='Shop.Theme.Labels'}</b></th>
						<th class="text-center"><b>{l s='date de création' d='Shop.Theme.Labels'}</b></th>
						<th class="text-center"><b>{l s='Provenance' d='Shop.Theme.Labels'}</b></th>
						<th class="text-center"><b>{l s='Statut' d='Shop.Theme.Labels'}</b></th>
						<th></th>
					</tr>
					<tr style="background-color:#f2f2f2">
						<th>
							<input type="text" id="search_reference" class="form-control">
						</th>
						<th>
							<select id="search_state" class="form-control">
								<option value="">-</option>
								{foreach from=Quotation::getStates() key=id item=name}
									<option value="{$id}">{$name}</option>
								{/foreach}
							</select>
						</th>
						<th>
							<select id="search_customer" class="form-control">
								<option value="">-</option>
								{foreach from=Customer::getCustomers() item=customer}
									<option value="{$customer.id_customer}">{$customer.firstname} {$customer.lastname}</option>
								{/foreach}
							</select>
						</th>
						<th>
							<select id="search_employee" class="form-control">
								<option value="">-</option>
								{foreach from=Employee::getEmployees() item=employee}
									<option value="{$employee.id_employee}">{$employee.firstname} {$employee.lastname}</option>
								{/foreach}
							</select>
						</th>
						<th>
							<input type="date" id="search_date" class="form-control">
						</th>
						<th></th>
						<th></th>
						<th class="text-right">
							<button type="button" id="eraze" class="btn btn-default" title="{l s='Effacer' d='Shop.Theme.Actions'}">
								<i class="icon-refresh"></i>
							</button>
							<button type="button" id="search" class="btn btn-primary">
								<b>{l s='Rechercher' d='Shop.Theme.Actions'}</b>
							</button>
						</th>
					</tr>
				</thead>
				<tbody id="table_body">
					<!-- CONTENU AJAX -->
				</tbody>
			</table>
		</form>
	</div>

</div>

<script>
	$(document).ready(function() {
		loadQuotations();

		// Confirmation copie
		$('.copy-quotation').on('click', function(e) {
			if(!confirm("Etes-vous sûr de vouloir copier ce devis ?"))
				e.preventDefault();
		});

		// Confirmation suppression
		$('.remove-quotation').on('click', function(e) {
			if(!confirm("Etes-vous sûr de vouloir supprimer ce devis ?"))
				e.preventDefault();
		});

		// Effacer les filtres et charger les devis
		$('#eraze').on('click', function() {
			$('#search_reference').val(null);
			$('#search_state').val(null);
			$('#search_customer').val(null);
			$('#search_employee').val(null);
			$('#search_date').val(null);

			loadQuotations();
		});

		// Charger les devis avec les filtres
		$('#search').on('click', function() {
			loadQuotations();
		});

		// Chargement des devis
		function loadQuotations() {
			$.ajax({
				url: "{$link->getAdminLink('AdminQuotations')}",
				data: {
					'action' : 'load_quotations',
					'reference' : $('#search_reference').val(),
					'state' : $('#search_state').val(),
					'customer' : $('#search_customer').val(),
					'employee' : $('#search_employee').val(),
					'date' : $('#search_date').val(),
					'ajax' : 1
				},
					//dataType: 'json',
					success: function(response) {
						$('#table_body').html(response);
					}
				});
		}

	});
</script>