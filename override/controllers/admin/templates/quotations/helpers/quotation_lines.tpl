{foreach $quotations as $quotation}
	<tr>
		<td>{$quotation->reference}</td>
		<td class="text-center">
			<span class="label label-{$quotation->getStatusClass()}">
				<b>{$quotation->getStatusLabel()}</b>
			</span>
		</td>
		<td class="text-center">
			{if $quotation->id_customer}
				{$quotation->getCustomer()->firstname} {$quotation->getCustomer()->lastname}
			{else}
				-
			{/if}
		</td>
		<td class="text-center">{$quotation->getEmployee()->firstname} {$quotation->getEmployee()->lastname}</td>
		<td class="text-center">{$quotation->date_add|date_format:'d/m/Y'}</td>
		<td class="text-center">
			{if $quotation->origin}
				<i class="icon-{$quotation->getOriginClass()}" title="{$quotation->getOriginLabel()}"></i>
			{/if}
		</td>
		<td class="text-center">
			{if $quotation->new}
				<i class="icon-star" style="color:#ffd24a" title="{l s='Nouveau' d='Shop.Theme.Labels'}"></i>
			{/if}
			{if $quotation->highlight}
				<i class="icon-asterisk text-danger" title="{l s='Mis en valeur' d='Shop.Theme.Labels'}"></i>
			{/if}
			{if $quotation->active}
				<span class="label label-success"><i class="icon-check"></i></span>
			{else}
				<span class="label label-danger"><i class="icon-times"></i></span>
			{/if}
		</td>
		<td class="text-right">
			<div class="btn-group">
				<a href="{$link->getAdminLink('AdminQuotations')}&dl_pdf&id={$quotation->id}" class="btn btn-xs btn-default" title="{l s='Download' d='Shop.Theme.Actions'}" target="_blank">
					<i class="icon-file"></i>
				</a>
					<a href="" class="btn btn-xs btn-default" title="{l s='Ajouter au panier' d='Shop.Theme.Actions'}">
						<i class="icon-shopping-cart"></i>
					</a>
			</div>
			<div class="btn-group">
				<a href="{$link->getAdminLink('AdminQuotations')}&details&id={$quotation->id}" class="btn btn-xs btn-default" title="{l s='Modifier' d='Shop.Theme.Actions'}">
					<i class="icon-edit"></i>
				</a>
				<button type="submit" class="btn btn-xs btn-default copy-quotation" name="copy_quotation" value="{$quotation->id}" title="{l s='Copier' d='Shop.Theme.Actions'}">
					<i class="icon-copy"></i>
				</button>
				<button type="submit" class="btn btn-xs btn-default remove-quotation" name="remove_quotation" value="{$quotation->id}" title="{l s='Supprimer' d='Shop.Theme.Actions'}">
					<i class="icon-trash"></i>
				</button>
			</div>
		</td>
	</tr>
{foreachelse}
	<tr>
		<td colspan="7">
			{l s="Aucun devis trouvé."}
		</td>
	</tr>
{/foreach}