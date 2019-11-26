{assign var=trust_key value=Configuration::getForShop('WEBEQUIP_TRUST_KEY', $context->shop)}
{assign var=trust_url value=Configuration::getForShop('WEBEQUIP_TRUST_URL', $context->shop)}
{if $trust_key and $trust_url}
	<div class="trustpilot-widget margin-top-sm" data-locale="fr-FR" data-template-id="539ad0ffdec7e10e686debd7" data-businessunit-id="{$trust_key}" data-style-height="350px" data-style-width="100%" data-theme="light" data-stars="3,4,5" data-schema-type="Organization"> 
		<a href="https://fr.trustpilot.com/review/{$trust_url}" target="_blank" rel="noopener">{l s="Trustpilot" d='Shop.Theme.Checkout'}</a> 
	</div>
{/if}