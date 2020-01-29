<div class="panel">
	<div class="panel-heading">
		Recherche produits
	</div>
	<form method="post" id="search_product">
		<input type="hidden" name="ajax" value="1">
		<div class="row">
			<div class="col-lg-3">
				<input type="text" name="reference" placeholder="Référence a rechercher" autocomplete="off" required>
			</div>
			<div class="col-lg-2">
				<button type="submit" class="btn btn-success">
					<b>Rechercher</b>
				</button>
			</div>
		</div>
	</form>
	<div class="alert alert-info" style="margin-top:10px">
		<b>Propriétés recherchées :</b> Référence produit, référence fournisseur
	</div>
</div>

<div id="test_result"></div>

<script>
	$(document).ready(function() {

		$('#search_product').on('submit', function(e) {
			e.preventDefault();

			$.ajax({
				url: "{$link->getAdminLink('AdminProductSearch')}",
				data: $(this).serialize(),
				success : function(response) {
					$('#test_result').html(response);
				}
			});
		});

	});
</script>