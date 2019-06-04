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
        <a href="{$urls.pages.history}" class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
          <div class="link-item">
            <i class="fa fa-3x fa-shopping-cart"></i>
            <p>{l s='Historique et détails de mes commandes' d='Shop.Theme.Customeraccount'}</p>
          </div>
        </a>
      {/if}

      <a href="{$urls.pages.identity}" class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
        <div class="link-item">
          <i class="fa fa-3x fa-edit"></i>
          <p>{l s='Mes information personnelles' d='Shop.Theme.Customeraccount'}</p>
        </div>
      </a>

      {if $customer.addresses|count}
        <a href="{$urls.pages.addresses}" class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
          <div class="link-item">
            <i class="fa fa-3x fa-address-card"></i>
            <p>{l s='Mes addresses' d='Shop.Theme.Customeraccount'}</p>
          </div>
        </a>
      {else}
        <a href="{$urls.pages.address}" class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
          <div class="link-item">
            <i class="fa fa-3x fa-address-book"></i>
            <p>{l s='Ajouter ma première adresse' d='Shop.Theme.Customeraccount'}</p>
          </div>
        </a>
      {/if}

      {if !$configuration.is_catalog}
        <a href="{$urls.pages.order_slip}" class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
          <div class="link-item">
            <i class="fa fa-3x fa-file-alt"></i>
            <p>{l s='Mes avoirs' d='Shop.Theme.Customeraccount'}</p>
          </div>
        </a>
      {/if}

      {if $configuration.voucher_enabled && !$configuration.is_catalog}
        <a href="{$urls.pages.discount}" class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
          <div class="link-item">
            <i class="fa fa-3x fa-tags"></i>
            <p>{l s='Mes bons de réductions' d='Shop.Theme.Customeraccount'}</p>
          </div>
        </a>
      {/if}

      {if $configuration.return_enabled && !$configuration.is_catalog}
        <a href="{$urls.pages.order_follow}" class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
          <div class="link-item">
            <i class="fa fa-3x fa-undo-alt"></i>
            <p>{l s='Merchandise returns' d='Shop.Theme.Customeraccount'}</p>
          </div>
        </a>
      {/if}

      <a href="{$link->getPageLink('QuotationList')}" class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
        <div class="link-item">
          <i class="fa fa-3x fa-calculator"></i>
          <p>{l s='Mes devis' d='Shop.Theme.Customeraccount'}</p>
        </div>
      </a>

      <a class="offset-lg-4 col-lg-4 col-md-6 col-sm-12 col-xs-12" href="{$link->getPageLink('AfterSales')}">
        <div class="link-item">
          <i class="fa fa-3x fa-comments"></i>
          <p>{l s='Service après vente'}</p>
        </div>
      </a>

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
