<div class="btn-group">
	<a href="{$link->getAdminLink('AdminQuotations')}&dl_pdf&id_quotation={$quotation->id}" class="btn btn-default" title="{l s='Télécharger' d='Shop.Theme.Actions'}">
		<i class="icon-download"></i>
	</a>
	<a href="{$link->getAdminLink('AdminQuotations')}&dupplicate&id_quotation={$quotation->id}" class="btn btn-default" title="{l s='Copier' d='Shop.Theme.Actions'}">
		<i class="icon-copy"></i>
	</a>
	<button class="btn btn-default send-mail" title="{l s='Send' d='Shop.Theme.Actions'}" value="{$quotation->id}" {if !$quotation->getCustomer()}disabled title="{l s='Vous devez affectuer un client au devis'}"{/if}>
		<i class="icon-envelope"></i>
	</button>
</div>