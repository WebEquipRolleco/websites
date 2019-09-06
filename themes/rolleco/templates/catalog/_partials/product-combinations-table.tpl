{if $product.main_variants|count > 0}
	<table class="table combinations-table vertical-align">
		<thead>
			<tr>
				<th class="hidden-lg-up bold text-center">
					{l s="Propriétés"}
				</th>
				<th class="hidden-lg-down">{l s="Référence"}</th>
				{foreach from=$groups item=group}
					<th class="hidden-lg-down">{$group.name}</th>
				{/foreach}
				<th>{l s="Prix unitaire HT"}</th>
				<th>{l s="Quantité"}</th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$combinations key=id_combination item=combination}
				{assign var='loop_from_quantity' value=0}
				{assign var='loop_price' value=0}
				<tr>
					<td class="hidden-lg-up text-center">
						{if $combination.id_image > 0}
							<a href="#" class="display-image" data-image-id="{$combination.id_image}">
								<b>{$combination.reference}</b>
							</a>
						{else}
							<b>{$combination.reference}</b>
						{/if}
						{foreach from=$combination.attributes_values item=value}
							<br />{$value}
						{/foreach}
					</td>
					<td class="hidden-lg-down bold text-center">{$combination.reference}</td>
					{foreach from=$combination.attributes_values item=value}
						<td class="hidden-lg-down text-center">{$value}</td>
					{/foreach}
					{assign var=prices value=SpecificPrice::getByProductId($product.id_product, $id_combination)}
					{if $prices|count}
						<td class="text-center" style="padding:5px">
							<table id="prices_{$id_combination}" class="prices-table">
								{foreach from=$prices item=specific_price name=loop_prices}
									{if !$smarty.foreach.loop_prices.first}
										<tr class="specific_prices_{$id_combination} {if $smarty.foreach.loop_prices.iteration == 2}active{/if}" data-min="{$loop_from_quantity}" data-max="{$specific_price.from_quantity-1}" data-price="{$loop_price}">
											<td class="hidden-sm-down text-left">
												{$loop_from_quantity} {l s='à'} {$specific_price.from_quantity-1}
											</td>
											<td class="hidden-sm-down text-right">{Tools::displayPrice($loop_price)}</td>
											<td class="hidden-md-up text-center">
												{$loop_from_quantity} {l s='à'} {$specific_price.from_quantity-1}
												<br />
												{Tools::displayPrice($loop_price)}
												<hr />
											</td>
										</tr>
									{/if}
									{assign var='loop_from_quantity' value=$specific_price.from_quantity}
									{assign var='loop_price' value=$specific_price.price}
								{/foreach}
								{if $loop_price}
									<tr class="specific_prices_{$id_combination}" data-min='{$loop_from_quantity}' data-price="{$loop_price}">
										<td class="hidden-sm-down text-left">
											{$loop_from_quantity} {l s='et +'}
										</td>
										<td class="hidden-sm-down text-right">{Tools::displayPrice($loop_price)}</td>
										<td class="hidden-md-up text-center">
												{$loop_from_quantity} {l s='et +'}
												<br />
												{Tools::displayPrice($loop_price)}
											</td>
									</tr>
								{/if}
							</table>
						</td>
					{else}
						{assign var=combination_price value=Product::getPriceStatic($product.id_product, false, $id_combination)}
						<td class="specific_prices_{$id_combination} text-center text-primary" data-price="{$combination_price}">
							{Tools::displayPrice($combination_price)}
						</td>
					{/if}
					<td class="text-center">
						<div class="qty">
	          				<input type="text" name="qty" id="quantity_wanted_{$id_combination}" value="0" class="input-group combination-quantity" min="{$product.minimal_quantity}" data-id-combination="{$id_combination}" aria-label="{l s='Quantity' d='Shop.Theme.Actions'}">
	        			</div>
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
	<div class="well">
		<div class="row">
			<div id="prices_summary" class="col-xs-12 col-lg-9 text-center">
				<span id="total_price_selection" style="display:none"></span>
				<span id="total_price_selection_wt" style="display:none"></span>
			</div>
			<div class="col-xs-12 col-lg-3">
				<button type="button" id="add_all_to_cart" class="btn btn-block btn-success bold disabled" data-dismiss="modal">
					{l s='Add to cart' d='Shop.Theme.Actions'}
				</button>
			</div>
		</div>
	</div>

{/if}