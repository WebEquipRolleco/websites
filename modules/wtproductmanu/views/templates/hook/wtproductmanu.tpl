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

{if isset($manu_f)}
<div class="wt-prod-manu clearfix">
<div class="container">
	<div class="wt-block-title">
	 </div>
	<div class="out-prod-manu clearfix">
		<ul id="wt-prod-manu-tabs" class="nav nav-tabs col-xs-12 col-sm-3">
			{foreach from=$manu_f item=manufacturer name=manufacturer_list}	
				<li class="nav-item">
					<a class="nav-link {if $smarty.foreach.manufacturer_list.first} active{/if}" data-toggle="tab" href="#manu_{$manufacturer.id_manufacturer|intval}" title="{$manufacturer.name|escape:'htmlall':'UTF-8'}">
					{$manufacturer.name|truncate:15:''|escape:'htmlall':'UTF-8'}
					</a>
				</li>
			{/foreach}
		</ul>
		<div class="tab-content col-xs-12 col-sm-9">
			{foreach from=$manu_f item=manufacturer name=manufacturer_list}
				<div id="manu_{$manufacturer.id_manufacturer|intval}" class="tab-pane {if $smarty.foreach.manufacturer_list.first} active{/if}">
					<div class="manu-desc col-xs-12">
					<div class="logo"><a class="image_hoverwashe" href="{$link->getmanufacturerLink($manufacturer.id_manufacturer, $manufacturer.link_rewrite)|escape:'htmlall':'UTF-8'}" title="{$manufacturer.name|escape:'htmlall':'UTF-8'}">
					<img src="{$ps_manu_img_dir|escape:'htmlall':'UTF-8'}{$manufacturer.id_manufacturer|escape:'htmlall':'UTF-8'}-logo_manu.jpg" alt="{$manufacturer.name|escape:'htmlall':'UTF-8'}" /> 
					</a>
					</div>
					<div class="desc">
						{if $manufacturer.description !=''}
								{$manufacturer.description|escape:'quotes':'UTF-8' nofilter}
							{else}
								{l s='Manufacturer Description' d='Modules.WTProductsManufacture'}
							{/if}
						</div>
					</div>
					<div class="manu-prod list_manu col-xs-12">
						<div class="owl-prod-manu">
							{$prod_manu = $manufacturer.product_list}
							{if $prod_manu && count($prod_manu) > 0}
							{$i=0}
							{foreach from=$prod_manu item=product name=product_list}
								{$i=$i+1}	
								{if $i%{$number_line_show|intval}==1}
								<div class="item ajax_block_product">								
								{/if}
								
									<div class="product-container">
										<div class="product-image-container col-sm-4">
											<a href="{$product.link|escape:'html':'UTF-8'}" title="{$product.legend|escape:html:'UTF-8'}">
												<img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'medium_default')|escape:'html':'UTF-8'}" alt="{$product.legend|escape:html:'UTF-8'}" />
											</a>
										</div>
										<div class="product-content col-sm-8">
												<h5 class="product-name"><a href="{$product.link|escape:'html':'UTF-8'}" title="{$product.legend|escape:html:'UTF-8'}">{$product.name|escape:'html':'UTF-8'}</a></h5>
												<p>{$product.description_short|strip_tags|truncate:75:'...'|escape:'quotes':'UTF-8'}</p>
											
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
										</div>
									</div>
									
							
								{if $i%{$number_line_show|intval}==0 || $i==count($prod_manu)}
								</div>
								{/if}
							
								
						
							{/foreach}
							{else}
								<p>{l s='There no product' d='Modules.WTProductsManufacture'}</p>
							{/if}
						</div>
					</div>
				</div>
			{/foreach}
		</div>
	</div>
	</div>
	{if $used_slider == 1}
	<script type="text/javascript">
		$(document).ready(function() {
		var owl = $(".owl-prod-manu");
		imagesLoaded(owl, function() {
			owl.owlCarousel({
				  loop: true,
					responsive: {
						0: { items: 1},
						464:{ items: 1, slideBy: 1},
						750:{ items: 1, slideBy: 1},
						974:{ items: 2, slideBy: 2},
						1170:{ items: 2, slideBy: 2}
						
					},
				  dots: false,
				  nav: true,
				  margin:30 
				  });
			});
		});
	</script>
	{/if}
</div>
{/if}