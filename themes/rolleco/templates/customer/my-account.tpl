{extends file='customer/page.tpl'}

{block name='page_title'}
  {l s='Your account' d='Shop.Theme.Customeraccount'}
{/block}

{block name='page_content'}

  {if Context::getContext()->customer->getState() and Context::getContext()->customer->getState()->show_customer}
    {assign var='state' value=Context::getContext()->customer->getState()}
    <div class="row">
      <div class="col-lg-12">
        <div style="padding:10px; background-color:{$state->color};{if $state->light_text}color:white;{/if}">
          <b>{$state->name}</b>
          {if Context::getContext()->customer->comment}
            <br /><small>{Context::getContext()->customer->comment}</small>
          {/if}
        </div>
      </div>
    </div>
  {/if}

  <div class="row">
    <div class="links">

      {if !$configuration.is_catalog}
        {include file='customer/_partials/account-link.tpl' url=$urls.pages.history icon='shopping-cart' text='Historique et détails de mes commandes'}
      {/if}

      {include file='customer/_partials/account-link.tpl' url=$urls.pages.identity icon='edit' text='Mes information personnelles'}

      {if $customer.addresses|count}
        {include file='customer/_partials/account-link.tpl' url=$urls.pages.addresses icon='address-card' text='Mes addresses'}
      {else}
        {include file='customer/_partials/account-link.tpl' url=$urls.pages.addresses icon='address-book' text='Ajouter ma première adresse'}
      {/if}

      {if !$configuration.is_catalog}
        {include file='customer/_partials/account-link.tpl' url=$urls.pages.order_slip icon='file-alt' text='Mes avoirs'}
        {if $configuration.voucher_enabled}
          {include file='customer/_partials/account-link.tpl' url=$urls.pages.discount icon='tags' text='Mes bons de réductions'}
        {/if}
        {if $configuration.return_enabled}
          {include file='customer/_partials/account-link.tpl' url=$urls.pages.order_follow icon='undo-alt' text='Merchandise returns'}
        {/if}
      {/if}

      {include file='customer/_partials/account-link.tpl' url=$link->getPageLink('QuotationList') icon='calculator' text='Mes devis'}
      {include file='customer/_partials/account-link.tpl' url=$link->getPageLink('AfterSales') icon='comments' text='Service après vente'}

      {block name='display_customer_account'}
        {hook h='displayCustomerAccount'}
      {/block}

    </div>
  </div>
{/block}


{block name='page_footer'}
  {block name='my_account_links'}
    <div class="text-sm-center margin-top-15">
      <a href="{$logout_url}" class="btn btn-danger bold">
        {l s='Sign out' d='Shop.Theme.Actions'}
      </a>
    </div>
  {/block}
{/block}
