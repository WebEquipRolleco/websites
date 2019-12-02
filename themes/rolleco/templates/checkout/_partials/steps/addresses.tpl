{extends file='checkout/_partials/steps/checkout-step.tpl'}

{block name='step_content'}
  <div class="js-address-form">
    <form method="POST" action="{$urls.pages.order}" data-refresh-url="{url entity='order' params=['ajax' => 1, 'action' => 'addressForm']}">

      {*if !$use_same_address}
        <h2 class="h4">{l s='Shipping Address' d='Shop.Theme.Checkout'}</h2>
      {/if}

      {if $use_same_address && !$cart.is_virtual}
        <p>
          {l s='The selected address will be used both as your personal address (for invoice) and as your delivery address.' d='Shop.Theme.Checkout'}
        </p>
      {elseif $use_same_address && $cart.is_virtual}
        <p>
          {l s='The selected address will be used as your personal address (for invoice).' d='Shop.Theme.Checkout'}
        </p>
      {/if*}

      <div class="row">
        <div class="col-xs-12 col-lg-6 offset-lg-3">
          <h4 class="text-center bold">{l s="Votre addresse de facturation" d='Shop.Theme.Checkout'}</h4>
          {foreach from=$customer.addresses item=address}
            <input type="hidden" name="id_address_invoice" value="{$address.id}">
            <article class="address-item">
              <header class="h4">
                <label class="radio-block">
                  <span class="address-alias h4">{$address.alias}</span>
                  <small class="text-muted">{l s="(Facturation)" d='Shop.Theme.Checkout'}</small>
                  <div class="address">
                    {$address.firstname} {$address.lastname} <br />
                    {$address.address1} <br />
                    {if $address.address2}{$address.address2} <br />{/if}
                    {$address.postcode} {$address.city} <br />
                    {$address.country} <br />
                    {$address.phone}
                  </div>
                </label>
              </header>
              <hr />
              <footer class="address-footer bg-grey">
                <a href="{url entity='order' params=['id_address' => $address.id, 'editAddress'=>'delivery', 'token' => $token]}" class="btn btn-warning edit-address" data-link-action="edit-address" title="{l s='Edit' d='Shop.Theme.Actions'}">
                  <i class="fa fa-edit edit"></i>
                </a>
                <a href="{url entity='order' params=['id_address' => $address.id, 'deleteAddress' => true, 'token' => $token]}" class="btn btn-danger delete-address"
                data-link-action="delete-address" title="{l s='Delete' d='Shop.Theme.Actions'}">
                  <i class="fa fa-trash-alt delete"></i>
                </a>
              </footer>
            </article>
            {break}
          {/foreach}
        </div>
      </div>

      <hr style="margin-top:10px !important; margin-bottom:10px !important;" />

      {if $show_delivery_address_form}
        <div id="delivery-address">
          {render file='checkout/_partials/address-form.tpl' ui=$address_form use_same_address=$use_same_address type="delivery" form_has_continue_button=$form_has_continue_button}
        </div>
      {elseif $customer.addresses|count > 0}
        <h4 class="text-center bold">{l s="Adresse de livraison" d='Shop.Theme.Checkout'}</h4>
        <div id="delivery-addresses" class="address-selector js-address-selector">
          {include  file='checkout/_partials/address-selector-block.tpl' addresses=$customer.addresses name="id_address_delivery" selected=$id_address_delivery type="delivery" interactive=!$show_delivery_address_form and !$show_invoice_address_form}
        </div>

        {if isset($delivery_address_error)}
          <p class="alert alert-danger js-address-error" name="alert-delivery" id="id-failure-address-{$delivery_address_error.id_address}">{$delivery_address_error.exception}</p>
        {else}
          <p class="alert alert-danger js-address-error" name="alert-delivery" style="display: none">{l s="Your address is incomplete, please update it." d="Shop.Notifications.Error"}</p>
        {/if}

        <p class="add-address text-center">
          <a href="{$new_address_delivery_url}" class="btn btn-info bold">
            {l s='add new address' d='Shop.Theme.Actions'}
          </a>
        </p>

        {*if $use_same_address && !$cart.is_virtual}
          <p>
            <a data-link-action="different-invoice-address" href="{$use_different_address_url}">
              {l s='Billing address differs from shipping address' d='Shop.Theme.Checkout'}
            </a>
          </p>
        {/if*}

      {/if}

      {if !$use_same_address}

        <h2 class="h4">{l s='Your Invoice Address' d='Shop.Theme.Checkout'}</h2>

        {if $show_invoice_address_form}
          <div id="invoice-address">
            {render file='checkout/_partials/address-form.tpl' ui=$address_form use_same_address=$use_same_address type="invoice" form_has_continue_button=$form_has_continue_button}
          </div>
        {else}
          <div id="invoice-addresses" class="address-selector js-address-selector">
            {include file='checkout/_partials/address-selector-block.tpl' addresses=$customer.addresses name="id_address_invoice" selected=$id_address_invoice type="invoice" interactive=!$show_delivery_address_form and !$show_invoice_address_form}
          </div>

          {if isset($invoice_address_error)}
            <p class="alert alert-danger js-address-error" name="alert-invoice" id="id-failure-address-{$invoice_address_error.id_address}">{$invoice_address_error.exception}</p>
          {else}
            <p class="alert alert-danger js-address-error" name="alert-invoice" style="display: none">{l s="Your address is incomplete, please update it." d="Shop.Notifications.Error"}</p>
          {/if}

          <p class="add-address">
            <a href="{$new_address_invoice_url}" class="btn btn-info bold">
              {l s='add new address' d='Shop.Theme.Actions'}
            </a>
          </p>
        {/if}

      {/if}

      {if !$form_has_continue_button}
        <div class="clearfix">
          <div class="well text-right">
            <button type="submit" class="btn btn-success bold continue" name="confirm-addresses" value="1">
                {l s='Continue' d='Shop.Theme.Actions'}
            </button>
            <input type="hidden" id="not-valid-addresses" value="{$not_valid_addresses}">
          </div>
        </div>
      {/if}

    </form>
  </div>
{/block}
