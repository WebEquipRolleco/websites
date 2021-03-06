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
<!doctype html>
<html lang="{$language.iso_code}">

  <head>
    {block name='head'}
      {include file='_partials/head.tpl'}
    {/block}
    <!-- Google Tag Manager -->
    {assign var=google_key value=Configuration::get('KEY_GOOGLE_TAG_MANAGER')}
    {if $google_key}
    <script>{literal}(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
              j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
              'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);{/literal}
      })(window,document,'script','dataLayer','{$google_key}');</script>
    {/if}
  </head>

  <body id="{$page.page_name}" class="{$page.body_classes|classnames}">
  <noscript>{literal}<iframe src="https://www.googletagmanager.com/ns.html?id={/literal}{$google_key}{literal}"
                             height="0" width="0" style="display:none;visibility:hidden"></iframe>{/literal}</noscript>
    {block name='hook_after_body_opening_tag'}
      {hook h='displayAfterBodyOpeningTag'}
    {/block}

    <header id="header">
      {block name='header'}
        {include file='checkout/_partials/header.tpl'}
      {/block}
    </header>

    {block name='notifications'}
      {include file='_partials/notifications.tpl'}
    {/block}

    <section id="wrapper" style="margin-top:0px !important;">
      
      {*include file='layouts/layout-brand.tpl'*}
      
      <div class="container" style="padding-top:60px !important;">

      <div class="row">
            <div class="col-md-12">
                {render file='checkout/checkout-process.tpl' ui=$checkout_process list=true}
            </div>
          </div>

      {block name='content'}
        <section id="content">

          <div class="row">
            <div class="col-md-12">
                {render file='checkout/checkout-process.tpl' ui=$checkout_process list=false}
            </div>
          </div>

          {*<div class="row">
            <div class="col-md-4">

              {block name='cart_summary'}
                {include file='checkout/_partials/cart-summary.tpl' cart = $cart}
              {/block}

              {hook h='displayReassurance'}
            </div>
          </div>*}

        </section>
      {/block}
      </div>
      {hook h="displayWrapperBottom"}
    </section>

    <footer id="footer">
      {block name='footer'}
        {include file='_partials/footer.tpl'}
      {/block}
    </footer>

    {block name='javascript_bottom'}
      {include file="_partials/javascript.tpl" javascript=$javascript.bottom}
      <script type="text/javascript" src="/themes/rolleco/assets/js/registration.js"></script>
      <script type="text/javascript" src="/themes/rolleco/assets/js/order.js"></script>
    {/block}

    {block name='hook_before_body_closing_tag'}
      {hook h='displayBeforeBodyClosingTag'}
    {/block}

    {assign var=bundle_key value=Configuration::get('KEY_FONT_AWESOME')}
    {if $bundle_key}
      <script src="https://kit.fontawesome.com/{$bundle_key}.js"></script>
    {/if}
    
  </body>

</html>
