{if !$secure_key || !$selected_state || !$nb_min_days || !$nb_max_days || !$associations|@count}
	<div class="alert alert-danger">
		<b>Erreur(s) :</b>
		<ul>
			{if !$secure_key}<li>La clé de sécurité n'est pas configuré</li>{/if}
			{if !$selected_state}<li>L'état commande à surveiller n'est pas configuré</li>{/if}
			{if !$nb_min_days}<li>Le nombre de jours minimum n'est pas configuré</li>{/if}
			{if !$nb_max_days}<li>Le nombre de jours maximum n'est pas configuré</li>{/if}
			{if !$associations|@count}<li>Aucun fournisseur n'est associé à un employé</li>{/if}
		</ul>
	</div>
{/if}

<div class="alert alert-info">
	<b>Url de configuration de la cron :</b> 
	<a href="https://{$cron_url}" target="_blank">{$cron_url}</a>
</div>

<form method="post">
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i> {l s='Configuration' mod="webequi_supplier"}
		</div>
		<div class="row">
			<div class="col-lg-4">
				<div class="form-group">
					<label>{l s="Clé de sécurité"}</label>
					<input type="text" class="form-control" name="PS_RECALL_SECURE_KEY" value="{$secure_key}" required>
				</div>
				<div class="form-group">
					<label>{l s='Etat commande surveillé'}</label>
					<select class="form-control" name="PS_RECALL_STATE">
						<option value="0">{l s='Choisir'}</option>
						{foreach from=$states item=state}
							<option value="{$state.id_order_state}" {if $selected_state == $state.id_order_state}selected{/if}>
								{$state.name}
							</option>
						{/foreach}
					</select>
				</div>
				<div class="form-group">
					<label>{l s='Nombre de jour minimum avant rappel'}</label>
					<input type="number" class="form-control" name="PS_RECALL_NB_MIN_DAYS" value="{$nb_min_days}">
				</div>
				<div class="form-group">
					<label>{l s='Nombre de jour maximum avant rappel'}</label>
					<input type="number" class="form-control" name="PS_RECALL_NB_MAX_DAYS" value="{$nb_max_days}">
				</div>
			</div>
		</div>
		<div class="panel-footer text-right">
			<button type="submit" class="btn btn-success" name="save_configuration">
				<i class="process-icon-save"></i> <b>{l s="Save" d='Shop.Theme.Actions'}</b>
			</button>
		</div>
	</div>
</form>


<div class="panel">
	<div class="panel-heading">
		<i class="icon-cogs"></i> {l s='Associations' mod="webequi_supplier"}
	</div>
	<form method="post">
		<div class="row">
			<div class="col-lg-4">
				<div class="form-group">
					<select class="form-control" name="new_employee" required>
						<option value="0">{l s='Choisir'}</option>
						{foreach from=$employees item=employee}
							<option value="{$employee.id_employee}">{$employee.firstname} {$employee.lastname}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="form-group">
					<select id="new_suppliers" class="form-control" name="new_suppliers[]" size="1" multiple required>
						<option value="0">{l s='Choisir'}</option>
						{foreach from=$suppliers item=supplier}
							<option value="{$supplier.id_supplier}">{$supplier.name}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="col-lg-4">
				<button class="btn btn-success" name="add_association">{l s='Ajouter' }</button>
			</div>
		</div>	
	</form>
	{if $associations|@count > 0}
		<hr />
		<form method="post">
			<table class="table table-striped">
				<thead>
					<tr>
						<th><b>{l s="Employé"}</b></th>
						<th><b>{l s="Fournisseurs"}</b></th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$associations item=association}
						<tr>
							<td>{$association.employee->firstname} {$association.employee->lastname}</td>
							<td>
								{foreach $association.suppliers as $id => $supplier}
									<button type="submit" class="btn btn-danger" name="remove_association" value="{$id}">
									{$supplier->name} &nbsp; <i class="icon-times" alt="{l s='Supprimer'}"></i>
								</button>
								{/foreach}
							</td>
						</tr>
					{/foreach}
				</tbody>
			</table>
		</form>
	{/if}
</div>

{if $order_details|@count}
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i> {l s='Produits actuellement concernés' mod="webequip_supplier"}
		</div>
		<table class="table table-striped">
			<thead>
				<tr>
					<th><b>{l s='Commande'}</b></th>
					<th><b>{l s='Produit'}</th>
					<th class="text-center"><b>{l s='Fournisseur'}</b></th>
					<th class="text-center"><b>{l s='Employé(s)'}</b></th>
					<th class="text-center"><b>{l s='Date'}</b></th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$order_details item=detail}
					<tr>
						<td>
							<a href="{Link::getAdminLink('adminOrders')}&id_order={$detail.order->id}&vieworder">
								{$detail.order->reference}
							</a>
						</td>
						<td>{$detail.product_name}</td>
						<td class="text-center">{$detail.supplier_name}</td>
						<td class="text-center">
							{foreach from=$detail.employees item=employee}
								<a href="{Link::getAdminLink('adminEmployees')}&id_employee={$employee->id}&updateemployee">
									<span class="label label-default" title="{$employee->email}">
										{$employee->firstname} {$employee->lastname}
									</span>
								</a>
							{/foreach}
						</td>
						<td class="text-center">{$detail.order->date_add|date_format:'d/m/Y'}</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
{/if}

<script>
	$(document).ready(function() {

		var timeout;
		$('#new_suppliers').on('click', function() {
			$("#new_suppliers").attr("size",$("#new_suppliers option").length);
			clearTimeout(timeout);
			timeout = setTimeout(function() {
				$("#new_suppliers").attr("size", 1);	
			}, 10000);
		});
	});
</script>