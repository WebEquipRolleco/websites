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
<div class="panel">
	<div class="panel-heading">
		  {l s='Tab List' d='Modules.WTProductsFilter'}
		<a style="text-decoration:none;" href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=wtproductfilter&addTab=1">
		<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Add new tab" data-html="true">
				<img src="{$path|escape:'html':'UTF-8'}/views/img/add.png" alt="" /> {l s='Add' d='Modules.WTProductsFilter'}
			</span>
		</a>
	</div>
	<div class="panel">
		<div class="panel">
			<div class="col-lg-1">
			<h3 class="pull-center" style="margin-left:20px;">{l s='Order' d='Modules.WTProductsFilter'}</h3>
			</div>
			<div class="col-md-3">
					<h3 class="pull-center" style="margin-left:30px;">{l s='Title tab' d='Modules.WTProductsFilter'}</h3>
			</div>
			<div class="col-md-5"  style="">
					<h3 class="pull-center" style="margin-left:10px;">{l s='Get product from' d='Modules.WTProductsFilter'}</h3>
			</div>
			<div class="col-md-3"  style="">
					<h3 class="pull-center" style="margin-left:20px;">{l s='Action' d='Modules.WTProductsFilter'}</h3>
			</div>
		</div>
	<div id="slidesContent">
		<div id="tabs">
		{assign var='dem' value=0}
			{foreach from=$tabs item=tab}
			{$dem=$dem+1}
				<div id="tabs_{$tab.id_tab|escape:'html':'UTF-8'}" class="panel" style="border-radius:3px;margin-bottom:8px;box-shadow:none;padding:3px 40px;">
					<div class="row">
						<div class="col-lg-1">
						<span style="position:absolute;top:15px;">{$dem|escape:'html':'UTF-8'}</span>
							<span style="position:absolute;top:15px;left:30px;"><i class="icon-arrows "></i></span>
						</div>
						<div class="col-md-3"  style="margin-top: 10px;">
							<h4 class="pull-left">#{$tab.id_tab|escape:'html':'UTF-8'} - {$tab.title|escape:'html':'UTF-8'}</h4>
						</div>
						<div class="col-md-5"  style="margin-top: 10px;">
							<h4 class="pull-left">{$tab.product_type|escape:'html':'UTF-8'}</h4>
						</div>
						<div class="col-md-3"  style="margin-top: 10px;">
							<div class="btn-group-action pull-right">
								{$tab.status|escape:'quotes':'UTF-8'}								
								<a class="btn btn-default"
									href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=wtproductfilter&id_tab={$tab.id_tab|escape:'html':'UTF-8'}">
									<i class="icon-edit"></i>
									{l s='Edit' d='Modules.WTProductsFilter'}
								</a>
								<a class="btn btn-default" href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=wtproductfilter&delete_id_tab={$tab.id_tab|escape:'html':'UTF-8'}">
									<i class="icon-trash"></i>
									{l s='Delete' d='Modules.WTProductsFilter'}
								</a>
							</div>
						</div>
					</div>
				</div>
			{/foreach}
		</div>
	</div>
	</div>
</div>
