<form method="post">
	<input type="hidden" name="reassurance[id]" value="{$reassurance->id}">
	<div id="modal_details" class="modal" tabindex="-1" role="dialog">
	 	<div class="modal-dialog modal-lg" role="document">
	    	<div class="modal-content">
	      		<div class="modal-body">
	        		<div class="alert bg-primary">
		        		<b>{l s="Détails" mod="webequip_reassurance"}</b>
	        		</div>
 					
 					<div class="panel">
						<div class="panel-heading">
							<b>{l s="Titre" d='Shop.Theme.Labels'}</b>
						</div>
						<input type="text" id="reassurance_name" class="form-control" name="reassurance[name]" value="{$reassurance->name}">
					</div>

					<div class="row">
						<div class="col-lg-6">
							<div class="panel">
								<div class="panel-heading">
									<b>{l s="Contenu" d='Shop.Theme.Labels'}</b>
								</div>
								<div class="form-group">
									<label form="reassurance_icon">
										<b>{l s="Icone" d='Shop.Theme.Labels'}</b>
										&nbsp; <small class="text-muted">HTML</small>
									</label>
									<textarea rows="3" id="reassurance_icon" class="form-control" name="reassurance[icon]">{$reassurance->icon}</textarea>
								</div>
								<div class="form-group">
									<label form="reassurance_text">
										<b>{l s="Texte" d='Shop.Theme.Labels'}</b>
										&nbsp; <small class="text-muted">HTML</small>
									</label>
									<textarea rows="5" id="reassurance_text" class="form-control" name="reassurance[text]">{$reassurance->text}</textarea>
								</div>
								<div class="form-group">
									<label form="reassurance_link">
										<b>{l s="Lien" d='Shop.Theme.Labels'}</b>
									</label>
									<input type="text" id="reassurance_link" class="form-control" name="reassurance[link]" value="{$reassurance->link}">
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="panel">
								<div class="panel-heading">
									<b>{l s="Options" d='Shop.Theme.Labels'}</b>
								</div>
								<div class="form-group">
									<label form="reassurance_location">
										<b>{l s="Position" d='Shop.Theme.Labels'}</b>
									</label>
									<select id="reassurance_location" class="form-control" name="reassurance[location]">
										{foreach from=$locations key=id item=location}
											<option value="{$id}" {if $id == $reassurance->location}selected{/if}>
												{$location}
											</option>
										{/foreach}
									</select>
								</div>
								<div class="form-group">
									<label form="reassurance_position">
										<b>{l s="Ordre d'affichage" d='Shop.Theme.Labels'}</b>
									</label>
									<input type="number" min="1" id="reassurance_position" class="form-control" name="reassurance[position]" value="{$reassurance->position}">
								</div>
								<div class="form-group">
									<label>
										<b>{l s="Active" d='Shop.Theme.Labels'}</b>
											<span class="switch prestashop-switch fixed-width-lg">
											<input type="radio" name="reassurance[active]" id="active_on" value="1" {if $reassurance->active}checked{/if}>
											<label for="active_on">{l s='Oui' d='Shop.Theme.Labels'}</label>
											<input type="radio" name="reassurance[active]" id="active_off" value="0" {if !$reassurance->active}checked{/if}>
											<label for="active_off">{l s='Non' d='Shop.Theme.Labels'}</label>
											<a class="slide-button btn"></a>
										</span>
									</label>
								</div>
							</div>
						</div>
					</div>

	      		</div>
	      		<div class="modal-footer" style="background-color:whitesmoke">
	        		<button type="submit" class="btn btn-success">
	        			<i class="icon-save"></i> &nbsp; <b>{l s="Save" d='Shop.Theme.Actions'}</b>
	        		</button>
	        		<button type="button" class="btn btn-danger" data-dismiss="modal">
	        			<i class="icon-close"></i> &nbsp; <b>{l s="Fermer" d='Shop.Theme.Actions'}</b>
	        		</button>
	      		</div>
	    	</div>
	  	</div>
	</div>
</form>