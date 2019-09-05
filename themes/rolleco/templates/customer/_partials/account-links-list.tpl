<div class="row">
  <div class="links">

    {if !$configuration.is_catalog}
      {include file='customer/_partials/account-link.tpl' url=$urls.pages.history icon='shopping-cart' text='Historique de mes commandes'}
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
    {*include file='customer/_partials/account-link.tpl' url=$link->getModuleLink('webequip_reviews', 'account') icon='star' text='Mes avis'*}

    {block name='display_customer_account'}
      {hook h='displayCustomerAccount'}
    {/block}

  </div>
</div>