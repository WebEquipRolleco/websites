<!doctype html>
<html lang="{$language.iso_code}">

  <head>
    {block name='head'}
      {include file='_partials/head.tpl'}
    {/block}
  </head>

  <body id="{$page.page_name}" class="{$page.body_classes|classnames}">

    <!-- Google Tag Manager -->
    {assign var=google_key value=Configuration::get('KEY_GOOGLE_TAG_MANAGER')}
    {if $google_key}
      <script>{literal}(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
      new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
      j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
      'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);{/literal}
      })(window,document,'script','dataLayer','{$google_key}');</script>
    {/if}
    <!-- End Google Tag Manager -->

    {block name='hook_after_body_opening_tag'}
      {hook h='displayAfterBodyOpeningTag'}
    {/block}

    <main>
      {block name='product_activation'}
        {include file='catalog/_partials/product-activation.tpl'}
      {/block}

      <header id="header">
        {block name='header'}
          {include file='_partials/header.tpl'}
        {/block}
      </header>

      {block name='notifications'}
        {include file='_partials/notifications.tpl'}
      {/block}

      <section id="wrapper">

        {*include file='layouts/layout-brand.tpl'*}

        <div class="container">
          {block name='breadcrumb'}
            {include file='_partials/breadcrumb.tpl'}
          {/block}

          {block name="left_column"}
            <div id="left-column" class="col-xs-12 col-sm-4 col-md-3">
              {if $page.page_name == 'product'}
                {hook h='displayLeftColumnProduct'}
              {else}
                {hook h="displayLeftColumn"}
              {/if}
            </div>
          {/block}

          {block name="content_wrapper"}
            <div id="content-wrapper" class="left-column right-column col-sm-4 col-md-6">
              {hook h="displayContentWrapperTop"}
              {block name="content"}
                <p>Hello world! This is HTML5 Boilerplate.</p>
              {/block}
              {hook h="displayContentWrapperBottom"}
            </div>
          {/block}

          {block name="right_column"}
            <div id="right-column" class="col-xs-12 col-sm-4 col-md-3">
              {if $page.page_name == 'product'}
                {hook h='displayRightColumnProduct'}
              {else}
                {hook h="displayRightColumn"}
              {/if}
            </div>
          {/block}
        </div>
        {hook h="displayWrapperBottom"}
      </section>

      <div class="container">
        {hook h="displayContentWrapperBottom"}
      </div>
      
      <footer id="footer">
        {block name="footer"}
          {include file="_partials/footer.tpl"}
        {/block}
      </footer>

    </main>

    {block name='javascript_bottom'}
      {include file="_partials/javascript.tpl" javascript=$javascript.bottom}
    {/block}

    {assign var=bundle_key value=Configuration::get('KEY_FONT_AWESOME')}
    {if $bundle_key}
      <script src="https://kit.fontawesome.com/{$bundle_key}.js"></script>
    {/if}

    <script type="text/javascript" src="/themes/_libraries/iziModal/js/iziModal.js"></script>
    <script type="text/javascript" src="/themes/rolleco/assets/js/newsletter.js"></script>
    <script type="text/javascript" src="/themes/rolleco/assets/js/doofinder.js"></script>

    {block name="custom_js"}{/block}

    {block name='hook_before_body_closing_tag'}
      {hook h='displayBeforeBodyClosingTag'}
    {/block}

    <div id="ajax_content"></div>

    {block name="tags"}
      {if $google_key}
        <noscript>
          <iframe src="//www.googletagmanager.com/ns.html?id={$google_key}" height="0" width="0" style="display:none;visibility:hidden"></iframe>
        </noscript>
      {/if}
    {/block}

  </body>

</html>
