<div id="comments-content" class="mb-3">
	<h2>Commentaires</h2>
	<div class="form-group">
		<label for="comment_1">Commentaire 1</label>
		<input type="text" id="comment_1" class="form-control" name="comment_1" value="{$product->comment_1}">
	</div>
	<div class="form-group">
		<label for="comment_2">Commentaire 2</label>
		<input type="text" id="comment_2" class="form-control" name="comment_2" value="{$product->comment_2}">
	</div>
</div>

<div id="accessories-content" class="mb-3">
	<h2>Accessoires</h2>
	<div class="form-group">
		<label for="add_accessory_reference">Référence à ajouter en accessoire</label>
		<input type="text" id="add_accessory_reference" class="form-control">
	</div>
</div>
<div id="accessory_result"></div>

<div id="icons-content" class="mb-3">
	<h2>Iconographie</h2>
</div>
<div id="icons_result"></div>

<script>
	$(document).ready(function() {
		
		$.post({
			url: "{$link->getAdminLink('AdminModules')}&configure=webequip_configuration",
			data: { ajax:true, action:'load_icons', id_product:$('#form_id_product').val() },
			success : function(response) {
				$('#icons_result').html(response);
			}
		});

		$('#add_accessory_reference').on('keyup', function(e) {
			if(e.keyCode == 13) {
				$.post({
					url: "{$link->getAdminLink('AdminModules')}&configure=webequip_configuration",
					data: { ajax:true, action:'find_reference', reference:$(this).val() },
					success : function(response) {
						$('#accessory_result').html(response);
					}
				});
			}
		});

		window.loadAccessories = function() {
			$.post({
				url: "{$link->getAdminLink('AdminModules')}&configure=webequip_configuration",
				data: { ajax:true, action:'load_accessories', id_product:$('#form_id_product').val() },
				success : function(response) {
					$('#accessory_result').html(response);
				}
			});
		}

		window.loadAccessories();
	});
</script>