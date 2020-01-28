
{if empty($products)}
	<div class="panel">
		<div class="alert alert-danger">Aucun produit ne correspond à votre recherche</div>
	</div>
{else}
	<div class="panel" style="padding:0px">
		<table class="table">
			<thead>
				<tr class="column-headers">
					<th><b>ID</b></th>
					<th class="text-center"><b>Désignation</b></th>
					<th class="text-center"><b>Référence</b></th>
					<th class="text-center"><b>Déclinaison</b></th>
					<th class="text-center"><b>Fournisseur</b></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$products item=product}
					<tr>
						<td>{$product.id_product}</td>
						<td class="text-center">{$product.name}</td>
						{if $product.reference|strstr:$search}
							<td class="text-center" style="background-color:yellow"><b>{$product.reference}</b></td>
						{else}
							<td class="text-center">{$product.reference}</td>
						{/if}
						{if $product.combination_reference|strstr:$search}
							<td class="text-center" style="background-color:yellow"><b>{$product.combination_reference}</b></td>
						{else}
							<td class="text-center">{$product.combination_reference}</td>
						{/if}
						{if $product.product_supplier_reference|strstr:$search}
							<td class="text-center" style="background-color:yellow"><b>{$product.product_supplier_reference}</b></td>
						{else}
							<td class="text-center">{$product.product_supplier_reference}</td>
						{/if}
						<td class="text-right">
							<a href="{$link->getAdminLink('AdminProducts', true, ['id_product'=>$product.id_product, 'editProduct'=>true])}" class="btn btn-default" target="_blank">
								<i class="icon icon-edit"></i> Editer le produit
							</a>
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
{/if}