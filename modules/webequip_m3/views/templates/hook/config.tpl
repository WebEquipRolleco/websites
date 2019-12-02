<form method="post">
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i> {l s="Paramètrage de l'API" mod="webequip_m3"}
		</div>
		<div class="row">
			<div class="col-lg-4">
				<div class="form-group">
					<label>{l s="URL de l'API" mod="webequip_m3"}</label>
					<input type="text" name="M3_API_URL" value="{$M3_API_URL}">
				</div>
			</div>
		</div>
		<hr />
		<div class="row">
			<div class="col-lg-4">
				<div class="form-group">
					<label>{l s="URL commande" mod="webequip_m3"}</label>
					<div class="input-group">
						<span class="input-group-addon">{$M3_API_URL}</span>
						<input type="text" name="M3_API_ORDER_SUFFIX" value="{$M3_API_ORDER_SUFFIX}">
					</div>
				</div>
				<div class="form-group">
					<label>{l s="URL récupération numéro commande" mod="webequip_m3"}</label>
					<div class="input-group">
						<span class="input-group-addon">{$M3_API_URL}</span>
						<input type="text" name="M3_API_ORDER_NUMBER" value="{$M3_API_ORDER_NUMBER}">
					</div>
				</div>
				<div class="form-group">
					<label>{l s="URL récupération des status commande" mod="webequip_m3"}</label>
					<div class="input-group">
						<span class="input-group-addon">{$M3_API_URL}</span>
						<input type="text" name="M3_API_ORDER_STATE" value="{$M3_API_ORDER_STATE}">
					</div>
				</div>
				<div class="form-group">
					<label>{l s="URL liste des adresses" mod="webequip_m3"}</label>
					<div class="input-group">
						<span class="input-group-addon">{$M3_API_URL}</span>
						<input type="text" name="M3_API_ADDRESS_LIST_SUFFIX" value="{$M3_API_ADDRESS_LIST_SUFFIX}">
					</div>
				</div>
				<div class="form-group">
					<label>{l s="URL ajout des adresses" mod="webequip_m3"}</label>
					<div class="input-group">
						<span class="input-group-addon">{$M3_API_URL}</span>
						<input type="text" name="M3_API_ADDRESS_ADD_SUFFIX" value="{$M3_API_ADDRESS_ADD_SUFFIX}">
					</div>
				</div>
			</div>
		</div>
		<div class="panel-footer text-right">
			<button type="submit" class="btn btn-success">
				<i class="process-icon-save"></i>
				<b>{l s="Save" mod="webequip_m3"}</b>
			</button>
		</div>
	</div>
</form>

<form method="post">
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i> {l s="Gestion des status commandes" mod="webequip_m3"}
		</div>
		<div class="row">
			<div class="col-lg-5">
				<div class="form-group">
					<label>{l s="Statut attribué à la création" mod="webequip_m3"}</label>
					
					<select class="form-control" name="DEFAULT_STATE_SUCCESS">
						<option value=""></option>
						{foreach from=$states item=state}
							<option value="{$state.id_order_state}" {if $state.id_order_state == $DEFAULT_STATE_SUCCESS}selected{/if}>
								{$state.name}
							</option>
						{/foreach}
					</select>
					<div class="text-right">
						<em class="text-muted">{l s="Toutes les commandes passées seront créées à cet état" mod="webequip_m3"}</em>
					</div>
				</div>
				<div class="form-group">
					<label>{l s="Statut attribué après envoi à M3" mod="webequip_m3"}</label>
					<select class="form-control" name="ID_ORDER_STATE_STANDBY">
						<option value=""></option>
						{foreach from=$states item=state}
							<option value="{$state.id_order_state}" {if $state.id_order_state == $ID_ORDER_STATE_STANDBY}selected{/if}>
								{$state.name}
							</option>
						{/foreach}
					</select>
					<div class="text-right">
						<em class="text-muted">{l s="Les commandes envoyées vers M3 passeront automatiquement à cet état" mod="webequip_m3"}</em>
					</div>
				</div>
				<div class="form-group">
					<label>{l s="Statut final des commandes" mod="webequip_m3"}</label>
					<select class="form-control" name="ID_ORDER_STATE_FINAL">
						<option value=""></option>
						{foreach from=$states item=state}
							<option value="{$state.id_order_state}" {if $state.id_order_state == $ID_ORDER_STATE_FINAL}selected{/if}>
								{$state.name}
							</option>
						{/foreach}
					</select>
					<div class="text-right">
						<em class="text-muted">{l s="Les commandes passées par cet état ne sont plus considérées par les changements d'état sur M3" mod="webequip_m3"}</em>
					</div>
				</div>
			</div>
		</div>
		<div class="panel-footer text-right">
			<button type="submit" class="btn btn-success">
				<i class="process-icon-save"></i>
				<b>{l s="Save" mod="webequip_m3"}</b>
			</button>
		</div>
	</div>
</form>