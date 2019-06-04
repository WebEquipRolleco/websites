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
	<h3><i class="icon-list-ul"></i> {l s='Rows Of Menu' d='Modules.WTMegamenu.Admin'}
	<span class="panel-heading-action">
		<a id="desc-product-new" class="list-toolbar-btn" href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=wtmegamenu&id_wtmegamenu={$id_wtmegamenu|intval}&addRow=1">
			<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Add new" data-html="true">
				<i class="process-icon-new "></i>
			</span>
		</a>
	</span>
	</h3>
	<div class="form-wrapper" id="menuRowContent">
		{foreach from=$info_rows item=info_row name=info_row}
			<div class="wt-menu-row container-fluid">
				<h4>{l s='Row' mod='wtmegamenu'} #{$smarty.foreach.info_row.iteration|intval}
					<a class="btn btn-default" href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=wtmegamenu&id_wtmegamenu={$id_wtmegamenu|intval}&id_row={$info_row.id_row|intval}&delete_row=1"><i class="icon-trash"></i>{l s='Delete' mod='wtmegamenu'}
					</a>
					<a class="btn btn-default" href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=wtmegamenu&id_wtmegamenu={$id_wtmegamenu|intval}&id_row={$info_row.id_row|intval}">
						<i class="icon-edit"></i>{l s='Edit' d='Modules.WTMegamenu.Admin'}
					</a>
					{$info_row.status|escape:'quotes':'UTF-8'}
				</h4>
				<div class="wt-menu-column-content">
					<a class="btn btn-default btn-lg button-new-item" href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=wtmegamenu&id_wtmegamenu={$id_wtmegamenu|intval}&id_row={$info_row.id_row|intval}&addCol=1"><i class="icon-plus-sign"></i>{l s='Add Column' mod='wtmegamenu'}
					</a>
					<div class="wt-menu-column">
						{foreach from=$info_row.list_col item=info_col}
							<div id="col_{$info_col.id_column|intval}" class="wt-column {$info_col.width|escape:'html':'UTF-8'}">
								<h4>
								<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Delete Colum' mod='wtmegamenu'}" data-html="true">
									<a class="btn btn-default" href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=wtmegamenu&id_wtmegamenu={$id_wtmegamenu|intval}&id_row={$info_row.id_row|intval}&id_column={$info_col.id_column|intval}&delete_col=1">
										<i class="icon-trash"></i>
									</a>
									</span>
									<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Edit Column' mod='wtmegamenu'}" data-html="true">
									<a class="btn btn-default" href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=wtmegamenu&id_wtmegamenu={$id_wtmegamenu|intval}&id_row={$info_row.id_row|intval}&id_column={$info_col.id_column|intval}">
										<i class="icon-edit"></i>
									</a>
									</span>
									<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Add new Item' mod='wtmegamenu'}" data-html="true">
									<a class="btn btn-default" href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=wtmegamenu&id_wtmegamenu={$id_wtmegamenu|intval}&id_row={$info_row.id_row|intval}&id_column={$info_col.id_column|intval}&addMenuItem=1">
										<i class="process-icon-new "></i>
									</a>
									</span>
								</h4>
								<ul class="block-items">
									{foreach from=$info_col.list_menu_item item=sub_menu_item}
										<li id="menuitem_{$sub_menu_item.id_item|intval}" class="{if $sub_menu_item.type_item == 1}item-header{else}item-line{/if}">
											<span>{$sub_menu_item.title|escape:'html':'UTF-8'}</span>
											<div class="group-action">
												<a class="btn btn-default" href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=wtmegamenu&id_wtmegamenu={$id_wtmegamenu|intval}&id_row={$info_row.id_row|intval}&id_column={$info_col.id_column|intval}&id_item={$sub_menu_item.id_item|intval}" title="{l s='Edit Item' mod='wtmegamenu'} ">
													<i class="icon-edit"></i>
												</a>
												<a class="btn btn-default" href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=wtmegamenu&id_wtmegamenu={$id_wtmegamenu|intval}&id_row={$info_row.id_row|intval}&id_column={$info_col.id_column|intval}&id_item={$sub_menu_item.id_item|intval}&delete_submenu_item=1" title="{l s='delete Item' mod='wtmegamenu'} "><i class="icon-trash"></i>
												</a>
												{$sub_menu_item.status|escape:'quotes':'UTF-8'}
											</div>
											{if $sub_menu_item.type_link == 3}
											<div class="text-html">
												{$sub_menu_item.text|escape:'quotes':'UTF-8'}
											</div>
											{/if}
											{if $sub_menu_item.type_link == 4}
												#{l s='Product ID' mod='wtmegamenu'}: {$sub_menu_item.id_product|intval}
											{/if}
										</li>
									{/foreach}
								</ul>
							</div>
						{/foreach}
					</div>
				</div>
			</div>
		{/foreach}
	</div>
	<div class="panel-footer">
		<a href="index.php?controller=AdminModules&amp;configure=wtmegamenu&amp;token={$token|escape:'html':'UTF-8'}" class="btn btn-default">
		<i class="process-icon-back"></i> {l s='Back to list' d='Modules.WTMegamenu.Admin'}</a>
	</div>
	<script type="text/javascript">
		$(function() {
			var $myMenus = $("ul.block-items");
			$myMenus.sortable({
				opacity: 0.6,
				cursor: "move",
				update: function() {
					var order = $(this).sortable("serialize") + "&action=updateMenusItemPosition";
					$.post("{$url_base|escape:'html':'UTF-8'}modules/wtmegamenu/ajax_wtmegamenu.php?secure_key={$secure_key|escape:'html':'UTF-8'}", order);
					}
				});
			$myMenus.hover(function() {
				$(this).css("cursor","move");
				},
				function() {
				$(this).css("cursor","auto");
			});
			
			var $myColumns = $("div.wt-menu-column");
			$myColumns.sortable({
				opacity: 0.6,
				cursor: "move",
				update: function() {
					var order1 = $(this).sortable("serialize") + "&action=updateColumnsPosition";
					$.post("{$url_base|escape:'html':'UTF-8'}modules/wtmegamenu/ajax_wtmegamenu.php?secure_key={$secure_key|escape:'html':'UTF-8'}", order1);
					}
				});
			$myColumns.hover(function() {
				$(this).css("cursor","move");
				},
				function() {
				$(this).css("cursor","auto");
			});
		});
	</script>
</div>