{*
* Project : everpscustomerconnect
* @author Team EVER
* @copyright Team EVER
* @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
* @link https://www.team-ever.com
*}
<div class="col-lg-12">
    <div class="bootstrap panel everpscustomerconnect">
        {if $logged && isset($logged)}
            <p><strong style="color:red;">{l s='You are currently logged as' mod='everpscustomerconnect'} {$firstname|escape:'htmlall':'UTF-8'} {$lastname|escape:'htmlall':'UTF-8'}</strong> <a href="{if $base_uri && isset($base_uri)}{$base_uri|escape:'htmlall':'UTF-8'}{/if}" target="_blank">{l s='go to shop' mod='everpscustomerconnect'}</a></p>
        {/if}
        {if $id_customer && isset($id_customer)}
            <form method="post" action="">
                <input type="hidden" name="id_customer" value="{$id_customer|escape:'htmlall':'UTF-8'}" />
                <button type="submit" name="submitSuperUser" value="1" class="btn btn-success button">
                    <i class="icon-user"></i> &nbsp; <b>{l s='Connect as this customer' mod='everpscustomerconnect'}</b>
                </button>
            </form>
        {/if}
    </div>
</div>