{**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{extends file='page.tpl'}

{block name='page_title'}
  {l s='Log in to your account' d='Shop.Theme.Customeraccount'}
{/block}

{block name='page_content'}
    {block name='login_form_container'}
      <section class="login-form">
        {render file='customer/_partials/login-form.tpl' ui=$login_form}
      </section>
      <hr/>
      {block name='display_after_login_form'}
        {hook h='displayCustomerLoginFormAfter'}
      {/block}

      <div class="row">
        <div class="col-xs-6 hidden-sm-down">
          <div class="no-account">
            <a href="{$urls.pages.register}" data-link-action="display-register-form">
              {l s='No account? Create one here' d='Shop.Theme.Customeraccount'}
            </a>
          </div>
        </div>
        <div class="col-xs-6  hidden-sm-down text-right">
          <div class="forgot-password">
            <a href="{$urls.pages.password}" rel="nofollow">
              {l s='Forgot your password?' d='Shop.Theme.Customeraccount'}
            </a>
          </div>
        </div>
        <div class="col-xs-12 text-center hidden-md-up">
          <a href="{$urls.pages.register}" class="btn btn-primary" data-link-action="display-register-form">
            <b>{l s='Créer un compte' d='Shop.Theme.Customeraccount'}</b>
          </a>
          <a href="{$urls.pages.password}" class="btn btn-primary" rel="nofollow">
            <b>{l s='Forgot your password?' d='Shop.Theme.Customeraccount'}</b>
          </a>
        </div>
      </div>
    {/block}
{/block}
