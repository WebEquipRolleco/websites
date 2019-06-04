<tr>
	<td class="text-center">
		<img src="{$line->getImageLink()}" class="cart-image">
	</td>
	<td>
		{$line->name}
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
	<td class="price text-center">
		<span class="product-price">
      		<strong>{Tools::displayPrice($line->getPrice())}</strong>
    	</span>
		
	</td>
	<td class="text-center">
		<div class="cart-line-product-actions">
			<a href="" class="remove-from-cart">
				<i class="material-icons fa fa-trash-alt"></i>
			</a>
		</div>
	</td>
</tr>