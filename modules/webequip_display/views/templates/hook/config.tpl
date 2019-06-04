<input type="hidden" id="load_details_url" value="{$module_link}">
<div id="ajax_content"></div>

<form method="post" class="defaultForm form-horizontal">
	<div class="panel">
		<div class="panel-heading">
			{l s="Liste des blocs publicitaires" d='Shop.Theme.Labels'}
			<span class="panel-heading-action">
				<a href="#" id="new_display" class="list-toolbar-btn" title="{l s='New' d='Shop.Theme.Actions'}">
					<i class="process-icon-new"></i>
				</a>
			</span>
		</div>
		<div class="form-wrapper">
			{if $displays|count == 0}
				<div class="alert alert-info">
					{l s="Aucun bloc publicitaire enregistré" mod='webequip_advertisement'}
				</div>
			{else}
				<table class="table table-striped table-hover">
					<thead>
						<tr>
							<th><b>{l s="Nom" d='Shop.Theme.Labels'}</b></th>
							<th class="text-center"><b>{l s="Lien" d='Shop.Theme.Labels'}</b></th>
							<th class="text-center"><b>{l s="Image" d='Shop.Theme.Labels'}</b></th>
							<th class="text-center"><b>{l s="Position" d='Shop.Theme.Labels'}</b></th>
							<th class="text-center"><b>{l s="Statut" d='Shop.Theme.Labels'}</b></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$displays item=display}
							<tr>
								<td>{$display->name}</td>
								<td class="text-center">{$display->link|default:'-'}</td>
								<td class="text-center">
									<img src="{$display->getUrl()}" style="height:50px" title="{$display->picture}">
								</td>
								<td class="text-center">#{$display->position}</td>
								<td class="text-center">
									{if $display->active}
										<span class="label label-success">
											<i class="icon-check"></i>
										</span>
									{else}
										<span class="label label-danger">
											<i class="icon-close"></i>
										</span>
									{/if}
								</td>
								<td class="text-right">
									<a href="#" class="btn btn-xs btn-warning load-details" data-id="{$display->id}" title="{l s='Modifier' d='Shop.Theme.Labels'}">
										<i class="icon-edit"></i>
									</a>
									<button type="submit" class="btn btn-xs btn-danger" class="remove-display" name="remove_display" value="{$display->id}" title="{l s='Supprimer' d='Shop.Theme.Labels'}">
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

{literal}
<script>
	$(document).ready(function() {

		$('#new_display').on('click', function(e) {
			e.preventDefault();
			loadDetails(0);
		});

		$('.load-details').on('click', function(e) {
			e.preventDefault();
			loadDetails($(this).data('id'));
		});

		$('.copy-display').on('click', function(e) {
			if(!confirm("Voulez-vous copier ce bloc publicitaire ?")) {
				e.preventDefault();
			}
		});

		$('.remove-display').on('click', function(e) {
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
				$('#details').modal('show');
			}
		});	
	}
</script>
{/literal}