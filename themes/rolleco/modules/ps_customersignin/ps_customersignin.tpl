{if $logged}
  <div id="_desktop_user_info" class="nav-block margin-top-sm">
    <div class="user-info text-center">
      <a href="{$my_account_url}" class="account" title="{l s='View my customer account' d='Shop.Theme.Customeraccount'}" rel="nofollow">
        <i class="fa fa-2x fa-user"></i> <br />
        <span class="hidden-sm-down">{$customerName}</span>
      </a>
    </div>
  </div>
  
{else}
  <div id="_desktop_user_info" class="nav-block margin-top-sm">
    <div class="user-info text-center">
      <a rel="nofollow" href="{$my_account_url}" title="{l s='Log in to your customer account' d='Shop.Theme.Customeraccount'}">
        <i class="fa fa-2x fa-user"></i> <br />
        <span class="hidden-sm-down">{l s='Connexion' d='Shop.Theme.Actions'}</span>
      </a>
    </div>
  </div>
{/if}