{foreach $send_orders as $send_order}
	<tr style="background-color:lightgrey">
		<td style="padding:5px; text-align:center">{$send_order->product_reference}</td>
		<td style="padding:5px; text-align:center">{$send_order->product_name}</td>
		<td style="padding:5px; text-align:center">{$send_order->product_quantity}</td>
		<td style="padding:5px; text-align:center">Expédition prévue {$send_order->getDate() |date_format:'%d-%m-%Y'}</td>
	</tr>
{/foreach}