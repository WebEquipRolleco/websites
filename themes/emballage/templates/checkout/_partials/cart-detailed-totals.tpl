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
            <tr class="cart-summary-line">
              <td class="bold">{l s='Total HT' d='Shop.Theme.Actions'}</td>
              <td class="bold text-right" style="border-left: 0px">{$cart.totals.total_excluding_tax.value}</td>
            </tr>
            <tr class="cart-summary-line">
              <td class="bold">{l s='TVA' d='Shop.Theme.Actions'}</td>
              <td class="bold text-right" style="border-left: 0px">{Tools::displayPrice($cart.totals.total_including_tax.amount - $cart.totals.total_excluding_tax.amount)}</td>
            </tr>
            <tr class="cart-total">
              <td class="bg-darkgreen">{l s='Total TTC' d='Shop.Theme.Actions'}</td>
              <td class="bg-darkgreen bold value text-right" style="border-left: 0px">{$cart.totals.total_including_tax.value}</td>
            </tr>
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
