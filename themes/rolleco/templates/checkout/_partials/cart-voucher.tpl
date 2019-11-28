{if $cart.vouchers.allowed}
  {block name='cart_voucher'}
    <table class="table combinations-table block-promo">
      <thead>
        <tr>
          <th colspan="2" class="bg-light" style="border-top:0px; color:black">
            {l s='Programme de fidélité rollcash' d='Shop.Theme.Checkout'}
          </th>
        </tr>
      </thead>
      <tbody>
        {if $cart.vouchers.added}
          {block name='cart_voucher_list'}
            {foreach from=$cart.vouchers.added item=voucher}
              <tr>
                <td class="bold">
                  <a href="{$voucher.delete_url}" class="remove-link" data-link-action="remove-voucher">
                    <i class="far fa-check-square"></i> &nbsp;
                    {l s="Utiliser mon %s" sprintf=[$voucher.name] d='Shop.Theme.Checkout'}
                  </a>
                </td>
                <td class="text-right bold">{$voucher.reduction_formatted}</td>
              </tr>
            {/foreach}
          {/block}
        {/if}
        {if $cart.discounts|count > 0}
          {foreach from=$cart.discounts item=discount}
            <tr>
              <td class="bold">
                <a href="#" class="discount-code" data-code="{$discount.code}" style="color:black">
                  <i class="far fa-square"></i> &nbsp;
                  {l s="Utiliser mon %s" sprintf=[$discount.name] d='Shop.Theme.Checkout'}
                </a> 
              </td>
              <td class="text-right bold">
                {if $discount.reduction_amount > 0}
                  -{Tools::displayPrice($discount.reduction_amount * 1.2)}
                {elseif $discount.reduction_percent}
                  -{$discount.reduction_percent} %
                {/if}
              </td>
            </tr>
          {/foreach}
        {/if}
        {block name='cart_voucher_form'}
          <tr class="promo-code">
            <td colspan="2" class="bg-light text-center uppercase bold text-muted">
              
              <form  method="post" action="{$urls.pages.cart}" data-link-action="add-voucher" class="form-inline">
                {l s='Promo code' d='Shop.Theme.Checkout'} &nbsp; 
                <input type="hidden" name="token" value="{$static_token}">
                <input type="hidden" name="addDiscount" value="1">
                <input type="text" class="form-control" name="discount_name" required>
                <button type="submit" class="btn btn-info bold"><span>{l s='Ok' d='Shop.Theme.Actions'}</span></button>
              </form>
              {block name='cart_voucher_notifications'}
                <div class="alert alert-danger js-error" role="alert">
                  <i class="material-icons">&#xE001;</i><span class="ml-1 js-error-text"></span>
                </div>
              {/block}
            </td>
          </tr>
        {/block}
        
      </tbody>
    </table>

  {/block}
{/if}


{*if $cart.vouchers.allowed}
  {block name='cart_voucher'}
    <div class="block-promo">
      <div class="cart-voucher">
        {if $cart.vouchers.added}
          {block name='cart_voucher_list'}
            <ul class="promo-name card-block">
              {foreach from=$cart.vouchers.added item=voucher}
                <li class="cart-summary-line">
                  <span class="label">{$voucher.name}</span>
                  <a href="{$voucher.delete_url}" data-link-action="remove-voucher"><i class="material-icons">&#xE872;</i></a>
                  <div class="float-xs-right">
                    {$voucher.reduction_formatted}
                  </div>
                </li>
              {/foreach}
            </ul>
          {/block}
        {/if}

        <p>
          <a class="collapse-button promo-code-button" data-toggle="collapse" href="#promo-code" aria-expanded="false" aria-controls="promo-code">
            {l s='Have a promo code?' d='Shop.Theme.Checkout'}
          </a>
        </p>

        <div class="promo-code collapse{if $cart.discounts|count > 0} in{/if}" id="promo-code">
          {block name='cart_voucher_form'}
            <form action="{$urls.pages.cart}" data-link-action="add-voucher" method="post">
              <input type="hidden" name="token" value="{$static_token}">
              <input type="hidden" name="addDiscount" value="1">
              <input class="promo-input" type="text" name="discount_name" placeholder="{l s='Promo code' d='Shop.Theme.Checkout'}">
              <button type="submit" class="btn btn-primary"><span>{l s='Add' d='Shop.Theme.Actions'}</span></button>
            </form>
          {/block}

          {block name='cart_voucher_notifications'}
            <div class="alert alert-danger js-error" role="alert">
              <i class="material-icons">&#xE001;</i><span class="ml-1 js-error-text"></span>
            </div>
          {/block}
        </div>

        {if $cart.discounts|count > 0}
          <p class="block-promo promo-highlighted">
            {l s='Take advantage of our exclusive offers:' d='Shop.Theme.Actions'}
          </p>
          <ul class="js-discount card-block promo-discounts">
          {foreach from=$cart.discounts item=discount}
            <li class="cart-summary-line">
              <span class="label"><span class="code">{$discount.code}</span> - {$discount.name}</span>
            </li>
          {/foreach}
          </ul>
        {/if}
      </div>
    </div>
  {/block}
{/if*}

