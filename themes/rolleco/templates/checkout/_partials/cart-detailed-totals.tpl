{block name='cart_detailed_totals'}
  {assign var=nb_products value=Context::getContext()->cart->nbProducts()}
  {if $nb_products}
    <div class="cart-detailed-totals">
      

        <table class="table combinations-table">
          <tbody>
            {foreach from=$cart.subtotals item="subtotal"}
              {if $subtotal.value && $subtotal.type !== 'tax'}
                <tr class="cart-summary-line" id="cart-subtotal-{$subtotal.type}">
                  <td class="label{if 'products' === $subtotal.type} js-subtotal{/if}">
                    {if 'products' == $subtotal.type}
                      {$nb_products} {l s="Article(s)"}
                    {else}
                      {$subtotal.label}
                    {/if}
                  </td>
                  <td class="text-center {if $subtotal.type === 'shipping'}shipping-fee{/if}">
                    {$subtotal.value}
                    {if $subtotal.type === 'shipping'}
                     <small>{hook h='displayCheckoutSubtotalDetails' subtotal=$subtotal}</small>
                    {/if}
                  </td>
                </tr>
              {/if}
            {/foreach}
            {assign var=options value=OrderOptionCart::findByCart()}
            {if $options|@count}
              <tr class="cart-summary-line">
                <td class="label">{$options|@count} option(s)</td>
                <td class="text-center">{Tools::displayPrice(OrderOptionCart::getCartTotal())}</td>
              </tr>
            {/if}
          </tbody>
        </table>

        {block name='cart_voucher'}
          {include file='checkout/_partials/cart-voucher.tpl'}
        {/block}

        <table class="table combinations-table no-bottom">
          <tbody>
            <tr class="cart-total">
              <td class="bg-blue">{$cart.totals.total.label} {$cart.labels.tax_short}</td>
              <td class="bg-blue value text-right" style="border-left: 0px">{$cart.totals.total.value}</td>
            </tr>
            {if $cart.subtotals.tax}
              <tr class="cart-summary-line">
                <td>{$cart.subtotals.tax.label}</td>
                <td class="value text-right">{$cart.subtotals.tax.value}</td>
              </tr>
            {/if}
            <tr>
              <td colspan="2" class="text-center">
                <a href="{$urls.pages.order}" class="btn btn-success bold">
                  {l s='Proceed to checkout' d='Shop.Theme.Actions'}
                </a>
                {*hook h='displayExpressCheckout'*}
              </td>
            </tr>
          </tbody>
        </table>

    </div>
  {/if}
{/block}
