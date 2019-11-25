{extends file='customer/page.tpl'}

{block name='page_title'}
	{l s="Mon SAV"}
{/block}

{block name='page_content_container'}
	<table class="table combinations-table vertical-align">
		<thead>
			<th class="bold">{l s="Numéro de dossier"}</th>
			<th class="bold text-center">{l s="Statut"}</th>
			<th class="bold text-center">{l s="Messages"}</th>
			<th class="bold text-center">{l s="Date"}</th>
			<th></th>
		</thead>
		<tbody>
			{foreach from=$requests item=request}
				<tr>
					<td class="text-center bold {if $request->hasNewMessageForCustomer()}bg-warning{/if}">{$request->reference}</td>
					<td class="text-center {if $request->hasNewMessageForCustomer()}bg-warning{/if}">
						<span class="label-{$request->getStatusClass()} bold">
							{$request->getStatusLabel()}
						</span>
					</td>
					<td class="text-center {if $request->hasNewMessageForCustomer()}bg-warning{/if}">{$request->getMessages(true)|@count}</td>
					<td class="text-center {if $request->hasNewMessageForCustomer()}bg-warning{/if}">{$request->date_add|date_format:'d/m/Y'}</td>
					<td class="text-center {if $request->hasNewMessageForCustomer()}bg-warning{/if}">
						<a href="{$link->getPageLink('AfterSales')}?sav={$request->reference}" class="btn btn-xs btn-default" title="{l s='Voir mon ticket'}">
							<span class="fa fa-edit"></span>
						</a>
					</td>
				</tr>
			{/foreach}
			<tr>
				<td colspan="5" class="text-center">
					<a href="{$link->getPageLink('AfterSaleRequest')}" class="btn btn-alt bold">
						{l s="Déclarer un nouveau SAV"}
					</a>
				</td>
			</tr>
		</tbody>
	</table>
{/block}