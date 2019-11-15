<table width="100%" cellpadding="5px">
	<tr>
		<th style="text-align:center; font-weight:bold; font-size:20px;">
			{l s="Facture" pdf=true} 
			{if $order->isProforma()}{l s="proforma"}{/if} 
			{if $order->isAcquitted()}{l s="acquittée"}{/if}
		</th>
	</tr>
	<tr><td>&nbsp;</td></tr>
</table>