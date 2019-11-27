{assign var=left_column value=50}
{assign var=right_column value=45}
{assign var=space_column value=(100 - $left_column - $right_column)}

<div style="font-size: 9pt; color: #444">

	<table>
		<tr><td style="text-align:center;font-weight:bold; font-size:20px;">{$order->reference}</td></tr>
	</table>

	<table>
		<tr><td style="line-height: 6px">&nbsp;</td></tr>
	</table>

	<table width="100%">
		<tr>

			{* COLONNE GAUCHE *}
			<td width="{$left_column}%">
				<table width="100%" cellpadding="4">
					<tr style="background-color:cornsilk;">
						<td colspan="2" style="border-bottom:1px solid black; text-align:center; font-weight:bold; font-size:14px">
							{l s="INFORMATION COMMANDE" d='Shop.Pdf' pdf=true}
						</td>
					</tr>
					<tr>
						<td style="text-align:center; border-bottom:1px solid black;">
							<b>{l s="Date de la commande :" d='Shop.Pdf' pdf=true}</b>
							<br />
							{$order->date_add|date_format:'d/m/Y à H:i'}
						</td>
						<td style="text-align:center; border-bottom:1px solid black;">
							<b>{l s="Type de paiement :" d='Shop.Pdf' pdf=true}</b>
							<br />
							{foreach from=$order->getOrderPayments() item='payment'}
								{$payment->payment_method}
							{/foreach}
						</td>
					</tr>
					<tr>
						<td style="text-align:center">
							{l s="Numéro de client :" d='Shop.Pdf' pdf=true}
							<br />
							<span style="font-size:14px; font-weight:bold; color:red">{$order->id_customer}</span>
						</td>
						<td style="text-align:center">
							{l s="Numéro de panier :" d='Shop.Pdf' pdf=true}
							<br />
							<span style="font-size:12px; font-weight:bold; color:black">{$order->id_cart}</span>
						</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr style="background-color:lightblue;">
						<td style="text-align:center; font-weight:bold;">
							{l s="ADRESSE DE FACTURATION" d='Shop.Pdf' pdf=true}
						</td>
						<td style="text-align:center; font-weight:bold;">
							{l s="ADRESSE DE LIVRAISON" d='Shop.Pdf' pdf=true}
						</td>
					</tr>
					<tr>
						<td style="text-align:center; font-size:8px">
							{assign var='address' value=$order->getAddressInvoice()}
							{if $address}
								{if $address->company}{$address->company} <br />{/if}
								{if $order->getCustomer()->siret}{l s="Siret :" d='Shop.Pdf' pdf=true} {$order->getCustomer()->siret} <br />{/if}
								{if $order->getCustomer()->tva}{l s="TVA intra :" d='Shop.Pdf' pdf=true} {$order->getCustomer()->tva} <br />{/if}
								{$address->lastname} {$address->firstname} <br />
								{if $address->address1}{$address->address1} <br />{/if}
								{if $address->address2}{$address->address2} <br />{/if}
								{$address->postcode} {$address->city} <br />
								{if $address->country}{$address->country} <br />{/if}
								{if $address->phone}{l s="Tél :" d='Shop.Pdf' pdf=true} {$address->phone}{/if}
							{/if}
						</td>
						<td style="text-align:center; font-size:8px">
							{assign var='address' value=$order->getAddressDelivery()}
							{if $address}
								{if $address->company}{$address->company} <br />{/if}
								{$address->lastname} {$address->firstname} <br />
								{if $address->address1}{$address->address1} <br />{/if}
								{if $address->address2}{$address->address2} <br />{/if}
								{$address->postcode} {$address->city} <br />
								{if $address->country}{$address->country} <br />{/if}
								{if $address->phone}{l s="Tél :" d='Shop.Pdf' pdf=true} {$address->phone} <br />{/if}
								{$order->delivery_information}
							{/if}
						</td>
					</tr>
				</table>
			</td>

			{* ESPACE *}
			<td width="{$space_column}%"></td>

			{* COLONNE DROITE *}
			<td width="{$right_column}%">
				<table width="100%" border="1" cellpadding="8px">
					<tr style="background-color:thistle">
						<td colspan="2" style="text-align:center; font-weight:bold; font-size:14px">
							{l s="ETAPES SAISIE" d='Shop.Pdf' pdf=true}
						</td>
					</tr>
					<tr>
						<td width="8%"></td>
						<td width="92%">
							{l s="N° Edeal :" d='Shop.Pdf' pdf=true} 
							{if {$order->getCustomer()->reference}}<b style="font-size:14px">{$order->getCustomer()->reference}</b>{/if}
						</td>
					</tr>
					<tr>
						<td width="8%"></td>
						<td width="92%">{l s="Vérif Prestashop" d='Shop.Pdf' pdf=true}</td>
					</tr>
					<tr>
						<td width="8%"></td>
						<td width="92%">{l s="N° cmde M3 : %s............" sprintf=[Configuration::get('PS_SHOP_PREFIX_M3', null, $order->id_shop)] d='Shop.Pdf' pdf=true}</td>
					</tr>
					<tr>
						<td width="8%"></td>
						<td width="92%">{l s="N° OA : %s............" sprintf=[Configuration::get('PS_SHOP_PREFIX_OA', null, $order->id_shop)] d='Shop.Pdf' pdf=true}</td>
					</tr>
					<tr>
						<td width="8%"></td>
						<td width="92%">{l s="Envoi commande fournisseur" d='Shop.Pdf' pdf=true}</td>
					</tr>
					<tr>
						<td width="8%"></td>
						<td width="92%">{l s="Date de paiement : ......./......./%s" sprintf=['now'|date_format:'Y'] d='Shop.Pdf' pdf=true}</td>
					</tr>
					<tr>
						<td width="8%"></td>
						<td width="92%">{l s="Date de remboursement : ......./......./20......" d='Shop.Pdf' pdf=true}</td>
					</tr>
					<tr>
						<td width="8%"></td>
						<td width="92%">{l s="Montant remboursé : ......................€" d='Shop.Pdf' pdf=true}</td>
					</tr>
				</table>
			</td>

		</tr>
	</table>

	<table cellpadding="3px">
		<tr><td>&nbsp;</td></tr>
	</table>

	{* PRODUITS *}
	<table width="100%" border="1" cellpadding="4px">
		<thead>
			<tr style="background-color:cornsilk;">
				<td width="20%" style="text-align:center">
					<span style="font-size:8pt; font-weight:bold;">{l s='Nom' d='Shop.Pdf' pdf=true}</span>
				</td>
				<td width="10%" style="text-align:center">
					<span style="font-size:8pt; font-weight:bold;">{l s='Réf.' d='Shop.Pdf' pdf=true}</span>
				</td>
				<td width="10%" style="text-align:center">
					<span style="font-size:8pt; font-weight:bold;">{l s="Prix d'achat" d='Shop.Pdf' pdf=true}</span>
				</td>
				<td width="10%" style="text-align:center">
					<span style="font-size:8pt; font-weight:bold;">{l s='Prix unitaire (HT)' d='Shop.Pdf' pdf=true}</span>
				</td>
				<td width="5%" style="text-align:center">
					<span style="font-size:8pt; font-weight:bold;">{l s='Qté' d='Shop.Pdf' pdf=true}</span>
				</td>
				<td width="10%" style="text-align:center">
					<span style="font-size:8pt; font-weight:bold;">{l s='Total (HT)' d='Shop.Pdf' pdf=true}</span>
				</td>
				<td width="20%" style="text-align:center">
					<span style="font-size:8pt; font-weight:bold;">{l s='Fournisseur' d='Shop.Pdf' pdf=true}</span>
				</td>
				<td width="15%" style="text-align:center">
					<span style="font-size:8pt; font-weight:bold;">{l s='Commentaire' d='Shop.Pdf' pdf=true}</span>
				</td>
			</tr>
		</thead>
		<tbody>
			{foreach from=$order->getDetails() item='detail'}
				<tr>
					<td width="20%">
						<b>{$detail->product_name}</b>
						{if $detail->comment}
							<br /><br /> {$detail->comment}
						{/if}
					</td>
					<td width="10%" style="text-align:center">
						{$detail->product_reference}
					</td>
					<td width="10%" style="text-align:center">
						{Tools::displayPrice($detail->getTotalBuyingPrice())}
						<br />
						<div style="font-size:8px">
							{l s="PA :" d='Shop.Pdf' pdf=true} {Tools::displayPrice($detail->purchase_supplier_price)} <br />
							{l s="Port :" d="Shop.Pdf" pdf=true} {Tools::displayPrice($detail->total_shipping_price_tax_excl / $detail->product_quantity)} <br />
							<i style="font-size:7px; color:green">{Tools::displayPrice($detail->ecotax)}</i>
						</div>
					</td>
					<td width="10%" style="text-align:center">
						{Tools::displayPrice($detail->unit_price_tax_excl)}
						<br /><br />
						<i style="font-size:7px; color:green">{Tools::displayPrice($detail->ecotax / $detail->product_quantity)}</i>
					</td>
					<td width="5%" style="text-align:center">
						{$detail->product_quantity}
					</td>
					<td width="10%" style="text-align:center">
						{Tools::displayPrice($detail->total_price_tax_excl)}
						<br /><br />
						<i style="font-size:7px; color:green">{Tools::displayPrice($detail->ecotax)}</i>
					</td>
					<td width="20%" style="text-align:center">
						{if $detail->getSupplier()}
							<div style="font-size:8px">
								{$detail->getSupplier()->reference} {$detail->getSupplier()->name} 
							</div><br />
						{/if}
						{$detail->product_supplier_reference}
						{if $detail->comment_product_1 || $detail->comment_product_2}
							<br /><br />
							{if $detail->comment_product_1}{$detail->comment_product_1} <br />{/if}
							{if $detail->comment_product_2}{$detail->comment_product_2}{/if}
						{/if}
					</td>
					<td width="15%" style="text-align:center">
						{if $detail->getQuotationLine()}
							{$detail->getQuotationLine()->comment}
						{/if}
					</td>
				</tr>
			{/foreach}
			<tr>
				<td colspan="7" style="text-align:right;">
					<span style="font-size:8pt; font-weight:bold;">{l s="Frais d'expédition" pdf='true'}</span>
				</td>
				<td style="text-align:center;">
					{Tools::displayPrice($order->getDeliveryPrice())}
				</td>
			</tr>
			<tr>
				<td colspan="7" style="text-align:right;">
					<span style="font-size:8pt; font-weight:bold;">{l s="Sous total HT" pdf='true'}</span>
				</td>
				<td style="text-align:center;">
					{Tools::displayPrice($order->total_paid_tax_excl)}
				</td>
			</tr>
			<tr>
				<td colspan="7" style="text-align:right;">
					<span style="font-size:8pt; font-weight:bold;">{l s="TVA" pdf='true'}</span>
				</td>
				<td style="text-align:center;">
					{Tools::displayPrice($order->total_paid_tax_incl - $order->total_paid_tax_excl)}
				</td>
			</tr>
			<tr>
				<td colspan="7" style="text-align:right;">
					<span style="font-size:8pt; font-weight:bold;">{l s="TOTAL TTC" pdf='true'}</span>
				</td>
				<td style="text-align:center;">
					<b>{Tools::displayPrice($order->total_paid_tax_incl)}</b>
				</td>
			</tr>
		</tbody>
	</table>

</div>