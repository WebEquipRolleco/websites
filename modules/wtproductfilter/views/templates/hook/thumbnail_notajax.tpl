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

{if $wt_thumbnails}
	<div class="thumbs-content">
		<a id="view_scroll_left{$wt_thumbnails_key|escape:'html':'UTF-8'}" class="view_scroll_left {if isset($wt_thumbnails) && count($wt_thumbnails) <= 4}hidden{/if}" href="javascript:{}" title="Other views" rel="11"><i class="icon-chevron-up"></i></a>
		<div id="thumbs_list{$wt_thumbnails_key|escape:'html':'UTF-8'}" class="thumbs_list {if isset($wt_thumbnails) && count($wt_thumbnails) > 3}show_sroll{/if}">
			<ul id="thumbs_list_frame{$wt_thumbnails_key|escape:'html':'UTF-8'}" class="thumbs_list_frame">
				{foreach from=$wt_thumbnails item=image name=thumbnails}
					{assign var=imageIds value="`$product.id_product`-`$image.id_image`"}
					{if !empty($image.legend)}
						{assign var=imageTitle value=$image.legend|escape:'html':'UTF-8'}
					{else}
						{assign var=imageTitle value=$product.name|escape:'html':'UTF-8'}
					{/if}
					<li id="thumbnail{$wt_thumbnails_key|escape:'html':'UTF-8'}_{$image.id_image|escape:'html':'UTF-8'}"{if $smarty.foreach.thumbnails.last} class="last"{/if}>
						<a 
							href="javascript:void(0);"
							data-fancybox-group="other-views-{$product.id_product|intval}" tv-img-src="{$link->getImageLink($product.link_rewrite, $imageIds, 'home_default')|escape:'html':'UTF-8'}"
							class="fancybox image_hoverwashe"
							title="{$imageTitle|escape:'quotes':'UTF-8'}">

							<img class="img-responsive" id="thumb_{$product.id_product|intval}_{$image.id_image|escape:'html':'UTF-8'}" src="{$link->getImageLink($product.link_rewrite, $imageIds, 'small_default')|escape:'html':'UTF-8'}" alt="{$imageTitle|escape:'html':'UTF-8'}" title="{$imageTitle|escape:'html':'UTF-8'}" itemprop="image" />
						<span class="hover_bkg_light"></span>
						</a>
					</li>
				{/foreach}
			</ul>
		</div>
		<a id="view_scroll_right{$wt_thumbnails_key|escape:'html':'UTF-8'}" class="view_scroll_right {if isset($wt_thumbnails) && count($wt_thumbnails) <= 4}hidden{/if}" href="javascript:{}" title="Other views" rel="11"><i class="icon-chevron-down"></i></a>
</div>
	<script type="text/javascript">
		$('#thumbs_list{$wt_thumbnails_key|intval}').serialScroll({
			items:'li:visible',
			prev:'#view_scroll_left{$wt_thumbnails_key|intval}',
			next:'#view_scroll_right{$wt_thumbnails_key|intval}',
			axis:'y',
			offset:0,
			start:0,
			stop:true,
			onBefore:serialScrollFixLock,
			duration:700,
			step: 2,
			lazy: true,
			lock: false,
			force:false,
			cycle:false
		});
		$('#thumbs_list{$wt_thumbnails_key|intval}').trigger('goto', 1);// SerialScroll Bug on goto 0 ?
		$('#thumbs_list{$wt_thumbnails_key|intval}').trigger('goto', 0);

		function serialScrollFixLock(event, targeted, scrolled, items, position)
		{
			serialScrollNbImages = $('#thumbs_list{$wt_thumbnails_key|intval} li:visible').length;
			serialScrollNbImagesDisplayed = 3;

			var leftArrow = position == 0 ? true : false;
			var rightArrow = position + serialScrollNbImagesDisplayed >= serialScrollNbImages ? true : false;

			$('#view_scroll_left{$wt_thumbnails_key|intval}').css('cursor', leftArrow ? 'default' : 'pointer').css('display', leftArrow ? 'block' : 'block').fadeTo(0, leftArrow ? 0 : 1);
			$('#view_scroll_right{$wt_thumbnails_key|intval}').css('cursor', rightArrow ? 'default' : 'pointer').fadeTo(0, rightArrow ? 0 : 1).css('display', rightArrow ? 'block' : 'block');
			return true;
		}
	</script>
{/if}