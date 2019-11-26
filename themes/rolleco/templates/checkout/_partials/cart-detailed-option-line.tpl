{if $option->display()}
	<tr>
		<td class="text-center">
			<form method="post">
				{if OrderOptionCart::hasAssociation(Context::getContext()->cart->id, $option->id)}
					<button type="submit" class="btn btn-link hvr-icon-pulse-grow" name="remove_option" value="{$option->id}" title="{l s='Retirer du panier' d='Shop.Theme.Checkout'}" style="color:black">
						<i class="far fa-2x fa-check-square hvr-icon"></i>
					</button>
				{else}
					<button type="submit" class="btn btn-link hvr-icon-pulse-grow" name="add_option" value="{$option->id}" title="{l s='Ajouter au panier' d='Shop.Theme.Checkout'}" style="color:black">
						<i class="far fa-2x fa-square hvr-icon"></i>
					</button>
				{/if}
			</form>
		</td>
		<td colspan="4">
			{if $option->description}
				<div><b>{$option->name}</b></div>
				<em class="text-muted">{$option->description}</em>
			{else}
				{$option->name}
			{/if}
		</td>
		<td colspan="2" class="text-center">
			<span class="product-price">
				<b>{Tools::displayPrice($option->getPrice())}</b>
			</span>
		</td>
	</tr>
{/if}