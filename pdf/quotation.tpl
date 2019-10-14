{$style_tab}

{assign var=left_column value=49}
{assign var=right_column value=49}
{assign var=space_column value=(100 - $left_column - $right_column)}

{assign var=block_height value=60}

<table class="combinations">
	<thead>
		<tr>
			<th>{l s="Images" d='Shop.Pdf' pdf='true'}</th>
			<th>{l s="Produit" d='Shop.Pdf' pdf='true'}</th>
			<th>{l s="Réf." d='Shop.Pdf' pdf='true'}</th>
			<th>{l s="Prix unitaire" d='Shop.Pdf' pdf='true'}</th>
			<th>{l s="Qté" d='Shop.Pdf' pdf='true'}</th>
			<th>{l s="Total" d='Shop.Pdf' pdf='true'}</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$quotation->getProducts() item=product}
			<tr class="{cycle values='odd,even'}">
				<td>
					<img src="{$product->getImageLink()}" style="height:25px; width:25px">
				</td>
				<td>
					<b>{$product->getProductName()}</b>
					{foreach from=$product->getProductProperties() item=property}
						<br /> {$property}
					{/foreach}
					{if $product->information} 
						<br /> {$product->information}
					{/if}
				</td>
				<td class="text-center" style="font-size:10px; font-weight:bold;">
					{$product->reference}
				</td>
				<td class="text-center">
					<div>{Tools::displayPrice($product->getPrice(false, false, false, 1))}</div>
					<i style="font-size:7px; color:{$quotation->getShop()->color}">
						{l s="Dont %s" sprintf=[Tools::displayPrice($product->getEcoTax(1))]}
					</i>
				</td>
				<td class="text-center" style="font-size:10px; font-weight:bold;">
					{$product->quantity}
				</td>
				<td class="text-center bold">
					<div>{Tools::displayPrice($product->getPrice())}</div>
					<i style="font-size:7px; color:{$quotation->getShop()->color}">
						{l s="Dont %s" sprintf=[Tools::displayPrice($product->getEcoTax())]}
					</i>
				</td>
			</tr>
		{/foreach}
		<tr>
			<td colspan="6">
				{l s="Fin devis %s" sprintf=[$quotation->date_end|date_format:'d/m/Y'] d='Shop.Pdf' pdf='true'}
			</td>
		</tr>
	</tbody>
</table>

<table>
	<tr><td>&nbsp;</td></tr>
</table>

<table>
	<tr>
		<td style="width:{$left_column}%;">

			{if !empty($quotation->getOptions())}
				<table class="combinations">
					<thead>
						<tr>
							<th colspan="3">{l s="Options" d='Shop.Pdf' pdf='true'}</th>
						</tr>
					</thead>
					<tbody>
						{foreach from=OrderOption::getOrderOptions(true, $quotation->id_shop) item=option}
							{if $option->id|in_array:$quotation->getOptions()}
								<tr class="bg-light">
									<td class="text-center" style="width:5%; border:1px solid black"></td>
									<td class="text-center"style="width:80%">{$option->name}</td>
									<td class="text-center" style="width:15%">{Tools::displayPrice($option->getQuotationPrice($quotation))}</td>
								</tr>
							{/if}
						{/foreach}
					</tbody>
				</table>
			{/if}

		</td>
		<td style="width:{$space_column}%"></td>
		<td style="width:{$right_column}%;">

			<table class="combinations">
				<tbody>
					<tr class="bg-light">
						<td class="text-right">
							{l s='Total produits HT :' d='Shop.Pdf' pdf='true'} <br />
							{l s="dont Ecotaxe :" d='Shop.Pdf' pdf='true'}
						</td>
						<td class="text-right">
							{Tools::displayPrice($quotation->getPrice())} <br />
							<i style="font-size:7px; color:{$quotation->getShop()->color}">
								{Tools::displayPrice($quotation->getEcoTax())}
							</i>
						</td>
					</tr>
					<tr class="bg-light">
						<td class="text-right">
							{l s='Frais de port HT' d='Shop.Pdf' pdf='true'} <br />
							<i>{l s='** hors îles accessibles par un pont gratuit' d='Shop.Pdf' pdf='true'}</i>
						</td>
						<td class="text-right">{Tools::displayPrice($quotation->getBuyingFees())}</td>
					</tr>
					<tr class="bg-light">
						<td class="text-right">{l s='Total HT' pdf=true}</td>
						<td class="text-right">{Tools::displayPrice($quotation->getPrice(false, true))}</td>
					</tr>
					<tr class="bg-light">
						<td>{l s='Total TVA' pdf=true}</td>
						<td class="text-right">{Tools::displayPrice($quotation->getPrice(true, true) - $quotation->getPrice(false, true))}</td>
					</tr>
				</tbody>
			</table>

			<table class="combinations">
				<tbody>
					<tr>
						<th class="text-center bold">{l s='Total TTC (sans options)' pdf=true}</th>
					</tr>
					<tr class="bg-light">
						<td class="text-center bold" style="font-size:12px; color:{$quotation->getShop()->color}">
							{Tools::displayPrice($quotation->getPrice(true, true, true))}
						</td>
					</tr>
				</tbody>
			</table>

		</td>
	</tr>
