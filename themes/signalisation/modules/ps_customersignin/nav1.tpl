{if $logged}
	<a rel="nofollow" href="logout_url" id="logout" class="nav-link-right"  title="{l s='Me déconnecter' d='Shop.Theme.Customeraccount'}">
		<i class="fa fa-power-off"></i>
	</a>
	<a rel="nofollow" href="#navigation_rapide" id="quick_navigation" class="nav-link-right" title="{l s='Navigation rapide'}">
        <i class="fa fa-list"></i>
   	</a>
	<div id="modal_navigation" class="iziModal">
    	{include file='customer/_partials/account-links-list.tpl'}
  	</div>
{/if}