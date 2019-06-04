<div id="modal_product_added" class="iziModal">
  
  <h1>{l s='Product successfully added to your shopping cart' d='Shop.Theme.Checkout'}</h1>
  
  {if $cart.products_count > 1}
    <p class="cart-products-count">{l s='There are %products_count% items in your cart.' sprintf=['%products_count%' => $cart.products_count] d='Shop.Theme.Checkout'}</p>
  {else}
    <p class="cart-products-count">{l s='There is %product_count% item in your cart.' sprintf=['%product_count%' =>$cart.products_count] d='Shop.Theme.Checkout'}</p>
  {/if}

  <div class="row">
    <div class="col-lg-12 text-right">
      <button type="button" class="btn btn-default bold" data-dismiss="modal">
        {l s='Continue shopping' d='Shop.Theme.Actions'}
      </button>
      <a href="{$cart_url}" class="btn btn-success bold">
        {l s='Proceed to checkout' d='Shop.Theme.Actions'}
      </a>
    </div>
  </div>

</div>

<script>
  $(document).ready(function() {
    $('#modal_product_added').iziModal({
      headerColor: "#1e4688",
      icon: 'fas fa-cart-arrow-down',
      title: "Rolléco",
      subtitle: "{l s='Produit(s) ajouté(s) au panier' d='Shop.Theme.Checkout'}",
      padding: "15px",
      closeButton: true,
      autoOpen: 1
    });
  });
</script>