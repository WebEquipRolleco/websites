{if isset($reduction)}
	<p class="text-center">
		{$reduction->description}
	</p>
	<p class="text-center">
		<b class="text-info">{$reduction->code}</b>
	</p>
	<p class="text-center text-muted">
		{l s="(Les codes promo ne sont pas applicables sur les devis)"}
	</p>
{else}
	{l s="Nous vous remercions de vous êtes inscrit à la newsletter !"}
{/if}
<p class="text-center">
	<button class="btn btn-success bold" data-izimodal-close="#ajax_modal_newsletter">
		{l s='Fermer' d='Shop.Theme.Labels'}
	</button>
</p>