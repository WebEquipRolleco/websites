<tr>
	<input type="hidden" class="line" name="lines[{$line->id}][id]" value="{$line->id}">
	<input type="file" id="new_file_{$line->id}" name="lines[{$line->id}][image]" style="height:0px; width:0px; overflow:hidden;">
	<input type="file" id="new_document_{$line->id}" name="lines[{$line->id}][document]" style="height:0px; width:0px; overflow:hidden;">
	
	{* REFERENCE / IMAGE *}
	<td width="5%" class="text-center">
		<div class="form-group">
			<div class="text-center text-muted"><em>{l s="Référence" d="Admin.Labels"}</em></div>
			<input type="text" class="form-control" name="lines[{$line->id}][reference]" value="{$line->reference}">
		</div>
		{if $line->getImageLink()}
			<img src="{$line->getImageLink()}" style="height:75px; width:75px; border: 1px solid lightgrey; margin-bottom: 2px">
		{/if}
		<button type="button" class="btn btn-xs btn-block btn-default change-file" data-id="#new_file_{$line->id}">
			Changer
		</button>
	</td>

	{* DESIGNATION / DIMENSIONS / INFORMATIONS COMPLEMENTAIRES *}
	<td width="20%">
		<div class="form-group">
			<div class="text-muted"><em>{l s="Désignation" d="Admin.Labels"}</em></div>
			<textarea rows="1" class="form-control" name="lines[{$line->id}][name]" style="resize:vertical">{$line->name}</textarea>
		</div>
		<div class="text-muted"><em>{l s="Dimensions" d="Admin.Labels"}</em></div>
		<textarea rows="1" class="form-control" name="lines[{$line->id}][properties]" style="resize:vertical">{$line->properties}</textarea>
		<div class="text-muted"><em>{l s="Informations complémentaires" d="Admin.Labels"}</em></div>
		<textarea rows="1" class="form-control" name="lines[{$line->id}][information]" style="resize:vertical">{$line->information}</textarea>
	</td>

	{* LOT *}
	<td class="text-center">
		{if $line->getProduct()}
			<div class="text-muted"><em>{l s="LOT" d="Admin.Labels"}</em></div>
			<b>{$line->getProduct()->batch}</b>
		{/if}
	</td>

	{* PRIX D'ACHAT *}
	<td class="text-center">
		{foreach from=$line->getSpecificPrices() item=specific_price}
			<div class="form-group">
				<div class="text-muted"><b>{l s="Par %s" sprintf=[$specific_price.from_quantity] d="Admin.Labels"}</b></div>
				<b>{Tools::displayPrice($specific_price.buying_price)}</b>
			</div>
		{/foreach}
	</td>

	{* FRAIS DE PORT *}
	<td class="text-center">
		{foreach from=$line->getSpecificPrices() item=specific_price}
			<div class="form-group">
				<div class="text-muted"><b>{l s="Par %s" sprintf=[$specific_price.from_quantity] d="Admin.Labels"}</b></div>
				<b>{Tools::displayPrice($specific_price.delivery_fees)}</b>
			</div>
		{/foreach}
	</td>

	{* TOTAL D'ACHAT *}
	<td class="text-center">
		{foreach from=$line->getSpecificPrices() item=specific_price}
			<div class="form-group">
				<div class="text-muted"><b>{l s="Par %s" sprintf=[$specific_price.from_quantity] d="Admin.Labels"}</b></div>
				<b>{Tools::displayPrice($specific_price.buying_price + $specific_price.delivery_fees)}</b>
			</div>
		{/foreach}
	</td>

	{* PRIX DE VENTE PRODUIT *}
	<td class="text-center">
		{foreach from=$line->getSpecificPrices() item=specific_price}
			<div class="form-group">
				<div class="text-muted"><b>{l s="Par %s" sprintf=[$specific_price.from_quantity] d="Admin.Labels"}</b></div>
				<b>{Tools::displayPrice($specific_price.price)}</b>
			</div>
		{/foreach}
	</td>

	{* PRIX DEVIS *}
	<td class="text-center">
		<div class="text-center text-muted"><em>{l s="PA" d="Admin.Labels"}</em></div>
		<input type="number" min="0" step="0.01" id="pa_{$line->id}" class="form-control text-center update-pa" data-id="{$line->id}" name="lines[{$line->id}][buying_price]" value="{$line->buying_price|string_format:"%.2f"}">
		<div class="text-center text-muted"><em>{l s="Ports" d="Admin.Labels"}</em></div>
		<input type="number" min="0" step="0.01" id="fees_{$line->id}" class="form-control text-center update-pa" data-id="{$line->id}" name="lines[{$line->id}][buying_fees]" value="{$line->buying_fees|string_format:"%.2f"}" style="margin-bottom:5px">
		<span class="label label-default"><b id="pa_with_fees_{$line->id}"></b></span>
		<div class="text-center text-muted"><em>{l s="PV" d="Admin.Labels"}</em></div>
		<input type="number" min="0" step="0.01" id="pv_{$line->id}" class="form-control text-center update-price" data-id="{$line->id}" name="lines[{$line->id}][selling_price]" value="{($line->selling_price - $line->eco_tax)|string_format:"%.2f"}">
		<div class="text-center text-success"><em><b>{l s="Ecotaxe" d="Admin.Labels"}</b></em></div>
		<input type="number" min="0" step="0.01" id="ecotax_{$line->id}" class="form-control text-center update-price" data-id="{$line->id}" name="lines[{$line->id}][eco_tax]" value="{$line->eco_tax|string_format:"%.2f"}">
		<div class="text-center text-muted"><em>{l s="PV Total" d="Admin.Labels"}</em></div>
		<span class="label label-success"><b id="unit_{$line->id}"></b></span>
	</td>

	{* QUANTITE *}
	<td class="text-center">
		<input type="text"  min="{if $line->getProduct()}{$line->getProduct()->minimal_quantity}{else}0{/if}" {if $line->getProduct() and $line->getProduct()->batch > 0}step="{$line->getProduct()->batch}"{/if} id="quantity_{$line->id}" class="form-control text-center update-price" data-id="{$line->id}" name="lines[{$line->id}][quantity]" value="{$line->quantity}">
	</td>

	{* TOTAL DE VENTE *}
	<td class="text-center">
		<span class="label label-default" title="{l s='Sans Ecotaxe' d="Admin.Labels"}">
			<b id="total_without_ec_{$line->id}"></b>
		</span>
		<div style="margin-top:10px">
			<span class="label label-success" title="{l s='Avec Ecotaxe' d="Admin.Labels"}">
				<b id="total_with_ec_{$line->id}"></b>
			</span>
		</div>
	</td>

	{* MARGE *}
	<td class="text-center">
		<b>{displayPrice price=$line->getMargin()}</b>
		<div>{$line->getMarginRate()|string_format:"%.2f"}%</div>
	</td>

	{* FOURNISSEUR *}
	<td class="text-center">
		<div class="text-center text-muted"><em>{l s="Sous-traitant" d="Admin.Labels"}</em></div>
		<select class="form-control" name="lines[{$line->id}][id_supplier]">
			<option value="0">-</option>
			{foreach from=$suppliers item=supplier}
				<option value="{$supplier.id_supplier}" {if $line->id_supplier == $supplier.id_supplier}selected{/if}>
					{$supplier.name}
				</option>
			{/foreach}
		</select>
		<div class="text-center text-muted"><em>{l s="Référence" d="Admin.Labels"}</em></div>
		<input type="text" class="form-control" name="lines[{$line->id}][reference_supplier]" value="{$line->reference_supplier}">	
	</td>

	{* COMMENTAIRE + FICHIER *}
	<td>
		<div class="form-group">
			<textarea rows="6" class="form-control" name="lines[{$line->id}][comment]" style="resize:vertical">{$line->comment}</textarea>
		</div>
		<div class="text-center">
			{if $line->getDocumentLink()}
				<a href="{$line->getDocumentLink()}">{l s="Télécharger le document joint"}</a>
				<div>
					<button type="button" class="btn btn-xs btn-default change-file" data-id="#new_document_{$line->id}">
						{l s="Changer le document"}
					</button>
					<button type="submit" class="btn btn-xs btn-danger" name="remove_document" value="{$line->id}" title="{l s='Delete'}">
						<i class="icon-times"></i>
					</button>
				</div>
			{else}
				<button type="button" class="btn btn-xs btn-default change-file" data-id="#new_document_{$line->id}">{l s="Ajouter un fichier"}</button>
			{/if}
		</div>
	</td>

	{* POSITION *}
	<td>
		<input type="text" class="form-control text-center" name="lines[{$line->id}][position]" value="{$line->position}">
	</td>

	{* ACTIONS *}
	<td class="text-right">
		<div class="btn-group">
			<button type="submit" class="btn btn-xs btn-danger remove_product" name="remove_product" value="{$line->id}" title="{l s='Remove' d='Admin.Actions'}">
				<i class="icon-times"></i>
			</button>
		</div>
	</td>

</tr>