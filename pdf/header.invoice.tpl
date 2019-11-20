{assign var=name value=Configuration::getForOrder('PS_SHOP_NAME', $order)}
{assign var=title value=Configuration::getForOrder('PS_SHOP_TITLE', $order)}
{assign var=type value=Configuration::getForOrder('PS_SHOP_TYPE', $order)}
{assign var=addr_1 value=Configuration::getForOrder('PS_SHOP_ADDR1', $order)}
{assign var=code value=Configuration::getForOrder('PS_SHOP_CODE', $order)}
{assign var=city value=Configuration::getForOrder('PS_SHOP_CITY', $order)}
{assign var=RCS value=Configuration::getForOrder('PS_SHOP_RCS', $order)}
{assign var=SIRET value=Configuration::getForOrder('PS_SHOP_SIRET', $order)}
{assign var=TVA value=Configuration::getForOrder('PS_SHOP_TVA', $order)}
{assign var=APE value=Configuration::getForOrder('PS_SHOP_APE', $order)}

<table style="width: 100%">
	<tr>
		<td style="width: 50%">
			<span style="font-weight:bold; font-size:10px">{$name}</span>
			<div style="font-size:8px">
				<br /> {$title}
				<br /> {$addr_1} {$code} {$city}
				{if $type}<br /> {$type}{/if}
				{if $RCS}<br /> <b>{l s="RCS :" pdf=true}</b> {$RCS}{/if}
				{if $SIRET}<br /> <b>{l s="Siret :" pdf=true}</b> {$SIRET}{/if}
				{if $TVA || $APE}
					<br /> 
					{if $TVA}<b>{l s="TVA :" pdf=true}</b> {$TVA}{/if}
					{if $TVA && $APE} - {/if}
					{if $TVA}<b>{l s="Code APE :" pdf=true}</b> {$APE}{/if}
				{/if}
			</div>
		</td>
		{if $logo_path}
			<td style="width: 50%; text-align: right;">
				<img src="{$logo_path}" style="width:{$width_logo}px; height:{$height_logo}px;" />
			</td>
		{/if}
	</tr>
</table>