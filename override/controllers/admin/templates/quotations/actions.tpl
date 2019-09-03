<div class="btn-group">
	<a href="{$link->getAdminLink('AdminQuotations')}&dl_pdf&id_quotation={$id}" class="btn btn-default" title="{l s='Télécharger' d='Shop.Theme.Actions'}">
		<i class="icon-download"></i>
	</a>
	<a href="{$link->getAdminLink('AdminQuotations')}&dupplicate&id_quotation={$id}" class="btn btn-default" title="{l s='Copier' d='Shop.Theme.Actions'}">
		<i class="icon-copy"></i>
	</a>
	<button class="btn btn-default send-mail" title="{l s='Send' d='Shop.Theme.Actions'}" value="{$id}">
		<i class="icon-envelope"></i>
	</button>
</div>