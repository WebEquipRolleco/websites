<h2>
	<i id="destocking_0" class="material-icons text-danger" title="{l s='Le bandeau est caché' mod='webequip_configuration'}" {if $product->destocking}style="display:none"{/if}>toggle_off</i> 
	<i id="destocking_1" class="material-icons text-success" title="{l s='Le bandeau est affiché' mod='webequip_configuration'}" {if !$product->destocking}style="display:none"{/if}>toggle_on</i> 
	{l s="Déstockage" mod="webequip_configuration"}
</h2>
<button id="change_destocking" class="btn btn-outline-secondary"> Afficher/cacher le bandeau</button>

<script>
	$(document).ready(function() {

		$('#change_destocking').on('click', function(e) {
			//e.preventDefault();

			$.post(
				"{$link->getAdminLink('AdminModules')}&configure=webequip_configuration&ajax=1&toggle_destocking={$product->id}",
				function(value) {

					$('#destocking_0').hide();
					$('#destocking_1').hide();

					$('#destocking_'+value).show();
				}
			);

			
		});
	});
</script>