<table id="product_selection" class="table combinations-table margin-top-sm vertical-align">
	<thead>
		<tr>
			<th class="hidden-md-up">{l s="Produit"}</th>
			<th width="10%" class="hidden-sm-down">{l s="Réf."}</th>
			<th width="20%" class="hidden-sm-down">{l s="Dimensions"}</th>
			<th width="35%" class="hidden-sm-down">{l s="Commentaire"}</th>
			<th width="10%" class="hidden-sm-down">{l s="Délai"}</th>
			<th width="15%">{l s="Prix unitaire HT"}</th>
			<th width="10%">{l s="Quantité"}</th>
		</tr>
	</thead>
	<tbody>
		{* PRODUIT AVEC DECLINAISONS *}
		{if $combinations|count > 0}
			{foreach from=$combinations key=id_combination item=combination}
				{capture "dimensions"}
					{foreach from=Combination::loadColumn($id_combination, 1) item=row name=column_1}
						<b>{$row.name}.</b> {$row.value} {if !$smarty.foreach.column_1.last} x {/if}
					{/foreach}
				{/capture}
				{capture "comments"}
					{assign var=comments value=Combination::loadComments($id_combination)}
					{if $comments.comment_1}<div>{$comments.comment_1 nofilter}</div>{/if}
					{if $comments.comment_2}<div>{$comments.comment_2 nofilter}</div>{/if}
				{/capture}
				{capture "delivery"}
					{foreach from=Combination::loadColumn($id_combination, 2) item=row}
						{$row.value}
					{/foreach}
				{/capture}
				{if $combination.reference}
					<tr>
						<td class="hidden-md-up">
							<div><b>{l s="Référence"}</b></div>
							{$combination.reference}
							<div class="margin-top-10"><b>{l s="Dimensions"}</b></div>
							{$smarty.capture.dimensions nofilter}
							<div class="margin-top-10"><b>{l s="Délai"}</b></div>
							{$smarty.capture.delivery nofilter}
							{if $smarty.capture.comments}
								<div class="margin-top-10"><b>{l s="Commentaires"}</b></div>
								{$smarty.capture.comments}
							{/if}
						</td>
						<td class="text-center hidden-sm-down">
							{$combination.reference}
						</td>
						<td class="text-center hidden-sm-down">
							{$smarty.capture.dimensions nofilter}
						</td>
						<td class="hidden-sm-down">
							{$smarty.capture.comments nofilter}
						</td>
						<td class="text-center hidden-sm-down">
							{$smarty.capture.delivery nofilter}
						</td>
						<td class="text-center" style="padding:5px">
							{assign var=prices value=SpecificPrice::getByProductId($product.id_product, $id_combination)}
							<table id="prices_{$id_combination}" class="prices-table">
								{if $prices|count > 1}
									{assign var='loop_from_quantity' value=0}
									{assign var='loop_price' value=0}
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

								{else}
									{foreach from=$prices item=specific_price name=loop_prices}
										{assign var='loop_from_quantity' value=$specific_price.from_quantity}
										{assign var='loop_price' value=$specific_price.price}
										{assign var='loop_full_price' value=$specific_price.full_price}
										{if $loop_full_price > 0 and $loop_full_price > $loop_price}
											<tr>
												<td class="text-danger text-left bold">{Tools::getRate($loop_price, $loop_full_price)|string_format:"%.2f"}%</td>
												<td class="text-right" style="text-decoration:line-through;">{Tools::displayPrice($loop_full_price)}</td>
											</tr>
										{/if}
										<tr >
											<td colspan="2" class="specific_prices_{$id_combination} text-center text-info" data-price="{$specific_price.price}">
												{if $specific_price.from_quantity > 1}{$specific_price.from_quantity} {l s='et +'} &nbsp;&nbsp;&nbsp;&nbsp;{/if}
												{Tools::displayPrice($specific_price.price)}
											</td>
										</tr>
									{/foreach}
								{/if}
							</table>
						</td>
						<td class="text-center">
							<div class="qty">
		          				<input type="text" name="qty" id="quantity_wanted_{$id_combination}" data-step="{if $combination.batch}{$combination.batch}{else}1{/if}" value="0" class="input-group combination-quantity" min="{$product.minimal_quantity}" data-id-combination="{$id_combination}" aria-label="{l s='Quantity' d='Shop.Theme.Actions'}">
		        			</div>
						</td>
					</tr>
				{/if}
			{/foreach}
		{* PRODUIT SIMPLE *}
		{else}
			{capture "dimensions"}
				{foreach from=Product::loadColumn($product.id_product, 1) item=row name=column_1}
					<b>{$row.name}.</b> {$row.value} {if !$smarty.foreach.column_1.last} x {/if}
				{/foreach}
			{/capture}
			{capture "comments"}
				{if $product.comment_1}<div>{$product.comment_1 nofilter}</div>{/if}
				{if $product.comment_2}<div>{$product.comment_2 nofilter}</div>{/if}
			{/capture}
			{capture "delivery"}
				{foreach from=Product::loadColumn($product.id_product, 2) item=row}
					{$row.value}
				{/foreach}
			{/capture}
			<tr>
				<td class="hidden-md-up">
					<div><b>{l s="Référence"}</b></div>
					{$product.reference}
					<div class="margin-top-10"><b>{l s="Dimensions"}</b></div>
					{$smarty.capture.dimensions nofilter}
					<div class="margin-top-10"><b>{l s="Délai"}</b></div>
					{$smarty.capture.delivery nofilter}
					{if $smarty.capture.comments}
						<div class="margin-top-10"><b>{l s="Commentaires"}</b></div>
						{$smarty.capture.comments nofilter}
					{/if}
				</td>
				<td class="text-center hidden-sm-down">
					{$product.reference}
				</td>
				<td class="text-center hidden-sm-down">
					{$smarty.capture.dimensions nofilter}
				</td>
				<td class="hidden-sm-down">
					{$smarty.capture.comments nofilter}
				</td>
				<td class="text-center hidden-sm-down">
					{$smarty.capture.delivery nofilter}
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
									{if $loop_full_price > 0 and $loop_full_price > $loop_price}
										<tr class='active'>
											<td class="text-danger text-left bold">{Tools::getRate($loop_price, $loop_full_price)|string_format:"%.2f"}%</td>
											<td class="text-right" style="text-decoration:line-through;">{Tools::displayPrice($loop_full_price)}</td>
										</tr>
									{/if}
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
								{if $specific_price.full_price > 0 and $specific_price.full_price > $specific_price.price}
									<div class="text-center">
										<span class="text-danger bold">{Tools::getRate($specific_price.price, $specific_price.full_price)|string_format:"%.2f"}%</span>
										&nbsp;
										<span class="text-info" style="text-decoration:line-through;">{Tools::displayPrice($specific_price.full_price)}</span>
									</div>
								{/if}
								<div class="specific_prices_{$product.id_product} text-center text-info" data-price="{$specific_price.price}">
									{if $specific_price.from_quantity > 1}{$specific_price.from_quantity} {l s='et +'} &nbsp;&nbsp;&nbsp;&nbsp;{/if}
									{Tools::displayPrice($specific_price.price)}
								</div>
							{/foreach}
						{/if}
					</table>
				</td>
				<td class="text-center">
					<div class="qty">
	          			<input type="text" data-step="{if $product.batch}{$product.batch}{else}1{/if}" name="qty" id="quantity_wanted_{$product.id_product}" value="0" class="input-group combination-quantity" min="{$product.minimal_quantity}" data-id-product="{$product.id_product}" data-id-combination="{$product.id_product}" aria-label="{l s='Quantity' d='Shop.Theme.Actions'}">
	        		</div>
				</td>
			</tr>
		{/if}
	</tbody>
