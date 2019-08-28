{*
* Project : everpscustomerconnect
* @author Team EVER
* @copyright Team EVER
* @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
* @link https://www.team-ever.com
*}

<div class="panel row">
    <div class="col-md-6">
    	<h3><i class="icon icon-smile"></i> {l s='Ever Customer Connect' mod='everpscustomerconnect'}</h3>
    	<img id="everlogo" src="{$evercustomerimage_dir|escape:'htmlall':'UTF-8'}ever.png" style="max-width: 120px;">
    	<p>
    		<strong>{l s='Welcome to Ever Customer Connect module !' mod='everpscustomerconnect'}</strong><br />
    		{l s='Thanks for using Team Ever\'s module' mod='everpscustomerconnect'}.<br />
    		<a href="https://www.team-ever.com/produit/prestashop-ever-ultimate-seo/" target="_blank">{l s='Have you seen this best SEO module for your Prestashop ?' mod='everpscustomerconnect'}</a>
    	</p>
    	<br />
    	{if $firstname && isset($firstname) && $lastname && isset($lastname)}
    	<p><strong style="color:red;">{l s='You are currently logged as' mod='everpscustomerconnect'} {$firstname|escape:'htmlall':'UTF-8'} {$lastname|escape:'htmlall':'UTF-8'}</strong> <a href="{$base_uri|escape:'htmlall':'UTF-8'}" target="_blank">{l s='go to shop' mod='everpscustomerconnect'}</a></p>
    	{/if}
    </div>
    <div class="col-md-6">
        <p class="alert alert-warning">
            {l s='This module is free and will always be ! You can support our free modules by making a donation by clicking the button below' mod='everpsquotation'}
        </p>
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
        <input type="hidden" name="cmd" value="_s-xclick" />
        <input type="hidden" name="hosted_button_id" value="3LE8ABFYJKP98" />
        <input type="image" src="https://www.team-ever.com/wp-content/uploads/2019/06/appel_a_dons-1.jpg" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Bouton Faites un don avec PayPal" />
        <img alt="" border="0" src="https://www.paypal.com/fr_FR/i/scr/pixel.gif" width="1" height="1" />
        </form>
    </div>
</div>