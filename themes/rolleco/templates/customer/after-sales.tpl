{extends file='customer/page.tpl'}

{block name='page_title'}
	{l s="Mes demandes de contact"}
{/block}

{block name='page_content_container'}
	
	{if $requests|count > 0}
		<table class="table combinations-table vertical-align">
			<thead>
				<th class="bold">{l s="Numéro de dossier"}</th>
				<th class="bold">{l s="Message"}</th>
				<th class="bold text-center">{l s="Statut"}</th>
				<th class="bold text-center">{l s="Date"}</th>
				<th></th>
			</thead>
			<tbody>
				{foreach from=$requests item=request}
					<tr>
						<td class="bold">XXX</td>
						<td>{$request->content|truncate:100:"..."}</td>
						<td class="bold text-center">EN COURS</td>
						<td class="text-center">{$request->date_add|date_format:'d/m/Y H:i:s'}</td>
						<td class="text-center">
							<a href="" class="btn btn-xs btn-default">
								<span class="fa fa-edit"></span>
							</a>
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	{else}
		<div class="alert alert-info">
			<b>{l s="Vous n'avez pas de demande en cours."}</b>
			<br />
			<a href="{$link->getPageLink('AfterSaleRequest')}">
				{l s="Vous pouvez nous contacter via le formulaire en cliquant ici."}
			</a>
		</div>
	{/if}

{/block}