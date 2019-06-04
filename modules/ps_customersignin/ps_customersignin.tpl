<div id="desktop_user_info">
  <div class="user-info">
    {if $logged}
      <a
        class="logout"
        href="{$logout_url}"
        rel="nofollow"
      >
        <i class="material-icons">&#xE7FF;</i>
        {l s='Sign out' d='Shop.Theme.Actions'}
      </a>
      <a
        class="account"
        href="{$my_account_url}"
        title="{l s='View my customer account' d='Shop.Theme.CustomerAccount'}"
        rel="nofollow"
      >
        <i class="material-icons logged">&#xE7FF;</i>
        <span class="">{$customerName}</span>
      </a>
    {else}
      <a
        href="{$my_account_url}"
        title="{l s='Log in to your customer account' d='Shop.Theme.CustomerAccount'}"
        rel="nofollow"
      >
        <i class="material-icons">&#xE7FF;</i>
        <span class="">{l s='Sign in' d='Shop.Theme.Actions'}</span>
      </a>
    {/if}
  </div>
</div>