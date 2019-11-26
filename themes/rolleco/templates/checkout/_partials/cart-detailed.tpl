{assign var=context value=Context::getContext()}
{assign var=quotations value=QuotationAssociation::find($context->cart->id)}
{assign var=option_lines value=OrderOption::getOrderOptions(true, $context->shop->id)}

{block name='cart_detailed_product'}
  <div class="cart-overview js-cart" data-refresh-url="{url entity='cart' params=['ajax'=>true, 'action'=>'refresh']}">
    {if $cart.products or !empty($quotations)}
      <table class="table combinations-table">
        <thead>
          <tr>
            <th>{l s="Images" d="Shop.Theme.Checkout"}</th>
            <th>{l s="Produit" d="Shop.Theme.Checkout"}</th>
            <th>{l s="Ref." d="Shop.Theme.Checkout"}</th>
            <th>{l s="Prix unitaire HT" d="Shop.Theme.Checkout"}</th>
            <th>{l s="Qté" d="Shop.Theme.Checkout"}</th>
            <th>{l s="Total HT" d="Shop.Theme.Checkout"}</th>
            <th></th>
          </tr>
        </thead>
        <tbody class="cart-items">
          {foreach from=$cart.products item=product}
            <tr>
              {block name='cart_detailed_product_line'}
                {include file='checkout/_partials/cart-detailed-product-line.tpl' product=$product}
              {/block}
            </tr>
          {/foreach}
          {foreach from=$quotations item=quotation}
            {include file='checkout/_partials/cart-detailed-quotation-line.tpl'}
          {/foreach}
        </tbody>
      </table>

      {if !empty($option_lines)}
        <table class="table combinations-table margin-top-15">
          <thead>
            <tr>
              <td colspan="7" class="bg-darkgrey">
                <b>{l s="Options disponibles" d='Shop.Theme.Checkout'}</b>
              </td>
            </tr>
          </thead>
          <tbody>
            {foreach from=$option_lines item=option}
              {include file='checkout/_partials/cart-detailed-option-line.tpl'}
            {/foreach}
          </tbody>
        </table>
      {/if}

    {else}
      <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle"></i> 
        &nbsp; {l s='There are no more items in your cart' d='Shop.Theme.Checkout'}
      </div>
      <div class="row">
        <div class="col-lg-12 text-center">
          <a href="/" class="btn btn-info bold">{l s='Continue shopping' d='Shop.Theme.Actions'}</a>
        </div>
      </div>

    {/if}
  </div>
{/block}
