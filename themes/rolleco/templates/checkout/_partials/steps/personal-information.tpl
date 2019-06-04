{extends file='checkout/_partials/steps/checkout-step.tpl'}

{block name='step_content'}
  {if $customer.is_logged && !$customer.is_guest}

    <div class="row">
      <div class="col-xs-12 offset-lg-3 col-lg-6">
        <div class="well text-center">
            <i class="fa fa-4x fa-user margin-top-15"></i>
            <p>{l s="Connected as" d='Shop.Theme.Checkout'}</p>
            <p>
              <a href='{$urls.pages.identity}' class="btn btn-info bold" title="{l s="My account" d='Shop.Theme.Checkout'}">
                {$customer.firstname} {$customer.lastname}
              </a>
              <a href='{$urls.actions.logout}' class="btn btn-danger" title="{l s='Not you ?' d='Shop.Theme.Checkout'}">
                <i class="fa fa-power-off"></i>
              </a>
            </p>
            {if !isset($empty_cart_on_logout) || $empty_cart_on_logout}
              <p><small>{l s='If you sign out now, your cart will be emptied.' d='Shop.Theme.Checkout'}</small></p>
            {/if}
        </div>
      </div>
    </div>

  {else}

    <ul class="nav nav-inline my-2" role="tablist">
      <li class="nav-item">
        <a
          class="nav-link {if !$show_login_form}active{/if}"
          data-toggle="tab"
          href="#checkout-guest-form"
          role="tab"
          aria-controls="checkout-guest-form"
          {if !$show_login_form} aria-selected="true"{/if}
          >
          {if $guest_allowed}
            {l s='Order as a guest' d='Shop.Theme.Checkout'}
          {else}
            {l s='Create an account' d='Shop.Theme.Customeraccount'}
          {/if}
        </a>
      </li>

      <li class="nav-item">
        <span href="nav-separator"> | </span>
      </li>

      <li class="nav-item">
        <a
          class="nav-link {if $show_login_form}active{/if}"
          data-link-action="show-login-form"
          data-toggle="tab"
          href="#checkout-login-form"
          role="tab"
          aria-controls="checkout-login-form"
          {if $show_login_form} aria-selected="true"{/if}
        >
          {l s='Sign in' d='Shop.Theme.Actions'}
        </a>
      </li>
    </ul>

    <div class="tab-content">
      <div class="tab-pane {if !$show_login_form}active{/if}" id="checkout-guest-form" role="tabpanel" {if $show_login_form}aria-hidden="true"{/if}>
        {render file='checkout/_partials/customer-form.tpl' ui=$register_form guest_allowed=$guest_allowed}
      </div>
      <div class="tab-pane {if $show_login_form}active{/if}" id="checkout-login-form" role="tabpanel" {if !$show_login_form}aria-hidden="true"{/if}>
        {render file='checkout/_partials/login-form.tpl' ui=$login_form}
      </div>
    </div>


  {/if}
{/block}
