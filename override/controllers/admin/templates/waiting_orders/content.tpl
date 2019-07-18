<form method="post">
	<div class="panel">
		<div class="panel-heading">{l s="Configuration"}</div>
		<div class="row">
			<div class="col-lg-4">
				<div class="form-group">
					<label for="CONFIG_OPTION_ID">{l s="Option à rechercher"}</label>
					<select class="form-control" name="CONFIG_OPTION_ID">
						<option value="0">-</option>
						{foreach from=$options item=option}
							<option value="{$option->id}" {if $option->id == $CONFIG_OPTION_ID}selected{/if}>{$option->name}</option>
						{/foreach}
					</select>
				</div>
			</div>
		</div>

			<label style="margin-top: 10px">{l s="Etats à éxclure de la recherche"}</label>
			<br />
			{foreach $states as $state}
				{assign var=selected value=$state.id_order_state|in_array:$PRINT_EXCLUDE_STATES}
				<div style="display:inline-block; background-color:{if $selected}orange{else}grey{/if}; color:white; padding:5px; margin-right:5px; margin-top:5px">
					<input type="checkbox" name="PRINT_EXCLUDE_STATES[]" value="{$state.id_order_state}" style="margin-top:0px; vertical-align:middle" {if $selected}checked{/if}> 
					&nbsp; <b>{$state.name}</b>
				</div>
			{/foreach}

		<div class="panel-footer text-right">
			<button type="submit" class="btn btn-success">
				<i class="process-icon-save"></i> <b>{l s="Exporter" d='Shop.Theme.Actions'}</b>
			</button>
		</div>
	</div>
</form>

{if !empty($orders)}
	<div class="panel">
		<div class="panel-heading">{l s="Liste des commandes"} ({$orders|@count})</div>
		<table class="table">
			<thead>
				<tr class="bg-primary">
					<th class="text-center"><b>{l s="ID"}</b></th>
					<th class="text-center"><b>{l s="Référence commande"}</b></th>
					<th class="text-center"><b>{l s="Référence interne"}</b></th>
					<th class="text-center"><b>{l s="Client"}</b></th>
					<th class="text-center"><b>{l s="Etat"}</b></th>
					<th class="text-center"><b>{l s="Date"}</b></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				{foreach $orders as $order}
					<tr>
						<td class="text-center"><b>{$order->id}</b></td>
						<td class="text-center">{$order->reference|default:'-'}</td>
						<td class="text-center">{$order->internal_reference|default:'-'}</td>
						<td class="text-center">{$order->getCustomer()->firstname} {$order->getCustomer()->lastname}</td>
						<td class="text-center">
							<span class="label color_field" style="background-color:{$order->getState()->color};color:white">
								{$order->getState()->name}
							</span>
						</td>
						<td class="text-center">{$order->date_add|date_format:'d/m/Y'}</td>
						<td class="text-right">
							<div class="btn-group">
								<a href="{$link->getAdminLink('AdminOrders')|escape:'htmlall':'UTF-8'}&vieworder&id_order={$order->id}" class="btn btn-default" title="{l s='Détails'}">
									<i class="icon-edit"></i>
								</a>
								{if $order->invoice_number}
									<a href="{$link->getAdminLink('AdminPdf')|escape:'htmlall':'UTF-8'}&submitAction=generateInvoicePDF&id_order={$order->id}" class="btn btn-default" title="{l s='Facture'}">
										<i class="icon-file"></i>
									</a>
								{/if}
								<a href="{$link->getAdminLink('AdminActivisOrderExtendsStatus')|escape:'htmlall':'UTF-8'}&submitAction=displayLetter&id_order={$order->id}" class="btn btn-default" title="{l s='Courrier'}">
									<i class="icon-envelope"></i>
								</a>
						</div>
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
{/if}