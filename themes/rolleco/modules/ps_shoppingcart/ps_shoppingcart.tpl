{assign var=nb_products value=$cart->nbProducts()}
<div id="_desktop_cart">
  <div class="blockcart cart-preview {if $nb_products > 0}active{else}inactive{/if}" data-refresh-url="{$refresh_url}">
    <div class="header">
      {if $nb_products > 0}
        <a rel="nofollow" href="{$cart_url}" class="show-cart-summary">
      {/if}
        <i class="fa fa-shopping-cart"></i>
        <span class="hidden-sm-down">{l s='Mon panier' d='Shop.Theme.Checkout'}</span>
        <span class="cart-products-count">({$nb_products})</span>
      {if $nb_products > 0}
        </a>
      {/if}
    </div>
  </div>
</div>
{*<div id="shopping_cart_summary" class="iziModal">
  <table class="table combinations-table" style="margin-bottom:0px">
    <tbody>
      <tr>
        <td class="text-center" style="border-left: 0px">
          <img src="http://local.prestashop-1-7.fr/28-home_default/brown-bear-printed-sweater.jpg" style="height:75px; border: 1px solid lightgrey">
        </td>
        <td style="vertical-align: middle; border-left:0px">
          <b>Mon produit</b>
          <br />1x 300€
        </td>
        <td class="text-center" style="vertical-align: middle; border-left: 0px">
          <i class="fa fa-2x fa-trash-alt text-danger"></i>
        </td>
      </tr>
      <tr>
        <td colspan="2" class="text-right bold" style="padding:5px; padding-right:15px; border-left: 0px">
          Frais de livraison
        </td>
        <td class="text-center bold text-danger" style="padding:5px">Offerts</td>
      </tr>
      <tr>
        <td colspan="2" class="text-right bold" style="padding:5px; padding-right:15px; border-left: 0px">
          TOTAL HT
        </td>
        <td class="text-center bold text-info" style="padding:5px">300€</td>
      </tr>
    </tbody>
  </table>
  <a href="#" class="btn btn-info btn-block bold">
    Voir mon panier
  </a>
</div>*}