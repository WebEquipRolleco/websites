<table id="product_selection" class="table combinations-table vertical-align">
	<thead>
		<tr>
			<th width="10%">{l s="Réf."}</th>
			<th width="20%">{l s="Dimensions"}</th>
			<th width="35%">{l s="Commentaire"}</th>
			<th width="10%">{l s="Délai"}</th>
			<th width="15%">{l s="Prix unitaire HT"}</th>
			<th width="10%">{l s="Quantité"}</th>
		</tr>
	</thead>
	<tbody>
		{* PRODUIT AVEC DECLINAISONS *}
		{if $combinations|count > 0}
			{foreach from=$combinations key=id_combination item=combination}
				<tr>
					<td class="text-center">
						{$combination.reference}
					</td>
					<td class="text-center">
						{foreach from=Combination::loadColumn($id_combination, 1) item=row name=column_1}
							<b>{$row.name}.</b> {$row.value} {if !$smarty.foreach.column_1.last} x {/if}
						{/foreach}
					</td>
					<td>
						{assign var=comments value=Combination::loadComments($id_combination)}
						{if $comments.comment_1}<div>{$comments.comment_1}</div>{/if}
						{if $comments.comment_2}<div>{$comments.comment_2}</div>{/if}
					</td>
					<td class="text-center">
						{foreach from=Combination::loadColumn($id_combination, 2) item=row}
							{$row.value} 
						{/foreach}
					</td>
					<td class="text-center" style="padding:5px">
						{assign var=prices value=SpecificPrice::getByProductId($product.id_product, $id_combination)}
						{if $prices|count > 1}
							{assign var='loop_from_quantity' value=0}
							{assign var='loop_price' value=0}
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
						{else}
							{foreach from=$prices item=specific_price}
								<span class="specific_prices_{$id_combination} text-info" data-price="{$specific_price.price}">{Tools::displayPrice($specific_price.price)}</span>
							{/foreach}
						{/if}
					</td>
					<td class="text-center">
						<div class="qty">
	          				<input type="text" name="qty" id="quantity_wanted_{$id_combination}" data-step="{$combination.batch}" value="0" class="input-group combination-quantity" min="{$product.minimal_quantity}" data-id-combination="{$id_combination}" aria-label="{l s='Quantity' d='Shop.Theme.Actions'}">
	        			</div>
					</td>
				</tr>
			{/foreach}
		{* PRODUIT SIMPLE *}
		{else}
			<tr>
				<td class="text-center">{$product.reference}</td>
				<td class="text-center">
					{foreach from=Product::loadColumn($product.id_product, 1) item=row name=column_1}
						<b>{$row.name}.</b> {$row.value} {if !$smarty.foreach.column_1.last} x {/if}
					{/foreach}
				</td>	
				<td>
					{if $product.comment_1}<div>{$product.comment_1}</div>{/if}
					{if $product.comment_2}<div>{$product.comment_2}</div>{/if}
				</td>
				<td class="text-center">
					{foreach from=Product::loadColumn($product.id_product, 2) item=row}
						{$row.value} 
					{/foreach}
				</td>
				<td>
					<table id="prices_{$product.id_product}" class="prices-table">
						{assign var=prices value=SpecificPrice::getByProductId($product.id_product)}
						{if $prices|count > 1}
							{assign var=loop_from_quantity value=0}
							{assign var=loop_price value=0}
							{assign var=loop_full_price value=0}
							{foreach from=$prices item=specific_price name=loop_prices}
								{if !$smarty.foreach.loop_prices.first}
									<tr class="specific_prices_{$product.id_product} {if $smarty.foreach.loop_prices.iteration == 2}active{/if}" data-min="{$loop_from_quantity}" data-max="{$specific_price.from_quantity-1}" data-price="{$loop_price}">
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
									{if $loop_full_price > 0}
										<tr class='active'>
											<td class="text-danger text-left bold">{Tools::getRate($loop_price, $loop_full_price)}%</td>
											<td class="text-right" style="text-decoration:line-through;">{Tools::displayPrice($loop_full_price)}</td>
										</tr>
									{/if}
								{/if}
								{assign var=loop_from_quantity value=$specific_price.from_quantity}
								{assign var=loop_price value=$specific_price.price}
								{assign var=loop_full_price value=$specific_price.full_price}
							{/foreach}
							{if $loop_price}
								<tr class="specific_prices_{$product.id_product}" data-min='{$loop_from_quantity}' data-price="{$loop_price}">
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
						{else}
							{foreach from=$prices item=specific_price}
								<div class="specific_prices_{$product.id_product} text-center text-info" data-price="{$specific_price.price}">{Tools::displayPrice($specific_price.price)}</div>
								{if $specific_price.full_price > 0}
									<div class="text-center">
										<span class="text-danger bold">{Tools::getRate($specific_price.price, $specific_price.full_price)}%</span>
										&nbsp;
										<span class="text-info" style="text-decoration:line-through;">{Tools::displayPrice($specific_price.full_price)}</span>
									</div>
								{/if}
							{/foreach}
						{/if}
					</table>
				</td>
				<td class="text-center">
					<div class="qty">
	          			<input type="text" data-step="{$product.batch}" name="qty" id="quantity_wanted_{$product.id_product}" value="0" class="input-group combination-quantity" min="{$product.minimal_quantity}" data-id-product="{$product.id_product}" data-id-combination="{$product.id_product}" aria-label="{l s='Quantity' d='Shop.Theme.Actions'}">
	        		</div>
				</td>
			</tr>
		{/if}
	</tbody>
