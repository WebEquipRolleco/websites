{foreach from=$products item=product}
	<tr style="background-color: lightgrey">
		<td>{$product.order->reference}</td>
		<td>{$product.product_name}</td>
		<td>{$product.supplier_name}</td>
		<td>{$product.order->date_add|date_format:'d/m/Y'}</td>
	</tr>
{/foreach}