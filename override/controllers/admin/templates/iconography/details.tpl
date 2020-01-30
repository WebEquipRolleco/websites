<form method="post" enctype="multipart/form-data">
	<input type="hidden" name="id" value="{$icon->id}">

	<div class="panel text-right">
		<a href="{$link->getAdminLink('AdminIconography')}" class="btn btn-default">
			<b>{l s='Retour' d='Shop.Theme.Labels'}</b>
		</a>
		<button type="submit" class="btn btn-success" name="save">
			<b>{l s='Enregistrer' d='Shop.Theme.Labels'}</b>
		</button>
	</div>

	<div class="row">

		<div class="col-lg-3">
			<div class="panel">
				<div class="panel-heading">
					Informations
				</div>
				<div class="form-group">
					<label for="name">{l s="Nom"} <em class="text-danger">*</em></label>
					<input type="text" id="name" class="form-control" name="form[name]" value="{$icon->name}" autocomplete="off" required>
				</div>
				<div class="form-group">
					<label for="location">{l s="Emplacement"} <em class="text-danger">*</em></label>
					<select id="location" class="form-control" name="form[location]" required>
						{foreach from=ProductIcon::getLocations() key=value item=name}
							<option value="{$value}" {if $value == $icon->location}selected{/if}>{$name}</option>
						{/foreach}
					</select>
				</div>
				<div class="form-group">
					<label for="position">{l s="Position"} <em class="text-danger">*</em></label>
					<input type="number" step="1" min="1" id="position" class="form-control" name="form[position]" value="{$icon->position}" required>
				</div>
				<span class="switch prestashop-switch fixed-width-lg" style="margin-left:auto; margin-right:auto;">
					<input type="radio" name="form[active]" id="active_on" value="1" {if $icon->active}checked{/if}>
					<label for="active_on">{l s='Active' d='Shop.Theme.Labels'}</label>
					<input type="radio" name="form[active]" id="active_off" value="0" {if !$icon->active}checked{/if}>
					<label for="active_off">{l s='Inactive' d='Shop.Theme.Labels'}</label>
					<a class="slide-button btn"></a>
				</span>
			</div>
			<div class="panel">
				<div class="panel-heading">
					{l s='Lien'}
				</div>
				<div class="form-group">
					<label for="title">{l s="Titre"} <em class="text-danger">*</em></label>
					<input type="text" id="title" class="form-control" name="form[title]" value="{$icon->title}" autocomplete="off" required>
				</div>
				<div class="form-group">
					<label for="url">{l s="Url"}</label>
					<input type="text" id="url" class="form-control" name="form[url]" value="{$icon->url}" autocomplete="off">
				</div>
			</div>
		</div>

		<div class="col-lg-4">
			<div class="panel">
				<div class="panel-heading">
					{l s='Image'}
				</div>
				<div class="form-group">
					<label for="picture">{l s="Nouvelle image"}</label>
					<input type="file" id="url" class="form-control" name="picture">
				</div>
				{if $icon->hasFile()}
					<hr />
					<div class="row">
						<div class="col-lg-3">
							<div class="form-group">
								<label for="height">{l s="Forcer la hauteur"}</label>
								<input type="number" step="1" min="0" id="height" class="form-control" name="form[height]" {if $icon->height}value="{$icon->height}"{/if} placeholder="Taille originale">
							</div>
							<div class="form-group">
								<label for="width">{l s="Forcer la largeur"}</label>
								<input type="number" step="1" min="0" id="width" class="form-control" name="form[width]" {if $icon->width}value="{$icon->width}"{/if} placeholder="Taille originale">
							</div>
						</div>
						<div class="col-lg-9 text-center">
							<img src="{$icon->getImgPath()}" {if $icon->height}height="{$icon->height}px"{/if} {if $icon->width}width="{$icon->width}px"{/if}>
						</div>
					</div>
				{/if}
			</div>
			<div class="panel">
				<div class="panel-heading">
					{l s="Multi-boutique"}
				</div>
				<table class="table">
					<tbody>
						{foreach from=Shop::getShops() item=shop}
							<tr>
								<td>{$shop.name}</td>
								<td>
									<span class="switch prestashop-switch fixed-width-lg" style="margin-left:auto;">
										<input type="radio" name="shops[{$shop.id_shop}]" id="shop_{$shop.id_shop}_on" value="1" {if $icon->hasShop($shop.id_shop)}checked{/if}>
										<label for="shop_{$shop.id_shop}_on">{l s='Oui' d='Shop.Theme.Labels'}</label>
										<input type="radio" name="shops[{$shop.id_shop}]" id="shop_{$shop.id_shop}_off" value="0" {if !$icon->hasShop($shop.id_shop, false)}checked{/if}>
										<label for="shop_{$shop.id_shop}_off">{l s='Non' d='Shop.Theme.Labels'}</label>
										<a class="slide-button btn"></a>
									</span>
								</td>
							</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		</div>

		<div class="col-lg-5">

			{assign var=white_list value=$icon->getWhiteList(true)}
			{assign var=black_list value=$icon->getBlackList(true)}

			<div class="panel">
				<div class="panel-heading">
					Condition d'affichage : Produits
				</div>
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
							{if empty($black_list)}
								<button type="submit" class="btn btn-default" name="add_white_list" {if !$icon->id}disabled{/if}>
									{l s="Liste blanche"}
								</button>
							{/if}
							{if empty($white_list)}
								<button type="submit" class="btn btn-default" name="add_black_list" {if !$icon->id}disabled{/if}>
									{l s="Liste noire"}
								</button>
							{/if}
						</div>
					</div>
				</div>
				<hr />
				
				{if empty($white_list) and empty($black_list)}
					<div class="alert alert-info">Tous les produits sont concernés par cette icône</div>
				{/if}

				{if !empty($white_list)}
					<table class="table">
						<thead>
							<tr class="bg-success">
								<td colspan="3">
									<b>{l s="Liste Blanche"}</b>
								</td>
							</tr>
						</thead>
						<tbody>
							{foreach from=$white_list item=row}
								<tr>
									<td><b>{$row['id_product']}</b></td>
									<td>{$row['name']}</td>
									<td class="text-right">
										<button type="submit" class="btn btn-xs btn-danger" name="remove_white_list" value="{$row['id_product']}">
											<i class="icon-trash"></i>
										</button>
									</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				{/if}

				{if !empty($black_list)}
					<table class="table">
						<thead>
							<tr class="bg-danger">
								<td colspan="3">
									<b>{l s="Liste Noire"}</b>
								</td>
							</tr>
						</thead>
						<tbody>
							{foreach from=$black_list item=row}
								<tr>
									<td><b>{$row['id_product']}</b></td>
									<td>{$row['name']}</td>
									<td class="text-right">
										<button type="submit" class="btn btn-xs btn-danger" name="remove_black_list" value="{$row['id_product']}">
											<i class="icon-trash"></i>
										</button>
									</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				{/if}

			</div>

			{assign var=white_list value=$icon->getCategoryWhiteList(true)}
			{assign var=black_list value=$icon->getCategoryBlackList(true)}

			<div class="panel">
				<div class="panel-heading">
					Condition d'affichage : Catégories
				</div>
				<div class="row">
					<div class="col-lg-8">
						<select class="form-control" name="category">
							<option value="">{l s="Choisir une catégorie"}</option>
							{foreach from=$categories item=category}
								<option value="{$category.id_category}">
									{$category.name}
								</option>
							{/foreach}
						</select>
					</div>
					<div class="col-lg-4">
						<div class="btn-group">
							{if empty($black_list)}
								<button type="submit" class="btn btn-default" name="add_white_list" {if !$icon->id}disabled{/if}>
									{l s="Liste blanche"}
								</button>
							{/if}
							{if empty($white_list)}
								<button type="submit" class="btn btn-default" name="add_black_list" {if !$icon->id}disabled{/if}>
									{l s="Liste noire"}
								</button>
							{/if}
						</div>
					</div>
				</div>
				<hr />

				{if empty($white_list) and empty($black_list)}
					<div class="alert alert-info">Toutes les catégories sont concernées par cette icône</div>
				{/if}

				{if !empty($white_list)}
					<table class="table">
						<thead>
							<tr class="bg-success">
								<td colspan="3">
									<b>{l s="Liste Blanche"}</b>
								</td>
							</tr>
						</thead>
						<tbody>
							{foreach from=$white_list item=row}
								<tr>
									<td><b>{$row['id_category']}</b></td>
									<td>{$row['name']}</td>
									<td class="text-right">
										<button type="submit" class="btn btn-xs btn-danger" name="remove_category_white_list" value="{$row['id_category']}">
											<i class="icon-trash"></i>
										</button>
									</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				{/if}

				{if !empty($black_list)}
					<table class="table">
						<thead>
							<tr class="bg-danger">
								<td colspan="3">
									<b>{l s="Liste Noire"}</b>
								</td>
							</tr>
						</thead>
						<tbody>
							{foreach from=$black_list item=row}
								<tr>
									<td><b>{$row['id_category']}</b></td>
									<td>{$row['name']}</td>
									<td class="text-right">
										<button type="submit" class="btn btn-xs btn-danger" name="remove_category_black_list" value="{$row['id_category']}">
											<i class="icon-trash"></i>
										</button>
									</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				{/if}

			</div>

			{assign var=white_list value=$icon->getSupplierWhiteList(true)}
			{assign var=black_list value=$icon->getSupplierBlackList(true)}

			<div class="panel">
				<div class="panel-heading">
					Condition d'affichage : Fournisseurs
				</div>
				<div class="row">
					<div class="col-lg-8">
						<select class="form-control" name="supplier">
							<option value="">{l s="Choisir un fournisseur"}</option>
							{foreach from=$suppliers item=supplier}
								<option value="{$supplier.id_supplier}">
									{$supplier.name}
								</option>
							{/foreach}
						</select>
					</div>
					<div class="col-lg-4">
						<div class="btn-group">
							{if empty($black_list)}
								<button type="submit" class="btn btn-default" name="add_white_list" {if !$icon->id}disabled{/if}>
									{l s="Liste blanche"}
								</button>
							{/if}
							{if empty($white_list)}
								<button type="submit" class="btn btn-default" name="add_black_list" {if !$icon->id}disabled{/if}>
									{l s="Liste noire"}
								</button>
							{/if}
						</div>
					</div>
				</div>
				<hr />

				{if empty($white_list) and empty($black_list)}
					<div class="alert alert-info">Tous les fournisseurs sont concernées par cette icône</div>
				{/if}

				{if !empty($white_list)}
					<table class="table">
						<thead>
							<tr class="bg-success">
								<td colspan="3">
									<b>{l s="Liste Blanche"}</b>
								</td>
							</tr>
						</thead>
						<tbody>
							{foreach from=$white_list item=row}
								<tr>
									<td><b>{$row['id_supplier']}</b></td>
									<td>{$row['name']}</td>
									<td class="text-right">
										<button type="submit" class="btn btn-xs btn-danger" name="remove_supplier_white_list" value="{$row['id_supplier']}">
											<i class="icon-trash"></i>
										</button>
									</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				{/if}

				{if !empty($black_list)}
					{l s="Tous les fournisseurs qui ne sont pas sur la liste noire sont concernés par cette icône."}
					<table class="table">
						<thead>
							<tr class="bg-danger">
								<td colspan="3">
									<b>{l s="Liste Noire"}</b>
								</td>
							</tr>
						</thead>
						<tbody>
							{foreach from=$black_list item=row}
								<tr>
									<td><b>{$row['id_supplier']}</b></td>
									<td>{$row['name']}</td>
									<td class="text-right">
										<button type="submit" class="btn btn-xs btn-danger" name="remove_supplier_black_list" value="{$row['id_supplier']}">
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

	</div>

</form>