<div class="nav-button">
  <a rel="nofollow" href="#navigation_rapide" id="quick_navigation" title="{l s='Navigation rapide'}">
    <i class="fa fa-list"></i>
  </a>
</div>
<div id="_desktop_user_info">
  <div class="user-info">
    {if $logged}
      
      <a href="{$my_account_url}" class="account" title="{l s='View my customer account' d='Shop.Theme.Customeraccount'}" rel="nofollow">
        <i class="fa fa-user hidden-md-up logged"></i>
          <span class="hidden-sm-down">{$customerName}</span>
      </a>
    {else}
      <a rel="nofollow" href="{$my_account_url}" title="{l s='Log in to your customer account' d='Shop.Theme.Customeraccount'}">
        <span class="hidden-sm-down">{l s='Mon compte' d='Shop.Theme.Actions'}</span>
      </a>
    {/if}
  </div>
</div>
<div id="logout" class="nav-button hvr-glow">
  <a rel="nofollow" href="{$logout_url}" class="logout" title="{l s='Me déconnecter' d='Shop.Theme.Customeraccount'}">
      <i class="fa fa-power-off"></i>
    </a>
</div>

{if $logged}
  <div id="modal_navigation" class="iziModal">
    {include file='customer/_partials/account-links-list.tpl'}
  </div>
{/if}