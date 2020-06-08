{if $logged}
	<a rel="nofollow" href="{$logout_url}" id="logout" class="nav-link-right"  title="{l s='Me déconnecter' d='Shop.Theme.CustomerAccount'}">
		<i class="fa fa-sign-out"></i>
	</a>
	<a rel="nofollow" href="#navigation_rapide" id="quick_navigation" class="nav-link-right" title="{l s='Navigation rapide'}">
        <i class="fa fa-list"></i>
   	</a>
	<div id="modal_navigation" class="iziModal">
    	{include file='customer/_partials/account-links-list.tpl'}
  	</div>
{else}
	<a href="{$link->getPageLink('authentication')}" class="nav-link-right hidden-lg-up" title="{l s='Me connecter' d='Shop.Theme.CustomerAccount'}">
		<i class="fa fa-user"></i>
	</a>
{/if}

<a href="{$link->getPageLink('cart?action=show')}" class="nav-link-right hidden-lg-up">
    <i class="fa fa-shopping-cart"></i>
</a>