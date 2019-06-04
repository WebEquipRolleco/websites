<tr>
	<input type="hidden" name="lines[{$line->id}][id]" value="{$line->id}">
	<input type="file" name="lines[{$line->id}][new_file]" style="height:0px; width:0px; overflow:hidden;">
	<td class="text-center">
		<img src="{$line->getImageLink()}" style="height:75px; width:75px; border: 1px solid lightgrey; margin-bottom: 2px">
		<button type="button" class="btn btn-xs btn-block btn-default">
			Changer
		</button>
	</td>
	<td>
		<input type="text" class="form-control" name="lines[{$line->id}][reference]" value="{$line->reference}">
	</td>
	<td>
		<input type="text" class="form-control" name="lines[{$line->id}][name]" value="{$line->name}" placeholder="{l s='Nom du produit'}">
		<input type="text" class="form-control" name="lines[{$line->id}][information]" value="{$line->information}" placeholder="{l s='Information complémentaire'}" style="margin-top:5px">
	</td>
	<td>
		<input type="number" class="form-control text-center" name="lines[{$line->id}][quantity]" value="{$line->quantity}">
	</td>
	<td>
		<input type="text" class="form-control text-center" name="lines[{$line->id}][buying_price]" value="{$line->buying_price}">
	</td>
	<td>
		<input type="text" class="form-control text-center" name="lines[{$line->id}][selling_price]" value="{$line->selling_price}">
	</td>
	<td class="text-center">
		<b>{displayPrice price=$line->getMargin()}</b>
		<br />
		{$line->getMarginRate()|round:2}%
	</td>
	<td>
		<textarea rows="3" class="form-control" name="lines[{$line->id}][comment]">{$line->comment}</textarea>
	</td>
	<td>
		<input type="text" class="form-control text-center" name="lines[{$line->id}][position]" value="{$line->position}" disabled>
	</td>
	<td class="text-right">
		<div class="btn-group">
			<button type="submit" class="btn btn-xs btn-default" name="remove_product" value="{$line->id}">
				<i class="icon-times"></i>
			</button>
		</div>
	</td>
</tr>