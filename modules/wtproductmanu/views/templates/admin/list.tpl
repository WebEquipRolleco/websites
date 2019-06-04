{**
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2016 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if count($banners) > 0}
<div class="panel"><h3><i class="icon-list-ul"></i> {l s='Banner List' d='Modules.WTProductsManufacture.Admin'}
	<span class="panel-heading-action">
		<a id="desc-product-new" class="list-toolbar-btn" href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=wtproductmanu&addBanner=1">
			<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Add new" data-html="true">
				<i class="process-icon-new "></i>
			</span>
		</a>
	</span>
	</h3>
	<div id="slidesContent">
		<div id="slides">
			{foreach from=$banners item=banner}
				<div id="slides_{$banner.id_wtproductmanu|intval}" class="panel">
					<div class="row">
						<div class="col-md-2">
								<h4 class="pull-left">ID : #{$banner.id_wtproductmanu|escape:'html':'UTF-8'}</h4>
						</div>
						<div class="col-md-2">
								<h4 class="pull-left">{$banner.title|escape:'html':'UTF-8'}</h4>
						</div>
						<div class="col-md-2">
							<h4 class="pull-left">{l s='Link' d='Modules.WTProductsManufacture.Admin'}: {$banner.link|escape:'html':'UTF-8'}</h4>
						</div>
						<div class="col-md-2">
							<h4 class="pull-left">{l s='Position' d='Modules.WTProductsManufacture.Admin'}: {$banner.position|escape:'html':'UTF-8'}</h4>
						</div>
						<div class="col-md-4">	
							<div class="btn-group-action pull-right">
								<a class="btn btn-default"
									href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=wtproductmanu&id_wtproductmanu={$banner.id_wtproductmanu|intval}&editBanner=1">
									<i class="icon-edit"></i>
									{l s='Edit' d='Modules.WTProductsManufacture.Admin'}
								</a>
								<a class="btn btn-default"
									href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=wtproductmanu&id_wtproductmanu={$banner.id_wtproductmanu|intval}&deleteBanner=1">
									<i class="icon-trash"></i>
									{l s='Delete' d='Modules.WTProductsManufacture.Admin'}
								</a>
								{$banner.status|escape:'quotes':'UTF-8'}
							</div>
						</div>
					</div>
				</div>
			{/foreach}
		</div>
	</div>
</div>
{/if}