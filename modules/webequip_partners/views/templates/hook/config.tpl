<form method="post" enctype="multipart/form-data" class="defaultForm form-horizontal">
	<div class="panel">
		<div class="panel-heading">
			{l s="Nouveau partenaire" d='Shop.Theme.Labels'}
		</div>
		<div class="form-wrapper">
			<div class="row">
				<div class="col-lg-3">
					<input type="text" class="form-control" name="new_slide[name]" placeholder="Nom">
				</div>
				<div class="col-lg-3">
					<input type="file" class="form-control" name="new_slide[file]" placeholder="Fichier" required>
				</div>
				<div class="col-lg-3">
					<button type="submit" class="btn btn-info">
						<b>{l s="Ajouter" d='Shop.Theme.Actions'}</b>
					</button>
				</div>
			</div>
		</div>
	</div>
</form>

<form method="post" class="defaultForm form-horizontal">
	<div class="panel">
		<div class="panel-heading">
			{l s="Liste des partenaires" d='Shop.Theme.Labels'}
		</div>
		<div class="form-wrapper">
			{if $slides|count == 0}
				<div class="alert alert-info">
					{l s="Aucun partenaire enregistré" mod='webequip_partners'}
				</div>
			{else}
				<table class="table table-striped table-hover">
					<thead>
						<tr>
							<th>{l s="Nom" d='Shop.Theme.Labels'}</th>
							<th>{l s="image" d='Shop.Theme.Labels'}</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$slides item=slide}
							<tr>
								<td>{$slide->name}</td>
								<td><img src='{$slide->getUrl()}' style="height:50px" title='{$slide->picture}'></td>
								<td class="text-right">
									<button type="submit" class="btn btn-xs btn-danger" name="remove_slide" value="{$slide->id}" title="Supprimer">
										<i class="icon-trash"></i>
									</button>
								</td>
							</tr>
						{/foreach}
					</tbody>
				</table>
			{/if}
		</div>
	</div>
</form>