<div style="font-size: 9pt; color: #444">

	<table>
		<tr><td>&nbsp;</td></tr>
	</table>

	{* TITRE *}
	<table width="100%" cellpadding="5px" style="border-collapse: collapse; border:1px solid #4D4D4D">
		<thead>
			<tr style="background-color:#4D4D4D; color:#FFF;">
				<td style="text-align:center">
					<span style="font-size:14pt; font-weight:bold;">{l s='BON DE PREPARATION' pdf='true'}</span>
				</td>
			</tr>
			<tr>
				<td style="text-align:center">
					{$employee->firstname} <span style="text-transform:uppercase;">{$employee->lastname}</span>
				</td>
			</tr>
		</thead>
	</table>

	<table>
		<tr><td style="line-height: 6px">&nbsp;</td></tr>
	</table>

	{* COMMANDE *}
	<table width="100%" cellpadding="5px" style="border-collapse: collapse; border:1px solid #4D4D4D">
		<tr style="background-color:#4D4D4D; color:#FFF;">
			<td colspan="2" style="text-align:center">
				<span style="font-size:14pt; font-weight:bold;">{l s='Commande' pdf='true'} {$order->reference}</span>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center; background-color:{$order->getState()->color}; {if Tools::getBrightness($order->getState()->color) < 128}color:white;{/if}">
				<b>{$order->getState()->name}</b>
			</td>
		</tr>
		<tr>
			<td width="70%" style="border-right:1px solid #4D4D4D; border-bottom:1px solid #4D4D4D; text-align:center;">
				{foreach from=$order->getOrderPayments() item='payment'}
					{$payment->payment_method}
				{/foreach}
			</td>
			<td width="30%" style="border-right:1px solid #4D4D4D; border-bottom:1px solid #4D4D4D; text-align:center;">
				{$order->date_add|date_format:'d/m/Y à H:i:s'}
			</td>
		</tr>
	</table>

	<table>
		<tr><td style="line-height: 6px">&nbsp;</td></tr>
	</table>

	{* LIGNE INFOS *}
	<table>
		<tbody>
			<tr>

				{* CLIENT *}
				<td width="45%">
					<table width="100%" cellpadding="5px" style="border-collapse: collapse; border:1px solid #4D4D4D">
						<tr style="background-color:#4D4D4D; color:#FFF;">
							<td colspan="2" style="text-align:center">
								<span style="font-size:14pt; font-weight:bold;">{l s='Client' pdf='true'}</span>
							</td>
						</tr>
						<tr>
							<td style="border-right:1px solid #4D4D4D; border-bottom:1px solid #4D4D4D;">
								<b>{l s="E-mail"}</b>
							</td>
							<td style="padding-right:10px; border-bottom:1px solid #4D4D4D; text-align:right;">
								{$order->getCustomer()->email|default:"-"}
							</td>
						</tr>
						<tr>
							<td style="border-right:1px solid #4D4D4D; border-bottom:1px solid #4D4D4D;">
								<b>{l s="Référence E-deal"}</b>
							</td>
							<td style="padding-right:10px; border-bottom:1px solid #4D4D4D; text-align:right;">
								{$order->getCustomer()->reference|default:"-"}
							</td>
						</tr>
						<tr>
							<td style="border-right:1px solid #4D4D4D; border-bottom:1px solid #4D4D4D;">
								<b>{l s="Société"}</b>
							</td>
							<td style="padding-right:10px; border-bottom:1px solid #4D4D4D; text-align:right;">
								{$order->getCustomer()->company|default:"-"}
							</td>
						</tr>
						<tr>
							<td style="border-right:1px solid #4D4D4D; border-bottom:1px solid #4D4D4D;">
								<b>{l s="SIRET"}</b>
							</td>
							<td style="padding-right:10px; border-bottom:1px solid #4D4D4D; text-align:right;">
								{$order->getCustomer()->siret|default:"-"}
							</td>
						</tr>
						<tr>
							<td style="border-right:1px solid #4D4D4D; border-bottom:1px solid #4D4D4D;">
								<b>{l s="TVA interne"}</b>
							</td>
							<td style="padding-right:10px; border-bottom:1px solid #4D4D4D; text-align:right;">
								{$order->getCustomer()->tva|default:"-"}
							</td>
						</tr>
					</table>
				</td>

				<td width="10%"></td>

				{* INFORMATIONS *}
				<td width="45%">
					<table width="100%" cellpadding="5px" style="border-collapse: collapse; border:1px solid #4D4D4D">
						<tr style="background-color:#4D4D4D; color:#FFF;">
							<td colspan="2" style="text-align:center">
								<span style="font-size:14pt; font-weight:bold;">{l s='Informations' pdf='true'}</span>
							</td>
						</tr>
						<tr>
							<td width="10%" style="border-right:1px solid #4D4D4D; border-bottom:1px solid #4D4D4D;">
								&nbsp;
							</td>
							<td width="90%" style="border-bottom:1px solid #4D4D4D;">
								{l s='WEB00______' pdf='true'}
							</td>
						</tr>
						<tr>
							<td width="10%" style="border-right:1px solid #4D4D4D; border-bottom:1px solid #4D4D4D;">
								&nbsp;
							</td>
							<td width="90%" style="border-bottom:1px solid #4D4D4D;">
								{l s='Saisie tableau CA' pdf='true'}
							</td>
						</tr>
						<tr>
							<td width="10%" style="border-right:1px solid #4D4D4D; border-bottom:1px solid #4D4D4D;">
								&nbsp;
							</td>
							<td width="90%" style="border-bottom:1px solid #4D4D4D;">
								{l s='Référence M3 = 000711_____' pdf='true'}
							</td>
						</tr>
						<tr>
							<td width="10%" style="border-right:1px solid #4D4D4D; border-bottom:1px solid #4D4D4D;">
								&nbsp;
							</td>
							<td width="90%" style="border-bottom:1px solid #4D4D4D;">
								{l s="N° Ordre d'achat : 704_____" pdf='true'}
							</td>
						</tr>
						<tr>
							<td width="10%" style="border-right:1px solid #4D4D4D; border-bottom:1px solid #4D4D4D;">
								&nbsp;
							</td>
							<td width="90%" style="border-bottom:1px solid #4D4D4D;">
								{l s='Commande fournisseur' pdf='true'}
							</td>
						</tr>
						<tr>
							<td width="10%" style="border-right:1px solid #4D4D4D; border-bottom:1px solid #4D4D4D;">
								&nbsp;
							</td>
							<td width="90%" style="border-bottom:1px solid #4D4D4D;">
								{l s='Date de paiement : __ / __ / 20__' pdf='true'}
							</td>
						</tr>
						<tr>
							<td width="10%" style="border-right:1px solid #4D4D4D; border-bottom:1px solid #4D4D4D;">
								&nbsp;
							</td>
							<td width="90%" style="border-bottom:1px solid #4D4D4D;">
								{l s='Date remboursement : __ / __ / 20__' pdf='true'}
							</td>
						</tr>
						<tr>
							<td width="10%" style="border-right:1px solid #4D4D4D; border-bottom:1px solid #4D4D4D;">
								&nbsp;
							</td>
							<td width="90%" style="border-bottom:1px solid #4D4D4D;">
								{l s='Montant Remboursement : _____________________ €' pdf='true'}
							</td>
						</tr>
					</table>
				</td>

			</tr>
		</tbody>
	</table>

	<table>
		<tr><td style="line-height: 6px">&nbsp;</td></tr>
	</table>

	{* ADRESSES *}
	<table width="100%" cellpadding="5px" style="border-collapse: collapse; border:1px solid #4D4D4D">
		<thead>
			<tr style="background-color:#4D4D4D; color:#FFF;">
				<td style="text-align:center">
					<span style="font-size:14pt; font-weight:bold;">{l s='Adresse de facturation' pdf='true'}</span>
				</td>
				<td style="text-align:center">
					<span style="font-size:14pt; font-weight:bold;">{l s='Adresse de livraison' pdf='true'}</span>
				</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td style="border-right:1px solid #4D4D4D; text-align:center">
					{assign var='address' value=$order->getAddressInvoice()}
					{if $address}
						<b>{$address->alias|default:"-"}</b> <br /><br />
						{$address->firstname} <span style="text-transform:uppercase;">{$address->lastname}</span> <br />
						{$address->address1} <br />
						{if $address->address2}
							{$address->address2} <br />
						{/if}
						{$address->postcode} <span style="text-transform:uppercase;">{$address->city}</span> <br />
						{$address->country}
						{if $address->phone or $address->phone_mobile}
							<br /><br />
							{$address->phone} 
							{if $address->phone and $address->phone_mobile} / {/if}
							{$address->phone_mobile} 
						{/if}
					{/if}
				</td>
				<td style="text-align:center">
					{assign var='address' value=$order->getAddressDelivery()}
					{if $address}
						<b>{$address->alias|default:"-"}</b> <br /><br />
						{$address->firstname} <span style="text-transform:uppercase;">{$address->lastname}</span> <br />
						{$address->address1} <br />
						{if $address->address2}
							{$address->address2} <br />
						{/if}
						{$address->postcode} <span style="text-transform:uppercase;">{$address->city}</span> <br />
						{$address->country}
						{if $address->phone or $address->phone_mobile}
							<br /><br />
							{$address->phone} 
							{if $address->phone and $address->phone_mobile} / {/if}
							{$address->phone_mobile} 
						{/if}
					{/if}
				</td>
			</tr>
		</tbody>
	</table>

	<table>
		<tr><td style="line-height: 6px">&nbsp;</td></tr>
	</table>

	{* PRODUITS *}
	<table width="100%" cellpadding="5px" style="border-collapse: collapse; border:1px solid #4D4D4D">
		<thead>
			<tr style="background-color:#4D4D4D; color:#FFF;">
				<td>
					<span style="font-size:8pt; font-weight:bold;">{l s='Produit' pdf='true'}</span>
				</td>
				<td style="text-align:center">
					<span style="font-size:8pt; font-weight:bold;">{l s='Référence' pdf='true'}</span>
				</td>
				<td style="text-align:center">
					<span style="font-size:8pt; font-weight:bold;">{l s='PA' pdf='true'}</span>
				</td>
				<td style="text-align:center">
					<span style="font-size:8pt; font-weight:bold;">{l s='PU (HT)' pdf='true'}</span>
				</td>
				<td style="text-align:center">
					<span style="font-size:8pt; font-weight:bold;">{l s='Quantité' pdf='true'}</span>
				</td>
				<td style="text-align:center">
					<span style="font-size:8pt; font-weight:bold;">{l s='Total (HT)' pdf='true'}</span>
				</td>
				<td style="text-align:center">
					<span style="font-size:8pt; font-weight:bold;">{l s='Fournisseur' pdf='true'}</span>
				</td>
				<td style="text-align:center">
					<span style="font-size:8pt; font-weight:bold;">{l s='Commentaire' pdf='true'}</span>
				</td>
			</tr>
		</thead>
		<tbody>
			{foreach from=$order->getProducts() item='detail'}
				<tr>
					<td style="border-right:1px solid #4D4D4D; border-bottom:1px solid #4D4D4D;">
						{$detail.product_name}
					</td>
					<td style="border-right:1px solid #4D4D4D; border-bottom:1px solid #4D4D4D; text-align:center">
						{$detail.product_reference}
					</td>
					<td style="border-right:1px solid #4D4D4D; border-bottom:1px solid #4D4D4D; text-align:center">

					</td>
					<td style="border-right:1px solid #4D4D4D; border-bottom:1px solid #4D4D4D; text-align:center">
						{Tools::displayPrice($detail.unit_price_tax_excl)}
					</td>
					<td style="border-right:1px solid #4D4D4D; border-bottom:1px solid #4D4D4D; text-align:center">
						{$detail.product_quantity}
					</td>
					<td style="border-right:1px solid #4D4D4D; border-bottom:1px solid #4D4D4D; text-align:center">
						{Tools::displayPrice($detail.total_price_tax_excl)}
					</td>
					<td style="border-right:1px solid #4D4D4D; border-bottom:1px solid #4D4D4D; text-align:center">

					</td>
					<td style="border-bottom:1px solid #4D4D4D; text-align:center">

					</td>
				</tr>
			{/foreach}
			<tr>
				<td colspan="7" style="border-right:1px solid #4D4D4D; border-bottom:1px solid #4D4D4D; text-align:right; padding-right:10px">
					<span style="font-size:8pt; font-weight:bold;">{l s="Frais d'expédition" pdf='true'}</span>
				</td>
				<td style="border-bottom:1px solid #4D4D4D; text-align:right; padding-right:10px">

				</td>
			</tr>
			<tr>
				<td colspan="7" style="border-right:1px solid #4D4D4D; border-bottom:1px solid #4D4D4D; text-align:right; padding-right:10px">
					<span style="font-size:8pt; font-weight:bold;">{l s="Sous total HT" pdf='true'}</span>
				</td>
				<td style="border-bottom:1px solid #4D4D4D; text-align:right; padding-right:10px">

				</td>
			</tr>
			<tr>
				<td colspan="7" style="border-right:1px solid #4D4D4D; border-bottom:1px solid #4D4D4D; text-align:right; padding-right:10px">
					<span style="font-size:8pt; font-weight:bold;">{l s="TVA" pdf='true'}</span>
				</td>
				<td style="border-bottom:1px solid #4D4D4D; text-align:right; padding-right:10px">

				</td>
			</tr>
			<tr>
				<td colspan="7" style="border-right:1px solid #4D4D4D; text-align:right; padding-right:10px">
					<span style="font-size:8pt; font-weight:bold;">{l s="TOTAL TTC" pdf='true'}</span>
				</td>
				<td style="border-bottom:1px solid #4D4D4D; text-align:right; padding-right:10px">

				</td>
			</tr>
		</tbody>
	</table>

</div>