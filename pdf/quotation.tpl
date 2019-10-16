{$style_tab}

{assign var=left_column value=49}
{assign var=right_column value=49}
{assign var=space_column value=(100 - $left_column - $right_column)}

{assign var=block_height value=60}
{assign var=line_height value=16}

<page>
<table cellpadding="3" class="combinations">
	<tr>
		<th>{l s="Images" d='Shop.Pdf' pdf=true}</th>
		<th>{l s="Produit" d='Shop.Pdf' pdf=true}</th>
		<th>{l s="Réf." d='Shop.Pdf' pdf=true}</th>
		<th>{l s="Prix unitaire" d='Shop.Pdf' pdf=true}</th>
		<th>{l s="Qté" d='Shop.Pdf' pdf=true}</th>
		<th>{l s="Total" d='Shop.Pdf' pdf=true}</th>
	</tr>
	{foreach from=$quotation->getProducts() item=product}
		<tr class="odd">
			<td class="text-center" style="vertical-align:middle;">
				<img src="{$product->getImageLink()}" width="30" height="30">
			</td>
			<td>
				<b>{$product->getProductName()}</b>
				{foreach from=$product->getProductProperties() item=property}
					<br /> {$property}
				{/foreach}
				{if $product->information} 
					<br /> <div style="font-size:8px">{$product->information|replace:"|":"<br />"}</div>
				{/if}
			</td>
			<td class="text-center" style="font-size:10px; font-weight:bold;">
				{$product->reference}
			</td>
			<td class="text-center">
				<div>{Tools::displayPrice($product->getPrice(false, false, false, 1))}</div>
				<i style="font-size:7px; color:{$quotation->getShop()->color}">
					{l s="Dont %s" sprintf=[Tools::displayPrice($product->getEcoTax(1))] d='Shop.Pdf' pdf=true}
				</i>
			</td>
			<td class="text-center" style="font-size:10px; font-weight:bold;">
				{$product->quantity}
			</td>
			<td class="text-center bold">
				<div>{Tools::displayPrice($product->getPrice())}</div>
				<i style="font-size:7px; color:{$quotation->getShop()->color}">
					{l s="Dont %s" sprintf=[Tools::displayPrice($product->getEcoTax())] d='Shop.Pdf' pdf=true}
				</i>
			</td>
		</tr>
	{/foreach}
	<tr class="odd">
		<td colspan="6">
			{l s="Fin devis %s" sprintf=[$quotation->date_end|date_format:'d/m/Y'] d='Shop.Pdf' pdf=true}
		</td>
	</tr>
</table>

<table>
	<tr><td>&nbsp;</td></tr>
</table>

<table>
	<tr>
		<td style="width:{$left_column}%;">

			{if !empty($quotation->getOptions())}
				<table cellpadding="3" class="combinations">
					<thead>
						<tr>
							<th style="width:5%;"></th>
							<th style="width:65%">{l s="Options" d='Shop.Pdf' pdf=true}</th>
							<th style="width:30%">{l s="Prix HT" d='Shop.Pdf' pdf=true}</th>
						</tr>
					</thead>
					<tbody>
						{foreach from=OrderOption::getOrderOptions(true, $quotation->id_shop) item=option}
							{if $option->id|in_array:$quotation->getOptions()}
								<tr class="bg-light">
									<td class="text-center" style="width:5%;"></td>
									<td class="text-center" style="width:65%">{$option->name}</td>
									<td class="text-center" style="width:30%">{Tools::displayPrice($option->getQuotationPrice($quotation))}</td>
								</tr>
							{/if}
						{/foreach}
					</tbody>
				</table>
			{/if}

		</td>
		<td style="width:{$space_column}%"></td>
		<td style="width:{$right_column}%;">

			<table cellpadding="3" class="combinations">

					<tr class="bg-light">
						<td class="text-right" style="width:70%">
							{l s='Total produits HT :' d='Shop.Pdf' pdf=true} <br />
							{l s="dont Ecotaxe :" d='Shop.Pdf' pdf=true}
						</td>
						<td class="text-right" style="width:30%">
							{Tools::displayPrice($quotation->getPrice())} <br />
							<i style="font-size:7px; color:{$quotation->getShop()->color}">
								{Tools::displayPrice($quotation->getEcoTax())}
							</i>
						</td>
					</tr>
					<tr class="bg-light">
						<td class="text-right">
							{l s='Frais de port HT :' d='Shop.Pdf' pdf=true} <br />
							<i style="font-size:8px">{l s='hors îles accessibles par un pont gratuit' d='Shop.Pdf' pdf=true}</i>
						</td>
						<td class="text-right">
							{if $quotation->getShop()->id == 1}
								{l s="Gratuit" d='Shop.Pdf' pdf=true}
							{else}
								{Tools::displayPrice($quotation->getBuyingFees())}
							{/if}
						</td>
					</tr>
					<tr class="bg-light">
						<td class="text-right">{l s='Total HT :' d='Shop.Pdf' pdf=true}</td>
						<td class="text-right">{Tools::displayPrice($quotation->getPrice(false, true))}</td>
					</tr>
					<tr class="bg-light">
						<td class="text-right">{l s='Total TVA :' d='Shop.Pdf' pdf=true}</td>
						<td class="text-right">{Tools::displayPrice($quotation->getTVA())}</td>
					</tr>
					<tr>
						<th colspan="2" class="text-center bold">
							{l s='Total TTC (sans options) :' d='Shop.Pdf' pdf=true}
						</th>
					</tr>
					<tr class="bg-light">
						<td colspan="2" class="text-center bold" style="font-size:12px; color:{$quotation->getShop()->color}">
							{Tools::displayPrice($quotation->getPrice(true, true, true))}
						</td>
					</tr>
			</table>

		</td>
	</tr>
