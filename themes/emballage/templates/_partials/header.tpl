{block name='header_banner'}
  <div class="header-banner">
    {hook h='displayBanner'}
  </div>
{/block}

{block name='header_nav'}
  <nav class="header-nav">
    {*<div class="container">*}
      <div class="row">
        <div class="hidden-sm-down">
          <div class="col-xs-12">
            
            {* BLOC DEVIS *}
            <a href="{$link->getPageLink('QuotationRegistration')}" id="_desktop_nav_quotation" class="nav-link">
              <i class="fa fa-calculator periodic-buzz"></i>
              {l s="Devis gratuit"}
            </a>
            {assign var=nb_quotations value=Quotation::countNew(Context::getContext()->customer->id)}
            {if $nb_quotations}
              &nbsp;
              <a href="{$link->getPageLink('QuotationList')}" title="{l s='Vous avez %s nouveau(x) devis' sprintf=[$nb_quotations]}">
                <i class="fa fa-exclamation" style="background-color:#d5121d; color:white; padding:5px"></i>
              </a>
            {/if}

            {hook h='displayNav1'}
            
          </div>
        </div>
        <div class="hidden-md-up text-sm-center mobile">
          <div class="float-xs-left" id="iziModal-menu-icon">
            <i class="material-icons fas fa-list d-inline"></i>
          </div>
          <div class="float-xs-right" id="_mobile_cart"></div>
          <div class="float-xs-right" id="_mobile_user_info"></div>
          <div class="top-logo" id="_mobile_logo"></div>
          <div class="clearfix"></div>
        </div>
      </div>
    {*</div>*}
  </nav>
{/block}

{block name="brand_top"}
    <div id="brand_nav" class="row">
      <div class="col-xs-12 col-lg-3 text-center">
        <a href="/">
          <img src="/img/contenant.png"  class="margin-top-10" style="width:175px;">
        </a>
      </div>
      <div class="col-xs-12 col-lg-3">
          <form method="get" action="{$link->getPageLink('search')}" class="margin-top-15 margin-bottom-10">
            <input type="hidden" name="controller" value="search">
            {l s="Contactez-nous au"} <b class="text-success">{Configuration::get('PS_SHOP_PHONE')}</b>
            <input type="text" style="width:100%; padding:10px" placeholder="{l s='Rechercher'}">
            <a href="" class="hvr-icon-forward" style="position:absolute; top:42px; right:25px;">
              <i class="fa fa-2x fa-play hvr-icon"></i>
            </a>
          </form>
      </div>

      <div class="col-xs-12 col-lg-6 text-right">
        {hook h='displayNav2'}
      </div>

    </div>
{/block}

{block name='header_top'}
  <div class="header-top">

       <div class="row-fluid">
        {hook h='displayTop'}
        <div class="clearfix"></div>
      </div>
      <div id="mobile_top_menu_wrapper" class="row hidden-md-up" style="display:none;">
        <div class="js-top-menu mobile" id="_mobile_top_menu"></div>
        <div class="js-top-menu-bottom">
          <div id="_mobile_currency_selector"></div>
          <div id="_mobile_language_selector"></div>
          <div id="_mobile_contact_link"></div>
        </div>
      </div>

  </div>
  {hook h='displayNavFullWidth'}
{/block}
