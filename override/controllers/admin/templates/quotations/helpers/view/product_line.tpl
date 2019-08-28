<tr>
	<input type="hidden" name="lines[{$line->id}][id]" value="{$line->id}">
	<input type="file" id="new_file_{$line->id}" name="lines[{$line->id}]" style="height:0px; width:0px; overflow:hidden;">
	<td class="text-center">
		<img src="{$line->getImageLink()}" style="height:75px; width:75px; border: 1px solid lightgrey; margin-bottom: 2px">
		<button type="button" class="btn btn-xs btn-block btn-default change-picture" data-id="#new_file_{$line->id}">
			Changer
		</button>
	</td>
	<td>
		<div class="text-muted"><em>{l s="Désignation" d="Admin.Labels"}</em></div>
		<input type="text" class="form-control" name="lines[{$line->id}][name]" value="{$line->name}">
		<div class="row">
			<div class="col-lg-3">
				<div class="text-center text-muted"><em>{l s="Référence" d="Admin.Labels"}</em></div>
				<input type="text" class="form-control" name="lines[{$line->id}][reference]" value="{$line->reference}">
			</div>
			<div class="col-lg-9">
				<div class="text-center text-muted"><em>{l s="Informations complémentaires" d="Admin.Labels"}</em></div>
				<input type="text" class="form-control" name="lines[{$line->id}][information]" value="{$line->information}">
			</div>
		</div>
	</td>
	<td>
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
	<td>
		<div class="text-center text-muted"><em>{l s="PA" d="Admin.Labels"}</em></div>
		<input type="text" class="form-control text-center" name="lines[{$line->id}][buying_price]" value="{$line->buying_price}">
		<div class="text-center text-muted"><em>{l s="Ports" d="Admin.Labels"}</em></div>
		<input type="text" class="form-control text-center" name="lines[{$line->id}][buying_fees]" value="{$line->buying_fees}">
	</td>
	<td>
		<div class="text-center text-muted"><em>{l s="PV" d="Admin.Labels"}</em></div>
		<input type="text" class="form-control text-center" name="lines[{$line->id}][selling_price]" value="{$line->selling_price}">
		<div class="text-center text-muted"><em>{l s="Quantité" d="Admin.Labels"}</em></div>
		<input type="number" class="form-control text-center" name="lines[{$line->id}][quantity]" value="{$line->quantity}">
	</td>
	<td class="text-center">
		<b>{displayPrice price=$line->getMargin()}</b>
		<br />
		{$line->getMarginRate()|round:2}%
	</td>
	<td>
		<textarea rows="4" class="form-control" name="lines[{$line->id}][comment]" style="resize:vertical">{$line->comment}</textarea>
	</td>
	<td>
		<input type="text" class="form-control text-center" name="lines[{$line->id}][position]" value="{$line->position}">
	</td>
	<td class="text-right">
		<div class="btn-group">
			<button type="submit" class="btn btn-xs btn-danger remove_product" name="remove_product" value="{$line->id}" title="{l s='Remove' d='Admin.Actions'}">
				<i class="icon-times"></i>
			</button>
		</div>
	</td>
</tr>