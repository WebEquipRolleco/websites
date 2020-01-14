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
{extends file='page.tpl'}

{block name='page_title'}
  {$cms_category.name}
{/block}

{block name='page_content'}
  {block name='cms_sub_categories'}
    {if $sub_categories}
      <p>{l s='List of sub categories in %name%:' d='Shop.Theme.Global' sprintf=['%name%' => $cms_category.name]}</p>
      <ul>
        {foreach from=$sub_categories item=sub_category}
          <li><a href="{$sub_category.link}">{$sub_category.name}</a></li>
        {/foreach}
      </ul>
    {/if}
  {/block}

  {block name='cms_sub_pages'}
    {if $cms_pages}
      {*<p>{l s='List of pages in %category_name%:' d='Shop.Theme.Global' sprintf=['%category_name%' => $cms_category.name]}</p>*}
        {foreach from=$cms_pages name=pages item=cms_page}
          {assign var=has_file value=is_file("{getcwd()}{"/img/"}{CMS::DIR}{$cms_page.id_cms}{'.png'}")}
          {if $smarty.foreach.pages.index is even}<div class="row margin-top-15">{/if}
          {if $has_file}
            <div class="col-lg-2">
                <a href="{$cms_page.link}">
                  <img src="{$urls.img_ps_url}{CMS::DIR}{$cms_page.id_cms}.png" style="max-width: 100%">
                </a>
            </div>
          {/if}
          <div class="col-lg-{if $has_file}4{else}6{/if}">
              <a href="{$cms_page.link}">{$cms_page.description|replace:'|':'<br />' nofilter}</a>
          </div>
        {if $smarty.foreach.pages.index is odd or $smarty.foreach.pages.last}</div>{/if}
        {/foreach}
      </div>
    {/if}
  {/block}
{/block}
