{*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="content-ajax-search">
<div class="search-title">
	{l s='The result search for ' d='Modules.WTbBockSearch'} "{$query|escape:'quotes':'UTF-8'}" <span>({$search_Total|escape:'quotes':'UTF-8'} {l s=' results have been found.' mod='wtblocksearch'})</span>

</div>
<ul class="items-list">
{if $searchResults}
{$num = 0}
	{foreach from=$searchResults item=product key=key}
			{$num = $num+1}
			{if $num > $limit_item}
			{break}
			{/if}
		<li class="item col_grid_5_2">
			<div class="image">
				<a href="{$product.link|escape:'quotes':'UTF-8'}" class="product_img_link" title="{$product.name|escape:'htmlall':'UTF-8'}">						
						<img src="{$link->getImageLink($product.link_rewrite|escape:'quotes':'UTF-8',$product.id_image ,'home_default')}" alt="{$product.legend|escape:'htmlall':'UTF-8'}" />					
				</a>
					{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
									{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
											<p class="sale-bkg animated" href="{$product.link|escape:'html':'UTF-8'}">
														<span class="sale">
														{if $product.specific_prices && $product.specific_prices.reduction_type == 'percentage'}
														-{$product.specific_prices.reduction|escape:'quotes':'UTF-8' * 100}%
														{else}
														-{$product.price_without_reduction-$product.price|floatval}
														{/if}
														</span>
													</p>
									{/if}
									{/if}
			</div>	
			<h5 class="product-name">
				<a class="product-name" href="{$product.link|escape:'quotes':'UTF-8'}" class="product_img_link" title="{$product.name|escape:'htmlall':'UTF-8'}">
					{$product.name|escape:'html':'UTF-8'}
				</a>
			</h5>
				
										
			
								<div class="review clearfix">
										{hook h='displayProductListReviews' product=$product}
								</div>
										
									{hook h='displayProductAttributes' product=$product}
									
									
									<div class="content_price product-price-and-shipping">
									{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
										<span itemprop="price" class="price {if isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0} special-price{/if}">												
												{Product::convertAndFormatPrice($product.price)}

												</span>
										{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
												<span class="old-price regular-price">
												{Product::convertAndFormatPrice($product.price_without_reduction)}
												</span>
										{/if}
											
									{/if}
								</div>
									
									
			
			
		</li>
		
	{/foreach}
	
	

	{else}
	<p class="noresult">{l s='No found any item!' d='Modules.WTbBockSearch'}</p>
{/if}
</ul>
{if $search_Total > $limit_item}
<p class="title_showall_text">{l s='Search limited to ' d='Modules.WTbBockSearch'} {$limit_item|escape:'quotes':'UTF-8'} {l s='products' mod='wtblocksearch'}</p>
<p class="title_showall">
<a href="#" onclick="Show_All_Search();return false;">
	<span>{l s='show alls' d='Modules.WTbBockSearch'}</span>
	</a>
</p>
{/if}
</div>
