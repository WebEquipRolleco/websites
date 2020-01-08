<div class="row">
	<div class="col-lg-5">
		<table class="table">
			<thead>
				<tr class="bg-primary">
					<th></th>
					<th class="text-center"><b>{l s="Ancienne BDD" mod='webequip_transfer'}</b></th>
					<th class="text-center"><b>{l s="Nouvelle BDD" mod='webequip_transfer'}</b></th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$data_list.data item=row}
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
	<input type="hidden" name="transfer_name" value="{$transfer_name}">
	{if $data_list.updatable}
	<span class="switch prestashop-switch fixed-width-lg">
		<input type="radio" name="eraze" id="eraze_on" value="1" checked>
		<label for="eraze_on">{l s='Effacer' mod='webequip_transfer'}</label>
		<input type="radio" name="eraze" id="eraze_off" value="0">
		<label for="eraze_off">{l s='Garder' mod='webequip_transfer'}</label>
		<a class="slide-button btn"></a>
	</span>
	<br />
	{else}
		<input type="hidden" name="eraze" value="1">
	{/if}
	<div class="alert alert-danger">
		<div><b>ATTENTION !</b> {l s="Les actions effectuées pourraient être irréversibles."  mod="webequip_transfer"}</div>
		<button type="submit" class="btn btn-danger">
			<b>{l s="Transférer les données" mod="webequip_transfer"}</b>
		</button>
	</div>
</form>