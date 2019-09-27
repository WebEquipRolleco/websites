{extends file='customer/page.tpl'}

{block name='page_title'}
	{l s="Mes devis"}
{/block}

{block name='page_content_container'}

	{if !empty($highlights)}
		{foreach from=$highlights item=quotation}
			<table class="table combinations-table table-labeled margin-bottom-15">
				<thead>
					<tr>
						<th colspan="{if $quotation->date_end}5{else}4{/if}">{$quotation->reference}</th>
				</thead>
				<tbody>
					{if $quotation->comment}
						<tr>
							<td colspan="{if $quotation->date_end}5{else}4{/if}" class="text-center">
								{$quotation->comment}
							</td>
						</tr>
					{/if}
					<tr>
						<td>
							<span class="label bg-{$quotation->getStatusClass()} btn-block text-center">
								<b>{$quotation->getStatusLabel()}</b>
							</span>
						</td>
						<td class="text-center">
							<b>{$quotation->getProducts()|count} {l s="Produit(s)"}</b>
						</td>
						<td class="text-center">
							{l s="Prix total de"} <b>{Tools::displayPrice($quotation->getPrice())}</b>
						</td>
						{if $quotation->date_end}
							<td class="text-center">
								{l s="Valable jusqu'au"} <b>{$quotation->date_end|date_format:'d/m/Y'}</b>
							</td>
						{/if}
						<td class="text-center">
							<div class="btn-group">
								<a href="{$link->getPageLink('QuotationDetail')}?reference={$quotation->reference}" class="btn btn-xs btn-default" title="{l s='Voir le devis'}">
									<span class="fa fa-edit"></span>
								</a>
								{if $quotation->isValid()}
									<a href="{$link->getPageLink('QuotationList')}?download={$quotation->reference}" target="_blank" class="btn btn-xs btn-default" title="{l s='Télécharger au format PDF'}">
										<span class="fa fa-download"></span>
									</a>
								{/if}
							</div>
							{if $quotation->isValid()}
								&nbsp;
								<div class="btn-group">
									{if !QuotationAssociation::has($quotation->id)}
										<a href="{$link->getPageLink('QuotationList&accept='|cat:$quotation->reference)}" class="btn btn-xs btn-success hvr-icon-pulse-grow" title="{l s='Ajouter au panier'}">
											<span class="fa fa-cart-plus hvr-icon"></span>
										</a>
									{/if}
									<a href="{$link->getPageLink('QuotationList')}?refuse={$quotation->reference}" class="btn btn-xs btn-danger hvr-icon-buzz-out" title="{l s='Refuser le devis'}">
										<span class="fa fa-ban hvr-icon"></span>
									</a>
								</div>
							{/if}
						</td>
					</tr>
				</tbody>
			</table>
		{/foreach}
	{/if}

	{if !empty($quotations)}
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
						<td {if $quotation->new}class="bg-warning"{/if}>
							<b>{$quotation->reference}</b>
						</td>
						<td {if $quotation->new}class="bg-warning"{/if}>
							<span class="label bg-{$quotation->getStatusClass()} btn-block text-center">
								{$quotation->getStatusLabel()}
							</span>
						</td>
						<td class="text-center {if $quotation->new}bg-warning{/if}">
							{$quotation->getProducts()|count}
						</td>
						<td class="text-center {if $quotation->new}bg-warning{/if}">
							{Tools::displayPrice($quotation->getPrice())}
						</td>
						<td class="text-center {if $quotation->new}bg-warning{/if}">
							{if $quotation->date_end}
								{$quotation->date_end|date_format:'d/m/Y'}
							{else}
								-
							{/if}
						</td>
						<td class="text-center {if $quotation->new}bg-warning{/if}">
							<div class="btn-group">
								<a href="{$link->getPageLink('QuotationDetail')}?reference={$quotation->reference}" class="btn btn-xs btn-default" title="{l s='Voir le devis'}">
									<span class="fa fa-edit"></span>
								</a>
								{if $quotation->isValid()}
									<a href="{$link->getPageLink('QuotationList')}?download={$quotation->reference}" target="_blank" class="btn btn-xs btn-default" title="{l s='Télécharger au format PDF'}">
										<span class="fa fa-download"></span>
									</a>
								{/if}
							</div>
							{if $quotation->isValid()}
								&nbsp;
								<div class="btn-group">
									{if !QuotationAssociation::has($quotation->id)}
										<a href="{$link->getPageLink('QuotationList&accept='|cat:$quotation->reference)}" class="btn btn-xs btn-success hvr-icon-pulse-grow" title="{l s='Ajouter au panier'}">
											<span class="fa fa-cart-plus hvr-icon"></span>
										</a>
									{/if}
									<a href="{$link->getPageLink('QuotationList')}?refuse={$quotation->reference}" class="btn btn-xs btn-danger hvr-icon-buzz-out" title="{l s='Refuser le devis'}">
										<span class="fa fa-ban hvr-icon"></span>
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
	{/if}
	
{/block}