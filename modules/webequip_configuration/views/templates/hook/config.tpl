<form id="config_tabs" action="#config_tabs" method="post">
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i> Configuration des onglets
		</div>
		{if $old_sav_id}
			<form method="post">
				<div class="alert alert-warning">
					<div class="row">
						<div class="col-lg-10">
							{l s="L'ancien module SAV est toujours installé." mod="webequip_configuration"}
						</div>
						<div class="col-lg-2 text-right">
							<button type="submit" class="btn btn-xs btn-danger" name="uninstall_sav">
								<i class="icon-trash"></i> &nbsp; <b>{l s="Désinstaller" d='Shop.Theme.Actions'}</b>
							</button>
						</div>
					</div>
				</div>
			</form>
		{/if}
		<table class="table">
			<thead>
				<tr class="bg-primary">
					<th colspan="2"><b>{l s='Menu' mod="webequip_configuration"}</b></th>
					<th class="text-center"><b>{l s='Statut' mod="webequip_configuration"}</b></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$tabs item=tab}
					<tr>
						<td style="background-color:lightgrey !important"><b>{$tab.name}</b></td>
						<td colspan="3" class="text-right" style="background-color:lightgrey !important">
							{if isset($tab.action) and !$tab.id}
								<button type="submit" class="btn btn-xs btn-default" {if $tab.id}disabled{else}name="action" value="{$tab.action}"{/if}>
									{l s='Installer' mod="webequip_configuration"}
								</button>
							{/if}
						</td>
					</tr>
					{if !isset($tab.id) or $tab.id}
						{foreach from=$tab.children item=child}
							<tr>
							<td></td>
							<td>{$child.name}</td>
							<td class="text-center">
								{if $child.id}
									<i class="icon-check text-success"></i>
								{else}
									<i class="icon-times text-danger"></i>
								{/if}
							</td>
							<td class="text-right">
								<button type="submit" class="btn btn-xs btn-default" {if $child.id}disabled{else}name="action" value="{$child.action}"{/if}>
									{l s='Installer' d='Shop.Theme.Actions'}
								</button>
								{if $child.id}
									<button type="submit" class="btn btn-xs btn-danger" name="remove_tab" value="{$child.id}" title="{l s="Désinstaller" d='Shop.Theme.Actions'}">
										<i class="icon-trash"></i>
									</button>
								{/if}
							</td>
						</tr>
						{/foreach}
					{/if}
				{/foreach}
			</tbody>
		</table>
	</div>
</form>

<form id="footer_links" action="#footer_links" method="post">
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i> Gestion des liens du footer
		</div>
		<div class="row">
			<div class="col-lg-4">
				<div class="form-group">
					<label>{l s="Lien de la page des moyens de paiements" mod="webequip_configuration"}</label>
					<select class="form-control" name="FOOTER_LINK_PAIEMENT">
						<option value="0">-</option>
						{foreach from=$cms item=page}
							<option value="{$page.id_cms}" {if $page.id_cms == $FOOTER_LINK_PAIEMENT}selected{/if}>
								{$page.meta_title}
							</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="form-group">
					<label>{l s="Lien de la page F.A.Q" mod="webequip_configuration"}</label>
					<select class="form-control" name="FOOTER_LINK_FAQ">
						<option value="0">-</option>
						{foreach from=$cms item=page}
							<option value="{$page.id_cms}" {if $page.id_cms == $FOOTER_LINK_FAQ}selected{/if}>
								{$page.meta_title}
							</option>
						{/foreach}
					</select>
				</div>
			</div>
		</div>
		<div class="panel-footer text-right">
			<button type="submit" class="btn btn-success">
				<i class="process-icon-save"></i> <b>{l s="Save" d='Shop.Theme.Actions'}</b>
			</button>
		</div>
	</div>
</form>

<form id="default_states" action="#default_states" method="post">
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i> Etat commandes par défaut	
		</div>
		<div class="row">
			<div class="col-lg-4">
				<div class="form-group">
					<label>Succès de l'enregistrement</label>
					<select class="form-control" name="DEFAULT_STATE_SUCCESS">
						<option value=""></option>
						{foreach from=$states item=state}
							<option value="{$state.id_order_state}" {if $DEFAULT_STATE_SUCCESS == $state.id_order_state}selected{/if}>{$state.name}</option>
						{/foreach}
					</select>
				</div>
				<div class="form-group">
					<label>Echec de l'enregistrement</label>
					<select class="form-control" name="DEFAULT_STATE_FAILURE">
						<option value=""></option>
						{foreach from=$states item=state}
							<option value="{$state.id_order_state}" {if $DEFAULT_STATE_FAILURE == $state.id_order_state}selected{/if}>{$state.name}</option>
						{/foreach}
					</select>
				</div>
			</div>
		</div>
		<div class="panel-footer text-right">
			<button type="submit" class="btn btn-success">
				<i class="process-icon-save"></i> <b>{l s="Save" d='Shop.Theme.Actions'}</b>
			</button>
		</div>
	</div>