</table>

{* ACCESSOIRES *}
{block name='product_accessories'}
  	{if $accessories}
  		<h3 class="text-primary uppercase margin-top-sm">{l s="Accessoires"}</h3>
  		<table class="table combinations-table vertical-align">
  			<thead>
				<tr>
					<th width="10%">{l s="Réf."}</th>
					<th width="25%">{l s="Désignation"}</th>
					<th width="15%">{l s="Dimensions"}</th>
					<th width="15%">{l s="Commentaire"}</th>
					<th width="10%">{l s="Délai"}</th>
					<th width="15%">{l s="Prix unitaire HT"}</th>
					<th width="10%">{l s="Quantité"}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$accessories item=product}
					<tr>
						<td class="text-center">{$product.reference}</td>
						<td>{$product.name}</td>
						<td class="text-center">
							{foreach from=Product::loadColumn($product.id_product, 1) item=row name=column_1}
								<b>{$row.name}.</b> {$row.value} {if !$smarty.foreach.column_1.last} x {/if}
							{/foreach}
						</td>
						<td>
							{if $product.comment_1}<div>{$product.comment_1}</div>{/if}
							{if $product.comment_2}<div>{$product.comment_2}</div>{/if}
						</td>
						<td class="text-center">
							{foreach from=Product::loadColumn($product.id_product, 2) item=row}
								{$row.value} 
							{/foreach}
						</td>
						<td>
							<table id="prices_{$product.id_product}" class="prices-table">
								{assign var=prices value=SpecificPrice::getByProductId($product.id_product)}
								{if $prices|count > 1}
									{assign var='loop_from_quantity' value=0}
									{assign var='loop_price' value=0}
									{foreach from=$prices item=specific_price name=loop_prices}
										{if !$smarty.foreach.loop_prices.first}
											<tr class="specific_prices_{$product.id_product} {if $smarty.foreach.loop_prices.iteration == 2}active{/if}" data-min="{$loop_from_quantity}" data-max="{$specific_price.from_quantity-1}" data-price="{$loop_price}">
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
										<tr class="specific_prices_{$product.id_product}" data-min='{$loop_from_quantity}' data-price="{$loop_price}">
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
								{else}
									{foreach from=$prices item=specific_price}
										<div class="specific_prices_{$product.id_product} text-center text-info" data-price="{$specific_price.price}">{Tools::displayPrice($specific_price.price)}</div>
									{/foreach}
								{/if}
							</table>
						</td>
						<td class="text-center">
							<div class="qty">
			          			<input type="text" name="qty" id="quantity_wanted_{$product.id_product}" value="0" class="input-group combination-quantity" min="{$product.minimal_quantity}" data-step="{$product.batch}" data-id-product="{$product.id_product}" data-id-combination="{$product.id_product}" aria-label="{l s='Quantity' d='Shop.Theme.Actions'}">
			        		</div>
						</td>
					</tr>
				{/foreach}
			</tbody>
  		</table>
    {/if}
{/block}

<div class="well margin-top-sm">
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