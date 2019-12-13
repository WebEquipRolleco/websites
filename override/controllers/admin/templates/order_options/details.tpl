<form method="post">
	<input type="hidden" name="id" value="{$option->id}">

	<div class="panel text-right">
		<a href="{$link->getAdminLink('AdminOrderOptions')}" class="btn btn-default">
			<b>{l s='Retour' d='Shop.Theme.Labels'}</b>
		</a>
		<button type="submit" class="btn btn-success" name="save">
			<b>{l s='Enregistrer' d='Shop.Theme.Labels'}</b>
		</button>
	</div>

	<div class="row">
		<div class="col-lg-6">
			<div class="panel">
				<div class="panel-heading">
					{l s="Affichage"}
				</div>
				<div class="form-group">
					<label for="reference">{l s="Référence"}</label>
					<input type="text" class="form-control" name="option[reference]" value="{$option->reference}">
				</div>
				<div class="form-group">
					<label for="name">{l s="Nom"} <em class="text-danger">*</em></label>
					<input type="text" class="form-control" name="option[name]" value="{$option->name}" required>
				</div>
				<div class="form-group">
					<label for="description">{l s="Description"}</label>
					<textarea rows="4" class="form-control" name="option[description]">{$option->description}</textarea>
				</div>
				<div class="form-group">
					<label for="warning">{l s="Message d'avertissement"}</label>
					<textarea rows="4" class="form-control" name="option[warning]">{$option->warning}</textarea>
					<div class="text-right text-muted"><em>{l s="Visible dans le panier pour les produits non concernés"}</em></div>
				</div>
			</div>
			<div class="panel">
				<div class="panel-heading">
					{l s="Options"}
				</div>
				<span class="switch prestashop-switch fixed-width-lg" style="margin-left:auto; margin-right:auto; margin-bottom:20px">
					<input type="radio" name="option[active]" id="active_on" value="1" {if $option->active}checked{/if}>
					<label for="active_on">{l s='Active' d='Shop.Theme.Labels'}</label>
					<input type="radio" name="option[active]" id="active_off" value="0" {if !$option->active}checked{/if}>
					<label for="active_off">{l s='Inactive' d='Shop.Theme.Labels'}</label>
					<a class="slide-button btn"></a>
				</span>
				{foreach from=Shop::getShops() item=shop}
					<div class="text-center"><b>{$shop.name}</b></div>
					<span class="switch prestashop-switch fixed-width-lg" style="margin-left:auto; margin-right:auto; margin-bottom:20px">
						<input type="radio" name="shops[{$shop.id_shop}]" id="shop_{$shop.id_shop}_on" value="1" {if $option->hasShop($shop.id_shop)}checked{/if}>
						<label for="shop_{$shop.id_shop}_on">{l s='Active' d='Shop.Theme.Labels'}</label>
						<input type="radio" name="shops[{$shop.id_shop}]" id="shop_{$shop.id_shop}_off" value="0" {if !$option->hasShop($shop.id_shop)}checked{/if}>
						<label for="shop_{$shop.id_shop}_off">{l s='Inactive' d='Shop.Theme.Labels'}</label>
						<a class="slide-button btn"></a>
					</span>
					
				{/foreach}
			</div>
			<div class="panel">
				<div class="panel-heading">
					{l s="Actions"}
				</div>
				<div class="alert alert-info">
					Purger l'option d'achat va la retirer de tous les paniers client en cours.
				</div>
				<div class="text-center">
					<button type="submit" class="btn btn-danger purge" name="purge">
						<b><i class="icon-warning"></i> &nbsp; {l s='Purger les paniers'} &nbsp; <i class="icon-warning"></i></b>
					</button>
				</div>
			</div>
		</div>
		<div class="col-lg-6">
			<div class="panel">
				<div class="panel-heading">
					{l s="Coût"}
				</div>
				<div class="form-group">
					<label form="type">{l s="Type"} <em class="text-danger">*</em></label>
					<select class="form-control" name="option[type]" required>
						<option value="">{l s="Choisir un type de calcul"}</option>
						{foreach from=OrderOption::getTypes() key=id item=type}
							<option value="{$id}" {if $option->type == $id}selected{/if}>{$type}</option>
						{/foreach}
					</select>
				</div>
				<div class="form-group">
					<label form="value">{l s="Valeur"} <em class="text-danger">*</em></label>
					<input type="number" step="any" class="form-control" name="option[value]" value="{$option->value}" required>
				</div>
			</div>
			<div class="panel">
				<div class="panel-heading">
					{l s="Produits"}
				</div>
				{if $option->id}
					<div class="row">
						<div class="col-lg-8">
							<select class="form-control" name="product">
								<option value="">{l s="Choisir un produit"}</option>
								{foreach from=$products item=product}
									<option value="{$product.id_product}">
										{$product.name}
									</option>
								{/foreach}
							</select>
						</div>
						<div class="col-lg-4">
							<div class="btn-group">
								<button type="submit" class="btn btn-default" name="add_white_list">
									{l s="Liste blanche"}
								</button>
								<button type="submit" class="btn btn-default" name="add_black_list">
									{l s="Liste noire"}
								</button>
							</div>
						</div>
					</div>
					<hr />
					<div class="alert alert-info">
						{l s="Tous les produits de la liste blanche doivent se trouver dans le panier pour que l'option soit disponible."}
					</div>
					<table class="table">
						<thead>
							<tr class="bg-primary">
								<td colspan="3">
									<b>{l s="Liste Blanche"}</b>
								</td>
							</tr>
						</thead>
						<tbody>
							{foreach from=$option->getWhiteList(true) item=row}
								<tr>
									<td><b>{$row['id_product']}</b></td>
									<td>{$row['name']}</td>
									<td class="text-right">
										<button type="submit" class="btn btn-xs btn-danger" name="remove_white_list" value="{$row['id_product']}">
											<i class="icon-trash"></i>
										</button>
									</td>
								</tr>
							{foreachelse}
								<tr>
									<td>{l s="Aucun produit enregistré"}</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
					<br />
					<div class="alert alert-info">
						{l s="L'option sera indisponible si au moins un des produits de la liste noire se trouve dans le panier."}
					</div>
					<table class="table">
						<thead>
							<tr class="bg-primary">
								<td colspan="3">
									<b>{l s="Liste Noire"}</b>
								</td>
							</tr>
						</thead>
						<tbody>
							{foreach from=$option->getBlackList(true) item=row}
								<tr>
									<td><b>{$row['id_product']}</b></td>
									<td>{$row['name']}</td>
									<td class="text-right">
										<button type="submit" class="btn btn-xs btn-danger" name="remove_black_list" value="{$row['id_product']}">
											<i class="icon-trash"></i>
										</button>
									</td>
								</tr>
							{foreachelse}
								<tr>
									<td>{l s="Aucun produit enregistré"}</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				{else}
					<div class="alert alert-warning">
						{l s="Enregistrer l'option avant de gérer les listes de produits."}
					</div>
				{/if}
			</div>
		</div>
	</div>

</form>

<script>
	$(document).ready(function() {

		$('.purge').on('click', function(e) {
			if(!confirm("Etes-vous sûr de vouloir purger tous les paniers clients ?"))
				e.preventDefault();
		});

	});
</script>