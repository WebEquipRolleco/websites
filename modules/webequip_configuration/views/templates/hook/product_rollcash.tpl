<div class="col-md-12">
	<div class="row">
		<div class="col-md-12">
			<h2>{l s="Gestion Rollcash"}</h2>
		</div>
		<div class="col-xl-2 col-lg-3 form-group">
			<label form="rollcash" class="form-control-label">{l s="Taux alloué au prix HT"}</label>
			<div class="input-group money-type">
                <input type="text" id="rollcash" name="rollcash" class="form-control" value="{$product->rollcash}">
              <div class="input-group-append">
                <span class="input-group-text"> %</span>
            </div>
            </div>
		</div>
	</div>
</div>