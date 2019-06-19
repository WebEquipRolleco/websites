{foreach $orders as $order}
	<tr style="background-color:lightgrey">
		<td style="padding:5px; padding-left:10px">{$order->reference}</td>
		<td style="padding:5px; padding-left:10px">{$order->getCustomer()->firstname} {$order->getCustomer()->lastname}</td>
		<td style="padding:5px; padding-left:10px">{displayPrice currency=$order->id_currency price=$order->total_paid_tax_incl}</td>
		<td style="padding:5px; padding-left:10px">{$order->getDeadline()->format('d/m/Y')}</td>
	</tr>
{/foreach}