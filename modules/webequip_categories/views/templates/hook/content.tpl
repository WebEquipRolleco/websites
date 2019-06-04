{if $categories|count > 0}
	<div id="category_list" class="row">
		{foreach from=$categories item=category}
			<div class="category-item col-xs-12 col-sm-6 col-lg-3">
				<a href="{$category->getLink()}">
					<img src="{$link->getCatImageLink($category->name, $category->id)}">
					<p class="title">
						{$category->name}
						{assign var=nb_products value=count($category->getProductsWs())}
						{if $nb_products > 0}
							<span class="nb-products">({$nb_products} références)</span>
						{/if}
					</p>
				</a>
			</div>
		{/foreach}
	</div>
{else}
	{if $listing.products|count}
      <section id="products">
    
          {block name='product_list_top'}
            {include file='catalog/_partials/products-top.tpl' listing=$listing}
          {/block}

        {block name='product_list_active_filters'}
          <div id="" class="hidden-sm-down">
            {$listing.rendered_active_filters nofilter}
          </div>
        {/block}

        <div id="">
          {block name='product_list'}
            {include file='catalog/_partials/products.tpl' listing=$listing}
          {/block}
        </div>

        <div id="js-product-list-bottom">
          {block name='product_list_bottom'}
            {include file='catalog/_partials/products-bottom.tpl' listing=$listing}
          {/block}
        </div>

    </section>
    {else}
      <div class="alert alert-danger">
        <table>
          <tr>
            <td><i class="fa fa-2x fa-sad-tear"></i></td>
            <td style="padding-left:15px; line-height:12px;">
              <strong>{l s="Aucun produit ..." d='Shop.Theme.Catalog'}</strong>
              <br /><small>{l s="Essayez une autre catégorie !" d='Shop.Theme.Catalog'}</small>
            </td>
          </tr>
        </table>
      </div>
    {/if}
{/if}