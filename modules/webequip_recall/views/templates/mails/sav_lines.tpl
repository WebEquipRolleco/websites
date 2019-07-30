{foreach $rows as $sav}
	<tr style="background-color:lightgrey">
		<td style="padding:5px; padding-left:10px">{$sav->reference}</td>
		<td style="padding:5px; text-align:center">{$sav->getCustomer()->company|default:'_'}</td>
		<td style="padding:5px; text-align:center">{$sav->date_add|date_format:'d/m/Y'}</td>
		<td style="padding:5px; text-align:center">{$sav->date_upd|date_format:'d/m/Y'}</td>
	</tr>
{/foreach}