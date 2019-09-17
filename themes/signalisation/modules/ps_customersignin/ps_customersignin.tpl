{if $logged}
  <div id="_desktop_user_info" class="nav-block">
    <div class="user-info text-center">
      <i class="fa fa-3x fa-user"></i> <br />
      <a href="{$my_account_url}" class="account" title="{l s='View my customer account' d='Shop.Theme.Customeraccount'}" rel="nofollow">
        <i class="fa fa-user hidden-md-up logged"></i>
        <span class="hidden-sm-down">{$customerName}</span>
      </a>
      <div class="text-center">
        <a rel="nofollow" href="#navigation_rapide" id="quick_navigation" class="nav-button" title="{l s='Navigation rapide'}">
          <i class="fa fa-list"></i>
        </a>
        &nbsp;
        <a rel="nofollow" href="{$logout_url}" id="logout" class="nav-button logout" title="{l s='Me déconnecter' d='Shop.Theme.Customeraccount'}">
          <i class="fa fa-power-off"></i>
        </a>
      </div>
    </div>
  </div>
  <div id="modal_navigation" class="iziModal">
    {include file='customer/_partials/account-links-list.tpl'}
  </div>
{else}
  <div id="_desktop_user_info" class="nav-block">
    <div class="user-info text-center">
      <i class="fa fa-3x fa-user"></i> <br />
      <a rel="nofollow" href="{$my_account_url}" title="{l s='Log in to your customer account' d='Shop.Theme.Customeraccount'}">
        <span class="hidden-sm-down">{l s='Connexion' d='Shop.Theme.Actions'}</span>
      </a>
    </div>
  </div>
{/if}