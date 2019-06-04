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
<div class="wt_home_filter_product_tab col-xs-12 col-sm-12" id="wt_home_filter_product_tab_ssl" wt_base_ssl="{$path_ssl|escape:'html':'UTF-8'}">

<div id="tabs">
	<ul id="ul_tv_tab" class="title-tab">
		{$i=0}
		{foreach from=$tabs item=tab name=tabs}
			{$i=$i+1}
			<li wt-name-module="{$name_module|escape:'html':'UTF-8'}" type-tab="{$tab.product_type|escape:'html':'UTF-8'}" id-tab="{$smarty.foreach.tabs.iteration|escape:'html':'UTF-8'}" class=" {if $smarty.foreach.tabs.first}first ui-tabs-selected ui-state-active{elseif $smarty.foreach.tabs.last}last{/if} ">
				<a class="title_block" href="javascript:void(0)">
				{if isset($tab.title)}
				{$tab.title|escape:'html':'UTF-8'}
				{else}
				{l s='not title' d='Modules.WTProductsFilter'}
				{/if}
				</a>
			</li>
		{/foreach}
	</ul>

	<div class="content-tab-product">
	{foreach from=$tabs item=tab name=tabs}
		
	<div class="tabs-carousel" id="tabs-{$smarty.foreach.tabs.iteration|escape:'html':'UTF-8'}">
		{if $tab.tab_product_list->product_list && count($tab.tab_product_list->product_list)>0}
		<a id="prev{$smarty.foreach.tabs.iteration|intval}" class="btn prev" href="#">&lt;</a>
		<a id="next{$smarty.foreach.tabs.iteration|intval}" class="btn next" href="#">&gt;</a>
		<div class="cycleElementsContainer" id="cycle-{$smarty.foreach.tabs.iteration|escape:'html':'UTF-8'}">
			<div id="elements-{$smarty.foreach.tabs.iteration|escape:'html':'UTF-8'}">
				<div class="list_carousel responsive">
					<ul id="carousel{$smarty.foreach.tabs.iteration|intval}" class="product-list">
					{$i=0}
					{foreach from=$tab.tab_product_list->product_list item=product name=product_list}
						{$i=$i+1}
					<li class="ajax_block_product {if $smarty.foreach.product_list.first|intval}first_item{elseif $smarty.foreach.product_list.last|intval}last_item{/if} js-product-miniature" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}">
					<div class="product-block wt_container_thumbnail" wt-name-module="{$name_module|escape:'html':'UTF-8'}" id-tab="{$smarty.foreach.tabs.iteration|escape:'html':'UTF-8'}" wt-data-id-product="{$product.id_product|intval}">
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
									<div class="thumbs-content" id="{$name_module|escape:'html':'UTF-8'}-{$smarty.foreach.tabs.iteration|escape:'html':'UTF-8'}-wt-thumbs-content-{$product.id_product|intval}"></div>
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
	</div>
	{/foreach}
	
</div>
<script type="text/javascript" src="{$path_|escape:'html':'UTF-8'}views/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript">
	$(window).load(function() {
		runSliderHometab();
	});

	$(window).resize(function() {
			runSliderHometab();
	});
	
	function runSliderHometab(){
	
	var item_hometab = 5;
		if(getWidthBrowser() > 1380)
		{	
			item_hometab = 4; 
		}
		else
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
		
		/*
		if(getWidthBrowser() <=767){
			$('#tabs div.title_tab_hide_show').show();
			
		} else {		
			$('#tabs div.title_tab_hide_show').hide();
		}
		*/
		
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
					width:260,
					height: 'variable',
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
						$("#carousel{$smarty.foreach.tabs.iteration|intval} li").removeClass("first_item");
						$("#carousel{$smarty.foreach.tabs.iteration|intval} li:nth-child(1)").addClass("first_item");
				   }
				}
			});
			{if $isMobile==0}
			{break}
			{/if}
			{/foreach}
	}
	

</script>
</div>
</div>
{/if}


