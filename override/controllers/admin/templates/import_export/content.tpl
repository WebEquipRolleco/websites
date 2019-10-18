<div class="panel">
	{assign var=shop value=Context::getContext()->shop}
	<strong>Boutique concernée :</strong> <span style="color:{$shop->color}">{$shop->name}</span>
</div>

<form method="post">
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-download"></i> <b>Export produits</b>
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
			<div class="col-lg-2">
				<div class="form-group">
					<label>Séparateur de colonnes</label>
					<input type="text" class="form-control" name="separator" value="{$separator}" required>
				</div>
				<div class="form-group">
					<label>Délimiter de valeurs</label>
					<input type="text" class="form-control" name="delimiter" value="{$delimiter}" required>
				</div>
			</div>
		</div>
		<div class="panel-footer text-right">
			<button type="submit" class="btn btn-success" name="export">
				<i class="process-icon-save"></i> <b>Exporter</b>
			</button>
		</div>
	</div>
</form>

<form method="post" enctype="multipart/form-data">
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-upload"></i> <b>Import produits</b>
		</div>
		<div class="row">
			<div class="col-lg-4">
				<div class="form-group">
					<label>Fichier CSV</label>
					<input type="file" class="form-control" name="import_file" required>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-2">
				<div class="form-group">
					<label>Nombre de lignes à ignorer</label>
					<input type="number" class="form-control" min="0" step="1" name="skip" value="1">
				</div>
				<div class="form-group">
					<label>Séparateur de colonnes</label>
					<input type="text" class="form-control" name="separator" value="{$separator}" required>
				</div>
				<div class="form-group">
					<label>Délimiter de valeurs</label>
					<input type="text" class="form-control" name="delimiter" value="{$delimiter}" required>
				</div>
			</div>
		</div>
		<div class="panel-footer text-right">
			<button type="submit" class="btn btn-success" name="import">
				<i class="process-icon-save"></i> <b>Importer</b>
			</button>
		</div>
	</div>
</form>

<script>
	$(document).on('ready', function() {

		$('.select2').select2({ placeholder:'Cliquez ici pour choisir dans la liste' });

	});
</script>