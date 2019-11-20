<table width="100%" border="1" cellpadding="3px">
	<tr>
		<td colspan="7" style="background-color:beige; text-align:center; font-weight:bold; font-size:10px">
			{l s="DETAIL DE LA COMMANDE" pdf=true}
		</td>
	</tr>
	<tr>
		<td width="8%" style="text-align:center; font-size:8px;">{l s="Référence" pdf=true}</td>
		<td width="34%" style="text-align:center; font-size:8px;">{l s="Désignation" pdf=true}</td>
		<td width="6%" style="text-align:center; font-size:8px;">{l s="Qté" pdf=true}</td>
		<td width="15%" style="text-align:center; font-size:8px;">{l s="Prix unitaire [1] (HT)" tags=["<br />"] pdf=true}</td>
		<td width="7%" style="text-align:center; font-size:8px;">{l s="Remise" pdf=true}</td>
		<td width="15%" style="text-align:center; font-size:8px;">{l s="Prix final unitaire [1] (HT)" tags=["<br />"] pdf=true}</td>
		<td width="15%" style="text-align:center; font-size:8px;">{l s="Montant [1] (HT)" tags=["<br />"] pdf=true}</td>
	</tr>
	{foreach $order_details as $details}
		<tr>
			<td width="8%" style="text-align:center; font-size:8px;">{$details.product_reference|default:'-'}</td>
			<td width="34%" style="text-align:center; font-size:8px;">
				{$details.product_name|replace:'|':'<br />'} 
				{if $details.comment_product_1}
					<br /> {$details.comment_product_1|replace:'|':'<br />'}
				{/if}
				{if $details.comment_product_2}
					<br /> {$details.comment_product_2|replace:'|':'<br />'}
				{/if}
			</td>
			<td width="6%" style="text-align:center; font-size:8px;">{$details.product_quantity}</td>
			<td width="15%" style="text-align:center; font-size:8px;">
				{Tools::displayPrice($details.unit_price_tax_excl_including_ecotax + $details.reduction_amount)}
				{if $details.ecotax > 0}
					<br /> {l s="dont @eco@ d'ecotaxe"|replace:"@eco@":Tools::displayPrice($details.ecotax) pdf=true}
				{/if}
			</td>
			<td width="7%" style="text-align:center; font-size:8px;">{Tools::displayPrice($details.reduction_amount)}</td>
			<td width="15%" style="text-align:center; font-size:8px;">{Tools::displayPrice($details.unit_price_tax_excl_including_ecotax)}</td>
			<td width="15%" style="text-align:center; font-size:8px;">{Tools::displayPrice($details.total_price_tax_excl_including_ecotax)}</td>
		</tr>
	{/foreach}
</table>

<table>
	<tr><td>&nbsp;</td></tr>
</table>

{*<table width="100%" cellpadding="5px" style="border:1px solid #4D4D4D">
	<tfoot>
		<tr style="background-color:#4D4D4D; color:#FFF; font-weight:bold;">
			<td colspan="4" style="text-align:right; padding-right:15px">
				{l s="Total"|upper}
			</td>
			<td style="text-align:center">
				{Tools::displayPrice($order->getTotalProductsWithoutTaxes())}
			</td>
		</tr>
	</tfoot>	
</table>*}