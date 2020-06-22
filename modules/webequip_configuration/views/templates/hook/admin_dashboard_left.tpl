{if $nb_priceless_products or $nb_priceless_combinations}
	<div class="panel">
		<div class="panel-heading">
			<b class="text-danger"><i class="icon-lock"></i> Sécurité</b>
		</div>
		<div class="panel-content">
			<div>
				<a href="{$link->getAdminLink('AdminImportExport')}" class="btn btn-danger">
					<b>{$nb_priceless_products}</b> produits sans prix
				</a>
			</div>
			<div style="margin-top:15px">
				<a href="{$link->getAdminLink('AdminImportExport')}" class="btn btn-danger">
					<b>{$nb_priceless_combinations}</b> déclinaisons sans prix
				</a>
			</div>
		</div>
	</div>
{/if}