{extends file=$layout}

{block name='content'}

  <section id="main">

    <div class="row top-space">
      <div class="col-lg-12">
        <h1 class="page-title">{l s='Mon panier' d='Shop.Theme.Checkout'}</h1>
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12">

          {block name='cart_overview'}
            {include file='checkout/_partials/cart-detailed.tpl' cart=$cart}
          {/block}

        {*block name='continue_shopping'}
          <a class="label" href="{$urls.pages.index}">
            <i class="material-icons">chevron_left</i>{l s='Continue shopping' d='Shop.Theme.Actions'}
          </a>
        {/block*}

        <!-- shipping informations -->
        {block name='hook_shopping_cart_footer'}
          {hook h='displayShoppingCartFooter'}
        {/block}
      </div>
    </div>

    <div class="row">

      <div class="col-xs-12 col-lg-6">
        {assign var=context value=Context::getContext()}

        {block name='cart_rollcash'}
          {include file='checkout/_partials/cart-rollcash.tpl' cart=$context->cart}
        {/block}

        {block name='cart_rollcash'}
          {include file='checkout/_partials/cart-trust.tpl'}
        {/block}
        
      </div>

      <div class="col-xs-12 col-lg-6">
        {block name='cart_summary'}
            {block name='hook_shopping_cart'}
              {hook h='displayShoppingCart'}
            {/block}

            {block name='cart_totals'}
              {include file='checkout/_partials/cart-detailed-totals.tpl' cart=$cart}
            {/block}

            {block name='cart_actions'}
              {include file='checkout/_partials/cart-detailed-actions.tpl' cart=$cart}
            {/block}

        {/block}
      </div>

    </div>

        {*block name='hook_reassurance'}
          {hook h='displayReassurance'}
        {/block*}

  </section>
{/block}
