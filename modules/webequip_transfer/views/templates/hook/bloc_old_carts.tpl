<div class="panel">
	<div class="panel-heading">
		<i class="icon-cart"></i> Anciens panier
		<div class="panel-heading-action">
			<a href="" class="list-toolbar-btn load-data" data-type="carts" data-update="0" title="{l s="Reload" d='Shop.Theme.Actions'}">
				<i id="refresh_carts" class="process-icon-refresh"></i>
			</a>
		</div>
	</div>
	<div class="panel-content">
		{if $nb}
			<div class="alert alert-warning">
				<b>{$nb}</b> paniers sur les anciennes boutiques
			</div>
		{else}
			<div class="alert alert-success">
				<b>Transfert terminé</b>
			</div>
		{/if}
	</div>
	{if $nb}
		<div class="panel-footer text-center">
			<button type="button" class="btn btn-warning load-data" data-type="carts" data-update="1">
				<b>Transférer</b>
			</button>
		</div>
	{/if}
</div>