<hr />

<div class="row">
	<div class="col-lg-4">
		<table class="table">
			<tr>
				<td colspan="2" class="text-center">
					<b>{$manager->getTypeLabel()}</b>
				</td>
			</tr>
			{foreach from=$manager->getProperties() item=property}
				<tr>
					<td class="text-center">{$property.name}</td>
					<td class="text-center">{$property.value}</td>
				</tr>
			{/foreach}
		</table>
	</div>
</div>