</form>

<form id="stat_states" action="#stat_states" method="post">
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i> Etats exclus et exports et statistiques
		</div>
		<div class="row">
			<div class="col-lg-4">
				<div class="form-group">
					<label>Etats commandes</label>
					<select class="form-control" name="EXPORT_EXCLUDED_STATES[]" multiple>
						{foreach from=$states item=state}
							<option value="{$state.id_order_state}" {if $state.id_order_state|in_array:$EXPORT_EXCLUDED_STATES}selected{/if}>{$state.name}</option>
						{/foreach}
					</select>
				</div>
			</div>
		</div>
		<div class="panel-footer text-right">
			<button type="submit" class="btn btn-success">
				<i class="process-icon-save"></i> <b>{l s="Save" d='Shop.Theme.Actions'}</b>
			</button>
		</div>
	</div>
</form>

<form id="BL_BC" action="#BL_BC" method="post">
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i> Configuration envoi des BC / BL
		</div>
		<div class="row">
			<div class="col-lg-4">
				<div class="form-group">
					<label for="BLBC_ORDER_STATE">Mail en copie lors de l'envoi</label>
					<input type="text" id="BLBC_ORDER_STATE" class="form-control" name="BLBC_HIDDEN_MAIL" value="{$BLBC_HIDDEN_MAIL}">
				</div>
				<div class="form-group">
					<label>Changement d'état lors de l'envoi</label>
					<select class="form-control" name="BLBC_ORDER_STATE">
						<option value=""></option>
						{foreach from=$states item=state}
							<option value="{$state.id_order_state}" {if $BLBC_ORDER_STATE == $state.id_order_state}selected{/if}>{$state.name}</option>
						{/foreach}
					</select>
				</div>
			</div>
		</div>
		<div class="panel-footer text-right">
			<button type="submit" class="btn btn-success">
				<i class="process-icon-save"></i> <b>{l s="Save" d='Shop.Theme.Actions'}</b>
			</button>
		</div>
	</div>
</form>

<form id="text_size" action="#text_size" method="post">
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i> Gestion du menu principal	
		</div>
		<div class="row">
			<div class="col-lg-4">
				<div class="form-group">
					<label>{l s="Taille de la police en PX" mod="webequip_configuration"}</label>
					<input type="text" class="form-control" name="MENU_FORCED_FONT_SIZE" value="{$MENU_FORCED_FONT_SIZE}">
				</div>
				<div class="form-group">
					<label>{l s="Nombre d'éléments affichés" mod="webequip_configuration"}</label>
					<input type="number" min=1 step=1 class="form-control" name="MENU_FORCED_NB_ELEMENTS" value="{$MENU_FORCED_NB_ELEMENTS}">
				</div>
			</div>
		</div>
		<div class="panel-footer text-right">
			<button type="submit" class="btn btn-success">
				<i class="process-icon-save"></i> <b>{l s="Save" d='Shop.Theme.Actions'}</b>
			</button>
		</div>
	</div>
</form>

<form id="after_sales" action="#after_sales" method="post">
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i> Service après vente
		</div>
		<div class="row">
			<div class="col-lg-4">
				<label>{l s="Activer les SAV"}</label>
				<span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" name="AFTER_SALES_ENABLED" id="AFTER_SALES_ENABLED_on" value="1" {if $AFTER_SALES_ENABLED}checked{/if}>
					<label for="AFTER_SALES_ENABLED_on">{l s='Oui' d='Shop.Theme.Labels'}</label>
					<input type="radio" name="AFTER_SALES_ENABLED" id="AFTER_SALES_ENABLED_off" value="0" {if !$AFTER_SALES_ENABLED}checked{/if}>
					<label for="AFTER_SALES_ENABLED_off">{l s='Non' d='Shop.Theme.Labels'}</label>
					<a class="slide-button btn"></a>
				</span>
			</div>
		</div>
		<div class="panel-footer text-right">
			<button type="submit" class="btn btn-success">
				<i class="process-icon-save"></i> <b>{l s="Save" d='Shop.Theme.Actions'}</b>
			</button>
		</div>
	</div>
</form>