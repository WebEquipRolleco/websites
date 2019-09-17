<div class="row top-space">
  <div class="col-lg-12">
    <h3 class="section-title">
      {l s='Nos bonnes affaires' d='Shop.Theme.Catalog'}
    </h3>
  </div>
</div>

<section class="row">
  {foreach from=$products item="product"}
    {include file="catalog/_partials/miniatures/product.tpl" product=$product}
  {/foreach}
</section>