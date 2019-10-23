<div class="col-md-12">
	<div class="row">
		<div class="col-md-12">
			<h2>{l s="Gestion Web-équip" mod='webequip_configuration'}</h2>
		</div>
		{if $display_delivery_fees}
			<div class="col-xl-2 col-lg-3 form-group">
				<label form="delivery_fees" class="form-control-label">{l s="Frais de port HT" mod='webequip_configuration'}</label>
				<div class="input-group money-type">
	                <input type="text" id="delivery_fees" name="delivery_fees" class="form-control" value="{$product->delivery_fees}">
	              <div class="input-group-append">
	                <span class="input-group-text"> €</span>
	            </div>
	            </div>
			</div>
		{/if}
		<div class="col-xl-2 col-lg-3 form-group">
			<label form="rollcash" class="form-control-label">{l s="Rollcash" mod='webequip_configuration'}</label>
			<div class="input-group money-type">
                <input type="text" id="rollcash" name="rollcash" class="form-control" value="{$product->rollcash}">
              <div class="input-group-append">
                <span class="input-group-text"> %</span>
            </div>
            </div>
		</div>
	</div>
</div>