</table>

<table>
	<tr><td>&nbsp;</td></tr>
</table>

<table>
	<tr>
		<td style="width:{$left_column}%;">

			<table class="combinations">
				<thead>
					<tr>
						<th colspan="2" class="text-center">
							{l s='Bon pour accord' pdf=true}
						</th>
					</tr>
				</thead>
				<tbody>
					<tr class="bg-light">
						<td colspan="2">
							<div class="text-center" style="font-size:8px; color:grey">
								<em>{l s='Toute acceptation de ce devis vaut pour acceptation des conditions générales de vente' pdf=true}</em>
							</div>
							<br />
						</td>
					</tr>
					<tr class="bg-light">
						<td style="width:50%;">
							{l s='Fait à :' pdf=true}
						</td>
						<td class="text-right" style="width:50%;">
							{l s='Le : ..... /..... / 2019' pdf=true}
						</td>
					</tr>
				</tbody>
			</table>

			<table>
				<tr><td>&nbsp;</td></tr>
			</table>

			<table class="combinations">
				<thead>
					<tr>
						<th class="text-center">
							{l s='Cachet et signature client' pdf=true}
						</th>
					</tr>
				</thead>
				<tbody>
					<tr class="bg-light">
						<td style="height:{$block_height}px"></td>
					</tr>
				</tbody>
			</table>

		</td>
		<td style="width:{$space_column}%"></td>
		<td style="width:{$right_column}%;">

			<table class="combinations">
				<thead>
					<tr>
						<th class="text-center">
							{l s='Adresse de facturation' pdf=true}
						</th>
					</tr>
				</thead>
				<tbody>
					<tr class="bg-light">
						<td style="height:{$block_height}px"></td>
					</tr>
				</tbody>
			</table>

			<table>
				<tr><td>&nbsp;</td></tr>
			</table>

			<table class="combinations">
				<thead>
					<tr>
						<th class="text-center">
							{l s='Adresse de livraison' pdf=true}
						</th>
					</tr>
				</thead>
				<tbody>
					<tr class="bg-light">
						<td style="height:{$block_height}px"></td>
					</tr>
				</tbody>
			</table>

		</td>
	</tr>
</table>

<table>
	<tr><td>&nbsp;</td></tr>
</table>

<table>
	<tr class="bg-grey">
		<td class="bold">{l s='Votre numéro de SIRET' pdf=true}</td>
	</tr>
	<tr class="bg-light">
		<td><br /><br /></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr class="bg-grey">
		<td class="bold">{l s='Votre adresse mail' pdf=true}</td>
	</tr>
	<tr class="bg-light">
		<td><br /><br /></td>
	</tr>
</table>

<table>
	<tr><td>&nbsp;</td></tr>
</table>

<table>
	<tr>
		<td style="width:{$left_column}%;">

			<table class="combinations">
				<thead>
					<tr>
						<th class="text-center">
							Qui contacter pour la livraison ?
						</th>
					</tr>
				</thead>
				<tbody>
					<tr class="bg-grey">
						<td class="bold">{l s='Nom / Prénom' pdf=true} <em class="text-danger">*</em></td>
					</tr>
					<tr class="bg-light">
						<td><br /><br /></td>
					</tr>
					<tr class="bg-grey">
						<td class="bold">{l s='Numéro de téléphone' pdf=true} <em class="text-danger">*</em></td>
					</tr>
					<tr class="bg-light">
						<td><br /><br /></td>
					</tr>
				</tbody>
			</table>

		</td>
		<td style="width:{$space_column}%"></td>
		<td style="width:{$right_column}%">

			<table class="combinations">
				<thead>
					<tr>
						<th class="text-center">
							{l s='Commentaire(s) pour la livraison' pdf=true}
						</th>
					</tr>
				</thead>
				<tbody>
					<tr class="bg-light">
						<td style="height:{$block_height}px"></td>
					</tr>
				</tbody>
			</table>

			
		</td>
	</tr>
</table>

<table>
	<tr><td>&nbsp;</td></tr>
	<tr><td class="text-danger text-center">{l s='* à préciser obligatoirement' pdf=true}</td></tr>
</table>