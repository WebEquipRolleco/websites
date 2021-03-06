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
				{if $order->internal_reference}
					<b>Numéro de commande du client</b> / Customer's order number :
					<div style="font-weight:bold; font-size:14pt; color:#1e4688">
						{$order->internal_reference}
					</div>
				{/if}
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
						{assign var='address' value=$order->getAddressDelivery()}
						<p><b>ADRESSE DE LIVRAISON</b> / DELIVERY ADDRESS</p>
						{if $address->company}
							{$address->company|upper}<br />
						{/if}
						{if $address->firstname || $address->lastname}
							{$address->firstname|upper} {$address->lastname|upper}<br />
						{/if}
						{if $address->address1}
							{$address->address1|upper}<br />
						{/if}
						{if $address->address2}
							{$address->address2|upper}<br />
						{/if}
						{if $address->postcode || $address->city}
							{$address->postcode|upper} {$address->city|upper}<br />
						{/if}
						{if $address->hasPhone()}
							{$address->phone} 
							{if $address->hasBothPhones()} / {/if}
							{$address->phone_mobile}
						{elseif $order->getAddressInvoice()->hasPhone()}
							{$order->getAddressInvoice()->phone} 
							{if $order->getAddressInvoice()->hasBothPhones()} / {/if}
							{$order->getAddressInvoice()->phone_mobile}
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
			<th style="text-align:center">
				{l s='Référence Fournisseur' pdf='true'}
			</th>
			<th style="text-align:center">
				{l s='Référence Webequip' pdf='true'}
			</th>
			<th style="text-align:center">
				{l s='Désignation' pdf='true'}
			</th>
			<th style="text-align:center">
				{l s='Qté' pdf='true'}
			</th>
			<th style="text-align:center">
				{l s='Prix unitaire' pdf='true'}
			</th>
			<th style="text-align:center">
				{l s='Port Total' pdf='true'}
			</th>
			<th style="text-align:center">
				{l s='Total HT' pdf='true'}
			</th>
		</tr>
	</thead>
	<tbody>
		{assign var=total value=0}
		{foreach $order->getDetails($oa->id_supplier) as $details}
			{assign var=row_price value=($details->purchase_supplier_price * $details->product_quantity)}
			{assign var=row_fees value=($details->delivery_fees * $details->product_quantity)}
			{assign var=row_total value=($row_price + $row_fees)}
			{assign var=total value=($total + $row_total)}
			<tr>
				<td style="text-align:center">
					<b>{$details->product_supplier_reference|default:'-'}</b>
				</td>
				<td style="text-align:center">
					<b>{$details->product_reference|default:'-'}</b>
				</td>
				<td style="text-align:center">
					{$details->getNameSansDelais()|replace:'||':'<br />'}
				</td>
				<td style="text-align:center">
					{$details->product_quantity}
				</td>
				<td style="text-align:center">
					{Tools::displayPrice($details->purchase_supplier_price)}
				</td>
				<td style="text-align:center">
					{Tools::displayPrice($row_fees)}
				</td>
				<td style="text-align:center">
					{Tools::displayPrice($row_total)}
				</td>
			</tr>
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