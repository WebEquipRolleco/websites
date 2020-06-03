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
{block name='header_banner'}
  <div class="header-banner">
    {hook h='displayBanner'}
  </div>
{/block}

{block name='header_nav'}
  <nav class="header-nav">
    {*<div class="container">*}
      <div class="row">

          <div class="col-xs-12">

            {* BLOC DEVIS *}
            <a href="{$link->getPageLink('quotationregistration')}" id="_desktop_nav_quotation" class="nav-link">
              <i class="fa fa-calculator periodic-buzz"></i>
              <span class="hidden-lg-down">{l s="Devis gratuit"}</span>
            </a>
            <a href="#menu_mobile" id="display_mobile_menu" class="nav-link hidden-sm-up">
              {l s="Menu"}
            </a>
            {assign var=nb_quotations value=Quotation::countNew(Context::getContext()->customer->id)}
            {if $nb_quotations}
              <a href="{$link->getPageLink('quotationlist')}" class="nav-link text-danger text-center" title="{l s='Vous avez %s nouveau(x) devis' sprintf=[$nb_quotations]}">
                <i class="fa fa-exclamation-triangle"></i>
              </a>
            {/if}

            {hook h='displayNav1'}

          </div>

      </div>
    {*</div>*}
  </nav>
{/block}

{block name="brand_top"}
    <div id="brand_nav" class="row">
      <div class="col-xs-12 col-sm-3 col-lg-3 text-center">
        <a href="/">
          <img src="/img/rolleco.png"  class="margin-top-sm" style="width:175px;">
          
        </a>
      </div>
      <div class="col-xs-12 col-sm-6 col-lg-4">
          <div class="margin-top-15 margin-bottom-10">
            <p id="brand_description" class="hidden-xs-down">Votre équipement à prix éco - Site réservé aux professionnels</p>
            <input type="text" id="search_query_top" style="width:100%; padding:10px" placeholder="{l s='Rechercher'}">
          </div>
      </div>

      <div class="col-xs-12 col-lg-5 text-right">
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
