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

{if count($tabs) > 0}
<div class="wt_home_filter_product_tab col_grid_3_2">
<div id="tabs">
	<ul id="ul_tv_tab" class="title-tab">
		{$i=0}
		{foreach from=$tabs item=tab name=tabs}
			{$i=$i+1}
			<li class=" {if $smarty.foreach.tabs.first}first{elseif $smarty.foreach.tabs.last}last{/if} refreshCarousel">
				<a class="title_block" href="#tabs-{$smarty.foreach.tabs.iteration|escape:'html':'UTF-8'}">
				{if isset($tab->title[(int)$cookie->id_lang])}
				{$tab->title[(int)$cookie->id_lang]|escape:'html':'UTF-8'}
				{else}
				{l s='not title' mod='wtproductfilter'}
				{/if}
				</a>
			</li>
		{/foreach}
	</ul>

	<div class="content-tab-product">
	{foreach from=$tabs item=tab name=tabs}
		<div class="title_tab_hide_show" style="display:none">
			<a href="{$tab->view_link|escape:'html':'UTF-8'}"><span>
			{if isset($tab->title[(int)$cookie->id_lang])}
				{$tab->title[(int)$cookie->id_lang]|escape:'html':'UTF-8'}
			{else}
				{l s='not title' mod='wtproductfilter'}
			{/if}
			</span></a>
		</div>
	<div class="tabs-carousel" id="tabs-{$smarty.foreach.tabs.iteration|escape:'html':'UTF-8'}">
		<div class="cycleElementsContainer" id="cycle-{$smarty.foreach.tabs.iteration|escape:'html':'UTF-8'}">	
			<div id="elements-{$smarty.foreach.tabs.iteration|escape:'html':'UTF-8'}">
			
				{if $tab->product_list && count($tab->product_list)>0}
				<div class="list_carousel responsive">
				<a id="prev{$smarty.foreach.tabs.iteration|intval}" class="btn prev" href="#">&lt;</a>
					<a id="next{$smarty.foreach.tabs.iteration|intval}" class="btn next" href="#">&gt;</a>
					<ul id="carousel{$smarty.foreach.tabs.iteration|intval}" class="product-list">
					{$i=0}
					{foreach from=$tab->product_list item=product name=product_list}
						{$i=$i+1}
					<li class="ajax_block_product {if $smarty.foreach.product_list.first|intval}first_item{elseif $smarty.foreach.product_list.last|intval}last_item{/if}">
					<div class="product-block wt_container_thumbnail">
					
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
														{if $product.specific_prices && $product.specific_prices.reduction == 'percentage'}
														-{$product.specific_prices.reduction|escape:'quotes':'UTF-8' * 100}%
														{else}
														-{convertPrice price=$product.price_without_reduction-$product.price|floatval}
														{/if}
														</span>
													</p>
									{/if}
									{/if}
									
									
									{hook h='displayProductListThumbnails' product=$product}
									</div>
								</div>
								{hook h='displayProductAttributes' product=$product}
								{hook h='displayCountDownProduct' product=$product prev_id='hf'}
								<h3 class="product-name"><a class="product-name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}">{$product.name|truncate:50:'...'|strip_tags|escape:'html':'UTF-8'}</a></h3>
								
								<div class="review clearfix">
										{hook h='displayProductListReviews' product=$product}
										</div>
								
										{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
									<div class="content_price">
									{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
										{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
												<span class="old-price">{convertPrice price=$product.price_without_reduction}</span>
										{/if}
												<span class="price">{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}</span>
											
											{hook h="displayProductPriceBlock" product=$product type="price"}
											{hook h="displayProductPriceBlock" product=$product type="unit_price"}
									{/if}
									</div>
									{/if}
							
							<div class="wt-button-container">
							<p class="cart">
							{if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.customizable != 2 && !$PS_CATALOG_MODE}		
									{if (!isset($product.customization_required) || !$product.customization_required) && ($product.allow_oosp || $product.quantity > 0)}
								{capture}add=1&amp;id_product={$product.id_product|intval}{if isset($static_token)}&amp;token={$static_token|escape:'html':'UTF-8'}{/if}{/capture}
								<a class="button ajax_add_to_cart_button btn-default" href="{$link->getPageLink('cart', true, NULL, $smarty.capture.default, false)|escape:'html':'UTF-8'}" rel="nofollow" title="" data-id-product="{$product.id_product|intval}" data-minimal_quantity="{if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity > 1}{$product.product_attribute_minimal_quantity|intval}{else}{$product.minimal_quantity|intval}{/if}">
									<span>{l s='Add to cart' mod='wtproductfilter'}</span>
								</a>
							{/if}
								{/if}
							</p>
							<p class="quickview">
								<a href="#" class="quick-view button default-quick-view" title="" href="{$product.link|escape:'html':'UTF-8'}" rel="{$product.link|escape:'html':'UTF-8'}"><span>{l s='Quick view' mod='wtproductfilter'}</span>							
							</a>	
							</p>
							<p class="wishlist">
							<a class="addToWishlist button wishlist_button" onclick="WishlistCart('wishlist_block_list', 'add', '{$product.id_product|intval}', false, 1); return false;" title="" href="#">									
							<span class="text">{l s='Add Wishlist' mod='wtproductfilter'}</span>	
							</a>
							</p>
														
						</div>
					</li>
						
					{/foreach}
					</ul>
					<div class="cclearfix"></div>						
					
				</div>
				{/if}
			</div>
		</div>
	</div>
	{/foreach}
	</div>
</div>
<script type="text/javascript">
	$(window).load(function() {
		runSliderHometab();
	});

	$(window).resize(function() {
			runSliderHometab();
	});
	
	function runSliderHometab(){
	
	var item_hometab = 3;
		
		if(getWidthBrowser() > 1180)
		{	
			item_hometab = 3; 
		}
		else
		if(getWidthBrowser() > 991)
		{	
			item_hometab = 2; 
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
		if(getWidthBrowser() <=767){
			$('#tabs').tabs('destroy');
			$('#ul_tv_tab').hide();
			$('#tabs div.title_tab_hide_show').show();
			
		} else {		
			$('#tabs').tabs({ fx: { opacity: 'toggle' }});	
			$('.tabs-carousel').show();
			$('#ul_tv_tab').show();
			$('#tabs div.title_tab_hide_show').hide();
		}
		
		
			{foreach from=$tabs item=tab name=tabs}
			$("#carousel{$smarty.foreach.tabs.iteration|intval} li:nth-child("+item_hometab+")").addClass("last_item");
			$('#carousel{$smarty.foreach.tabs.iteration|intval}').carouFredSel({
				responsive: true,
				width: '100%',
				height: 'variable',
				onWindowResize: 'debounce',
				prev: '#prev{$smarty.foreach.tabs.iteration|intval}',
				next: '#next{$smarty.foreach.tabs.iteration|intval}',
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
					//$("#carousel{$smarty.foreach.tabs.iteration|intval} li:nth-child("+item_hometab+")").addClass("last_item");	
					//var n=5;
						//n=data.items.visible.length;
						
					},
					onAfter	: function(data) {
						$("#carousel{$smarty.foreach.tabs.iteration|intval} li").removeClass("first_item");
						$("#carousel{$smarty.foreach.tabs.iteration|intval} li:nth-child(1)").addClass("first_item");
				   }
				}
			});
			{/foreach}
	}
	

	
</script>
</div>
{/if}


