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

		{if isset($products_list) && count($products_list) > 1}
		<a id="prev{$id_tab|intval}" class="btn prev" href="#">&lt;</a>
		<a id="next{$id_tab|intval}" class="btn next" href="#">&gt;</a>
		<div class="cycleElementsContainer" id="cycle-{$id_tab|intval}">
			<div id="elements-{$id_tab|intval}">
				<div class="list_carousel responsive">
					<ul id="carousel{$id_tab|intval}" class="product-list">
					{$i=0}
					{foreach from=$products_list item=product name=product_list}
						{$i=$i+1}
					<li class="ajax_block_product {if $smarty.foreach.product_list.first|intval}first_item{elseif $smarty.foreach.product_list.last|intval}last_item{/if} js-product-miniature" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}">
					<div class="product-block wt_container_thumbnail" wt-name-module="{$name_module|escape:'html':'UTF-8'}" id-tab="{$id_tab|intval}" wt-data-id-product="{$product.id_product|intval}">
						<h5 class="cat-name">{$product.category|escape:'html':'UTF-8'}</h5>
						<h3 class="product-name"><a class="product-name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}">{$product.name|truncate:50:'...'|strip_tags|escape:'html':'UTF-8'}</a></h3>
						<div class="product-image-container">
							<div class="div-product-image">							
									<a class="product_image" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.legend|escape:'html':'UTF-8'}">
										<img class="img-responsive wt-image" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{$product.legend|escape:'html':'UTF-8'}" />
										<span class="overlay"></span>
									</a>
									<!--
									{if isset($product.new) && $product.new == 1}
									<span class="new-label"><span>{l s='New' mod='wtproductfilter'}</span></span>
									{/if}-->									
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
									<div class="thumbs-content" id="{$name_module|escape:'html':'UTF-8'}-{$id_tab|intval}-wt-thumbs-content-{$product.id_product|intval}"></div>
							</div>
						</div>
						<div>
								{hook h='displayProductAttributes' product=$product}
									<div class="review">									
											{hook h='displayProductListReviews' product=$product}
									</div>
						</div>
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
											{hook h="displayProductPriceBlock" product=$product type="price"}
											{hook h="displayProductPriceBlock" product=$product type="unit_price"}
									{/if}
								</div>	
						
						<div class="wt-button-container">
						{include file='catalog/_partials/customize/button-cart.tpl' product=$product}
						{include file='catalog/_partials/customize/button-quickview.tpl' product=$product}
						</div>
						
					</div>
					</li>
					{/foreach}
					</ul>
					<div class="cclearfix"></div>					
				</div>
			</div>
		</div>
		{else}
			<p class="alert alert-warning">{l s='No product at this time' d='Modules.WTProductsFilter'}</p>
		{/if}
	
{if $products_list && count($products_list)>0}
<script type="text/javascript">
	$(window).ready(function() {
		runSliderHometab_Ajax();
	});



	function runSliderHometab_Ajax(){
	
	var item_hometab = 4;
		
		if(getWidthBrowser() > 1180)
		{	
			item_hometab = 4; 
		}
		else
		if(getWidthBrowser() > 991)
		{	
			item_hometab = 3; 
		}
		else
		if(getWidthBrowser() > 767)
		{	
			item_hometab = 2; 
		}		
		else
		if(getWidthBrowser() > 540)
		{	
			item_hometab = 2; 
		}
		else
		if(getWidthBrowser() > 340)
		{	
			item_hometab = 1; 
		}			
		
			$("#carousel{$id_tab|intval} li:nth-child("+item_hometab+")").addClass("last_item");
			$('#carousel{$id_tab|intval}').carouFredSel({
				responsive: true,
				width: '100%',
				height: 'variable',
				onWindowResize: 'debounce',
				prev: '#prev{$id_tab|intval}',
				next: '#next{$id_tab|intval}',
				auto: false,
				swipe: {
					onTouch : true
				},
				items: {
					width:160,
					height: 'auto',
					visible: {
						min: 1,
						max: item_hometab
					}
				},
				scroll: {
					items:item_hometab,
					direction : 'left',    
					duration  : 700 ,  
					onBefore: function(data) { 
					},
					onAfter	: function(data) {
						var n=5;
						n=data.items.visible.length;
						$("#carousel{$id_tab|intval} li").removeClass("first_item");
						$("#carousel{$id_tab|intval} li:nth-child(1)").addClass("first_item");
				   }
				}
			});
	}
</script>
{/if}


