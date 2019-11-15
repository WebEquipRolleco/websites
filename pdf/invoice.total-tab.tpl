<table width="100%; font-size:8px;">
	<tr>
		<td width="65%"></td>
		<td width="35%">
			<table width="100%" border="1" cellpadding="3px">
				<tr style="font-size:10px;">
					<td colspan="2" style="background-color:beige; text-align:center; font-weight:bold;">
						{l s="Total" pdf=true}
					</td>
				</tr>
				<tr style="font-size:8px;">
					<td>{l s="Sous total HT" pdf=true}</td>
					<td style="text-align:center;">{Tools::displayPrice($order->total_products)}</td>
				</tr>
				<tr style="font-size:8px;">
					<td>{l s="Frais d'expédition" pdf=true}</td>
					<td style="text-align:center">{Tools::displayPrice($order->total_shipping)}</td>
				</tr>
				<tr style="font-size:8px;">
					<td>{l s="Total HT" pdf=true}</td>
					<td style="text-align:center">{Tools::displayPrice($order->total_paid_tax_excl)}</td>
				</tr>
				<tr style="font-size:8px;">
					<td>{l s="TVA" pdf=true}</td>
					<td style="text-align:center">{Tools::displayPrice($order->total_paid_tax_incl - $order->total_paid_tax_excl)}</td>
				</tr>
				<tr style="font-size:8px;">
					<td>{l s="Total TTC" pdf=true}</td>
					<td style="text-align:center">{Tools::displayPrice($order->total_paid_tax_incl)}</td>
				</tr>
			</table>
		</td>
	</tr>
</table>