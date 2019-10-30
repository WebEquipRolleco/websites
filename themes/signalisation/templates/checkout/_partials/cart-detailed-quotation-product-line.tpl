<tr>
	<td class="text-center">
		{if $line->getImageLink()}
			<img src="{$line->getImageLink()}" class="cart-image">
		{/if}
	</td>
	<td>
		<div class="bold">{$line->getProductName()}</div>
		{foreach from=$line->getProductProperties() item=property}
			<div>{$property}</div>
		{/foreach}
		{if $line->information}
			<em class="text-muted">{$line->information|replace:"|":'<br />' nofilter}</em>
		{/if}
	</td>
	<td class="cart_reference text-center">
		{$line->reference}
	</td>
	<td class="price text-center">
		{if $line->selling_price}
			<div class="product-line-info product-price h5">
	            <div class="current-price h5">
	        		<span class="price">{Tools::displayPrice($line->selling_price)}</span>
	            </div>
	            {if $line->eco_tax}
		            <div class="text-muted">
		            	<small>{l s="Dont %s d'écotaxe" sprintf=[Tools::displayPrice($line->eco_tax)] d='Shop.Theme.Checkout'}</small>
		            </div>
	            {/if}
	    	</div>
	    {/if}
	</td>
	<td class="text-center">
		{if $line->quantity}
			{$line->quantity}
		{/if}
	</td>
	<td colspan="2" class="price text-center">
		{if $line->selling_price}
			<span class="product-price h5">
	      		<strong>{Tools::displayPrice($line->getPrice())}</strong>
	    	</span>
	    	{if $line->eco_tax}
				<div class="text-muted">
		           	<small>{l s="Dont %s d'écotaxe" sprintf=[Tools::displayPrice($line->eco_tax * $line->quantity)] d='Shop.Theme.Checkout'}</small>
		        </div>
		    {/if}
	    {/if}
	</td>
</tr>