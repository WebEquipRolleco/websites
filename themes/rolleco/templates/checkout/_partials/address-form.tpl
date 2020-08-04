{extends file='customer/_partials/address-form.tpl'}

{block name='form_field'}
  {if $field.name|in_array:["vat_number", "id_country"]}
    {* we don't ask for alias here *}
  {elseif $field.name == 'phone'}
    {$field.required = true}
    {$smarty.block.parent}
  {else}
    {$smarty.block.parent}
  {/if}
{/block}

{block name="address_form_url"}
    <form
      method="POST"
      action="{url entity='order' params=['id_address' => $id_address]}"
      data-id-address="{$id_address}"
      data-refresh-url="{url entity='order' params=['ajax' => 1, 'action' => 'addressForm']}"
    >
{/block}

{block name='form_fields' append}
  <input type="hidden" name="saveAddress" value="{$type}">
  <input type="hidden" id="account_type" value="{Context::getContext() -> customer-> getType() -> company}">
  {*if $type === "delivery"}
    <div class="form-group row">
      <div class="col-md-9 col-md-offset-3">
        <input name = "use_same_address" type = "checkbox" value = "1" {if $use_same_address} checked {/if}>
        <label>{l s='Use this address for invoice too' d='Shop.Theme.Checkout'}</label>
      </div>
    </div>
  {/if*}
{/block}

{block name='form_buttons'}
  {if !$form_has_continue_button}
    <div class="text-right margin-bottom-15">
      <a class="js-cancel-address cancel-address" href="{url entity='order' params=['cancelAddress' => {$type}]}" style="display:inline-block;">{l s='Cancel' d='Shop.Theme.Actions'}</a>
      <button type="submit" class="btn btn-success bold">{l s='Save' d='Shop.Theme.Actions'}</button>
      
    </div>
  {else}
    <form>
      <button type="submit" class="continue btn btn-primary float-xs-right mb-3 bold" name="confirm-addresses" value="1">
          {l s='Save' d='Shop.Theme.Actions'}
      </button>
      {if $customer.addresses|count > 0}
        <a class="js-cancel-address cancel-address float-xs-right" href="{url entity='order' params=['cancelAddress' => {$type}]}">{l s='Cancel' d='Shop.Theme.Actions'}</a>
      {/if}
    </form>
  {/if}
{/block}
