<div class="row">
	<div class="col-md-12">
		<h2 class="title">{l s="Gestion Rollcash"}</h2>
	</div>
	<div class="col-xl-2 col-lg-3 form-group">
		<label form="combination_{$combination->id}_attribute_rollcash" class="form-control-label">{l s="Taux alloué au prix HT"}</label>
		<div class="input-group money-type">
            <input type="text" id="combination_{$combination->id}_attribute_rollcash" name="rollcash_{$combination->id}" class="form-control" value="{$combination->rollcash}">
            <div class="input-group-append">
             	<span class="input-group-text"> %</span>
            </div>
        </div>
	</div>
</div>