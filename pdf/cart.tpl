{$style_tab}

{assign var=left_column value=49}
{assign var=right_column value=49}
{assign var=space_column value=(100 - $left_column - $right_column)}

{assign var=block_height value=60}
{assign var=line_height value=16}

{assign var=quotations value=QuotationAssociation::find($cart->id)}
{assign var=option_lines value=OrderOption::getOrderOptions(true, $cart->id_shop)}

<page>
	<table cellpadding="3" class="combinations">
        <thead>
        	<tr>
            	<th>{l s="Images" d="Shop.Theme.Checkout"}</th>
            	<th>{l s="Produit" d="Shop.Theme.Checkout"}</th>
            	<th>{l s="Ref." d="Shop.Theme.Checkout"}</th>
            	<th>{l s="Prix unitaire HT" d="Shop.Theme.Checkout"}</th>
            	<th>{l s="Qté" d="Shop.Theme.Checkout"}</th>
            	<th>{l s="Total HT" d="Shop.Theme.Checkout"}</th>
          	</tr>
        </thead>
        <tbody class="cart-items">
          	{foreach from=$cart->getProducts() item=product}
          		{assign var=image value=Product::getCoverPicture($product.id_product, $product.id_product_attribute)}
            	<tr class="bg-light">
              		<td class="text-center"><img src="{$image->getFileUrl('cart')}"></td>
              		<td class="text-center">{$product.name}</td>
              		<td class="text-center">{$product.reference}</td>
              		<td class="text-center">{displayPrice price=$product.price}</td>
              		<td class="text-center">{$product.quantity}</td>
              		<td class="text-center">{$product.total}</td>
            	</tr>
          	{/foreach}
          	{foreach from=$quotations item=quotation}
          		{foreach from=$quotation->getProducts() item=product}
	            	<tr class="bg-light">
	              		<td>
	              			{if $product->getImageLink()}
								<img src="{$product->getImageLink()}" width="30" height="30">
							{/if}
	              		</td>
	              		<td>
	              			<b>{$product->getProductName()}</b>
							{foreach from=$product->getProductProperties() item=property}
								<br /> {$property}
							{/foreach}
							{if $product->information} 
								<br /> <div style="font-size:8px">{$product->information|replace:"|":"<br />"}</div>
							{/if}
	              		</td>
	              		<td class="text-center">{$product->reference}</td>
	              		<td class="text-center">
	              			{assign var=price value=$product->getPrice(false, false, false, 1)}
							{if $price}{displayPrice price=$price}{/if}
	              		</td>
	              		<td class="text-center">
	              			{if $product->quantity}
								{$product->quantity}
							{/if}
	              		</td>
	              		<td class="text-center">
	              			{assign var=price value=$product->getPrice()}
							{if $price}{displayPrice price=$price}{/if}
	              		</td>
	            	</tr>
          		{/foreach}
          	{/foreach}
          	{foreach from=$option_lines item=option}
          		{if OrderOptionCart::hasAssociation($cart->id, $option->id)}
	              	<tr class="bg-light">
		              	<td></td>
		              	<td colspan="4">
		              		<b>{$option->name}</b>
		              		{if $option->description}<br />{$option->description}{/if}
		              	</td>
		              	<td class="text-center">
		              		{displayPrice price=$option->getPrice($cart)}
		              	</td>
		            </tr>
		        {/if}
            {/foreach}
        </tbody>
    </table>

    <table>
		<tr><td>&nbsp;</td></tr>
	</table>

	<table>
		<tr>
			<td style="width:{$left_column}%;">



			</td>
			<td style="width:{$space_column}%"></td>
			<td style="width:{$right_column}%;">

				{assign var=options_ht value=OrderOptionCart::getCartTotal()}
				{assign var=options_ttc value=$options_ht * 1.2}
				{assign var=total_ht value=$cart->getOrderTotal(false) + $options_ht}
				{assign var=total_ttc value=$cart->getOrderTotal(true) + $options_ttc}

				<table cellpadding="3" class="combinations">

						{*<tr class="bg-light">
							<td class="text-right" style="width:70%">
								{l s='Total produits HT :' d='Shop.Pdf' pdf=true} <br />
								{l s="dont Ecotaxe :" d='Shop.Pdf' pdf=true}
							</td>
							<td class="text-right" style="width:30%">
								
							</td>
						</tr>
						<tr class="bg-light">
							<td class="text-right">
								{l s='Frais de port HT :' d='Shop.Pdf' pdf=true} <br />
								<i style="font-size:8px">{l s='hors îles accessibles par un pont gratuit' d='Shop.Pdf' pdf=true}</i>
							</td>
							<td class="text-right">
								
							</td>
						</tr>*}
						<tr class="bg-light">
							<td class="text-right">{l s='Total HT :' d='Shop.Pdf' pdf=true}</td>
							<td class="text-right">{displayPrice price=$total_ht}</td>
						</tr>
						<tr class="bg-light">
							<td class="text-right">{l s='Total TVA :' d='Shop.Pdf' pdf=true}</td>
							<td class="text-right">{displayPrice price=$total_ttc - $total_ht}</td>
						</tr>
						<tr>
							<th colspan="2" class="text-center bold" style="font-size:8px;">
								{l s='Total TTC :'|upper d='Shop.Pdf' pdf=true}
							</th>
						</tr>
						<tr class="bg-light">
							<td colspan="2" class="text-center bold" style="font-size:12px;">
								{displayPrice price=$total_ttc}
							</td>
						</tr>
				</table>

			</td>
		</tr>
	</table>

</page>