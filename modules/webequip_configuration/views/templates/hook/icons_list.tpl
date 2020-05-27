<div class="row">
	{foreach from=$icons item=icon}
		{if $icon->hasFile()}
			<div class="col-lg-3">
				<div class="alert alert-secondary text-center" style="padding-left:0px">
					<img src="{$icon->getImgPath()}" title="{$icon->name}">
					<div class="mt-3">
						{if $icon->display($id_product)}
							<button class="btn btn-outline-secondary" title="L'icone est affichée sur la page produit" disabled>
								<i class="material-icons text-success">toggle_on</i>
							</button>
							<button type="button" class="btn btn-outline-secondary change_icon" data-action="disable_icon" value="{$icon->id}">Bloquer</button>
						{else}
							<button class="btn btn-outline-secondary" title="L'icone n'est pas affichée sur la page produit" disabled>
								<i class="material-icons text-danger">toggle_off</i>
							</button>
							<button type="button" class="btn btn-outline-secondary change_icon" data-action="enable_icon" value="{$icon->id}">Autoriser</button>
						{/if}
					</div>
				</div>
			</div>
		{/if}
	{/foreach}
</div>

<script>
	$(document).ready(function() {

		$('.change_icon').on('click', function() {

			$.post({
				url: "{$link->getAdminLink('AdminModules')}&configure=webequip_configuration",
				data: { ajax:true, action:$(this).data('action'), id_icon:$(this).val(), id_product:{$id_product} },
				success : function(response) {
					$('#icons_result').html(response);
				}
			});

		});

	});
</script>