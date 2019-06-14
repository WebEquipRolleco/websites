<div style="font-size: 9pt; color: #444">

<table>
	<tr><td>&nbsp;</td></tr>
</table>

{* TITRE *}
<table width="100%" cellpadding="5px" style="border-collapse: collapse; border:1px solid #4D4D4D">
	<thead>
		<tr style="background-color:#4D4D4D; color:#FFF;">
			<td  colspan="2" style="text-align:center">
				<span style="font-size:14pt; font-weight:bold;">{l s='BON DE COMMANDE' pdf='true'}</span>
				<br />
				<span style="font-size:10pt;">{l s='Order form' pdf='true'}</span>
			</td>
		</tr>
		<tr>
			<td width="60%" style="border-right:1px solid #4D4D4D; text-align:center">
				<b>Numéro de commande</b> / order number :
				<div style="font-weight:bold; font-size:14pt; color:#1e4688">
					{$order->reference} - {$oa->code}
				</div>
				<br />
				<b>Date de la commande</b> / order date :
				<div style="font-weight:bold">{'now'|date_format:'d/m/Y'}</div>
			</td>
			<td width="40%" style="text-align:center">
				<b>Fournisseur</b> / Supplier
				<p>
					{assign var=address value=Address::getAddressIdBySupplierId($oa->getSupplier()->id)}
					{$oa->getSupplier()->name} <br />
					{if $oa->getSupplier()->getAddress()->address1}
						{$oa->getSupplier()->getAddress()->address1} <br />
					{/if}
					{if $oa->getSupplier()->getAddress()->address2}
						{$oa->getSupplier()->getAddress()->address2} <br />
					{/if}
					{$oa->getSupplier()->getAddress()->postcode} {$oa->getSupplier()->getAddress()->city}
				</p>
			</td>
		</tr>
	</thead>
</table>

<table width="100%" cellpadding="10px">
	<tr>
		<td width="50%" style="text-align:center;">
			<table width="100%">
				<tr>
					<td style="text-align:center"><img src="{$img_ps_dir}/arrow.png" style="height:15px"></td>
					<td style="text-align:center"><img src="{$img_ps_dir}/arrow.png" style="height:15px"></td>
				</tr>
			</table>
			<table width="100%" cellpadding="10px" style="border:6px solid darkorange;">
				<tr>
					<td>
						<p><b>ADRESSE DE LIVRAISON</b> / DELIVERY ADDRESS</p>
						{if $order->getDeliveryAddress()->company}
							{$order->getDeliveryAddress()->company|upper}<br />
						{/if}
						{if $order->getDeliveryAddress()->firstname || $order->getDeliveryAddress()->lastname}
							{$order->getDeliveryAddress()->lastname|upper} {$order->getDeliveryAddress()->firstname|upper}<br />
						{/if}
						{if $order->getDeliveryAddress()->address1}
							{$order->getDeliveryAddress()->address1|upper}<br />
						{/if}
						{if $order->getDeliveryAddress()->address2}
							{$order->getDeliveryAddress()->address2|upper}<br />
						{/if}
						{if $order->getDeliveryAddress()->postcode || $order->getDeliveryAddress()->city}
							{$order->getDeliveryAddress()->postcode|upper} {$order->getDeliveryAddress()->city|upper}<br />
						{/if}
						{if $order->getDeliveryAddress()->hasPhone()}
							{$order->getDeliveryAddress()->phone} 
							{if $order->getDeliveryAddress()->hasBothPhones()} / {/if}
							{$order->getDeliveryAddress()->phone_mobile}
						{elseif $order->getInvoiceAddress()->hasPhone()}
							{$order->getInvoiceAddress()->phone} 
							{if $order->getInvoiceAddress()->hasBothPhones()} / {/if}
							{$order->getInvoiceAddress()->phone_mobile}
						{/if}
					</td>
				</tr>
			</table>
		</td>
		<td width="50%" style="text-align:center;">
			{if $order->delivery_information}
				<table width="100%">
					<tr>
						<td style="text-align:center;height:15px">&nbsp;</td>
					</tr>
				</table>
				<table width="99%" cellpadding="10px" style="border:1px solid grey; background-color:powderblue;">
					<tr>
						<td>
							<span style="color:red; font-weight:bold">{l s='CONTACT ET INFORMATIONS LIVRAISON' pdf='true'}</span>
							<br />{l s='name and phone of the delivery receiver' pdf='true'}
						</td>
					</tr>
					<tr>
						<td style="font-weight:bold">{$order->delivery_information|replace:'|':'<br />'}</td>
					</tr>
				</table>
			{/if}
		</td>
	</tr>
