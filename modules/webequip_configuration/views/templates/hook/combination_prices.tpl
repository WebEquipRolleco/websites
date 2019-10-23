<div class="row">
	<div class="col-md-12">
		<h2>{l s="Gestion Web-équip" mod='webequip_configuration'}</h2>
	</div>
	<div class="col-xl-2 col-lg-3 form-group">
		<label form="combination_{$combination->id}_attribute_delivery_fees" class="form-control-label">{l s="Frais de port HT" mod='webequip_configuration'}</label>
		<div class="input-group money-type">
            <input type="text" id="combination_{$combination->id}_attribute_delivery_fees" name="delivery_fees_{$combination->id}" class="form-control" value="{$combination->delivery_fees}">
            <div class="input-group-append">
             	<span class="input-group-text"> €</span>
            </div>
        </div>
	</div>
	<div class="col-xl-2 col-lg-3 form-group">
		<label form="combination_{$combination->id}_attribute_rollcash" class="form-control-label">{l s="Rollcash" mod='webequip_configuration'}</label>
		<div class="input-group money-type">
            <input type="text" id="combination_{$combination->id}_attribute_rollcash" name="rollcash_{$combination->id}" class="form-control" value="{$combination->rollcash}">
            <div class="input-group-append">
             	<span class="input-group-text"> %</span>
            </div>
        </div>
	</div>
	<div class="col-xl-2 col-lg-3 form-group">
		<label form="combination_{$combination->id}_attribute_position" class="form-control-label">{l s="Ordre d'affichage"}</label>
        <input type="number" id="combination_{$combination->id}_attribute_position" name="position_{$combination->id}" class="form-control" value="{$combination->position}">
	</div>
</div>