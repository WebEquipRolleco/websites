{if isset($alert)}
	<div class="alert alert-{$alert.type}">
		<b>{$alert.content}</b>
	</div>
{/if}

{foreach from=$shops item=shop}
	<form method="post" enctype="multipart/form-data">
		<input type="hidden" name="selected_shop" value="{$shop->id}">
		<div class="panel">
			<div class="panel-heading">
				<b style="color:{$shop->color}">{$shop->name}</b>
			</div>
			<table class="table">
				<tr>
					<td>
						<b>{l s="Conditions de ventes"}</b></td>
					<td>
					<td class="text-center">
						<b>{l s="PDF"}</b>
					</td>
					<td>
						<input type="file" class="form-control" name="new_conditions">
					</td>
					<td class="text-right">
						{if $shop->hasConditionsFile()}
							<a href="{$shop->getConditionsFilePath()}" class="btn btn-default" target="_blank">
								<i class="icon-file"></i>
							</a>
							<button type="submit" class="btn btn-danger" name="remove_conditions">
								<i class="icon-trash"></i>
							</button>
						{else}
							<div class="label label-default">
								<b><i class="icon-times"></i> &nbsp; {l s="Aucun fichier"}</b>
							</div>
						{/if}
					</td>
				</tr>
			</table>
			<div class="panel-footer text-right">
				<button type="submit" class="btn btn-success">
					<i class="process-icon-save"></i> <b>{l s="Save" d='Shop.Theme.Actions'}</b>
				</button>
			</div>
		</div>
	</form>
{/foreach}