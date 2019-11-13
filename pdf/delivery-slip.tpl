<div style="font-size: 9pt; color: #444">

<table>
	<tr><td>&nbsp;</td></tr>
</table>

{* TITRE *}
<table width="100%" cellpadding="5px" style="border-collapse: collapse; border:1px solid #4D4D4D">
	<thead>
		<tr style="background-color:#4D4D4D; color:#FFF;">
			<td style="text-align:center">
				<span style="font-size:14pt; font-weight:bold;">{l s='BON DE LIVRAISON' pdf='true'}</span>
				<br />
				<span style="font-size:10pt;">{l s='Delivery note' pdf='true'}</span>
			</td>
		</tr>
	</thead>
</table>

<table>
	<tr><td style="line-height: 6px">&nbsp;</td></tr>
</table>

<table width="100%" cellpadding="10px">
	<tr>
		<td width="50%" style="text-align:center;">
			{if $order->delivery_information}
				<table width="99%" cellpadding="10px" style="border:1px solid grey; background-color:powderblue;">
					<tr>
						<td>
							<span style="color:red; font-weight:bold">{l s='MESSAGE CONCERNANT LA LIVRAISON' pdf='true'}</span>
							{l s='IMPORTANT MESSAGE TO CARRIER' pdf='true'}
						</td>
					</tr>
					<tr>
						<td style="font-weight:bold">{$order->delivery_information|replace:'|':'<br />'}</td>
					</tr>
				</table>
			{/if}
		</td>
		<td width="50%">
			<table width="100%" cellpadding="10px" style="border:6px solid darkorange;">
				<tr>
					<td>
						{assign var=address value=$order->getAddressDelivery()}
						{if $address->company}
							{$address->company|upper}<br />
						{/if}
						{if $address->firstname || $address->lastname}
							{$address->lastname|upper} {$address->firstname|upper}<br />
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
	</tr>
</table>

<table>
	<tr><td style="line-height: 6px">&nbsp;</td></tr>
</table>

{* REFERENCES *}
<table width="100%" cellpadding="5px" style="border:1px solid #4D4D4D;">
	<thead>
		<tr style="background-color:#4D4D4D; color:#FFF; font-weight:bold;">
			<th width="33%" style="text-align:center">
				{l s='Date' pdf='true'}
			</th>
			<th width="34%" style="text-align:center">
				{l s='Notre numéro de commande' pdf='true'}
				<div style="font-weight: normal">{l s='Our order number' pdf='true'}</div>
			</th>
			<th  width="33%" style="text-align:center">
				{l s='Référence commande du client' pdf='true'}
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td width="33%" style="text-align:center">
				{$order->date_add|date_format:'d/m/Y'}
			</td>
			<td width="34%" style="text-align:center">
				<span style="font-weight:bold; font-size:14pt; color:#1e4688">
					{$order->reference|default:'-'}
				</span>
			</td>
			<td width="33%" style="text-align:center">
				{$order->internal_reference|default:'-'}
			</td>
		</tr>
	</tbody>
</table>

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
			<th width="20%" style="text-align:center">
				{l s='Référence article' pdf='true'}
			</th>
			<th width="60%" style="text-align:center">
				{l s='Désignation' pdf='true'}
			</th>
			<th  width="20%" style="text-align:center">
				{l s='Quantité' pdf='true'}
			</th>
		</tr>
	</thead>
	<tbody>
		{foreach $order->getDetails($oa->id_supplier) as $details}
			<tr>
				<td width="20%" style="text-align:center">
					<strong>{$details->product_reference}</strong>
				</td>
				<td width="60%" style="text-align:center">
					{$details->product_name}
				</td>
				<td width="20%" style="text-align:center">
					{$details->product_quantity}
				</td>
			</tr>
		{/foreach}
	</tbody>		
</table>

{* GARANTIE *}
<table width="100%" cellpadding="10px">
	<tr>
		<td style="text-align:center; color:red; text-transform:uppercase;">
			<strong style="font-size:14pt">
				{l s='INFORMATION LIVRAISON' pdf='true'}
			</strong>
			<br />
			<strong style="font-size:10pt">
				{l s='MERCI DE CONTRÔLER IMPÉRATIVEMENT LA MARCHANDISE EN PRÉSENCE DU LIVREUR' pdf='true'}
			</strong>
		</td>
	</tr>
	<tr>
		<td>
			Un emballage d'apparence correcte peut cacher des chocs. En cas de <b style='text-decoration:underline;'>défaut</b> ou de <b style='text-decoration:underline;'>casse</b>, nous vous invitons <b style='text-decoration:underline;'>à refuser le colis</b>. Il est <b style='text-decoration:underline;'>impératif de notifier sur le bon de transport</b> le motif de votre refus et de nous en informer en nous transmettant le bon de transport. <br />
			<span style='color:red; text-decoration:underline;'>Il est indispensable de déballer le ou les produits <b>AVANT</b> signature du récépissé</span>. Légalement vous disposez de 15 minutes pour l'ouverture et la vérification des colis. <br />
			Si le livreur ne veut pas attendre la fin du déballage, inscrivez en toutes lettres 'le livreur n'a pas souhaité rester' et faire signer le chauffeur puis adresser un courrier recommandé sous 3 jours au transporteur.
			<br />
			Si vous décidez néanmoins d'accepter le colis, vous devrez impérativement noter sur le bon de transport un descriptif détaillé des dégradations constatées et conserver une copie de ce bon de transport.<br />
			Il vous sera demandé pour toute indemnisation ou échange. <br />
			Les mentions 'sous réserve de déballage', 'abimé sous emballage intact' ou toute autre remarque sur l'emballage n’ont aucune valeur juridique et ne permettent aucun recours contre le transporteur.
		</td>
	</tr>
</table>

</div>