{if $rating}
	<div class="well padding-left-20">
		{include file="./stars.tpl"}
		{if !$hide_link}
			<a href="#customers_reviews" class="review-link">
				{l s="Voir les avis clients" mod="webequip_reviews"}
			</a>
		{else}
			<small class="text-info">
				{$nb} {l s='avis clients' mod="webequip_reviews"}
			</small>
		{/if}
	</div>
{/if}