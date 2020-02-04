<div class="row">
	<div class="col-md-12">
		<h2>{l s="Gestion Web-équip" mod='webequip_configuration'}</h2>
	</div>
	<div class="col-xs-12 col-sm-6 form-group">
		<label class="form-control-label">Référence fournisseur</label>
		<input type="text"  class="form-control" value="{Product::getSupplierReference($combination->id_product, $combination->id)}" disabled>
	</div>
	<div class="col-xs-12 col-sm-6 form-group">
		<label class="form-control-label">SKU</label>
		<input type="text"  class="form-control" value="{SkuManager::getStaticCombinationSku($combination->id)}" disabled>
	</div>
	<div class="col-xs-2 col-lg-3 form-group">
		<label form="combination_{$combination->id}_attribute_batch" class="form-control-label">{l s="Lot" mod='webequip_configuration'}</label>
        <input type="text" id="combination_{$combination->id}_attribute_batch" name="batch_{$combination->id}" class="form-control" value="{$combination->batch}">
	</div>
	<div class="col-xs-2 col-lg-3 form-group">
		<label form="combination_{$combination->id}_attribute_custom_ecotax" class="form-control-label">{l s="Ecotaxe" mod='webequip_configuration'}</label>
		<div class="input-group money-type">
            <input type="text" id="combination_{$combination->id}_attribute_custom_ecotax" name="custom_ecotax_{$combination->id}" class="form-control" value="{$combination->custom_ecotax}">
            <div class="input-group-append">
             	<span class="input-group-text"> €</span>
            </div>
        </div>
	</div>
	<div class="col-xs-2 col-lg-3 form-group">
		<label form="combination_{$combination->id}_attribute_rollcash" class="form-control-label">{l s="Rollcash" mod='webequip_configuration'}</label>
		<div class="input-group money-type">
            <input type="text" id="combination_{$combination->id}_attribute_rollcash" name="rollcash_{$combination->id}" class="form-control" value="{$combination->rollcash}">
            <div class="input-group-append">
             	<span class="input-group-text"> %</span>
            </div>
        </div>
	</div>
	<div class="col-xs-2 col-lg-3 form-group">
		<label form="combination_{$combination->id}_attribute_position" class="form-control-label">{l s="Ordre d'affichage"}</label>
        <input type="number" id="combination_{$combination->id}_attribute_position" name="position_{$combination->id}" class="form-control" value="{$combination->position}">
	</div>
	<div class="col-xs-12 col-sm-6 form-group">
		<label form="combination_{$combination->id}_attribute_comment-1" class="form-control-label">{l s="Commentaire 1"}</label>
        <input type="text" id="combination_{$combination->id}_attribute_comment-1" name="comment-1_{$combination->id}" class="form-control" value="{$combination->comment_1}">
	</div>
	<div class="col-xs-12 col-sm-6 form-group">
		<label form="combination_{$combination->id}_attribute_comment-2" class="form-control-label">{l s="Commentaire 2"}</label>
        <input type="text" id="combination_{$combination->id}_attribute_comment-2" name="comment-2_{$combination->id}" class="form-control" value="{$combination->comment_2}">
	</div>
</div>