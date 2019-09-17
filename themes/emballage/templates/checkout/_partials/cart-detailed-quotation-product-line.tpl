<tr>
	<td class="text-center">
		<img src="{$line->getImageLink()}" class="cart-image">
	</td>
	<td>
		{if $line->information}
			<div class="bold">{$line->name}</div>
			<em class="text-muted">{$line->information}</em>
		{else}
			{$line->name}
		{/if}
	</td>
	<td class="cart_reference text-center">
		{$line->reference}
	</td>
	<td class="price text-center">
		<div class="product-line-info product-price h5 ">
            <div class="current-price">
        		<span class="price">{Tools::displayPrice($line->selling_price)}</span>
            </div>
    	</div>
	</td>
	<td class="text-center">
		{$line->quantity}
	</td>
	<td colspan="2" class="price text-center">
		<span class="product-price">
      		<strong>{Tools::displayPrice($line->getPrice())}</strong>
    	</span>
		
	</td>
</tr>