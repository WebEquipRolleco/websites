<div class="row">
	<div class="col-lg-12">
		<div class="panel">
	   		<span class="switch prestashop-switch fixed-width-lg" style="margin-left:auto; margin-right:auto;">
				<input type="radio" name="modal[active]" id="active_on" value="1" {if $modal->active}checked{/if}>
				<label for="active_on">{l s='Active' d='Shop.Theme.Labels'}</label>
				<input type="radio" name="modal[active]" id="active_off" value="0" {if !$modal->active}checked{/if}>
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
				<b>{l s="Affichage" mod="webequip_modal"}</b>
			</div>
   			<div class="form-group">
   				<label>{l s='Icône' mod='webequip_modal'}</label>
   				<input type="text" class="form-group" name="modal[icon]" value="{$modal->icon}">
   			</div>
	        <div class="form-group">
	        	<label>{l s='Titre' mod='webequip_modal'} <em class="text-danger">*</em></label>
	        	<input type="text" class="form-group" name="modal[title]" value="{$modal->title}" required>
	       	</div>
	        <div class="form-group">
	        	<label>{l s='Sous-titre' mod='webequip_modal'}</label>
	        	<input type="text" class="form-group" name="modal[subtitle]" value="{$modal->subtitle}">
	        </div>
	    </div>
	    <div class="panel">
			<div class="panel-heading">
				<b>{l s="Animations" mod="webequip_modal"}</b>
			</div>
			<div class="row">
				<div class="col-lg-6">
			        <div class="form-group text-center">
			        	<label>{l s='Apparition' mod='webequip_modal'}</label>
						<select class="form-group select2 text-center" name="modal[transition_in]">
			        		{foreach from=$animations_in item=animation}
			        			<option value='{$animation}' {if $modal->transition_in == $animation}selected{/if}>
			        				{$animation}
			        			</option>
			        		{/foreach}
			        	</select>
			        </div>
			    </div>
			    <div class="col-lg-6">
			        <div class="form-group text-center">
			        	<label>{l s='Disparition' mod='webequip_modal'}</label>
			        	<select class="form-group select2 text-center" name="modal[transition_out]">
			        		{foreach from=$animations_out item=animation}
			        			<option value='{$animation}' {if $modal->transition_out == $animation}selected{/if}>
			        				{$animation}
			        			</option>
			        		{/foreach}
			        	</select>
			        </div>
			    </div>
		    </div>
		    <div class="row">
				<div class="col-lg-6">
			        <div class="form-group text-center">
			        	<label>{l s='Ouverture automatique' mod='webequip_modal'}</label>
	        			<input type="text" class="form-group text-center" name="modal[auto_open]" {if $modal->auto_open}value="{$modal->auto_open}"{/if}>
	        		</div>
			    </div>
			    <div class="col-lg-6">
			        <div class="form-group text-center">
			        	<label>{l s='Fermeture automatique' mod='webequip_modal'}</label>
	        			<input type="text" class="form-group text-center" name="modal[auto_close]" {if $modal->auto_close}value="{$modal->auto_close}"{/if}>
	        		</div>
			    </div>
		    </div>
	    </div>
	    <div class="panel">
			<div class="panel-heading">
				<b>{l s="Contrôles" mod="webequip_modal"}</b>
			</div>
			<div class="row">
				<div class="col-lg-6 text-center">
					<div class="form-group">
						<label>
							{l s='Fermeture "boutons"' mod='webequip_modal'}
							<a href="#" class="text-info" title="{l s='Afficher un bouton de fermeture'}">
								<i class="icon-info-circle"></i>
							</a>
						</label>
						<span class="switch prestashop-switch fixed-width-lg" style="margin-left:auto; margin-right: auto;">
							<input type="radio" name="modal[close_button]" id="close_button_on" value="1" {if $modal->close_button}checked{/if}>
							<label for="close_button_on">{l s='Oui' d='Shop.Theme.Labels'}</label>
							<input type="radio" name="modal[close_button]" id="close_button_off" value="0" {if !$modal->close_button}checked{/if}>
							<label for="close_button_off">{l s='Non' d='Shop.Theme.Labels'}</label>
							<a class="slide-button btn"></a>
						</span>
					</div>
				</div>
				<div class="col-lg-6 text-center">
					<div class="form-group">
						<label>
							{l s='Overlay' mod='webequip_modal'}
							<a href="#" class="text-info" title="{l s='Griser l\'arrière plan'}">
								<i class="icon-info-circle"></i>
							</a>
						</label>
						<span class="switch prestashop-switch fixed-width-lg" style="margin-left:auto; margin-right: auto;">
							<input type="radio" name="modal[overlay]" id="overlay_on" value="1" {if $modal->overlay}checked{/if}>
							<label for="overlay_on">{l s='Oui' d='Shop.Theme.Labels'}</label>
							<input type="radio" name="modal[overlay]" id="overlay_off" value="0" {if !$modal->overlay}checked{/if}>
							<label for="overlay_off">{l s='Non' d='Shop.Theme.Labels'}</label>
							<a class="slide-button btn"></a>
						</span>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-6 text-center">
					<div class="form-group">
						<label>
							{l s='fermeture "clavier"' mod='webequip_modal'}
							<a href="#" class="text-info" title="{l s='Fermer la modal en appuyant sur ECHAP'}">
								<i class="icon-info-circle"></i>
							</a>
						</label>
						<span class="switch prestashop-switch fixed-width-lg" style="margin-left:auto; margin-right: auto;">
							<input type="radio" name="modal[close_escape]" id="close_escape_on" value="1" {if $modal->close_escape}checked{/if}>
							<label for="close_escape_on">{l s='Oui' d='Shop.Theme.Labels'}</label>
							<input type="radio" name="modal[close_escape]" id="close_escape_off" value="0" {if !$modal->close_escape}checked{/if}>
							<label for="close_escape_off">{l s='Non' d='Shop.Theme.Labels'}</label>
							<a class="slide-button btn"></a>
						</span>
					</div>
				</div>
				<div class="col-lg-6 text-center">
					<div class="form-group">
						<label>
							{l s='Option "Plein écran"' mod='webequip_modal'}
							<a href="#" class="text-info" title="{l s='Afficher un bouton de plein écran'}">
								<i class="icon-info-circle"></i>
							</a>
						</label>
						<span class="switch prestashop-switch fixed-width-lg" style="margin-left:auto; margin-right: auto;">
							<input type="radio" name="modal[fullscreen]" id="fullscreen_on" value="1" {if $modal->fullscreen}checked{/if}>
							<label for="fullscreen_on">{l s='Oui' d='Shop.Theme.Labels'}</label>
							<input type="radio" name="modal[fullscreen]" id="fullscreen_off" value="0" {if !$modal->fullscreen}checked{/if}>
							<label for="fullscreen_off">{l s='Non' d='Shop.Theme.Labels'}</label>
							<a class="slide-button btn"></a>
						</span>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-6 text-center">
					<div class="form-group">
						<label>
							{l s='fermeture "overlay"' mod='webequip_modal'}
							<a href="#" class="text-info" title="{l s='Fermer la modal en cliquant en dehors'}">
						<i class="icon-info-circle"></i>
					</a>
						</label>
						<span class="switch prestashop-switch fixed-width-lg" style="margin-left:auto; margin-right: auto;">
							<input type="radio" name="modal[close_overlay]" id="close_overlay_on" value="1" {if $modal->close_overlay}checked{/if}>
							<label for="close_overlay_on">{l s='Oui' d='Shop.Theme.Labels'}</label>
							<input type="radio" name="modal[close_overlay]" id="close_overlay_off" value="0" {if !$modal->close_overlay}checked{/if}>
							<label for="close_overlay_off">{l s='Non' d='Shop.Theme.Labels'}</label>
							<a class="slide-button btn"></a>
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-6">
	    <div class="panel">
			<div class="panel-heading">
				<b>{l s="Contenu" mod="webequip_modal"}</b>
				&nbsp; <small class="text-muted">HTML</small>
			</div>
	        <div class="form-group">
	        	<textarea rows="25" class="form-group" name="modal[content]">{$modal->content}</textarea>
	        </div>
	    </div>
	    <div class="panel">
	        <div class="panel-heading">
	        	<b>{l s="Thème" mod="webequip_modal"}</b>
	        </div>
	        <div class="row">
	        	<div class="col-lg-3 text-center">
	        		<label>{l s="Couleur" mod="webequip_modal"}</label>
	        		<input type="color" class="form-control" name="modal[header_color]" value="{$modal->header_color}">
	        	</div>
	        	<div class="col-lg-3 text-center">
	        		<label>{l s='Largeur' mod='webequip_modal'}</label>
	        		<input type="text" class="form-control text-center" name="modal[width]" value="{$modal->width}" placeholder="{l s='en PX ou %' mod='webequip_modal'}">
	        	</div>
	        	<div class="col-lg-3 text-center">
	        		<label>{l s='Espace haut' mod='webequip_modal'}</label>
	        		<input type="text" class="form-control text-center" name="modal[top]" value="{$modal->top}" placeholder="{l s='en PX' mod='webequip_modal'}">
	        	</div>
	        	<div class="col-lg-3 text-center">
	        		<label>{l s='Espace bas' mod='webequip_modal'}</label>
	        		<input type="text" class="form-control text-center" name="modal[bottom]" value="{$modal->bottom}" placeholder="{l s='en PX' mod='webequip_modal'}">
	        	</div>
	        </div>
	    </div>
	</div>
</div>