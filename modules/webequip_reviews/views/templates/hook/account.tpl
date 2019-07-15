{extends file='customer/page.tpl'}

{block name='page_title'}
  {l s='Mes avis produits' d='Shop.Theme.Customeraccount'}
{/block}

{block name='page_content'}

	<table>
		<tbody>
			<tr>
				<td>
					<img src="/modules/webequip_reviews/logo.png">
				</td>
				<td class="description-cell">
					{l s="Vous retrouverez ici les produits que vous avez commandé afin de pouvoir laisser un avis."} <br />
					{l s="Vos retours sont importants pour nous alors n'hésitez pas à partager votre expérience."}
				</td>
			</tr>
		</tbody>
	</table>

	<div class="row">
		{foreach from=$reviews item=review}
			<form method="post">
				<input type="hidden" name="id_review" value="{$review->id}">
				<input type="hidden" name="review[id_product]" value="{$review->id_product}">
				<input type="hidden" name="review[id_shop]" value="{$review->id_shop}">
				<input type="hidden" name="review[name]" value="{$review->name}">
				<div class="col-lg-12">
					<div class="well margin-top-15">
						<h1>{$review->name}</h1>

						<div class="col-xs-12 col-lg-4">
							<div class="form-group">
								<select class="form-control" name="review[rating]">
									{for $x=0 to 5}
										<option value="{$x}" {if $review->rating == $x}selected{/if}>{$x} / 5</option>
									{/for}
								</select>
							</div>
						</div>
						<div class="form-group">
							<textarea rows="5" class="form-control" name="review[comment]" placeholder="{l s='Mon avis sur ce produit'}">{$review->comment}</textarea>
						</div>
						<div class="form-group text-right">
							<button type="submit" class="btn btn-success bold">
								Enregistrer
							</button>
						</div>
					</div>
				</div>
			</form>
		{/foreach}
	</div>
	<br />

{/block}