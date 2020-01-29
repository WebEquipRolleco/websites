{if empty($products)}
	<div class="alert alert-danger"> &nbsp; Aucun résultat trouvé pour votre recherche</div>
{else}
	<div class="alert alert-secondary" style="padding:0px">
		<table class="table" style="margin-bottom:-1px">
			<thead>
				<tr>
					<th>Désignation</th>
					<th class="text-center">Produit</th>
					<th class="text-center">Déclinaison</th>
					<th class="text-center">Fournisseur</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$products item=product}
					<tr>
						<td>{$product.name}</td>
						<td class="text-center">{$product.reference|default:'-'}</td>
						<td class="text-center">{$product.combination_reference|default:'-'}</td>
						<td class="text-center">{$product.product_supplier_reference|default:'-'}</td>
						<td class="text-right">
							<button type="button" class="btn btn-outline-secondary add-accessory" value="{$product.id_product}_{$product.id_product_attribute|default:'0'}">
								<i class="material-icons">add</i>
							</button>
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>

	<div class="text-center">
		<a href="" class="load-accessories">Retour à la liste des accessoires</a>
	</div>
{/if}

	<script>
		$(document).ready(function() {

			$('.add-accessory').on('click', function(e) {
				$.post({
					url: "{$link->getAdminLink('AdminModules')}&configure=webequip_configuration",
					data: { ajax:true, action:'add_accessory', id_product:$('#form_id_product').val(), data:$(this).val() },
					success : function(response) {
						$('#accessory_result').html(response);
					}
				});
			});

			$('.load-accessories').on('click', function(e) {
				e.preventDefault();
				window.loadAccessories();
			})
		});
	</script>

