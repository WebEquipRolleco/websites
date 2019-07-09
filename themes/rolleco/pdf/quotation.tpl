{$style_tab}

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
			<th>{l s="Produit" pdf='true'}</th>
			<th>{l s="Référence" pdf='true'}</th>
			<th>{l s="Quantité" pdf='true'}</th>
			<th>{l s="Prix Total HT" pdf='true'}</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$quotation->getProducts() item=product}
			<tr class="{cycle values='odd,even'}">
				<td style="padding:5px">
					<b>{$product->name}</b>
					{if $product->information} 
						<br /> {$product->information}
					{/if}
				</td>
				<td style="text-align:center; padding:5px">
					<b>{$product->reference}</b>
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