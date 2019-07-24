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
        <div class="hidden-sm-down">
          <div class="col-xs-12 col-lg-5 left-nav">
            {hook h='displayNav1'}

            {* BLOC CONTACT *}
            <div id="nav_contact" class="text-center hidden-lg-down">
              {l s="Une question, un conseil ? Appelez-nous au"} 
              <span class="phone">{Configuration::get('PS_SHOP_PHONE')}</span> 
              {l s="(Appel local non surtaxé)"}
            </div>
            
          </div>
          <div class="col-xs-12 col-lg-5 right-nav">

            {* BLOC DEVIS *}
            <div id="_desktop_nav_quotation">
              <div class="nav-quotation">
                <a href="{$link->getPageLink('QuotationRegistration')}">
                  {l s="Demander votre devis"}
                </a>
                {assign var=nb_quotations value=Quotation::countNew(Context::getContext()->customer->id)}
                {if $nb_quotations}
                  &nbsp;
                  <a href="{$link->getPageLink('QuotationList')}" title="{l s='Vous avez %s nouveau(x) devis' sprintf=[$nb_quotations]}">
                    <i class="fa fa-exclamation" style="background-color:#d5121d; color:white; padding:5px"></i>
                  </a>
                {/if}
              </div>
            </div>

            {hook h='displayNav2'}
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
