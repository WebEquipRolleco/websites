<div class="panel">
	<div class="panel-heading text-center">
		<b style="color:{$shop.color}">{$shop.name}</b>
	</div>
	<div class="row">
		<div class="col-lg-4 text-center">
			<div class="text-muted">{l s="CA HT"}</div>
			<div class="text-muted"><b>{displayPrice price=$shop.turnover}</b></div>
			<div class="text-{if $shop.rate_turnover}success{else}danger{/if}">
				{if $shop.rate_turnover}+{/if}{$shop.rate_turnover|string_format:"%.2f"} %
			</div>
		</div>
		<div class="col-lg-4 text-center">
			<div class="text-muted">{l s="Transactions"}</div>
			<div class="text-muted"><b>{$shop.nb_orders}</b></div>
			<div class="text-{if $shop.rate_nb_orders}success{else}danger{/if}">
				{if $shop.rate_nb_orders}+{/if}{$shop.rate_nb_orders|string_format:"%.2f"} %
			</div>
		</div>
		<div class="col-lg-4 text-center">
			<div class="text-muted">{l s="Panier moyen HT"}</div>
			<div class="text-muted"><b>{displayPrice price=$shop.avg}</b></div>
			<div class="text-{if $shop.rate_avg}success{else}danger{/if}">
				{if $shop.rate_avg}+{/if}{$shop.rate_avg|string_format:"%.2f"} %
			</div>
		</div>
	</div>
</div>