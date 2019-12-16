<section class="featured-products clearfix">
	<h3 class="section-title top-space">
		{l s='%s : Les produits similaires' sprintf=[$name] d='Modules.Featuredproducts.Shop'}
	</h3>
  <div class="products">
    {foreach from=$products item="product"}
      {include file="catalog/_partials/miniatures/product.tpl" product=$product}
    {/foreach}
  </div>
</section>