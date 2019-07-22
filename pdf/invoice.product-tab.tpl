<table width="100%" cellpadding="5px" style="border:1px solid #4D4D4D">
	<thead>
		<tr style="background-color:#4D4D4D; color:#FFF; font-weight:bold;">
			<th style="text-align:center">
				{l s='Référence' pdf='true'}
			</th>
			<th style="text-align:center">
				{l s='Désignation' pdf='true'}
			</th>
			<th style="text-align:center">
				{l s='PU HT' pdf='true'}
			</th>
			<th style="text-align:center">
				{l s='Qté' pdf='true'}
			</th>
			<th style="text-align:center">
				{l s='Total HT' pdf='true'}
			</th>
		</tr>
	</thead>
	<tbody>
		{foreach $order_details as $details}
			<tr>
				<td style="text-align:center">
					<b>{$details.product_reference|default:'-'}</b>
				</td>
				<td style="text-align:center">
					{$details.product_name|replace:'||':'<br />'} 
				</td>
				<td style="text-align:center">
					{Tools::displayPrice($details.unit_price_tax_excl_including_ecotax)}
				</td>
				<td style="text-align:center">
					{$details.product_quantity}
				</td>
				<td style="text-align:center">
					{Tools::displayPrice($details.total_price_tax_excl_including_ecotax)}
				</td>
			</tr>
		{/foreach}
	</tbody>
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
</table>