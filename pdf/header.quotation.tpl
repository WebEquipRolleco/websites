<table style="width: 100%">
	<tr>
		<td style="width:30%; color:white; background-color:{$quotation->getShop()->color}">
			&nbsp; {l s="Devis n° %s" sprintf=[$quotation->reference] d='Shop.Pdf' pdf='true'}<br />
			&nbsp; {l s="Date :" d='Shop.Pdf' pdf='true'} {$quotation->date_add|date_format:'d/m/Y'}<br />
			&nbsp; {l s="Valable jusqu'au :" d='Shop.Pdf' pdf='true'} {$quotation->date_end|date_format:'d/m/Y'}<br />
			&nbsp; {l s="Votre contact :" d='Shop.Pdf' pdf='true'} {$quotation->getEmployee()->firstname}
		</td>
		<td style="width:39%; text-align:center;">
			{if $logo_path}
				<img src="{$logo_path}" style="width:{$width_logo}px; height:{$height_logo}px;" />
			{/if}
		</td>
		<td style="width:30%; text-align:center; font-size:8px">
			{if $quotation->details}
				<div style="font-weight:bold">{l s="Informations client" d='Shop.Pdf' pdf='true'}</div>
				{$quotation->details|replace:'|':'<br />'}
			{/if}
		</td>
	</tr>
</table>