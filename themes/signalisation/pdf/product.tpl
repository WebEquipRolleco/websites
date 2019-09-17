{$style_tab}

<table class="left-title">
	<thead>
		<tr>
			<th colspan="2">{$company.name}</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="title">{l s='Adresse' pdf=true}</td>
			<td>{$company.address_1} {$company.address_2}</td>
		</tr>
		<tr>
			<td class="title">{l s='Ville' pdf=true}</td>
			<td>{$company.code} {$company.city}</td>
		</tr>
		{if $company.email}
			<tr>
				<td class="title">{l s='E-mail' pdf=true}</td>
				<td>{$company.email}</td>
			</tr>
		{/if}
		{if $company.phone}
			<tr>
				<td class="title">{l s='Téléphone' pdf=true}</td>
				<td>{$company.phone}</td>
			</tr>
		{/if}
	</tbody>
</table>

<table class="product">
	<tbody>
		<tr>
			<td class="image">
				{assign var="cover" value=Image::getCover($product->id)}
				<img src="http://{$link->getImageLink($product->link_rewrite, $cover.id_image, 'large_default')}" style="width:200px" />
			</td>
		</tr>
		<tr>
			<td class="description">
				{$product->description nofilter}
			</td>
		</tr>
	</tbody>
</table>

<div class="page-break"></div>

<table class="combinations">
	<thead>
		<tr>
			<th><b>{l s="Référence" pdf='true'}</b></th>
			{foreach from=$groups item=group}
				<th style="text-align:center"><b>{$group.name}</b></th>
			{/foreach}
			<th style="text-align:center"><b>{l s="Prix" pdf='true'}</b></th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$combinations key=id_combination item=combination}
			<tr class="{cycle values='odd,even'}">
				<td><b>{$combination.reference}</b></td>
				{foreach from=$combination.attributes_values item=value}
					<td style="text-align:center">{$value}</td>
				{/foreach}
				{assign var=prices value=SpecificPrice::getByProductId($product->id, $id_combination)}
				{if $prices|count}
					<td style="text-align:center; padding:5px">
						<table>
							{foreach from=$prices item=specific_price name=loop_prices}
								{if !$smarty.foreach.loop_prices.first}
									<tr>
										<td style="text-align:center">
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
								<tr>
									<td style="text-align:center">
										{$loop_from_quantity} {l s='et +'}
										<br />
										{Tools::displayPrice($loop_price)}
									</td>
								</tr>
							{/if}
						</table>
					</td>
				{else}
					<td style="text-align:center">
						{Tools::displayPrice(Product::getPriceStatic($product->id, false, $id_combination))}
					</td>
				{/if}
			</tr>
		{/foreach}
	</tbody>
</table>
