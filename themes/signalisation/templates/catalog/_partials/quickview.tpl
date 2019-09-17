<div id="quickview-modal-{$product.id}-{$product.id_product_attribute}" class="modal fade quickview" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
   <div class="modal-content">
     <div class="modal-header">
       <h1 class="product-title">{$product.name}</h1>
     </div>
     <div class="modal-body">
      <div class="row">
        <div class="col-md-6 col-sm-6 hidden-xs-down">
          {block name='product_cover_thumbnails'}
            {include file='catalog/_partials/product-cover-thumbnails.tpl'}
          {/block}
          <div class="arrows js-arrows">
            <i class="material-icons arrow-up js-arrow-up">&#xE316;</i>
            <i class="material-icons arrow-down js-arrow-down">&#xE313;</i>
          </div>
        </div>
        <div class="col-md-6 col-sm-6">

          {hook h='displayProductAfterTitle' product=$product hide_link=true}

          {block name='product_prices'}
            {include file='catalog/_partials/product-prices.tpl'}
          {/block}
          {block name='product_description_short'}
            <div id="product-description-short" itemprop="description">{$product.description_short nofilter}</div>
          {/block}
          {block name='product_buy'}
            <div class="product-actions">
              <form action="{$urls.pages.cart}" method="post" id="add-to-cart-or-refresh">
                <input type="hidden" name="token" value="{$static_token}">
                <input type="hidden" name="id_product" value="{$product.id}" id="product_page_product_id">
                <input type="hidden" name="id_customization" value="{$product.id_customization}" id="product_customization_id">
                
                {*block name='product_variants'}
                  {include file='catalog/_partials/product-variants.tpl'}
                {/block*}

                {if $product.main_variants|count == 0}
                  {block name='product_add_to_cart'}
                    {include file='catalog/_partials/product-add-to-cart.tpl'}
                  {/block}
                {/if}

                {* Input to refresh product HTML removed, block kept for compatibility with themes *}
                {block name='product_refresh'}{/block}
              </form>
            </div>
          {/block}

            <div class="row">
              <div class="col-lg-12 text-center">
                <a href="{$link}" class="btn btn-info bold">
                  {l s="Voir le produit"}
                </a>
              </div>
            </div>

        </div>
      </div>

      <div class="row">
        <div class="col-lg-12">
          {block name='product_combinations_table'}
            {include file='catalog/_partials/product-combinations-table.tpl'}
          {/block}
        </div>
      </div>

     </div>
      {assign var=displayProductAdditionalInfo value={hook h='displayProductAdditionalInfo' product=$product}}
      {if $displayProductAdditionalInfo}
        <div class="modal-footer">
          <div class="row">
            {$displayProductAdditionalInfo}
          </div>
        </div>
      {/if}
   </div>
 </div>
</div>

<script>
  $(document).ready(function() {
    loadQtyTouchSpin();
  });
</script>