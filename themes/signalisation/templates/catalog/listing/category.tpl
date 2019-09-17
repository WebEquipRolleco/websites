{**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{extends file='catalog/listing/product-list.tpl'}

{block name='product_list_header'}

  <h1 class="category-title margin-top-15">{$category.name}</h1>
  {if $category.description}
    <div class="row">
      {*if $category.image.large.url}
        <div class="col-lg-3 text-center">
          <img src="{$category.image.large.url}" alt="{if !empty($category.image.legend)}{$category.image.legend}{else}{$category.name}{/if}">
        </div>
      {/if*}
      <div class="col-lg-12">
        <div id="category-description">{$category.description nofilter}</div>
      </div>
    </div>
  {/if}
  <hr class="category-bar" />
{/block}

{block name='product_list_footer'}
  <div class="row">
    <div class="col-lg-12">
      {$category.bottom_description nofilter}
    </div>
  </div>
{/block}
