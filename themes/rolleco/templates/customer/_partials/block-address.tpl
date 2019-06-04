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
{block name='address_block_item'}
  <article id="address-{$address.id}" class="address" data-id-address="{$address.id}">
    <div class="address-header">
      <strong>{$address.alias}</strong>
      {if $first}
        <span class="text-muted">{l s="(Facturation)"}</span>
      {/if}
    </div>
    <div class="address-body">
      
      <address>{$address.formatted nofilter}</address>
    </div>

    {block name='address_block_item_actions'}
      <div class="address-footer text-center">
        {if $first}
          <span class="text-muted">{l s="Non modifiable"}</span>
        {else}
          <a href="{url entity=address id=$address.id}" class="btn btn-warning" data-link-action="edit-address" title="{l s='Update' d='Shop.Theme.Actions'}">
            <i class="fa fa-edit"></i>
          </a>
          <a href="{url entity=address id=$address.id params=['delete' => 1, 'token' => $token]}" class="btn btn-danger" data-link-action="delete-address" title="{l s='Delete' d='Shop.Theme.Actions'}">
            <i class="fa fa-trash-alt"></i>
          </a>
        {/if}
      </div>
    {/block}
  </article>
{/block}
