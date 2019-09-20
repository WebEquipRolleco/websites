<tr>
	<td class="product-miniature">
		{block name='product_thumbnail'}
			<a href="{$product.url}" title="{$product.name}">
				{if $product.cover}
			        <img src="{$product.cover.bySize.small_default.url}" alt="{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:30:'...'}{/if}" data-full-size-image-url="{$product.cover.large.url}">
			    {else}
			        <img src="{$urls.no_picture_image.bySize.small_default.url}">
			    {/if}
			</a>
		{/block}
	</td>
	<td>
		{block name='product_name'}
	        <div class="description" itemprop="name">
	        	<a href="{$product.url}" title="{$product.name}">{$product.name}</a>
	        </div>
	    {/block}
	    <div class="margin-top-10">
	    	{$product.description_short nofilter}
	    </div>
	    {if $product.rollcash}
	   		<span class="well rollcash">{l s="AtoutCash"} {$product.rollcash}%</span>
	   	{/if}
	</td>
	<td class="product-options">
		{block name='product_price_and_shipping'}
          {if $product.show_price}
            <div>
              {if $product.has_discount}
                {hook h='displayProductPriceBlock' product=$product type="old_price"}

                <span class="sr-only">{l s='Regular price' d='Shop.Theme.Catalog'}</span>
                <span class="regular-price">{$product.regular_price}</span>
                {if $product.discount_type === 'percentage'}
                  <span class="discount-percentage discount-product">{$product.discount_percentage}</span>
                {elseif $product.discount_type === 'amount'}
                  <span class="discount-amount discount-product">{$product.discount_amount_to_display}</span>
                {/if}
              {/if}

              {hook h='displayProductPriceBlock' product=$product type="before_price"}

              <div>{l s="à partir de"}</div>
              <span class="sr-only">{l s='Price' d='Shop.Theme.Catalog'}</span>
              <span itemprop="price" class="price bold">{$product.price}</span>

              {hook h='displayProductPriceBlock' product=$product type='unit_price'}
              {hook h='displayProductPriceBlock' product=$product type='weight'}

              <a href="{$product.url}" class="btn btn-block btn-success margin-top-sm bold">
              	{l s='Voir le produit'}
              </a>
            </div>
          {/if}
        {/block}
	</td>
</tr>