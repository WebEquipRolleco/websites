<div class="panel">
	{assign var=shop value=Context::getContext()->shop}
	<strong>Boutique concernée :</strong> <span style="color:{$shop->color}">{$shop->name}</span>
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