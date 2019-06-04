{if !empty($reviews)}
	<div id="customers_reviews">
		<h3 class="section-title top-space">
			{l s="Avis clients" mod="webequip_reviews"}
		</h3>
		<table id="table_reviews" class="table">
			<thead>
				<tr class="bg-grey">
					<td colspan="2" class="padding-left-20">
						<b>{$product_name}</b>
						<br />
						{include file="./stars.tpl"}
						<span>{$nb} {l s="Avis" mod="webequip_reviews"}</span>
					</td>
				</tr>
			</thead>
			<tbody>
				{foreach from=$reviews item=review}
					<tr>
						<td>
							{include file="./stars.tpl" rating=$review->rating}
							<div class="author">
								{l s="Par" mod="webequip_reviews"}
								{$review->getCustomer()->firstname|substr:0:1}.
								{$review->getCustomer()->lastname}
								<br />
								{l s="Le" mod="webequip_reviews"} {$review->date_add|date_format:'d/m/Y'}
							</div>
						</td>
						<td>
							{$review->comment}
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
{/if}