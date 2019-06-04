<form method="post">
	<div class="panel">
		<div class="panel-heading">
			{l s="Gestion des icônes produits"}
			<span class="panel-heading-action">
				<a href="{$link->getAdminLink('AdminIconography')}&details" id="new_option" class="list-toolbar-btn" title="{l s='New' d='Shop.Theme.Actions'}">
					<i class="process-icon-new"></i>
				</a>
			</span>
		</div>
		<table class="table">
			<thead>
				<tr class="bg-primary">
					<th><b>{l s="ID"}</b></th>
					<th><b>{l s="Nom"}</b></th>
					<th class="text-center"><b>{l s="ALT"}</b></th>
					<th class="text-center"><b>{l s="URL"}</b></th>
					<th class="text-center"><b>{l s="Liste blanche"}</b></th>
					<th class="text-center"><b>{l s="Liste noire"}</b></th>
					<th class="text-center"><b>{l s="Position"}</b></th>
					<th class="text-center"><b>{l s="Statut"}</b></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$icons item=icon}
					<tr>
						<td>{$icon->id}</td>
						<td>{$icon->name}</td>
						<td class="text-center">{$icon->title}</td>
						<td class="text-center">{$icon->url}</td>
						<td class="text-center">
							{assign var=nb_white_list value=$icon->getWhiteList()|@count}
							{if $nb_white_list}
								<span class="badge"><b>{$nb_white_list}</b></span>
							{else}
								<span class="badge" style="background-color:lightgrey">{$nb_white_list}</span>
							{/if}
						</td>
						<td class="text-center">
							{assign var=nb_black_list value=$icon->getBlackList()|@count}
							{if $nb_black_list}
								<span class="badge"><b>{$nb_black_list}</b></span>
							{else}
								<span class="badge" style="background-color:lightgrey">{$nb_black_list}</span>
							{/if}
						</td>
						<td class="text-center">
							<span class="label label-default">
								<b>{$icon->position}</b>
							</span>
						</td>
						<td class="text-center">
							{if $icon->active}
								<span class="label label-success">
									<i class="icon-check"></i>
								</span>
							{else}
								<span class="label label-danger">
									<i class="icon-times"></i>
								</span>
							{/if}
						</td>
						<td class="text-right">
							<div class="btn-group">
								<a href="{$link->getAdminLink('AdminIconography')}&details&id={$icon->id}" class="btn btn-xs btn-default">
									<i class="icon-edit"></i>
								</a>
								<button type="submit" class="btn btn-xs btn-default" name="toggle" value="{$icon->id}">
									<i class="icon-refresh"></i>
								</button>
							</div>
							<button type="submit" class="btn btn-xs btn-danger delete" name="delete" value="{$icon->id}">
								<i class="icon-trash"></i>
							</button>
						</td>
					</tr>
				{foreachelse}
					<tr>
						<td colspan="8">
							{l s="Aucune icône enregistrée."}
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
</form>

<script>
	$(document).ready(function() {

		$('.delete').on('click', function(e) {
			if(!confirm('Etes-vous sur de vouloir supprimer cet icône ?'))
				e.preventDefault();
		});

	});
</script>