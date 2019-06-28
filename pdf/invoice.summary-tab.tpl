<table width="100%" cellpadding="5px" style="border-collapse: collapse; border:1px solid #4D4D4D">
	<thead>
		<tr style="background-color:#4D4D4D; color:#FFF;">
			<td colspan="2" style="text-align:center">
				<span style="font-size:14pt; font-weight:bold;">{l s='FACTURE N°' pdf='true'} {$order->invoice_number}</span>
			</td>
		</tr>
		<tr>
			<td style="text-align:center;">{l s='Commande n°' d='Shop.Pdf' pdf='true'} {$order->getUniqReference()}</td>
			<td style="text-align:center;">{dateFormat date=$order->invoice_date full=0}</td>
		</tr>
	</thead>
</table>

<table>
	<tr><td>&nbsp;</td></tr>
</table>