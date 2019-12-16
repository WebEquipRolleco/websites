<div class="col-md-12">
	<div class="row">
		<div class="col-md-12">
			<h2>{l s="Gestion Web-équip" mod='webequip_configuration'}</h2>
		</div>
		<div class="col-xl-2 col-lg-3 form-group">
			<label form="batch" class="form-control-label">{l s="Lot" mod='webequip_configuration'}</label>
	        <input type="text" id="batch" name="batch" class="form-control" value="{$product->batch}">
		</div>
		<div class="col-xl-2 col-lg-3 form-group">
			<label form="rollcash" class="form-control-label">{l s="Rollcash" mod='webequip_configuration'}</label>
			<div class="input-group money-type">
                <input type="text" id="rollcash" name="rollcash" class="form-control" value="{$product->rollcash}">
              <div class="input-group-append">
                <span class="input-group-text"> %</span>
            </div>
            </div>
		</div>
	</div>
</div>

<div class="col-md-12">
	<div class="row">
		<div class="col-md-12">
			<h2>{l s="Gestion des informations prix" mod='webequip_configuration'}</h2>
			<em class="text-muted">/!\ {l s="Les informations produits sont modifiables uniquement via les imports"} /!\</em>
		</div>
		<div class="col-md-12">
			<table id="table_prices" class="table mt-3">
				<thead class="thead-default">
					<tr class="thead-default">
						<th>{l s="Produit"}</th>
						<th class="text-center">{l s="Quantité"}</th>
						<th class="text-center">{l s="Prix d'achat"}</th>
						<th class="text-center">{l s="Frais de ports"}</th>
						<th class="text-center">{l s="Prix de vente"}</th>
						<th class="text-center">{l s="Commentaire 1"}</th>
						<th class="text-center">{l s="Commentaire 2"}</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$prices item=price}
						<tr>
							<td width="20%">
								<div><b>{l s="Référence : "}</b> {$price->getProduct()->reference|default:'-'}</div>

									<div><b>{l s="Déclinaison : "}</b> {$price->getCombination()->reference|default:'-'}</div>

								{if $price->getProduct()->getSupplier()}
									<br />
									<div><b>{l s="Fournisseur : "}</b> {$price->getProduct()->getSupplier()->name|default:'-'}</div>
									<div><b>{l s="Référence : "}</b> {Product::getSupplierReference($price->id_product, $price->id_product_attribute)|default:'-'}</div>
								{/if}
							</td>
							<td class="text-center">{$price->from_quantity}</td>
							<td><input type="text" class="form-control" name="prices[{$price->id}][buying_price]" value="{$price->buying_price}"></td>
							<td><input type="text" class="form-control" name="prices[{$price->id}][delivery_fees]" value="{$price->delivery_fees}"></td>
							<td><input type="text" class="form-control" name="prices[{$price->id}][price]" value="{$price->price}"></td>
							<td><input type="text" class="form-control" name="prices[{$price->id}][comment_1]" value="{$price->comment_1}"></td>
							<td><input type="text" class="form-control" name="prices[{$price->id}][comment_2]" value="{$price->comment_2}"></td>
						</tr>
					{/foreach}
				</tbody>
				<tfoot>
					<tr>
						<td colspan="7" class="text-right">
							<button type="submit" id="save_prices" class="btn btn-success">
								<b>{l s="Enregistrer les prix"}</b>
							</button>
						</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {

		$('#save_prices').on('click', function() {
			$.post( {
					url: "{$link->getAdminLink('AdminModules')}&configure=webequip_configuration",
					data: { ajax:true, action:'save_prices', form:$('#table_prices :input').serialize() },
					dataType: "json",
					success : function(response) {

				}
			});
		});
	});
</script>