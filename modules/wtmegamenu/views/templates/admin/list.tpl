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

<div class="panel">
	<h3><i class="icon-list-ul"></i> {l s='Menu List' d='Modules.WTMegamenu.Admin'}
	<span class="panel-heading-action">
		<a id="desc-product-new" class="list-toolbar-btn" href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=wtmegamenu&addMenu=1">
			<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Add new" data-html="true">
				<i class="process-icon-new "></i>
			</span>
		</a>
	</span>
	</h3>
	<div id="menuContent">
		<div id="menus">
			{foreach from=$info_menus item=info_menu}
				<div id="menu_{$info_menu.id_wtmegamenu|intval}" class="panel">
					<div class="row">
						<div class="col-md-2">
							<h4 class="pull-left">#{$info_menu.id_wtmegamenu|escape:'html':'UTF-8'}</h4>
						</div>
						<div class="col-md-2">
							<h4 class="pull-left">{$info_menu.title|escape:'html':'UTF-8'}</h4>
						</div>
						<div class="col-md-8">	
							<div class="btn-group-action pull-right">
								{if $info_menu.dropdown == 0 || $info_menu.type_link == 1}
									<a class="btn btn-default"
										href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=wtmegamenu&id_wtmegamenu={$info_menu.id_wtmegamenu|intval}&buildMenu=1">
										<i class="icon-layer"></i>
										{l s='Build SubMenu' d='Modules.WTMegamenu.Admin'}
									</a>
								{/if}
								<a class="btn btn-default"
									href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=wtmegamenu&id_wtmegamenu={$info_menu.id_wtmegamenu|intval}">
									<i class="icon-edit"></i>
									{l s='Edit' d='Modules.WTMegamenu.Admin'}
								</a>
								<a class="btn btn-default"
									href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=wtmegamenu&delete_id_menu={$info_menu.id_wtmegamenu|intval}">
									<i class="icon-trash"></i>
									{l s='Delete' d='Modules.WTMegamenu.Admin'}
								</a>
								{$info_menu.status|escape:'quotes':'UTF-8'}
							</div>
						</div>
					</div>
				</div>
			{/foreach}
		</div>
	</div>
	<script type="text/javascript">
		$(function() {
			var $myMenus = $("#menus");
			$myMenus.sortable({
				opacity: 0.6,
				cursor: "move",
				update: function() {
					var order = $(this).sortable("serialize") + "&action=updateMenusPosition";
					$.post("{$url_base|escape:'html':'UTF-8'}modules/wtmegamenu/ajax_wtmegamenu.php?secure_key={$secure_key|escape:'html':'UTF-8'}", order);
					}
				});
			$myMenus.hover(function() {
				$(this).css("cursor","move");
				},
				function() {
				$(this).css("cursor","auto");
			});
		});
	</script>
</div>