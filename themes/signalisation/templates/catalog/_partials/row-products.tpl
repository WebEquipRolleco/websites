<div id="js-product-list" class="margin-top-sm">

  <table id="table_products" class="table">
    <tbody>
      {foreach from=$listing.products item="product"}
        {block name='product_miniature'}
          {include file='catalog/_partials/miniatures/row-product.tpl' product=$product}
        {/block}
      {/foreach}
    </tbody>
  </table>

  {block name='pagination'}
    {include file='_partials/pagination.tpl' pagination=$listing.pagination}
  {/block}

  <div class="hidden-md-up text-xs-right up">
    <a href="#header" class="btn btn-secondary">
      {l s='Back to top' d='Shop.Theme.Actions'}
      <i class="material-icons">&#xE316;</i>
    </a>
  </div>
</div>