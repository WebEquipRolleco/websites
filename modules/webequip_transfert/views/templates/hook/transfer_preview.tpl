<div class="row">
	<div class="col-lg-4">
		<table class="table">
			<thead>
				<tr class="bg-primary">
					<th></th>
					<th class="text-center"><b>{l s="Ancienne BDD" mod='webequip_transfert'}</b></th>
					<th class="text-center"><b>{l s="Nouvelle BDD" mod='webequip_transfert'}</b></th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$data_list item=row}
					<tr>
						{foreach from=$row item=data name=line}
							{if $smarty.foreach.line.first}
								<td><b>{$data}</b></td>
							{else}
								<td class="text-center">{$data}</td>
							{/if}
						{/foreach}
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
</div>

<br />

<form id="transfer_form" method="post">
	<input type="hidden" name="ajax" value="1">
	<input type="hidden" name="action" value="load_data">
	<input type="hidden" name="transfert_name" value="{$transfert_name}">
	<div class="alert alert-danger">
		<div><b>ATTENTION !</b> Le contenu actuel des tables sera effacé.</div>
		<button type="submit" class="btn btn-danger">
			<b>{l s="Effacer le contenu et transférer les données"}</b>
		</button>
	</div>
</form>