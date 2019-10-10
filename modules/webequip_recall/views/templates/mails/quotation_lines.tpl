{foreach $quotations as $quotation}
	<tr style="background-color:lightgrey">
		<td style="padding:5px; text-align:center">{$quotation->reference}</td>
		<td style="padding:5px; text-align:center">{$quotation->getCustomer()->firstname} {$quotation->getCustomer()->lastname}</td>
	</tr>
{/foreach}