<form method="post">
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i> {l s="Configuration" mod="webequip_payment_administrative"}
		</div>
		<div class="row">
			<div class="col-lg-4">
				<div class="form-group">
					<label for="PS_PAYMENT_ADMINISTRATIVE_TO">{l s="Ordre du virement" mod="webequip_payment_administrative"}</label>
					<input type="text" id="PS_PAYMENT_ADMINISTRATIVE_TO" class="form-control" name="PS_PAYMENT_ADMINISTRATIVE_TO" value="{$PS_PAYMENT_ADMINISTRATIVE_TO}">
				</div>
				<div class="form-group">
					<label for="DEFAULT_ID_STATE_OK">{l s="Nombre de jours après facturation" mod="webequip_payment_administrative"}</label>
					<select id="DEFAULT_ID_STATE_OK" class="form-control" name="DEFAULT_ID_STATE_OK">
						<option value="0">-</option>
						{foreach from=OrderState::getOrderStates(1) item=state}
							<option value="{$state.id_order_state}" {if $DEFAULT_ID_STATE_OK == $state.id_order_state}selected{/if}>{$state.name}</option>
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

<form method="post">
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i> {l s="Coordonnées" mod="webequip_payment_administrative"}
		</div>
		<div class="alert alert-info">
			{l s="Les informations ci-dessous sont des rappels de la configuration des coordonnées de la boutique également configurables dans la page [1]Coordonnées[/1] de l'onglet [1]WEB-EQUIP[/1]." tags=["<b>"] mod="webequip_payment_administrative"}
		</div>
		<div class="row">
			<div class="col-lg-4">
				<div class="form-group">
					<label for="PS_SHOP_CIC">{l s="CIC" mod="webequip_payment_administrative"}</label>
					<input type="text" id="PS_SHOP_CIC" class="form-control" name="PS_SHOP_CIC" value="{$PS_SHOP_CIC}">
				</div>
				<div class="form-group">
					<label for="PS_SHOP_IBAN">{l s="IBAN" mod="webequip_payment_administrative"}</label>
					<input type="text" id="PS_SHOP_IBAN" class="form-control" name="PS_SHOP_IBAN" value="{$PS_SHOP_IBAN}">
				</div>
				<div class="form-group">
					<label for="PS_SHOP_BIC">{l s="BIC" mod="webequip_payment_administrative"}</label>
					<input type="text" id="PS_SHOP_BIC" class="form-control" name="PS_SHOP_BIC" value="{$PS_SHOP_BIC}">
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