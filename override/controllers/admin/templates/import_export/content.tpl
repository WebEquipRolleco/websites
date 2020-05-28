<div class="panel">
	{assign var=shop value=Context::getContext()->shop}
	<strong>Gestion des données produits, boutique concernée :</strong> <span style="color:{$shop->color}">{$shop->name}</span>
	<br /><br />
	<div class="alert alert-info">
		Les colonnes marquées d'un <b>*</b> sont à des titre d'indications et ne changeront aucune valeur lors des imports
	</div>
</div>

<div class="row">

	<div class="col-lg-6">
		<form method="post">
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-download"></i> <b>Export</b>
				</div>
				<div class="row">
					<div class="col-lg-6">
						<div class="form-group">
							<label>Catégories</label>
							<select class="form-control select2" name="categories[]" multiple="multiple">
								{foreach from=$categories item=category}
									<option value="{$category.id_category}">{$category.name}</option>
								{/foreach}
							</select>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="form-group">
							<label>Fournisseurs</label>
							<select class="form-control select2" name="suppliers[]" multiple="multiple">
								{foreach from=$suppliers item=supplier}
									<option value="{$supplier.id_supplier}">{if $supplier.reference}{$supplier.reference} - {/if}{$supplier.name}</option>
								{/foreach}
							</select>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
							<label>Séparateur de colonnes</label>
							<input type="text" class="form-control" name="separator" value="{$separator}" required>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
							<label>Délimiter de valeurs</label>
							<input type="text" class="form-control" name="delimiter" value="{$delimiter}" required>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
							<label>Statut des produits concernés</label>
							<select class="form-control" name="status_type">
								<option value="0">Tous</option>
								<option value="1">Produits activés uniquement</option>
								<option value="2">Produits désactivés uniquement</option>
							</select>
						</div>
					</div>
				</div>
				<div class="panel-footer text-right">
					<button type="submit" class="btn btn-success" name="action" value="export_products" style="width:69px">
						<i class="process-icon-download"></i> <b>Produits</b>
					</button>
					<button type="submit" class="btn btn-info" name="action" value="export_prices" style="width:69px">
						<i class="process-icon-download"></i> <b>Prix</b>
					</button>
					<button type="submit" class="btn btn-danger" name="action" value="export_comments">
						<i class="process-icon-download"></i> <b>Commentaires</b>
					</button>
					<button type="submit" class="btn btn-warning" name="action" value="export_url" style="width:69px">
						<i class="process-icon-download"></i> <b>URL</b>
					</button>
				</div>
			</div>
		</form>
	</div>

	<div class="col-lg-6">
		<form method="post" enctype="multipart/form-data">
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-upload"></i> <b>Import</b>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<div class="form-group">
							<label>Fichier CSV</label>
							<input type="file" class="form-control" name="import_file" required>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-4">
						<div class="form-group">
							<label>Nombre de lignes à ignorer</label>
							<input type="number" class="form-control" min="0" step="1" name="skip" value="1">
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
							<label>Séparateur de colonnes</label>
							<input type="text" class="form-control" name="separator" value="{$separator}" required>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
							<label>Délimiter de valeurs</label>
							<input type="text" class="form-control" name="delimiter" value="{$delimiter}" required>
						</div>
					</div>
				</div>
				<div class="panel-footer text-right">
					<button type="submit" class="btn btn-success" name="action" value="import_products" style="width:69px">
						<i class="process-icon-upload"></i> <b>Produits</b>
					</button>
					<button type="submit" class="btn btn-info" name="action" value="import_prices" style="width:69px">
						<i class="process-icon-upload"></i> <b>Prix</b>
					</button>
					<button type="submit" class="btn btn-danger" name="action" value="import_comments">
						<i class="process-icon-upload"></i> <b>Commentaires</b>
					</button>
				</div>
			</div>
		</form>
	</div>

</div>

