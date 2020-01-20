<hr />

<div class="row">
	<div class="col-lg-6">

		{assign var=properties value=$manager->getProperties()}
		{if !empty($properties)}
			<table class="table">
				<tr>
					<td colspan="2" class="text-center">
						<b>{$manager->getTypeLabel()}</b>
					</td>
				</tr>
				{foreach from=$properties item=property}
					<tr>
						<td class="text-center">{$property.name}</td>
						<td class="text-center">{$property.value}</td>
					</tr>
				{/foreach}
			</table>
		{else}
			<div class="alert alert-danger">
				{l s="Le SKU n'a pas pu être reconnu. Merci de vérifier la configuration des attributs et/ou le SKU fourni"}
			</div>
		{/if}

	</div>
</div>