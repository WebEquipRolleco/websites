{if !empty($combinations)}
	<div class="form-group">
		<select class="form-control" name="id_combination" required>
			<option value="">{l s="Choisir une déclination" mod="webequip_quotation"}</option>
			{foreach $combinations as $reference => $combination}
				<option value="{$combination.id}">{$reference} : {$combination.name}</option>
			{/foreach}
		</select>
	</div>
{/if}