<div class="row">
	<div class="col-lg-6">
		<form method="post">
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-download"></i> <b>Export commandes</b>
				</div>
				<div class="row">
					<div class="col-lg-4">
						<div class="form-group">
							<label for="date_begin">Date de début</label>
							<input type="date" id="date_begin" class="form-control" name="date_begin" required>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
							<label for="date_end">Date de fin</label>
							<input type="date" id="date_end" class="form-control" name="date_end" required>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
							<label>Séparateur de colonnes</label>
							<input type="text" class="form-control" name="separator" value="{$separator}" required>
						</div>
					</div>
				</div>
				<div class="row">
					{foreach from=Shop::getShops() item=shop}
						<div class="col-lg-4 text-center">
							<label for="shop_{$shop.id_shop}" style="color:{$shop.color}">{$shop.name}</label>
							<span class="switch prestashop-switch fixed-width-lg" style="margin-left:auto; margin-right:auto;">
								<input type="radio" name="shops[{$shop.id_shop}]" id="shop_{$shop.id_shop}_on" value="1" checked>
								<label for="shop_{$shop.id_shop}_on">Oui</label>
								<input type="radio" name="shops[{$shop.id_shop}]" id="shop_{$shop.id_shop}_off" value="0">
								<label for="shop_{$shop.id_shop}_off">Non</label>
								<a class="slide-button btn"></a>
							</span>
						</div>
					{/foreach}
				</div>
				<div class="panel-footer text-right">
					<button type="submit" class="btn btn-success" name="action" value="export_orders" style="width:69px">
						<i class="process-icon-download"></i> <b>Exporter</b>
					</button>
				</div>
			</div>
		</form>
	</div>
	<div class="col-lg-3">
		<form method="post">
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-lock text-danger"></i> <b class="text-danger">{l s="Sécurité"}</b>
				</div>
				<div class="panel-content">
					<div>
						<button type="submit" class="btn btn-block btn-danger" name="action" value="export_products-without-prices">
							<i class="icon-bug"></i> &nbsp; <b>Exporter : Produits actifs sans prix</b>
						</button>
					</div>
					<div style="margin-top:15px">
						<button type="submit" class="btn btn-block btn-danger" name="action" value="export_combinations-without-prices">
							<i class="icon-bug"></i> &nbsp; <b>Exporter : Déclinaisons actives sans prix</b>
						</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="panel">
			<b>Gestion des données SKU : <span class="text-warning">Multiboutique</span></b>
			<br /><br />
			<div class="alert alert-info">Les SKU des groupes doivent être uniques et strictement composés de 3 caractères</div>
		</div>
	</div>
</div>

<div class="row">

	<div class="col-lg-6">
		<form method="post">
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-download"></i> <b>Export des catégories</b>
				</div>
				<div class="row">
					<div class="col-lg-6">
						<div class="form-group">
							<label>Séparateur de colonnes</label>
							<input type="text" class="form-control" name="separator" value="{$separator}" required>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="form-group">
							<label>Délimiter de valeurs</label>
							<input type="text" class="form-control" name="delimiter" value="{$delimiter}" required>
						</div>
					</div>
				</div>
				<div class="panel-footer text-right">
					<button type="submit" class="btn btn-success" name="action" value="export_categories">
						<i class="process-icon-download"></i> <b>Exporter</b>
					</button>
				</div>
			</div>
		</form>
	</div>

	<div class="col-lg-6">
		<form method="post" enctype="multipart/form-data">
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-upload"></i> <b>Import des catégories</b>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<div class="form-group">
							<label>Fichier CSV</label>
							<input type="file" class="form-control" name="import_file" required>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-4">
						<div class="form-group">
							<label>Nombre de lignes à ignorer</label>
							<input type="number" class="form-control" min="0" step="1" name="skip" value="1">
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
							<label>Séparateur de colonnes</label>
							<input type="text" class="form-control" name="separator" value="{$separator}" required>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
							<label>Délimiter de valeurs</label>
							<input type="text" class="form-control" name="delimiter" value="{$delimiter}" required>
						</div>
					</div>
				</div>
				<div class="panel-footer text-right">
					<button type="submit" class="btn btn-success" name="action" value="import_categories">
						<i class="process-icon-upload"></i> <b>Importer</b>
					</button>
				</div>
			</div>
		</form>
	</div>

</div>

