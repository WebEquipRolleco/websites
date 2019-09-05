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

  {include file='customer/_partials/account-links-list.tpl'}
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
