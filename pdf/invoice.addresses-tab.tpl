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
				{$invoice_address}
			</td>
			<td style="text-align:center">
				{$delivery_address}
			</td>
		</tr>
	</tbody>
</table>

<table>
	<tr><td>&nbsp;</td></tr>
</table>