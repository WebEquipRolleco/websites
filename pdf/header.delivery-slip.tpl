<table style="width: 100%">
	<tr>
		<td style="width:40%;">
			&nbsp; {Configuration::get('PS_SHOP_TITLE')}<br />
			&nbsp; {Configuration::get('PS_SHOP_ADDRESS1')}<br />
			&nbsp; {Configuration::get('PS_SHOP_CODE')} {Configuration::get('PS_SHOP_CITY')}<br />
			&nbsp; {Configuration::get('PS_SHOP_TYPE')}<br />
			&nbsp; Siret {Configuration::get('PS_SHOP_SIRET')}<br />
		</td>
		<td style="width:32%; text-align:center;">
			{if $logo_path}
				<img src="{$logo_path}" style="width:{$width_logo}px; height:{$height_logo}px;" />
			{/if}
		</td>
		<td style="width:21%; text-align:right; font-size:8px">
			&nbsp; <b>{Configuration::get('PS_SHOP_PHONE')}</b><br />
			&nbsp; {Configuration::get('PS_SHOP_EMAIL')}
		</td>
	</tr>
</table>