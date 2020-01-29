{if !empty($accessories)}
	<div class="alert alert-secondary" style="padding:0px">
		<table class="table" style="margin-bottom:-1px">
			<thead>
				<tr class="column-headers">
					<th><b>Désignation</b></th>
					<th class="text-center"><b>Référence</b></th>
					<th class="text-center"><b>Déclinaison</b></th>
					<th class="text-center"><b>Statut</b></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$accessories item=accessory}
					<tr>
						<td>{$accessory->getProduct()->name}</td>
						<td class="text-center">{$accessory->getProduct()->reference}</td>
						<td class="text-center">{if $accessory->getCombination()}{$accessory->getCombination()->reference}{else}-{/if}</td>
						<td class="text-center">
							{if $accessory->getProduct()->active}
								<i class="material-icons text-success" title="Produit actif">check</i>
							{else}
								<i class="material-icons text-danger" title="Produit inactif">clear</i>
							{/if}
						</td>
						<td class="text-right">
							<button type="button" class="btn btn-outline-secondary remove-accessory" value="{$accessory->id}" title="Supprimer">
								<i class="material-icons">delete</i>
							</button>
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>

	<script>
		$(document).ready(function() {

			$('.remove-accessory').on('click', function(e) {
				if(confirm("Etes-vous sûr(e) de vouloir supprimer cet accessoire ?")) {
					$.post({
						url: "{$link->getAdminLink('AdminModules')}&configure=webequip_configuration",
						data: { ajax:true, action:'remove_accessory', id:$(this).val() },
						success : function(response) {
							$('#accessory_result').html(response);
						}
					});
				}
			});

		});
	</script>
{/if}