</table>

<table>
	<tr><td>&nbsp;</td></tr>
</table>

<hr />

<table>
	<tr><td>&nbsp;</td></tr>
</table>

<table>
	<tr>
		<td style="width:{$left_column}%;">

			<table class="block">
				<tr>
					<td colspan="2" class="text-center bold">{l s='BON POUR ACCORD' d='Shop.Pdf' pdf=true}</td>
				</tr>
				<tr>
					<td colspan="2" class="text-center" style="font-size:8px">
						<em>{l s='Toute acceptation de ce devis vaut pour acceptation des conditions générales de vente' d='Shop.Pdf' pdf=true}</em>
					</td>
				</tr>
				<tr>
					<td style="height:15px"></td>
				</tr>
				<tr>
					<td style="width:50%;">
						{l s='Fait à :' d='Shop.Pdf' pdf=true}
					</td>
					<td class="text-right" style="width:50%;">
						{l s='Le : ...../...../%s' sprintf=['now'|date_format:'Y'] d='Shop.Pdf' pdf=true}
					</td>
				</tr>
			</table>

			<table>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td class="block text-center" style="height:{$block_height}px">
						<b>{l s='Cachet et Signature client :' d='Shop.Pdf' pdf=true}</b>
					</td>
				</tr>
			</table>

		</td>
		<td style="width:{$space_column}%"></td>
		<td style="width:{$right_column}%;">

			<table>
				<tr>
					<td class="block text-center" style="height:{$block_height}px">
						<b>{l s='Adresse de facturation' d='Shop.Pdf' pdf=true}</b>
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td class="block text-center" style="height:{$block_height}px">
						<b>{l s='Adresse de livraison :' d='Shop.Pdf' pdf=true}</b>
						<div>{l s='A compléter si différent de facturation' d='Shop.Pdf' pdf=true}</div>
					</td>
				</tr>
			</table>

		</td>
	</tr>
</table>

<table>
	<tr><td>&nbsp;</td></tr>
</table>

<table>
	<tr>
		<td style="width:{$left_column}%;">{l s='Votre numéro de SIRET :' d='Shop.Pdf' pdf=true}</td>
		<td style="width:{$space_column}%"></td>
		<td style="width:{$left_column}%;">{l s='Votre numéro de commande interne :' d='Shop.Pdf' pdf=true}</td>
	</tr>
	<tr>
		<td class="bg-light" style="width:{$left_column}%; height:{$line_height}px;"></td>
		<td style="width:{$space_column}%"></td>
		<td class="bg-light" style="width:{$left_column}%; height:{$line_height}px;"></td>
	</tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr>
		<td colspan="3">{l s='Votre adresse mail :' d='Shop.Pdf' pdf=true}</td>
	</tr>
	<tr class="bg-light">
		<td colspan="3" class="text-center" style="height:{$line_height}px; vertical-align:middle;">@</td>
	</tr>
</table>

<table>
	<tr><td>&nbsp;</td></tr>
</table>

<table>
	<tr>
		<td style="width:{$left_column}%;">
			<table>
				<tr>
					<td colspan="2" class="text-danger">
						{l s='A préciser obligatoirement' d='Shop.Pdf' pdf=true}
					</td>
				</tr>
				<tr>
					<td colspan="2" class="text-center bold">
						Qui contacter pour la livraison ?
					</td>
				</tr>
				<tr>
					<td style="width:30%">{l s='Nom/Prénom :' d='Shop.Pdf' pdf=true}</td>
					<td class="bg-light" style="width:70%; border-bottom: 1px dashed white; height:{$line_height}px;"></td>
				</tr>
				<tr>
					<td>{l s='Téléphone :' d='Shop.Pdf' pdf=true}</td>
					<td class="bg-light" style="height:{$line_height}px;"></td>
				</tr>
			</table>
		</td>
		<td style="width:{$space_column}%"></td>
		<td class="block text-center bold" style="width:{$right_column}%; height:{$block_height}px;">
			{l s='Commentaire(s) pour la livraison' d='Shop.Pdf' pdf=true}
		</td>
	</tr>
</table>
</page>