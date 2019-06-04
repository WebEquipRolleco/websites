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
		{if $quotations|count}
			<form method="post">
				<table id="data_table" class="table table-striped table-hover">
					<thead>
						<tr>
							<th><b>{l s='ID' d='Shop.Theme.Labels'}</b></th>
							<th><b>{l s='Référence' d='Shop.Theme.Labels'}</b></th>
							<th class="text-center"><b>{l s='Etat' d='Shop.Theme.Labels'}</b></th>
							<th class="text-center"><b>{l s='Client' d='Shop.Theme.Labels'}</b></th>
							<th class="text-center"><b>{l s='Créateur' d='Shop.Theme.Labels'}</b></th>
							<th class="text-center"><b>{l s='Statut' d='Shop.Theme.Labels'}</b></th>
							<th class="text-center"><b>{l s='date de création' d='Shop.Theme.Labels'}</b></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						{foreach $quotations as $quotation}
							<tr>
								<td>{$quotation->id}</td>
								<td>{$quotation->reference}</td>
								<td class="text-center">
									<span class="label label-{$quotation->getStatusClass()}">
										<b>{$quotation->getStatusLabel()}</b>
									</span>
								</td>
								<td class="text-center">
									{if $quotation->id_customer}
										{$quotation->getCustomer()->firstname} {$quotation->getCustomer()->lastname}
									{else}
										-
									{/if}
								</td>
								<td class="text-center">{$quotation->getEmployee()->firstname} {$quotation->getEmployee()->lastname}</td>
								<td class="text-center">
									{if $quotation->active}
										<span class="label label-success"><i class="icon-check"></i></span>
									{else}
										<span class="label label-danger"><i class="icon-times"></i></span>
									{/if}
								</td>
								<td class="text-center">{$quotation->date_add|date_format:'d/m/Y'}</td>
								<td class="text-right">
									<div class="btn-group">
										<a href="" class="btn btn-xs btn-default">
											<i class="icon-file"></i>
										</a>
										<a href="" class="btn btn-xs btn-default">
											<i class="icon-shopping-cart"></i>
										</a>
									</div>
									<div class="btn-group">
										<a href="{$link->getAdminLink('AdminQuotations')}&details&id={$quotation->id}" class="btn btn-xs btn-default">
											<i class="icon-edit"></i>
										</a>
										<button type="submit" class="btn btn-xs btn-default" name="remove_quotation" value="{$quotation->id}">
											<i class="icon-trash"></i>
										</button>
									</div>
								</td>
							</tr>
						{/foreach}
					</tbody>
				</table>
			</form>
		{else}
			<div class="alert alert-info">
				<b>{l s="Aucun devis enregistré pour le moment" mod='webequip_partners'}</b>
			</div>
		{/if}
	</div>

</div>

<script>
	$(document).ready(function() {
		$('#data_table').dataTable();
	});
</script>