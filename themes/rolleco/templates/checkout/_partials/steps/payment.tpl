{extends file='checkout/_partials/steps/checkout-step.tpl'}

{block name='step_content'}

  {hook h='displayPaymentTop'}

  <div class="row">
    {assign var=context value=Context::getContext()}

    <div class="col-xs-12 col-lg-12">
      <h4 class="text-center bold uppercase">
        {l s="Récapitulatif de la commande" d='Shop.Theme.Checkout'}
      </h4>
    </div>

    <div class="col-xs-12 col-lg-6">
      <table class="table combinations-table">
        <tr class="cart-total">
          <td class="bg-blue text-center uppercase">{l s="Adresse de facturation" d='Shop.Theme.Checkout'}</td>
        </tr>
        <tr>
          <td class="text-muted">
            {assign var=address value=$context->cart->getAddressInvoice()}
            <div class="bold">{$address->alias}</div>
            <br />
            {$address->firstname} {$address->lastname} <br />
            {if $address->company}{$address->company} <br />{/if}
            <br />
            {$address->address1} <br />
            {if $address->address2}{$address->address2} <br />{/if}
            {$address->postcode} {$address->city} <br />
            {$address->country} <br />
            {$address->phone}
          </td>
        </tr>
      </table>
    </div>

    <div class="col-xs-12 col-lg-6">
      <table class="table combinations-table">
        <tr class="cart-total">
          <td class="bg-blue text-center uppercase">{l s="Adresse de livraison" d='Shop.Theme.Checkout'}</td>
        </tr>
        <tr>
          <td class="text-muted">
            {assign var=address value=$context->cart->getAddressDelivery()}
            <div class="bold">{$address->alias}</div>
            <br />
            {$address->firstname} {$address->lastname} <br />
            {if $address->company}{$address->company} <br />{/if}
            <br />
            {$address->address1} <br />
            {if $address->address2}{$address->address2} <br />{/if}
            {$address->postcode} {$address->city} <br />
            {$address->country} <br />
            {$address->phone}
          </td>
        </tr>
      </table>
    </div>

    <div class="col-xs-12 margin-top-15">
      <table class="table combinations-table">
        <tr class="cart-total">
          <td class="bg-blue text-center uppercase">{l s="Produit" d='Shop.Theme.Checkout'}</td>
          <td class="bg-blue text-center uppercase">{l s="Quantité" d='Shop.Theme.Checkout'}</td>
          <td class="bg-blue text-center uppercase">{l s="Prix unitaire HT" d='Shop.Theme.Checkout'}</td>
          <td class="bg-blue text-center uppercase">{l s="Prix total HT" d='Shop.Theme.Checkout'}</td>
        </tr>
        {foreach from=$cart.products item=product}
          <tr class="text-muted">
            <td class="text-center" style="vertical-align:middle;">
              <div class="bold">{$product.name}</div>
              {l s="Référence : %s" sprintf=[$product.reference]}
            </td>
            <td class="text-center" style="vertical-align:middle;">
              {$product.cart_quantity}
            </td>
            <td class="text-center" style="vertical-align:middle;">
              {Tools::displayPrice($product.price_with_reduction_without_tax)}
            </td>
            <td class="text-center" style="vertical-align:middle;">
              {Tools::displayPrice($product.price_with_reduction_without_tax * $product.cart_quantity)}
            </td>
          </tr>
        {/foreach}
        {foreach from=$product_quotation item=productQuotation}
          <tr class="text-muted">
            <td class="text-center" style="vertical-align:middle;">
              <div class="bold">{$productQuotation->name}</div>
              {l s="Référence : %s" sprintf=[$productQuotation->reference]}
            </td>
            <td class="text-center" style="vertical-align:middle;">
              {$productQuotation->quantity}
            </td>
            <td class="text-center" style="vertical-align:middle;">
              {Tools::displayPrice($productQuotation->selling_price)}
            </td>
            <td class="text-center" style="vertical-align:middle;">
              {Tools::displayPrice($productQuotation->quantity * $productQuotation->selling_price)}
            </td>
          </tr>
        {/foreach}
        {foreach from=OrderOptionCart::findByCart($context->cart->id) item=option}
          <tr class="text-muted">
            <td class="text-center" style="vertical-align:middle;">
              <div class="bold">{$option->name}</div>
              {$option->description}
            </td>
            <td class="text-center" style="vertical-align:middle;">

            </td>
            <td class="text-center" style="vertical-align:middle;">
              {Tools::displayPrice($option->getPrice())}
            </td>
            <td class="text-center" style="vertical-align:middle;">
              {Tools::displayPrice($option->getPrice())}
            </td>
          </tr>
        {/foreach}
        <tr class="text-muted">
          <td colspan="3" class="text-right bold">{l s="Sous-total HT" d='Shop.Theme.Checkout'}</td>
          <td class="text-center">{$cart.totals.total_excluding_tax.value}</td>
        </tr>
        <tr class="text-muted">
          <td colspan="3" class="text-right bold">{l s="Frais de livraison" d='Shop.Theme.Checkout'}</td>
          <td class="text-center">{$cart.subtotals.shipping.value}</td>
        </tr>
        <tr class="text-muted">
          <td colspan="3" class="text-right bold">{l s="Taxes" d='Shop.Theme.Checkout'}</td>
          <td class="text-center">{Tools::displayPrice($cart.totals.total_including_tax.amount - $cart.totals.total_excluding_tax.amount)}</td>
        </tr>
        <tr class="text-muted">
          <td colspan="3" class="text-right bold">{l s="Total TTC" d='Shop.Theme.Checkout'}</td>
          <td class="text-center">{$cart.totals.total_including_tax.value}</td>
        </tr>
      </table>
    </div>

    <div class="col-xs-12 col-lg-12 margin-top-15">
      <hr />
      <h4 class="text-center bold uppercase margin-top-15">
        {l s="Type de paiement" d='Shop.Theme.Checkout'}
      </h4>
    </div>

  </div>

  {if $is_free}
    <p>{l s='No payment needed for this order' d='Shop.Theme.Checkout'}</p>
  {/if}
  <div class="margin-top-15 payment-options {if $is_free}hidden-xs-up{/if}">
    {foreach from=$payment_options item="module_options"}
      {foreach from=$module_options item="option"}
        <div>
          <div id="{$option.id}-container" class="payment-option clearfix">
            {* This is the way an option should be selected when Javascript is enabled *}
            <span class="custom-radio float-xs-left">
              <input
                class="ps-shown-by-js {if $option.binary} binary {/if}"
                id="{$option.id}"
                data-module-name="{$option.module_name}"
                name="payment-option"
                type="radio"
                required
                {if $selected_payment_option == $option.id || $is_free} checked {/if}
              >
              <span></span>
            </span>
            {* This is the way an option should be selected when Javascript is disabled *}
            <form method="GET" class="ps-hidden-by-js">
              {if $option.id === $selected_payment_option}
                {l s='Selected' d='Shop.Theme.Checkout'}
              {else}
                <button class="ps-hidden-by-js" type="submit" name="select_payment_option" value="{$option.id}">
                  {l s='Choose' d='Shop.Theme.Actions'}
                </button>
              {/if}
            </form>

            <label for="{$option.id}">
              <span>{$option.call_to_action_text}</span>
              {if $option.logo}
                <img src="{$option.logo}">
              {/if}
            </label>

          </div>
        </div>

        {if $option.additionalInformation}
          <div
            id="{$option.id}-additional-information"
            class="js-additional-information definition-list additional-information{if $option.id != $selected_payment_option} ps-hidden {/if}"
          >
            {$option.additionalInformation nofilter}
          </div>
        {/if}

        <div
          id="pay-with-{$option.id}-form"
          class="js-payment-option-form {if $option.id != $selected_payment_option} ps-hidden {/if}"
        >
          {if $option.form}
            {$option.form nofilter}
          {else}
            <form id="payment-form" method="POST" action="{$option.action nofilter}">
              {foreach from=$option.inputs item=input}
                <input type="{$input.type}" name="{$input.name}" value="{$input.value}">
              {/foreach}
              <button style="display:none" id="pay-with-{$option.id}" type="submit"></button>
            </form>
          {/if}
        </div>
      {/foreach}
    {foreachelse}
      <p class="alert alert-danger">{l s='Unfortunately, there are no payment method available.' d='Shop.Theme.Checkout'}</p>
    {/foreach}
  </div>

  {if $conditions_to_approve|count}
    <p class="ps-hidden-by-js">
      {* At the moment, we're not showing the checkboxes when JS is disabled
         because it makes ensuring they were checked very tricky and overcomplicates
         the template. Might change later.
      *}
      {l s='By confirming the order, you certify that you have read and agree with all of the conditions below:' d='Shop.Theme.Checkout'}
    </p>

    <form id="conditions-to-approve" method="GET">
      <ul>
        {foreach from=$conditions_to_approve item="condition" key="condition_name"}
          <li>
            <div class="float-xs-left">
              <span class="custom-checkbox">
                <input  id    = "conditions_to_approve[{$condition_name}]"
                        name  = "conditions_to_approve[{$condition_name}]"
                        required
                        type  = "checkbox"
                        value = "1"
                        class = "ps-shown-by-js"
                >
                <span><i class="material-icons rtl-no-flip checkbox-checked">&#xE5CA;</i></span>
              </span>
            </div>
            <div class="condition-label">
              <label class="js-terms" for="conditions_to_approve[{$condition_name}]">
                {$condition nofilter}
              </label>
            </div>
          </li>
        {/foreach}
      </ul>
    </form>
  {/if}

  {if $show_final_summary}
    {include file='checkout/_partials/order-final-summary.tpl'}
  {/if}

  <div id="payment-confirmation">
    <div class="ps-shown-by-js">
      <div class="well text-right">
        <button type="submit" {if !$selected_payment_option} disabled {/if} class="btn btn-success bold center-block">
          {l s='Order with an obligation to pay' d='Shop.Theme.Checkout'}
        </button>
      </div>
      {if $show_final_summary}
        <article class="alert alert-danger mt-2 js-alert-payment-conditions" role="alert" data-alert="danger">
          {l
            s='Please make sure you\'ve chosen a [1]payment method[/1] and accepted the [2]terms and conditions[/2].'
            sprintf=[
              '[1]' => '<a href="#checkout-payment-step">',
              '[/1]' => '</a>',
              '[2]' => '<a href="#conditions-to-approve">',
              '[/2]' => '</a>'
            ]
            d='Shop.Theme.Checkout'
          }
        </article>
      {/if}
    </div>
    <div class="ps-hidden-by-js">
      {if $selected_payment_option and $all_conditions_approved}
        <div class="well text-right">
          <label for="pay-with-{$selected_payment_option}">
            {l s='Order with an obligation to pay' d='Shop.Theme.Checkout'}
          </label>
        </div>
      {/if}
    </div>
  </div>

  {hook h='displayPaymentByBinaries'}

  <div class="modal fade" id="modal">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <button type="button" class="close" data-dismiss="modal" aria-label="{l s='Close' d='Shop.Theme.Global'}">
          <span aria-hidden="true">&times;</span>
        </button>
        <div class="js-modal-content"></div>
      </div>
    </div>
  </div>
{/block}