</table>

<table>
	<tr><td style="line-height: 6px">&nbsp;</td></tr>
</table>

{if $order->supplier_information}
	<table style="border:1px solid black" cellpadding="10px">
		<tr>
			<td style="text-align:center">
				<span style="font-weight:bold; color:red">
					MESSAGE IMPORTANT DE WEB EQUIP AU FOURNISSEUR
				</span>
				<br />
				IMPORTANT MESSAGE FROM WEB EQUIP TO SUPPLIER
				<p><b style="background-color:yellow">{$order->supplier_information|upper|replace:'|':'<br />'}</b></p>
			</td>
		</tr>
	</table>
{/if}

<table width="100%" cellpadding="10px">
	<tr>
		<td style="text-align:center">
			<strong>ATTENTION :</strong> {l s='Merci de vérifier les informations ci-dessous et nous contacter en cas d’erreur' pdf='true'}
		</td>
	</tr>
</table>

{* PRODUITS *}
<table width="100%" cellpadding="5px" style="border:1px solid #4D4D4D">
	<thead>
		<tr style="background-color:#4D4D4D; color:#FFF; font-weight:bold;">
			<th width="13%" style="text-align:center">
				{l s='Référence Fournisseur' pdf='true'}
			</th>
			<th width="13%" style="text-align:center">
				{l s='Référence Webequip' pdf='true'}
			</th>
			<th width="35%" style="text-align:center">
				{l s='Désignation' pdf='true'}
			</th>
			<th width="6%" style="text-align:center">
				{l s='Qté' pdf='true'}
			</th>
			<th width="11%" style="text-align:center">
				{l s='Prix unitaire' pdf='true'}
			</th>
			<th width="11%" style="text-align:center">
				{l s='Port Total' pdf='true'}
			</th>
			<th width="11%" style="text-align:center">
				{l s='Total HT' pdf='true'}
			</th>
		</tr>
	</thead>
	<tbody>
		{assign var=total value=0}
		{foreach $order->getProducts() as $product}
			{if ($product.override_id_supplier && $product.override_id_supplier == $oa->getSupplier()->id) || (!$product.override_id_supplier && $product.supplier_name == $oa->getSupplier()->name)}
				{if !$product.product_supplier_reference|strstr:'option_'}
					{assign var=details value=$product.specific_price|unserialize}
					{assign var=total value=$total + ($details.purchasing_price * $product.product_quantity)}
					<tr>
						<td width="13%" style="text-align:center">
							<strong>{$product.product_supplier_reference}</strong>
						</td>
						<td width="13%" style="text-align:center">
							<strong>{$product.product_reference}</strong>
						</td>
						<td width="35%" style="text-align:center">
							{$product.product_name|replace:'||':'<br />'} 
						</td>
						<td width="6%" style="text-align:center">
							{$product.product_quantity}
						</td>
						<td width="11%" style="text-align:center">
							{Tools::displayPrice($details.purchasing_price_ws)}
						</td>
						<td width="11%" style="text-align:center">
							{Tools::displayPrice($details.shipping_price * $product.product_quantity)}
						</td>
						<td width="11%" style="text-align:center">
							{Tools::displayPrice($details.purchasing_price * $product.product_quantity)}
						</td>
					</tr>
				{/if}
			{/if}
		{/foreach}
	</tbody>
	<tfoot>
		<tr style="background-color:#4D4D4D; color:#FFF; font-weight:bold;">
			<td colspan="6" style="text-align:right; padding-right:15px">
				{l s="Total"|upper}
			</td>
			<td style="text-align:center">
				{Tools::displayPrice($total)}
			</td>
		</tr>
	</tfoot>	
</table>

</div>