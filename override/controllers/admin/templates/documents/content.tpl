{if isset($alert)}
	<div class="alert alert-{$alert.type}">
		<b>{$alert.content}</b>
	</div>
{/if}

{foreach from=$shops item=shop}
	{if $shop->id == 1} {* Activer Rolléco uniquement *}
		<form method="post" enctype="multipart/form-data">
			<input type="hidden" name="selected_shop" value="{$shop->id}">
			<div class="panel">
				<div class="panel-heading">
					<b style="color:{$shop->color}">{$shop->name}</b>
				</div>
				<table class="table">
					{foreach from=$shop->getDocuments() item=document}
						<tr>
							<td>
								<b>{$document.label}</b></td>
							<td>
							<td class="text-center">
								<b>{l s="PDF" d='Admin.Labels'}</b>
							</td>
							<td>
								<input type="file" class="form-control" name="new_file[{$document.name}]">
							</td>
							<td class="text-right">
								{if $document.exists}
									<a href="{$document.path}" class="btn btn-default" target="_blank">
										<i class="icon-file"></i>
									</a>
									<button type="submit" class="btn btn-danger" name="remove_file" value="{$document.name}">
										<i class="icon-trash"></i>
									</button>
								{else}
									<div class="label label-default">
										<b><i class="icon-times"></i> &nbsp; {l s="Aucun fichier" d='Admin.Labels'}</b>
									</div>
								{/if}
							</td>
						</tr>
					{/foreach}
				</table>
				<div class="panel-footer text-right">
					<button type="submit" class="btn btn-success">
						<i class="process-icon-save"></i> <b>{l s="Save" d='Shop.Theme.Actions'}</b>
					</button>
				</div>
			</div>
		</form>
	{/if}
{/foreach}