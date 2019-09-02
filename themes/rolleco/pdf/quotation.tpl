{$style_tab}

{assign var=left_column value=49}
{assign var=right_column value=49}
{assign var=space_column value=(100 - $left_column - $right_column)}

{assign var=block_height value=60}

<table class="left-title">
	<thead>
		<tr>
			<th colspan="2">{l s='Devis #' pdf=true}{$quotation->reference}</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="title">{l s='Votre contact' pdf=true}</td>
			<td>{$quotation->getEmployee()->firstname} {$quotation->getEmployee()->lastname}</td>
		</tr>
		<tr>
			<td class="title">{l s='Début de validité' pdf=true}</td>
			<td>{$quotation->date_begin|date_format:'d/m/Y'}</td>
		</tr>
		<tr>
			<td class="title">{l s='Fin de validité' pdf=true}</td>
			<td>{$quotation->date_end|date_format:'d/m/Y'}</td>
		</tr>
	</tbody>
</table>

<table>
	<tr><td>&nbsp;</td></tr>
</table>

<table class="combinations">
	<thead>
		<tr>
			<th></th>
			<th>{l s="Produit" pdf='true'}</th>
			<th>{l s="Référence" pdf='true'}</th>
			<th>{l s="PU HT" pdf='true'}</th>
			<th>{l s="Quantité" pdf='true'}</th>
			<th>{l s="Prix HT" pdf='true'}</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$quotation->getProducts() item=product}
			<tr class="{cycle values='odd,even'}">
				<td>
					<img src="{$product->getImageLink()}" style="height:25px; width:25px">
				</td>
				<td style="padding:5px">
					<b>{$product->getProductName()}</b>
					{foreach from=$product->getProductProperties() item=property}
						<br /> {$property}
					{/foreach}
					{if $product->information} 
						<br /> {$product->information}
					{/if}
				</td>
				<td style="text-align:center; padding:5px">
					<b>{$product->reference}</b>
				</td>
				<td style="text-align:center; padding:5px">
					{Tools::displayPrice($product->getPrice(false, false, false, 1))}
				</td>
				<td style="text-align:center; padding:5px">
					{$product->quantity}
				</td>
				<td style="text-align:center; padding:5px">
					{Tools::displayPrice($product->getPrice())}
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>

<table>
	<tr><td>&nbsp;</td></tr>
</table>

<table>
	<tr>
		<td style="width:{$left_column}%; background-color: blue; height:10px;">


		</td>
		<td style="width:{$space_column}%"></td>
		<td style="width:{$right_column}%;">

			<table class="combinations">
				<tbody>
					<tr class="bg-light">
						<td>{l s='Total produits HT' pdf=true}</td>
						<td class="text-right">{Tools::displayPrice($quotation->getPrice())}</td>
					</tr>
					<tr class="bg-light">
						<td>{l s='Frais de port HT' pdf=true} <em class="text-primary">**</em></td>
						<td class="text-right">{Tools::displayPrice($quotation->getBuyingFees())}</td>
					</tr>
					<tr class="bg-light">
						<td>{l s='Total HT' pdf=true}</td>
						<td class="text-right">{Tools::displayPrice($quotation->getPrice(false, true))}</td>
					</tr>
					<tr class="bg-light">
						<td>{l s='Total TVA' pdf=true}</td>
						<td class="text-right">{Tools::displayPrice($quotation->getPrice(true, true) - $quotation->getPrice(false, true))}</td>
					</tr>
					<tr class="bg-light">
						<td>{l s='Eco-participation' pdf=true}</td>
						<td class="text-right">{Tools::displayPrice($quotation->getEcoTax())}</td>
					</tr>
				</tbody>
			</table>

			<table class="combinations">
				<tbody>
					<tr>
						<td class="bg-primary text-center bold">{l s='Total TTC' pdf=true}</td>
					</tr>
					<tr class="bg-light">
						<td class="text-center text-primary bold">{Tools::displayPrice($quotation->getPrice(true, true, true))}</td>
					</tr>
				</tbody>
			</table>

		</td>
	</tr>
</table>

<table>
	<tr><td>&nbsp;</td></tr>
</table>

<hr>

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
	<tr><td class="text-primary text-center">{l s='** hors îles accessibles par un pont gratuit' pdf=true}</td></tr>
</table>