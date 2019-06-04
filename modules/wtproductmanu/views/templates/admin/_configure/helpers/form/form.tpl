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

{extends file="helpers/form/form.tpl"}
{block name="field"}
	{if $input.type == 'file'}
		{$smarty.block.parent}
		{if isset($background_image)}
			<div class="col-lg-3"></div>
			<div class="col-lg-9">
				<input type="hidden" name = "image_hidden" value ="{$background_image_name|escape:'html':'UTF-8'}"/>
				<p><img class="imgm img-thumbnail" src="{$background_image|escape:'html':'UTF-8'}"/></p>
				<p><a href="{$link_delete_background|escape:'html':'UTF-8'}" id="img_delete_icon" class="delete_product_image btn btn-default"><i class="icon-trash"></i>{l s='Delete' mod='wtproductmanu'}</a></p>
			</div>
		{elseif isset($input.banner) && $input.banner != ''}
			<div class="col-lg-3"></div>
			<div class="col-lg-9">
			<p><img class="imgm img-thumbnail" src="{$input.banner|escape:'html':'UTF-8'}"/></p>
			</div>
		{/if}
	{else}
		{$smarty.block.parent}
	{/if}
{/block}