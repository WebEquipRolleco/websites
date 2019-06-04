<div id="desktop_cart">
  <div class="blockcart cart-preview {if $cart.products_count > 0}active{else}inactive{/if}" data-refresh-url="{$refresh_url}">
    <div class="header">
      {if $cart.products_count > 0}
        <a rel="nofollow" href="{$cart_url}">
      {/if}
        <i class="material-icons shopping-cart">{l s='shopping_cart' d='Shop.Theme.Checkout'}</i>
        <span class="title-cart">{l s='Cart' d='Shop.Theme.Checkout'}</span>
        <span class="cart-products-count">({$cart.products_count})</span> 
      {if $cart.products_count > 0}
        </a>
      {/if}
    </div>
  </div>
</div>
