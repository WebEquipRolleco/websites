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
{block name='address_selector_blocks'}
  {foreach from=$addresses item=address name=addresses}
    <article id="{$name|classname}-address-{$address.id}" class="address-item{if $address.id == $selected} selected{/if}">
      <header class="h4">
        <label class="radio-block">
          <span class="custom-radio">
            <input type="radio" name="{$name}" value="{$address.id}" {if $address.id == $selected}checked{/if}>
            <span></span>
          </span>
          <span class="address-alias h4">
            {$address.alias}
            {if $smarty.foreach.addresses.first}
              <small class="text-muted">{l s="(Facturation)"}</small>
            {/if}
          </span>
          <div class="address">{$address.formatted nofilter}</div>
        </label>
      </header>
      <hr>
      <footer class="address-footer bg-grey">
        {if $interactive}
          {if $smarty.foreach.addresses.first}
            <small class="text-muted">{l s="Non modifiable"}</small>
          {else}
            <a href="{url entity='order' params=['id_address' => $address.id, 'editAddress' => $type, 'token' => $token]}" class="btn btn-warning edit-address" data-link-action="edit-address" title="{l s='Edit' d='Shop.Theme.Actions'}">
              <i class="fa fa-edit edit"></i>
            </a>
            <a href="{url entity='order' params=['id_address' => $address.id, 'deleteAddress' => true, 'token' => $token]}" class="btn btn-danger delete-address"
            data-link-action="delete-address" title="{l s='Delete' d='Shop.Theme.Actions'}">
              <i class="fa fa-trash-alt delete"></i>
            </a>
          {/if}
        {/if}
      </footer>
    </article>
  {/foreach}
  {if $interactive}
    <p>
      <button class="ps-hidden-by-js form-control-submit center-block" type="submit">{l s='Save' d='Shop.Theme.Actions'}</button>
    </p>
  {/if}
{/block}
