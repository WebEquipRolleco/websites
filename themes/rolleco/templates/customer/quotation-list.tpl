{extends file='customer/page.tpl'}

{block name='page_title'}
	{l s="Mes devis"}
{/block}

{block name='page_content_container'}
	<h6 class="text-center">
    	{l s="Vous trouverez ici vos devis en cours"}
	</h6>

	<table class="table combinations-table table-labeled">
		<thead>
			<tr>
				<th>{l s='Référence' d='Shop.Theme.Checkout'}</th>
				<th>{l s='Statut' d='Shop.Theme.Checkout'}</th>
				<th>{l s='Produits' d='Shop.Theme.Checkout'}</th>
				<th>{l s='Montant' d='Shop.Theme.Checkout'}</th>
				<th>{l s="Expiration" d='Shop.Theme.Checkout'}</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$quotations item=quotation}
				<tr>
					<td><b>{$quotation->reference}</b></td>
					<td>
						<span class="label bg-{$quotation->getStatusClass()} btn-block text-center">
							{$quotation->getStatusLabel()}
						</span>
					</td>
					<td class="text-center">
						{$quotation->getProducts()|count}
					</td>
					<td class="text-center">
						{Tools::displayPrice($quotation->getPrice())}
					</td>
					<td class="text-center">
						{if $quotation->date_end}
							{$quotation->date_end|date_format:'d/m/Y'}
						{else}
							-
						{/if}
					</td>
					<td class="text-center">
						<div class="btn-group">
							<a href="{$link->getPageLink('QuotationDetail')}?reference={$quotation->reference}" class="btn btn-xs btn-default" title="{l s='Voir le devis'}">
								<span class="fa fa-edit"></span>
							</a>
							{if $quotation->isValid()}
								<button type="button" class="btn btn-xs btn-default" title="{l s='Télécharger au format PDF'}">
									<span class="fa fa-download"></span>
								</button>
							{/if}
						</div>
						{if $quotation->isValid()}
							&nbsp;
							<div class="btn-group">
								<a href="?accept={$quotation->reference}" class="btn btn-xs btn-success" title="{l s='Ajouter au panier'}">
									<span class="fa fa-cart-plus"></span>
								</a>
								<a href="?refuse={$quotation->reference}" class="btn btn-xs btn-danger" title="{l s='Refuser le devis'}">
									<span class="fa fa-ban"></span>
								</a>
							</div>
						{/if}
					</td>
				</tr>
			{foreachelse}
				<tr>
					<td colspan="6">
						{l s="Vous n'avez aucun devis en cours."}
					</td>
				</tr>	
			{/foreach}
		</tbody>
	</table>

{/block}