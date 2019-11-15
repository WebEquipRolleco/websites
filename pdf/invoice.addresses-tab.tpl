<table width="100%" cellpadding="3px">
	<tr>
		<td colspan="3" style="background-color:beige; text-align:center; font-weight:bold; font-size:10px">
			{l s="INFORMATIONS FACTURATION" pdf=true}
		</td>
	</tr>
	<tr>
		<td style="width:33%; border-right:1px solid black;">
			<table width="100%">
				<tr style="font-size:8px">
					<td width="55%">
						{l s="N° facture :" pdf=true}
						<br /> {l s="N° commande :" pdf=true}
						<br /> {l s="Votre N° commande :" pdf=true}
						<br /> {l s="N° client :" pdf=true}
						<br /> {l s="N° Siret client :" pdf=true}
					</td>
					<td width="45%" style="text-align:center">
						<b>{$order->invoice_number}</b>
						<br /> {$order->reference}
						<br /> {$order->internal_reference|default:'-'}
						<br /> {$order->getCustomer()->id}
						<br /> {$order->getCustomer()->siret|default:'-'}
					</td>
				</tr>
			</table>
		</td>
		<td style="width:33%; border-right:1px solid black;">
			<table width="100%">
				<tr style="font-size:8px">
					<td width="50%">
						{l s="Date de commande :" pdf=true}
						<br /> {l s="Date de facture :" pdf=true}
						<br /> {l s="Date échéance :" pdf=true}
						<br /> {l s="Mode de paiement :" pdf=true}
						<br /> {l s="N° TVA client :" pdf=true}
					</td>
					<td width="50%" style="text-align:center">
						{$order->date_add|date_format:'d/m/Y'}
						<br /> {$order->invoice_date|date_format:'d/m/Y'}
						<br /> {$order->getPaymentDeadline()->format('d/m/Y')}
						<br /> {$order->payment|default:'-'}
						<br /> {$order->getCustomer()->tva|default:'-'}
					</td>
				</tr>
			</table>
		</td>
		<td style="width:33%">
			<table width="100%">
				<tr style="font-size:8px">
					<td width="55%">
						{l s="Facture réglée :" pdf=true}
						<br /> {l s="Date de réglement :" pdf=true}
						<br /> {l s="[1]RIB[/1] vendeur :" tags=["<b>"] pdf=true}
						<br /> {l s="[1]IBAN[/1] vendeur :" tags=["<b>"] pdf=true}
						<br /> {l s="[1]SWIFT/BIC[/1] vendeur :" tags=["<b>"] pdf=true}
					</td>
					<td width="45%" style="text-align:center">
						{if $order->getState()->paid}Oui{else}Non{/if}
						<br /> {foreach from=$order->getPaymentList() item=payment}{$payment.date_add|date_format:'d/m/Y'}{break}{foreachelse}-{/foreach}
						<br /> {Configuration::getForOrder('PS_SHOP_RIB', $order, '-')}
						<br /> {Configuration::getForOrder('PS_SHOP_IBAN', $order, '-')}
						<br /> {Configuration::getForOrder('PS_SHOP_BIC', $order, '-')}
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td colspan="3">&nbsp;</td></tr>
</table>

<table width="100%" cellpadding="3px">
	<tr>
		<td width="50%" style="background-color:beige; text-align:center; font-weight:bold; font-size:10px">
			{l s='ADRESSE FACTURATION' pdf='true'}
		</td>
		<td width="50%" style="background-color:beige; text-align:center; font-weight:bold; font-size:10px">
			{l s='ADRESSE LIVRAISON' pdf='true'}
		</td>
	</tr>
	<tr>
		<td width="50%" style="text-align:center; font-weight:8px">
			{assign var=address value=$order->getAddressInvoice()}
			{if $address}
				{if $address->company}{$address->company} <br />{/if}
				{if $address->firstname || $address->lastname}{$address->firstname} {$address->lastname} <br />{/if}
				{if $address->address1 || $address->address2}
					{$address->address1} 
					{if $address->address1 || $address->address2} - {/if} 
					{$address->address2}
					<br />
				{/if}
				{if $address->postcode || $address->city || $address->country}
					{$address->postcode} {$address->city}
					{if ($address->postcode || $address->city) && $address->country} - {/if}
					{$address->country}
				{/if}
			{/if}
		</td>
		<td width="50%" style="text-align:center; font-weight:8px">
			{assign var=address value=$order->getAddressDelivery()}
			{if $address}
				{if $address->company}{$address->company} <br />{/if}
				{if $address->firstname || $address->lastname}{$address->firstname} {$address->lastname} <br />{/if}
				{if $address->address1 || $address->address2}
					{$address->address1} 
					{if $address->address1 || $address->address2} - {/if} 
					{$address->address2}
					<br />
				{/if}
				{if $address->postcode || $address->city || $address->country}
					{$address->postcode} {$address->city}
					{if ($address->postcode || $address->city) && $address->country} - {/if}
					{$address->country}
				{/if}
			{/if}
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
</table>