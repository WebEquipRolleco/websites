<input type="hidden" id="load_details_url" value="{$module_link}">

<div class="panel">
	<div class="panel-heading">
		{l s="Configuration" d='Shop.Theme.Labels'}
		<span class="panel-heading-action">
			<a href="#" id="new_reassurance" class="list-toolbar-btn" title="{l s='New' d='Shop.Theme.Actions'}">
				<i class="process-icon-new"></i>
			</a>
		</span>
	</div>
	{if $reassurances|count > 0}
		<table class="table">
			<thead>
				<tr class="bg-info">
					<th><strong>{l s="Titre" d='Shop.Theme.Labels'}</strong></th>
					<th class="text-center"><strong>{l s="Lien" d='Shop.Theme.Labels'}</strong></th>
					<th class="text-center"><strong>{l s="Position" d='Shop.Theme.Labels'}</strong></th>
					<th class="text-center"><strong>{l s="Order d'affichage" d='Shop.Theme.Labels'}</strong></th>
					<th class="text-center"><strong>{l s="Statut" d='Shop.Theme.Labels'}</strong></th>
					<th class="text-center"><strong>{l s="Boutiques" d='Shop.Theme.Labels'}</strong></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$reassurances item=reassurance}
					<tr>
						<td>{$reassurance->name}</td>
						<td class="text-center">{$reassurance->link|default:'-'}</td>
						<td class="text-center">
							{foreach from=$locations key=value item=location}
								{if $reassurance->location == $value}
									{$location}
								{/if}
							{/foreach}
						</td>
						<td class="text-center">{$reassurance->position}</td>
						<td class="text-center">
							{if $reassurance->active}
								<span class="label label-success">
									<i class="icon-check"></i>
								</span>
							{else}
								<span class="label label-danger">
									<i class="icon-times"></i>
								</span>
							{/if}
						</td>
						<td class="text-center">
							{foreach from=$shops item=shop}
								{if $reassurance->hasShop($shop.id_shop)}
									<span class="label label-success" title="{$shop.name|capitalize}">
										<i class="icon-check"></i>
									</span>
								{else}
									<span class="label label-danger" title="{$shop.name|capitalize}">
										<i class="icon-times"></i>
									</span>
								{/if}
								&nbsp;
							{/foreach}
						</td>
						<td class="text-right">
							<form method="post">
								<button type="button" class="btn btn-default load-details" data-id="{$reassurance->id}" title="{l s='Editer' d='Shop.Theme.Actions'}">
									<i class="icon-edit"></i>
								</button>
								<button type="submit" class="btn btn-danger" name="remove" value="{$reassurance->id}" title="{l s='Supprimer' d='Shop.Theme.Actions'}">
									<i class="icon-trash"></i>
								</button>
							</form>
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	{else}
		<div class="alert alert-info">
			{l s="Vous n'avez enregistré aucune réassurance." mod="webequip_reassurance"}
		</div>
	{/if}
</div>

<div id="ajax_content"></div>

<script>
	$(document).ready(function() {

		$('#new_reassurance').on('click', function() {
			loadDetails(0);
		});

		$('.load-details').on('click', function() {
			loadDetails($(this).data('id'));
		});
	});

	{literal}
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
	{/literal}

</script>