{block name='cart_detailed_totals'}
  {assign var=nb_products value=Context::getContext()->cart->nbProducts()}
  {assign var=options_ht value=OrderOptionCart::getCartTotal()}
  {assign var=options_ttc value=$options_ht * 1.2}
  {assign var=total_ht value=$cart.totals.total_excluding_tax.amount + $options_ht}
  {assign var=total_ttc value=$cart.totals.total_including_tax.amount + $options_ttc}
  {if $nb_products}
    <div class="cart-detailed-totals">

        <table class="table combinations-table">
          <thead>
            <tr>
              <th class="text-center">{l s="Total produits et options HT" d='Shop.Theme.Checkout'}</th>
              <th class="text-center">{Tools::displayPrice(Context::getContext()->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS) + $options_ht)}</th>
          </thead>
          <tbody>
            <tr>
              <td>
                <div class="bold">{l s="Frais de port"}</div>
                <em class="text-muted">{l s="Hors îles non accessibles par un pont gratuit" d='Shop.Theme.Checkout'}</em>
              </td>
              <td class="text-center">
                <span {if $cart.subtotals.shipping.amount == 0}class="bold text-danger"{/if}>
                  {$cart.subtotals.shipping.value}
                </span>
              </td>
          </tbody>
        </table>

        {block name='cart_voucher'}
          {include file='checkout/_partials/cart-voucher.tpl'}
        {/block}

        <table class="table combinations-table no-bottom">
          <tbody>
            <tr class="cart-summary-line">
              <td class="bold">{l s='Total HT' d='Shop.Theme.Checkout'}</td>
              <td class="bold text-right" style="border-left: 0px">{Tools::displayPrice($total_ht)}</td>
            </tr>
            <tr class="cart-summary-line">
              <td class="bold">{l s='TVA' d='Shop.Theme.Checkout'}</td>
              <td class="bold text-right" style="border-left: 0px">{Tools::displayPrice($total_ttc - $total_ht)}</td>
            </tr>
            <tr class="cart-total">
              <td class="bg-blue">{l s='Total TTC' d='Shop.Theme.Checkout'}</td>
              <td class="bg-blue value text-right" style="border-left: 0px">{Tools::displayPrice($total_ttc)}</td>
            </tr>
            <tr class="cart-summary-line">
              <td class="text-muted" style="padding:5px;">{l s='Dont éco-particiation' d='Shop.Theme.Checkout'}</td>
              <td class="text-muted text-right" style="padding:5px; border-left:0px">{Tools::displayPrice(Cart::getEcoTax())}</td>
            </tr>
            <tr>
              <td colspan="2" class="text-center">
                <a href="{$link->getPageLink("cart")}?action=show&dl_pdf" target="_blank" class="btn btn-info bold">
                  <i class="fa fa-print"></i> {l s="Imprimer" d='Shop.Theme.Actions'}
                </a>
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