</table>

{* ACCESSOIRES *}
{block name='product_accessories'}
	{assign var=product_accessories value=Accessory::find($product.id_product)}
  	{if !empty($product_accessories)}
  		<h3 class="text-primary uppercase margin-top-sm">{l s="Accessoires"}</h3>
  		<table class="table combinations-table vertical-align">
  			<thead>
				<tr>
					<th class="hidden-md-up">{l s="Produit"}</th>
					<th width="10%" class="hidden-sm-down">{l s="Réf."}</th>
					<th width="25%" class="hidden-sm-down">{l s="Désignation"}</th>
					<th width="15%" class="hidden-sm-down">{l s="Dimensions"}</th>
					<th width="15%" class="hidden-sm-down">{l s="Commentaire"}</th>
					<th width="10%" class="hidden-sm-down">{l s="Délai"}</th>
					<th width="15%">{l s="Prix unitaire HT"}</th>
					<th width="10%">{l s="Quantité"}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$product_accessories item=accessory}
					{capture "dimensions"}
						{if $accessory->getCombination()}
							{foreach from=Combination::loadColumn($accessory->id_combination_accessory, 1) item=row name=column_1}
								<b>{$row.name}.</b> {$row.value} {if !$smarty.foreach.column_1.last} x {/if}
							{/foreach}
						{else}
							{foreach from=Product::loadColumn($accessory->id_product_accessory, 1) item=row name=column_1}
								<b>{$row.name}.</b> {$row.value} {if !$smarty.foreach.column_1.last} x {/if}
							{/foreach}
						{/if}
					{/capture}
					{capture name="comments"}
						{if $accessory->getTarget()->comment_1}<div>{$accessory->getTarget()->comment_1 nofilter}</div>{/if}
						{if $accessory->getTarget()->comment_2}<div>{$accessory->getTarget()->comment_2 nofilter}</div>{/if}
					{/capture}
					{capture "delivery"}
						{if $accessory->getCombination()}
							{foreach from=Combination::loadColumn($accessory->id_combination_accessory, 2) item=row}
								{$row.value}
							{/foreach}
						{else}
							{foreach from=Product::loadColumn($accessory->id_product_accessory, 2) item=row}
								{$row.value}
							{/foreach}
						{/if}
					{/capture}
					<tr>
						<td class="hidden-md-up">
							<div><b>{l s="Référence"}</b></div>
							{$accessory->getTarget()->reference}
							<div class="margin-top-10"><b>{l s="Désignation"}</b></div>
							{$accessory->getProduct()->name|replace:"|":"<br />"}
							{if $smarty.capture.dimensions}
								<div class="margin-top-10"><b>{l s="Dimensions"}</b></div>
								{$smarty.capture.dimensions nofilter}
							{/if}
							{if $smarty.capture.comments}
								<div class="margin-top-10"><b>{l s="Commentaires"}</b></div>
								{$smarty.capture.comments nofilter}
							{/if}
							{if $smarty.capture.delivery}
								<div class="margin-top-10"><b>{l s="Délai"}</b></div>
								{$smarty.capture.delivery nofilter}
							{/if}
						</td>
						<td class="hidden-sm-down text-center">{$accessory->getTarget()->reference}</td>
						<td class="hidden-sm-down">{$accessory->getProduct()->name}</td>
						<td class="hidden-sm-down text-center">
							{$smarty.capture.dimensions nofilter}
						</td>
						<td class="hidden-sm-down">
							{$smarty.capture.comments nofilter}
						</td>
						<td class="hidden-sm-down text-center">
							{$smarty.capture.delivery nofilter}
						</td>
						<td>
							<table id="prices_{$product.id_product}" class="prices-table">
								{assign var=prices value=SpecificPrice::getByProductId($accessory->id_product_accessory, $accessory->id_combination_accessory)}
								{if $prices|count > 1}
									{assign var='loop_from_quantity' value=0}
									{assign var='loop_price' value=0}
									{foreach from=$prices item=specific_price name=loop_prices}
										{if !$smarty.foreach.loop_prices.first}
											<tr class="specific_prices_{$accessory->getTarget()->id} {if $smarty.foreach.loop_prices.iteration == 2}active{/if}" data-min="{$loop_from_quantity}" data-max="{$specific_price.from_quantity-1}" data-price="{$loop_price}">
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
										<tr class="specific_prices_{$accessory->getTarget()->id}" data-min='{$loop_from_quantity}' data-price="{$loop_price}">
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
										<div class="specific_prices_{$accessory->getTarget()->id} text-center text-info" data-price="{$specific_price.price}">{Tools::displayPrice($specific_price.price)}</div>
									{/foreach}
								{/if}
							</table>
						</td>
						<td class="text-center">
							<div class="qty">
			          			<input type="text" name="qty" id="quantity_wanted_{$accessory->getTarget()->id}" value="0" class="input-group combination-quantity" min="{$accessory->getTarget()->minimal_quantity}" data-step="{$product.batch}" data-id-product="{$accessory->getTarget()->id}" data-id-combination="{$accessory->getTarget()->id}" aria-label="{l s='Quantity' d='Shop.Theme.Actions'}">
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
			<button type="button" id="add_all_to_cart" class="btn btn-block btn-success bold disabled" data-dismiss="modal" disabled>
				{l s='Add to cart' d='Shop.Theme.Actions'}
			</button>
		</div>
	</div>
</div>