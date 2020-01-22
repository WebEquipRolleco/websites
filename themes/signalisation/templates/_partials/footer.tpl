<div class="footer-wrapper">
  <div id="before_footer" class="row">
    {block name='hook_footer_before'}

      {* BLOC TRUST PILOT *}
      {*assign var=trust_key value=Configuration::get('WEBEQUIP_TRUST_KEY')}
      {assign var=trust_url value=Configuration::get('WEBEQUIP_TRUST_URL')}
      {if $trust_key && $trust_url}
        <div class="col-xs-12 col-lg-4 margin-top-sm">
          <h3 class="margin-top-15"><i class="fa fa-star"></i> {l s="Ce que nos clients pensent de nous"}</h3>
          <script type="text/javascript" src="//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js" async></script>
          <div class="trustpilot-widget margin-top-15" data-locale="fr-FR" data-template-id="5613c9cde69ddc09340c6beb" data-businessunit-id="{$trust_key}" data-style-height="100px" data-style-width="250" data-theme="dark">
            <a href="{$trust_url}" target="_blank">
              {l s="Trustpilot"}
            </a>
          </div>
        </div>
      {/if*}

      {* BLOC PAIEMENTS *}
      <div class="col-xs-12 col-lg-4 margin-top-sm">
        <h3 class="margin-top-15"><i class="fa fa-lock"></i> {l s="Paiement sécurisé"}</h3>
        <div class="text-center margin-top-15">
          <img src="/img/paiement.png">
        </div>
        {assign var=id_cms value=Configuration::get('FOOTER_LINK_PAIEMENT')}
        {if $id_cms}
          <div class="text-center margin-top-10">
            <a href="{$link->getCMSLink($id_cms)}">{l s='Voir nos moyens de paiment'}</a>
          </div>
        {/if}
      </div>

      {* BLOC CONTACT *}
      <div class="col-xs-12 col-lg-4 margin-top-sm">
        <h3 class="margin-top-15"><i class="fa fa-phone fa-flip-horizontal"></i> {l s="Nous contacter"}</h3>
        <div id="footer_phone" class="text-center margin-top-15">
          {Configuration::get('PS_SHOP_PHONE')}
        </div>
        <div class="text-center">
          {l s='du lundi au vendredi de 9h à 12h et de 14h à 18h'}
        </div>
        {assign var=id_cms value=Configuration::get('FOOTER_LINK_FAQ')}
        {if $id_cms}
          <div class="text-center margin-top-15">
            <a href="{$link->getCMSLink($id_cms)}" class="btn margin-bottom-15">{l s='Notre foire aux questions'}</a>
          </div>
        {/if}
      </div>

      {include file='_partials/block_newsletter.tpl'}
      
    {/block}
  </div>
</div>
<div id="footer_links" class="footer-container">
    <div class="row">
      {block name='hook_footer'}
        {hook h='displayFooter'}
      {/block}
    </div>
    <div class="row">
      {block name='hook_footer_after'}
        {hook h='displayFooterAfter'}
      {/block}
    </div>
</div>
