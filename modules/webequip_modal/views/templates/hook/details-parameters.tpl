<div class="row">
	<div class="col-lg-4">
		<div class="panel">
			<div class="panel-heading">
				<b>{l s="Dates de fonctionnement" mod="webequip_modal"}</b>
			</div>
			<div class="form-group">
				<label>{l s="Date de début" d="Shop.Theme.Labels"}</label>
				<input type="date" class="form-control" name="modal[date_begin]" value="{$modal->date_begin}">
			</div>
			<div class="form-group">
				<label>{l s="Date de fin" d="Shop.Theme.Labels"}</label>
				<input type="date" class="form-control" name="modal[date_end]" value="{$modal->date_end}">
			</div>
		</div>
	    <div class="panel">
			<div class="panel-heading">
				<b>{l s="Restrictions globales" mod="webequip_modal"}</b>
			</div>
			<div class="form-group text-center">
				<label>{l s="Visiteur(s) connecté(s)" mod="webequip_modal"}</label>
				<span class="switch prestashop-switch fixed-width-lg" style="margin-left:auto; margin-right: auto;">
					<input type="radio" name="modal[display_for_customers]" id="display_for_customers_on" value="1" {if $modal->display_for_customers}checked{/if}>
					<label for="display_for_customers_on">{l s='Oui' d='Shop.Theme.Labels'}</label>
					<input type="radio" name="modal[display_for_customers]" id="display_for_customers_off" value="0" {if !$modal->display_for_customers}checked{/if}>
					<label for="display_for_customers_off">{l s='Non' d='Shop.Theme.Labels'}</label>
					<a class="slide-button btn"></a>
				</span>
			</div>
			<div class="form-group text-center">
				<label>{l s="Visiteur(s) non-connecté(s)" mod="webequip_modal"}</label>
				<span class="switch prestashop-switch fixed-width-lg" style="margin-left:auto; margin-right: auto;">
					<input type="radio" name="modal[display_for_guests]" id="display_for_guests_on" value="1" {if $modal->display_for_guests}checked{/if}>
					<label for="display_for_guests_on">{l s='Oui' d='Shop.Theme.Labels'}</label>
					<input type="radio" name="modal[display_for_guests]" id="display_for_guests_off" value="0" {if !$modal->display_for_guests}checked{/if}>
					<label for="display_for_guests_off">{l s='Non' d='Shop.Theme.Labels'}</label>
					<a class="slide-button btn"></a>
				</span>
			</div>
		</div>
		<div class="panel">
			<div class="panel-heading">
				<b>{l s="Fréquence d'affichage" mod="webequip_modal"}</b>
			</div>
			<div class="form-group text-center">
				<label>
					{l s="Fermeture obligatoire" mod="webequip_modal"}
					<a href="#" class="text-info" title="{l s='Le client doit fermer manuellement la popin pour prendre en compte sa durée de réaffichage' mod='webequip_modal'}">
						<i class="icon-info-circle"></i>
					</a>
				</label>
				<span class="switch prestashop-switch fixed-width-lg" style="margin-left:auto; margin-right: auto;">
					<input type="radio" name="modal[validation]" id="validation_on" value="1" {if $modal->validation}checked{/if}>
					<label for="validation_on">{l s='Oui' d='Shop.Theme.Labels'}</label>
					<input type="radio" name="modal[validation]" id="validation_off" value="0" {if !$modal->validation}checked{/if}>
					<label for="validation_off">{l s='Non' d='Shop.Theme.Labels'}</label>
					<a class="slide-button btn"></a>
				</span>
			</div>
			<div class="form-group text-center">
				<label>
					{l s="Connexion minimum" mod="webequip_modal"}
					<a href="#" class="text-info" title="{l s='X minutes de navigation avant l\'affichage de la modal'}">
						<i class="icon-info-circle"></i>
					</a>
				</label>
				<input type="text" class="form-control text-center" name="modal[browsing]" {if $modal->browsing != 0}value="{$modal->browsing}"{/if} placeholder="{l s='Temps en minutes' mod="webequip_modal"}">
			</div>
			<div class="form-group text-center">
				<label>
					{l s="Délai entre 2 affichages" mod="webequip_modal"}
					<a href="#" class="text-info" title="{l s='La modal s\'affichera toutes les X minutes'}">
						<i class="icon-info-circle"></i>
					</a>
				</label>
				<input type="text" class="form-control text-center" name="modal[expiration]" {if $modal->expiration != 0}value="{$modal->expiration}"{/if} placeholder="{l s='Temps en minutes' mod="webequip_modal"}">
				<div class="text-center text-muted">{l s="(-1 pour afficher une seule fois)" mod='webequip_modal'}</div>
			</div>
		</div>
	</div>
	<div class="col-lg-8">
		<div class="panel">
			<div class="panel-heading">
				<b>{l s="Restrictions des pages" mod="webequip_modal"}</b>
				<a href="#" class="text-info pull-right" title="{l s='Limiter l\'affichage sur certaines pages'}">
					<i class="icon-info-circle"></i>
				</a>
			</div>
			<div class="form-group">
				<label>
					<a href="#" class="text-info" title="{l s='Afficher uniquement sur ces pages'}">
						<i class="icon-info-circle"></i>
					</a>
					{l s='Pages autorisées' mod='webequip_modal'}
				</label>
	        	<input type="text" class="form-group" name="modal[allow_pages]" value="{$modal->allow_pages}"">
	        </div>
	        <div class="form-group">
	        	<label>
	        		<a href="#" class="text-info" title="{l s='Afficher partout sauf sur ces pages'}">
						<i class="icon-info-circle"></i>
					</a>
	        		{l s='Pages interdites' mod='webequip_modal'}
	        	</label>
	        	<input type="text" class="form-group" name="modal[disable_pages]" value="{$modal->disable_pages}">
	        </div>
		</div>
		<div class="panel">
			<div class="panel-heading">
				<b>{l s="Restrictions clients" mod="webequip_modal"}</b>
				<a href="#" class="text-info pull-right" title="{l s='Limiter l\'affichage pour certains clients'}">
					<i class="icon-info-circle"></i>
				</a>
			</div>
			<div class="form-group">
				<label>
					<a href="#" class="text-info" title="{l s='Afficher uniquement pour ces clients'}">
						<i class="icon-info-circle"></i>
					</a>
					{l s="Liste blanche" mod="webequip_modal"}
				</label>
				<select class="form-control select2" name="modal[allow_customers][]" multiple="multiple">
					{assign var=allow value=$modal->getAllowCustomersIds()}
					{foreach from=$customers item=customer}
						<option value="@{$customer.id_customer}@" {if $customer.id_customer|in_array:$allow}selected{/if}>
							{$customer.firstname} {$customer.lastname} ({$customer.email})
						</option>
					{/foreach}
				</select>
			</div>
			<div class="form-group">
				<label>
					<a href="#" class="text-info" title="{l s='Afficher pour tout le monde sauf ces clients'}">
						<i class="icon-info-circle"></i>
					</a>
					{l s="Liste noire" mod="webequip_modal"}
				</label>
				<select class="form-control select2" name="modal[disable_customers][]" multiple="multiple">
					{assign var=disable value=$modal->getDisableCustomersIds()}
					{foreach from=$customers item=customer}
						<option value="@{$customer.id_customer}@" {if $customer.id_customer|in_array:$disable}selected{/if}>
							{$customer.firstname} {$customer.lastname} ({$customer.email})
						</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="panel">
			<div class="panel-heading">
				<b>{l s="Restrictions groupes" mod="webequip_modal"}</b>
				<a href="#" class="text-info pull-right" title="{l s='Limiter l\'affichage pour certains groupes'}">
					<i class="icon-info-circle"></i>
				</a>
			</div>
			<div class="form-group">
				<label>
					<a href="#" class="text-info" title="{l s='Afficher uniquement pour les groupes suivant'}">
						<i class="icon-info-circle"></i>
					</a>
					{l s="Liste blanche" mod="webequip_modal"}
				</label>
				<select class="form-control select2" name="modal[allow_groups][]"  multiple="multiple">
					{assign var=allow value=$modal->getAllowGroupsIds()}
					{foreach from=$groups item=group}
						<option value="@{$group.id_group}@" {if $group.id_group|in_array:$allow}selected{/if}>
							{$group.name}
						</option>
					{/foreach}
				</select>
			</div>
			<div class="form-group">
				<label>
					<a href="#" class="text-info" title="{l s='Afficher pour tout le monde sauf pour les groupes suivant'}">
						<i class="icon-info-circle"></i>
					</a>
					{l s="Liste noire" mod="webequip_modal"}
				</label>
				<select class="form-control select2" name="modal[disable_groups][]" multiple="multiple">
					{assign var=disable value=$modal->getDisableGroupsIds()}
					{foreach from=$groups item=group}
						<option value="@{$group.id_group}@" {if $group.id_group|in_array:$disable}selected{/if}>
							{$group.name}
						</option>
					{/foreach}
				</select>
			</div>
		</div>
	</div>
</div>