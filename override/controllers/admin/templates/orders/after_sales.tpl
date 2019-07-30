<div class="panel">
	<div class="panel-heading">
		<i class="icon-comment"></i> {l s="SAV"}
		<span class="panel-heading-action">
			<a href="{$link->getAdminLink('AdminAfterSales')}&addafter_sale&id_order={$order->id}&id_customer={$order->getCustomer()->id}" class="list-toolbar-btn" title="{l s='Nouveau'}">
				<i class="process-icon-new"></i>
			</a>
		</span>
	</div>
	<table class="table">
		<thead>
			<tr class="bg-primary">
				<th><b>{l s="Numero SAV"}</b></th>
				<th class="text-center"><b>{l s="Statut"}</b></th>
				<th class="text-center"><b>{l s="Date"}</b></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			{foreach from=AfterSale::findByOrder($order->id) item=sav}
				<tr>
					<td>
						<b>{$sav->reference}</b>
					</td>
					<td class="text-center">
						<span class="label label-{$sav->getStatusClass()} bold">
							{$sav->getStatusLabel()}
						</span>
					</td>
					<td class="text-center">
						{$sav->date_add|date_format:'d/m/Y'}
					</td>
					<td class="text-right">
						<a href="{$link->getAdminLink('AdminAfterSales')}&id_after_sale={$sav->id}&updateafter_sale" class="btn btn-xs btn-default">
							<i class="icon-edit"></i>
						</a>
					</td>
				</tr>
			{foreachelse}
				<tr>
					<td colspan="4">
						{l s="Aucun SAV n'a été créé pour cette commande."}
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
</div>