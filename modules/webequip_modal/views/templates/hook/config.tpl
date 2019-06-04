<input type="hidden" id="load_details_url" value="{$module_link}">

<div class="alert alert-info">
	<strong>IziModal : </strong>
	<a href="//izimodal.marcelodolce.com" target="_blank">
		{l s="Lien vers la documentation" mod="webequip_modal"}
	</a>
	<br />
	<strong>Font-awesome icônes : </strong>
	<a href="//fontawesome.com/icons?d=gallery" target="_blank">
		{l s="Lien vers la documentation" mod="webequip_modal"}
	</a>
</div>

<div class="panel">
	<div class="panel-heading">
		{l s="Configuration" d='Shop.Theme.Labels'}
		<span class="panel-heading-action">
			<a href="#" id="new_modal" class="list-toolbar-btn" title="{l s='New' d='Shop.Theme.Actions'}">
				<i class="process-icon-new"></i>
			</a>
		</span>
	</div>
	{if $modals|count > 0}
		<table class="table table-striped table-hover">
			<thead>
				<tr class="bg-primary">
					<th></th>
					<th><b>{l s="Titre" d='Shop.Theme.Labels'}</b></th>
					<th class="text-center"><b>{l s="Date de début" d='Shop.Theme.Labels'}</b></th>
					<th class="text-center"><b>{l s="Date de fin" d='Shop.Theme.Labels'}</b></th>
					<th class="text-center"><b>{l s="Boutiques" d='Shop.Theme.Labels'}</b></th>
					<th class="text-center"><b>{l s="Ouverture" d='Shop.Theme.Labels'}</b></th>
					<th class="text-center"><b>{l s="Fermeture" d='Shop.Theme.Labels'}</b></th>
					<th class="text-center"><b>{l s="Clients" d='Shop.Theme.Labels'}</b></th>
					<th class="text-center"><b>{l s="Groupes" d='Shop.Theme.Labels'}</b></th>
					<th class="text-center"><b>{l s="Statut" d='Shop.Theme.Labels'}</b></th>
					<th class="text-center"><b>{l s="Affichage" d='Shop.Theme.Labels'}</b></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$modals item=modal}
					<tr>
						<td>{$modal->id}</td>
						<td>{$modal->title}</td>
						<td class="text-center">
							{if $modal->date_begin != '0000-00-00'}
								{$modal->date_begin|date_format:'d/m/Y'}
							{else}
								-
							{/if}
						</td>
						<td class="text-center">
							{if $modal->date_end != '0000-00-00'}
								{$modal->date_end|date_format:'d/m/Y'}
							{else}
								-
							{/if}
						</td>
						<td class="text-center">{$modal->countShops()}</td>
						<td class="text-center">
							{if $modal->auto_open}
								{$modal->auto_open}s
							{else}
								{l s="non" d='Shop.Theme.Labels'}
							{/if}
						</td>
						<td class="text-center">
							{if $modal->auto_close}
								{$modal->auto_close}s
							{else}
								{l s="non" d='Shop.Theme.Labels'}
							{/if}
						</td>
						<td class="text-center">
							<span class="badge badge-success" title="{l s='Liste blanche' d='Shop.Theme.Labels'}">
								<b>{$modal->getAllowCustomersIds()|count}</b>
							</span>
							<span class="badge badge-danger" title="{l s='Liste noire' d='Shop.Theme.Labels'}">
								<b>{$modal->getDisableCustomersIds()|count}</b>
							</span>
						</td>
						<td class="text-center">
							<span class="badge badge-success" title="{l s='Liste blanche' d='Shop.Theme.Labels'}">
								<b>{$modal->getAllowGroupsIds()|count}</b>
							</span>
							<span class="badge badge-danger" title="{l s='Liste noire' d='Shop.Theme.Labels'}">
								<b>{$modal->getDisableGroupsIds()|count}</b>
							</span>
						</td>
						<td class="text-center">
							{if $modal->active}
								<span class="label label-success" title="{l s='Actif' d='Shop.Theme.Labels'}">
									<i class="icon-check"></i>
								</span>
							{else}
								<span class="label label-danger" title="{l s='Inactif' d='Shop.Theme.Labels'}">
									<i class="icon-remove"></i>
								</span>
							{/if}
						</td>
						<td class="text-center">
							{if $modal->display_for_customers}
								<span class="label label-success" title="{l s='Visiteur(s) connecté(s)' mod='webequip_modal'}">
									<i class="icon-check"></i>
								</span>
							{else}
								<span class="label label-danger" title="{l s='Visiteur(s) connecté(s)' mod='webequip_modal'}">
									<i class="icon-remove"></i>
								</span>
							{/if}
							&nbsp;
							{if $modal->display_for_guests}
								<span class="label label-success" title="{l s='Visiteur(s) non-connecté(s)' mod='webequip_modal'}">
									<i class="icon-check"></i>
								</span>
							{else}
								<span class="label label-danger" title="{l s='Visiteur(s) non-connecté(s)' mod='webequip_modal'}">
									<i class="icon-remove"></i>
								</span>
							{/if}
							&nbsp;
							{if $modal->validation}
								<span class="label label-success" title="{l s='Validation client nécessaire' mod='webequip_modal'}">
									<i class="icon-check"></i>
								</span>
							{else}
								<span class="label label-danger" title="{l s='Validation client facultative' mod='webequip_modal'}">
									<i class="icon-remove"></i>
								</span>
							{/if}
						</td>
						<td class="text-right">
							<form method="post">
								<button type="submit" class="btn btn-xs btn-default copy-modal" name="copy_modal" value="{$modal->id}">
									<i class="icon-copy"></i>
								</button>
								<a href="#" class="btn btn-xs btn-default load-details" data-id="{$modal->id}" title="{l s='Editer' d='Shop.Theme.Actions'}">
									<i class="icon-edit"></i>
								</a>
								<button type="submit" class="btn btn-xs btn-danger remove-modal" name="delete_modal" value="{$modal->id}" title="{l s='Supprimer' d='Shop.Theme.Actions'}">
									<i class="icon-trash"></i>
								</button>
							</form>
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	{else}
		<div class="alert alert-warning">
			{l s="Vous n'avez enregistré aucune modal" mod="webequip_modal"}
		</div>
	{/if}
</div>

<div id="ajax_content"></div>

{literal}
<script>
	$(document).ready(function() {

		$('#new_modal').on('click', function(e) {
			e.preventDefault();
			loadDetails(0);
		});

		$('.load-details').on('click', function(e) {
			e.preventDefault();
			loadDetails($(this).data('id'));
		});

		$(document).on('click', '#switch_display', function() {
			$('#modal_content').toggle();
			$('#modal_parameters').toggle();
		});

		$('.copy-modal').on('click', function(e) {
			if(!confirm("Voulez-vous copier cette modal ?")) {
				e.preventDefault();
			}
		});

		$('.remove-modal').on('click', function(e) {
			if(!confirm("Etes-vous sûr ?")) {
				e.preventDefault();
			}
		});

	});

	function loadDetails(id) {
		$.ajax({
			url: $('#load_details_url').val(),
			data: {'ajax':true, 'action':'details', 'id':id},
			success : function(result) {

				$('#ajax_content').html(result);
				$('#modal_details').modal('show');
			}
		});	
	}
</script>
{/literal}