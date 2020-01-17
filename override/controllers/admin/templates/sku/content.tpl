<form method="post">
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i> Configuration
		</div>
		<div class="row">
			<div class="col-lg-4">
				<div class="form-group">
					<label for="SKU_PRODUCT_PREFIX">Préfix des SKU produits</label>
					<input type="text" id="SKU_PRODUCT_PREFIX" name="SKU_PRODUCT_PREFIX" value="{$SKU_PRODUCT_PREFIX}">
				</div>
			</div>
			<div class="col-lg-4">
				<div class="form-group">
					<label for="SKU_COMBINATION_PREFIX">Préfix des SKU déclinaisons</label>
					<input type="text" id="SKU_COMBINATION_PREFIX" name="SKU_COMBINATION_PREFIX" value="{$SKU_COMBINATION_PREFIX}">
				</div>
			</div>
			<div class="col-lg-4">
				<div class="form-group">
					<label for="SKU_SEPARATOR">Séparateur de groupes</label>
					<input type="text" id="SKU_SEPARATOR" name="SKU_SEPARATOR" value="{$SKU_SEPARATOR}">
				</div>
			</div>
		</div>
		<div class="panel-footer text-right">
			<button type="submit" class="btn btn-success">
				<b><i class="process-icon-save"></i> Enregistrer</b>
		</div>
	</div>
</form>

<div class="panel">
	<div class="panel-heading">
		<i class="icon-cogs"></i> Testeur de SKU
	</div>
	<form id="run_test">
		<div class="row">
			<div class="col-lg-4">
				<input type="text" id="sku" placeholder="Sku à tester">
			</div>
			<div class="col-lg-2">
				<button type="button" class="btn btn-success">
					<b>Tester</b>
				</button>
			</div>
		</div>
	</form>
	<div id="test_result"></div>
</div>

<script>
	$(document).ready(function() {

		$('#run_test').on('submit', function(e) {
			e.preventDefault();

			alert('not on my watch');
		});

	});
</script>