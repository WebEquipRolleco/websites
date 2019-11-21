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
{extends file='customer/page.tpl'}

{block name='page_title'}
  {l s='Order details' d='Shop.Theme.Customeraccount'}
{/block}

{block name='page_content'}
  {block name='order_infos'}
    <div id="order-infos">
      <table class="table combinations-table">
        <thead>
          <tr>
            <th colspan="2">
              {if $order.details.recyclable}
                <span class="badge bg-green" title="{l s='You have given permission to receive your order in recycled packaging.' d='Shop.Theme.Customeraccount'}">
                  <i class="fa fa-leaf"></i>
                </span>
                &nbsp;
              {/if}
              {l s='Order Reference %reference% - placed on %date%' d='Shop.Theme.Customeraccount' sprintf=['%reference%' => $order.details.reference, '%date%' => $order.details.order_date]}
            </th>
            <th class="text-right">
              {if $order.details.invoice_url}
                <a href="{$order.details.invoice_url}" class="btn btn-default" title="{l s='Download your invoice as a PDF file.' d='Shop.Theme.Customeraccount'}">
                  <i class="fa fa-file-pdf"></i>
                </a>
              {/if}
              {if $order.follow_up}
                <a class="btn btn-default" title="{l s='Click the following link to track the delivery of your order' d='Shop.Theme.Customeraccount'}">
                  <i class="fa fa-truck"></i>
                </a>
              {/if}
              <a href="{$order.details.reorder_url}" class="btn btn-success" title="{l s='Reorder' d='Shop.Theme.Actions'}">
                <i class="fa fa-cart-arrow-down"></i>
              </a>
              <a href="{$link->getPageLink('AfterSaleRequest&order='|cat:$order.details.reference)}" class="btn btn-danger" title="{l s='Ouvrir un SAV' d='Shop.Theme.Actions'}">
                <i class="fa fa-exchange"></i>
              </a>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="bold">{l s='Carrier' d='Shop.Theme.Checkout'}</td>
            <td>{$order.carrier.name|capitalize}</td>
            <td></td>
          </tr>
          <tr>
            <td class="bold">{l s='Payment method' d='Shop.Theme.Checkout'}</td>
            <td>{$order.details.payment}</td>
            <td></td>
          </tr>
          {if $order.details.gift_message}
            <tr>
              <td colspan="3">
                <div class="row">
                  <div class="col-lg-1">
                    <span class="btn btn-success" title="{l s='You have requested gift wrapping for this order.' d='Shop.Theme.Customeraccount'}" disabled>
                      <i class="fa fa-gift"></i>
                    </span>
                  </div>
                  <div class="col-lg-11">
                    {l s='Message' d='Shop.Theme.Customeraccount'} {$order.details.gift_message nofilter}
                  </div>
                </div>
              </td>
            </tr>
          {/if}
        </tbody>
      </table>
    </div>
  {/block}

  {block name='order_history'}
    <section id="order-history">
      <table class="table combinations-table table-labeled">
        <thead>
          <tr>
            <th colspan="2" class="bg-blue uppercase">
              {l s='Follow your order\'s status step-by-step' d='Shop.Theme.Customeraccount'}
            </th>
          </tr>
        </thead>
        <tbody>
          {foreach from=$order.history item=state}
            <tr>
              <td>{$state.history_date}</td>
              <td class="text-center">{$state.ostate_name}</td>
            </tr>
          {/foreach}
        </tbody>
      </table>
    </section>
  {/block}

  {block name='addresses'}
    <div class="addresses">
      {if $order.addresses.delivery}
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
          <table id="delivery-address" class="table combinations-table">
            <thead>
              <tr>
                <th class="bg-blue">
                  {l s='Adresse de livraison' d='Shop.Theme.Checkout'}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="bold">{$order.addresses.delivery.alias}</td>
              </tr>
              <tr>
                <td>
                  {$order.addresses.delivery.formatted nofilter}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      {/if}
      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12s">
        <table id="invoice-address" class="table combinations-table">
            <thead>
              <tr>
                <th class="bg-blue">
                  {l s='Adresse de facturation' d='Shop.Theme.Checkout'}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="bold">{$order.addresses.invoice.alias}</td>
              </tr>
              <tr>
                <td>
                  {$order.addresses.invoice.formatted nofilter}
                </td>
              </tr>
            </tbody>
          </table>
      </div>
    </div>
  {/block}

  {$HOOK_DISPLAYORDERDETAIL nofilter}

  {block name='order_detail'}
    {if $order.details.is_returnable}
      {include file='customer/_partials/order-detail-return.tpl'}
    {else}
      {include file='customer/_partials/order-detail-no-return.tpl'}
    {/if}
  {/block}

  {block name='order_carriers'}
    {if $order.shipping}
      <div class="box">
        <table class="table table-striped table-bordered hidden-sm-down">
          <thead class="thead-default">
            <tr>
              <th>{l s='Date' d='Shop.Theme.Global'}</th>
              <th>{l s='Carrier' d='Shop.Theme.Checkout'}</th>
              <th>{l s='Weight' d='Shop.Theme.Checkout'}</th>
              <th>{l s='Shipping cost' d='Shop.Theme.Checkout'}</th>
              <th>{l s='Tracking number' d='Shop.Theme.Checkout'}</th>
            </tr>
          </thead>
          <tbody>
            {foreach from=$order.shipping item=line}
              <tr>
                <td>{$line.shipping_date}</td>
                <td>{$line.carrier_name}</td>
                <td>{$line.shipping_weight}</td>
                <td>{$line.shipping_cost}</td>
                <td>{$line.tracking nofilter}</td>
              </tr>
            {/foreach}
          </tbody>
        </table>
        <div class="hidden-md-up shipping-lines">
          {foreach from=$order.shipping item=line}
            <div class="shipping-line">
              <ul>
                <li>
                  <strong>{l s='Date' d='Shop.Theme.Global'}</strong> {$line.shipping_date}
                </li>
                <li>
                  <strong>{l s='Carrier' d='Shop.Theme.Checkout'}</strong> {$line.carrier_name}
                </li>
                <li>
                  <strong>{l s='Weight' d='Shop.Theme.Checkout'}</strong> {$line.shipping_weight}
                </li>
                <li>
                  <strong>{l s='Shipping cost' d='Shop.Theme.Checkout'}</strong> {$line.shipping_cost}
                </li>
                <li>
                  <strong>{l s='Tracking number' d='Shop.Theme.Checkout'}</strong> {$line.tracking nofilter}
                </li>
              </ul>
            </div>
          {/foreach}
        </div>
      </div>
    {/if}
  {/block}

  {block name='order_messages'}
    {include file='customer/_partials/order-messages.tpl'}
  {/block}
{/block}
