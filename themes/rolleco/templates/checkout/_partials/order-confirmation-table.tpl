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
<div id="order-items" style="border-right:0px" class="col-md-12">

  <div class="order-confirmation-table">

    {block name='order_confirmation_table'}
      <table class="table combinations-table">
        <thead>
          <th colspan="5">{l s='Order items' d='Shop.Theme.Checkout'}</th>
        </thead>
        <tbody>
          {foreach from=$products item=product}
            <tr>
              <td>
                <span class="image">
                  {if isset($product.cover)}
                    <img src="{$product.cover.small.url}" />
                  {elseif $product.id_quotation_line}
                    {assign var=line value=QuotationLine::find($product.id_quotation_line)}
                    <img src="{$line->getImageLink()}" style="width:153px; height:153px;" />
                  {/if}
                </span>
              </td>
              <td>
                {if $add_product_link}<a href="{$product.url}" target="_blank">{/if}
                  <span>{$product.name}</span>
                {if $add_product_link}</a>{/if}
                {if $product.customizations|count}
                  {foreach from=$product.customizations item="customization"}
                    <div class="customizations">
                      <a href="#" data-toggle="modal" data-target="#product-customizations-modal-{$customization.id_customization}">{l s='Product customization' d='Shop.Theme.Catalog'}</a>
                    </div>
                    <div class="modal fade customization-modal" id="product-customizations-modal-{$customization.id_customization}" tabindex="-1" role="dialog" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title">{l s='Product customization' d='Shop.Theme.Catalog'}</h4>
                          </div>
                          <div class="modal-body">
                            {foreach from=$customization.fields item="field"}
                              <div class="product-customization-line row">
                                <div class="col-sm-3 col-xs-4 label">
                                  {$field.label}
                                </div>
                                <div class="col-sm-9 col-xs-8 value">
                                  {if $field.type == 'text'}
                                    {if (int)$field.id_module}
                                      {$field.text nofilter}
                                    {else}
                                      {$field.text}
                                    {/if}
                                  {elseif $field.type == 'image'}
                                    <img src="{$field.image.small.url}">
                                  {/if}
                                </div>
                              </div>
                            {/foreach}
                          </div>
                        </div>
                      </div>
                    </div>
                  {/foreach}
                {/if}
                {hook h='displayProductPriceBlock' product=$product type="unit_price"}
              </td>
              <td class="text-center">
                {$product.price_ht}
              </td>
              <td class="text-center">
                {$product.quantity}
              </td>
              <td class="text-center bold">
                  {Tools::displayPrice($product.total_price_tax_excl)}
              </td>
            </tr>
          {/foreach}
        </tbody>
      </table>

      <div class="row">
        <div class="col-xs-12 col-lg-6 offset-lg-6">
            <table class="table combinations-table">
                <tr>
                    <td class="font-weight-bold">{l s="Total HT"}</td>
                    <td class="font-weight-bold">{$totals.total_ht.value}</td>
                </tr>
                {foreach $subtotals as $subtotal}

                    {if $subtotal.type == 'products'}
                    {elseif $subtotal.type !== 'tax'}
                      <tr>
                          <td class="font-weight-bold">{$subtotal.label}</td>
                          <td class="font-weight-bold">{$subtotal.value}</td>
                        </tr>
                    {/if}
                {/foreach}
              {if $subtotals.tax.label !== null}
                <tr class="sub">
                <td class="font-weight-bold">{$subtotals.tax.label}</td>
                <td class="font-weight-bold">{$subtotals.tax.value}</td>
              </tr>
            {/if}
            <tr class="cart-total font-weight-bold">
              <td class="bg-blue text-uppercase">{l s="Total TTC"}</td>
              <td class="bg-blue">{$totals.total_paid.value}</td>
            </tr>
          </table>
        </div>
      </div>
    {/block}
  </div>
</div>
