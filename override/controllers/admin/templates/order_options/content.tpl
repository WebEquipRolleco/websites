<div class="panel">

	<div class="panel-heading">
		{l s="Options de commande"}
		<span class="panel-heading-action">
			<a href="{$link->getAdminLink('AdminOrderOptions')}&details" id="new_option" class="list-toolbar-btn" title="{l s='New' d='Shop.Theme.Actions'}">
				<i class="process-icon-new"></i>
			</a>
		</span>
	</div>

	<form method="post">
		<table class="table">
			<thead>
				<tr>
					<th><b>{l s="ID"}</b></th>
					<th><b>{l s="Nom"}</b></th>
					<th class="text-center"><b>{l s="Référence"}</b></th>
					<th class="text-center"><b>{l s="Type"}</b></th>
					<th class="text-center"><b>{l s="Valeur"}</b></th>
					<th class="text-center"><b>{l s="Liste blanche"}</b></th>
					<th class="text-center"><b>{l s="Liste noire"}</b></th>
					<th class="text-center"><b>{l s="Statut"}</b></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$options item=option}
					<tr>
						<td>{$option->id}</td>
						<td>{$option->name}</td>
						<td class="text-center">{$option->reference|default:'-'}</td>
						<td class="text-center">{$option->getTypeLabel()}</td>
						<td class="text-center">{$option->value}</td>
						<td class="text-center">
							{assign var=nb_white_list value=$option->getWhiteList()|@count}
							{if $nb_white_list}
								<span class="badge"><b>{$nb_white_list}</b></span>
							{else}
								<span class="badge" style="background-color:lightgrey">{$nb_white_list}</span>
							{/if}
						</td>
						<td class="text-center">
							{assign var=nb_black_list value=$option->getBlackList()|@count}
							{if $nb_black_list}
								<span class="badge"><b>{$nb_black_list}</b></span>
							{else}
								<span class="badge" style="background-color:lightgrey">{$nb_black_list}</span>
							{/if}
						</td>
						<td class="text-center">
							{if $option->active}
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
								<a href="{$link->getAdminLink('AdminOrderOptions')}&details&id={$option->id}" class="btn btn-xs btn-default">
									<i class="icon-edit"></i>
								</a>
								<button type="submit" class="btn btn-xs btn-default" name="toggle_option" value="{$option->id}">
									<i class="icon-refresh"></i>
								</button>
							</div>
							<button type="submit" class="btn btn-xs btn-danger remove-option" name="remove_option" value="{$option->id}">
								<i class="icon-trash"></i>
							</button>
						</td>
					</tr>
				{foreachelse}
					<tr>
						<td colspan="4">
							{l s="Aucune option de commande enregistrée."}
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</form>

</div>

<script>
	$(document).ready(function() {

		$('.remove-option').on('click', function(e) {
			if(!confirm("Etes-vous sur de vouloir supprimer cette option ?"))
				e.preventDefault();
		});

	});
</script>