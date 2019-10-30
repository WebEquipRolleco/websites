{extends file='customer/page.tpl'}

{block name='page_title'}
	{l s="Mon devis"}
{/block}

{block name='page_content_container'}

	<table class="table combinations-table table-labeled">
		<thead>
			<tr>
				<th colspan="{if $quotation->isValid()}1{else}2{/if}">
					{l s='Devis #'}{$quotation->reference}
				</th>
				{if $quotation->isValid()}
					<th width="160px" class="text-center">
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
					</th>
				{/if}
			</tr>		
		</thead>
		<tbody>
			<tr class="bg-blue">
				<td class="bg-blue text-center">{l s="Statut du devis"}</td>
				<td class="bg-blue text-center">{l s="Fin de validité"}</td>
			</tr>
			<tr>
				<td class="text-center">
					<span class="label bg-{$quotation->getStatusClass()}">
						<b>{$quotation->getStatusLabel()}</b>
					</span>
				</td>
				<td class="text-center">{$quotation->date_end|date_format:'d/m/Y'}</td>
			</tr>
		</tbody>
	</table>

	<table class="table combinations-table">
		<thead>
			<tr>
				<th colspan="5">{l s='Produits' d='Shop.Theme.Checkout'}</th>
			</tr>
			<tr>
				<th class="bg-blue" style="width:85px"></th>
				<th class="bg-blue"></th>
				<th class="bg-blue">{l s='PU' d='Shop.Theme.Checkout'}</th>
				<th class="bg-blue">{l s='Quantité' d='Shop.Theme.Checkout'}</th>
				<th class="bg-blue">{l s='Total' d='Shop.Theme.Checkout'}</th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$quotation->getProducts() item=line}
				<tr>
					<td>
						{if $line->getImageLink()}
							<img src="{$line->getImageLink()}" style="height:75px; width:75px; border:1px solid lightgrey; margin-bottom:2px">
						{/if}
					</td>
					<td>
						<b>{if $line->reference}{$line->reference} -{/if} {$line->getProductName()}</b>
						{foreach from=$line->getProductProperties() item=property}
							<div>{$property}</div>
						{/foreach}
						{if $line->information}
							<div class="text-muted">{$line->information|replace:"|":'<br />' nofilter}</div>
						{/if}
					</td>
					<td class="text-center">{if $line->selling_price}{Tools::displayPrice($line->selling_price)}{/if}</td>
					<td class="text-center">{if $line->quantity}{$line->quantity}{/if}</td>
					<td class="text-center">{if $line->selling_price}{Tools::displayPrice($line->getPrice())}{/if}</td>
				</tr>
			{/foreach}
		</tbody>
	</table>

	{assign var=total_ht value=$quotation->getPrice()}
	{assign var=total_ttc value=$quotation->getPrice(true)}
	<div class="row">
		{if $quotation->details}
			<div class="col-xs-12 col-lg-7">
				<table class="table combinations-table">
					<thead>
						<tr>
							<th>{l s='Information complémentaire' d='Shop.Theme.Checkout'}</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								{$quotation->details}
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		{/if}
		<div class="col-xs-12 col-lg-5 {if !$quotation->details}offset-lg-7{/if}">
			<table class="table combinations-table">
				<thead>
					<tr>
						<th colspan="2">{l s='Récapitulatif' d='Shop.Theme.Checkout'}</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><b>{l s='Total HT' d='Shop.Theme.Checkout'}</b></td>
						<td class="text-right">{Tools::displayPrice($total_ht)}</td>
					</tr>
					<tr>
						<td><b>{l s='TVA' d='Shop.Theme.Checkout'}</b></td>
						<td class="text-right">{Tools::displayPrice($total_ttc - $total_ht)}</td>
					</tr>
					<tr>
						<td><b>{l s='Total TTC' d='Shop.Theme.Checkout'}</b></td>
						<td class="text-right">{Tools::displayPrice($total_ttc)}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

{/block}