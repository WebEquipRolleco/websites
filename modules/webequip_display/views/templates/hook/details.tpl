<form method="post" enctype="multipart/form-data">
	<input type="hidden" name="display[id]" value="{$display->id}">
	<div id="details" class="modal" tabindex="-1" role="dialog">
	  	<div class="modal-dialog modal-lg" role="document">
	    	<div class="modal-content">
	      		<div class="modal-body">
	        		<div class="alert bg-primary">
		        		<b>{l s="Détails" d='Shop.Theme.Labels'}</b>
	        		</div>
	        		<div class="row">
						<div class="col-lg-12">
							<div class="panel">
						   		<span class="switch prestashop-switch fixed-width-lg" style="margin-left:auto; margin-right:auto;">
									<input type="radio" name="display[active]" id="active_on" value="1" {if $display->active}checked{/if}>
									<label for="active_on">{l s='Active' d='Shop.Theme.Labels'}</label>
									<input type="radio" name="display[active]" id="active_off" value="0" {if !$display->active}checked{/if}>
									<label for="active_off">{l s='Inactive' d='Shop.Theme.Labels'}</label>
									<a class="slide-button btn"></a>
								</span>
							</div>
					   	</div>
					</div>
					<div class="row">
						<div class="col-lg-6">
							<div class="panel">
								<div class="panel-heading">
									<b>{l s="Affichage" d='Shop.Theme.Labels'}</b>
								</div>
					   			<div class="form-group">
					   				<label>{l s='Titre' d='Shop.Theme.Labels'}</label>
					   				<input type="text" class="form-control" name="display[name]" value="{$display->name}" placeholder="{l s='Lien' d='Shop.Theme.Labels'}">
					   			</div>
					   			<div class="form-group">
					   				<label>{l s='Titre' d='Shop.Theme.Labels'}</label>
					   				<input type="text" class="form-control" name="display[link]" value="{$display->link}" placeholder="{l s='Lien' d='Shop.Theme.Labels'}">
					   			</div>
					   			<div class="form-group">
					   				<label>{l s='Position' d='Shop.Theme.Labels'}</label>
					   				<input type="number" min="1" class="form-control" name="display[position]" value="{$display->position}" placeholder="{l s='Position' d='Shop.Theme.Labels'}">
					   			</div>
				    		</div>
				    		<div class="panel">
								<div class="panel-heading">
									<b>{l s="Multi-boutique" mod="webequip_modal"}</b>
								</div>
						        {foreach from=$shops item=shop}
						        	<div class="form-group text-center">
										<label>{$shop.name}</label>
										<span class="switch prestashop-switch fixed-width-lg" style="margin-left:auto; margin-right: auto;">
											<input type="radio" name="display[shops][{$shop.id_shop}]" id="shop_{$shop.id_shop}_on" value="1" {if $display->hasShop($shop.id_shop)}checked{/if}>
											<label for="shop_{$shop.id_shop}_on">{l s='Oui' d='Shop.Theme.Labels'}</label>
											<input type="radio" name="display[shops][{$shop.id_shop}]" id="shop_{$shop.id_shop}_off" value="0" {if !$display->hasShop($shop.id_shop)}checked{/if}>
											<label for="shop_{$shop.id_shop}_off">{l s='Non' d='Shop.Theme.Labels'}</label>
											<a class="slide-button btn"></a>
										</span>
									</div>
						        {/foreach}
						    </div>
						</div>
						<div class="col-lg-6">
							<div class="panel">
								<div class="panel-heading">
									<b>{l s="Fichier" d='Shop.Theme.Labels'}</b>
								</div>
								<input type="file" class="form-control" name="display[file]" placeholder="Fichier" {if !$display->id}required{/if}>
								{if $display->id}
									<div class="text-center">
										<img src="{$display->getUrl()}" title="{$display->picture}">
									</div>
								{/if}
							</div>
						</div>
					</div>
	    		</div>
		    <div class="modal-footer" style="background-color:whitesmoke">
		        <button type="submit" class="btn btn-xs btn-success">
		        	<i class="process-icon-ok"></i>
		        </button>
		        <button type="button" class="btn btn-xs btn-danger" data-dismiss="modal">
		        	<i class="process-icon-close"></i>
		        </button>
		    </div>
		</div>
	</div>
</form>