<div class="row">
	
	<div class="col-lg-6">
		<form method="post">
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-download"></i> <b>Export des groupes</b>
				</div>
				<div class="row">
					<div class="col-lg-6">
						<div class="form-group">
							<label>Séparateur de colonnes</label>
							<input type="text" class="form-control" name="separator" value="{$separator}" required>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="form-group">
							<label>Délimiter de valeurs</label>
							<input type="text" class="form-control" name="delimiter" value="{$delimiter}" required>
						</div>
					</div>
				</div>
				<div class="panel-footer text-right">
					<button type="submit" class="btn btn-success" name="action" value="export_attribute-groups">
						<i class="process-icon-download"></i> <b>Attributs (déclinaisons)</b>
					</button>
					<button type="submit" class="btn btn-info" name="action" value="export_feature-groups">
						<i class="process-icon-download"></i> <b>Caractéristiques (produits)</b>
					</button>
				</div>
			</div>
		</form>
	</div>

	<div class="col-lg-6">
		<form method="post" enctype="multipart/form-data">
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-upload"></i> <b>Import des groupes</b>
				</div>
				<div class="row">
					<div class="col-lg-6">
						<div class="form-group">
							<label>Fichier CSV</label>
							<input type="file" class="form-control" name="import_file" required>
						</div>
					</div>
					<div class="col-lg-3">
						<div class="form-group">
							<label>Nombre de lignes à ignorer</label>
							<input type="number" class="form-control" min="0" step="1" name="skip" value="1">
						</div>
					</div>
					<div class="col-lg-3">
						<div class="form-group">
							<label>Séparateur de colonnes</label>
							<input type="text" class="form-control" name="separator" value="{$separator}" required>
						</div>
					</div>
				</div>
				<div class="panel-footer text-right">
					<button type="submit" class="btn btn-success" name="action" value="import_attribute-groups">
						<i class="process-icon-upload"></i> <b>Attributs (déclinaisons)</b>
					</button>
					<button type="submit" class="btn btn-info" name="action" value="import_feature-groups">
						<i class="process-icon-upload"></i> <b>Caractéristiques (produits)</b>
					</button>
				</div>
			</div>
		</form>
	</div>

</div>

<div class="row">

	<div class="col-lg-6">
		<form method="post">
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-download"></i> <b>Export des valeurs</b>
				</div>
				<div class="row">
					<div class="col-lg-6">
						<div class="form-group">
							<label>Séparateur de colonnes</label>
							<input type="text" class="form-control" name="separator" value="{$separator}" required>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="form-group">
							<label>Délimiter de valeurs</label>
							<input type="text" class="form-control" name="delimiter" value="{$delimiter}" required>
						</div>
					</div>
				</div>
				<div class="panel-footer text-right">
					<button type="submit" class="btn btn-success" name="action" value="export_attribute-values">
						<i class="process-icon-download"></i> <b>Attributs (déclinaisons)</b>
					</button>
					<button type="submit" class="btn btn-info" name="action" value="export_feature-values">
						<i class="process-icon-download"></i> <b>Caractéristiques (produits)</b>
					</button>
				</div>
			</div>
		</form>
	</div>

	<div class="col-lg-6">
		<form method="post" enctype="multipart/form-data">
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-upload"></i> <b>Import des groupes</b>
				</div>
				<div class="row">
					<div class="col-lg-6">
						<div class="form-group">
							<label>Fichier CSV</label>
							<input type="file" class="form-control" name="import_file" required>
						</div>
					</div>
					<div class="col-lg-3">
						<div class="form-group">
							<label>Nombre de lignes à ignorer</label>
							<input type="number" class="form-control" min="0" step="1" name="skip" value="1">
						</div>
					</div>
					<div class="col-lg-3">
						<div class="form-group">
							<label>Séparateur de colonnes</label>
							<input type="text" class="form-control" name="separator" value="{$separator}" required>
						</div>
					</div>
				</div>
				<div class="panel-footer text-right">
					<button type="submit" class="btn btn-success" name="action" value="import_attribute-values">
						<i class="process-icon-upload"></i> <b>Attributs (déclinaisons)</b>
					</button>
					<button type="submit" class="btn btn-info" name="action" value="import_feature-values">
						<i class="process-icon-upload"></i> <b>Caractéristiques (produits)</b>
					</button>
				</div>
			</div>
		</form>
	</div>
	
</div>

<script>
	$(document).on('ready', function() {

		$('.select2').select2({ placeholder:'Cliquez ici pour choisir dans la liste' });

	});
</script>