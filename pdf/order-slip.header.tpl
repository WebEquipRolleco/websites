<table style="width: 100%">
    <tr>
        <td style="width: 25%">
            {if $logo_path}
                <img src="{$logo_path}" style="width:{$width_logo}px; height:{$height_logo}px;" />
            {/if}
        </td>
        <td style="width: 25%">
            &nbsp;{Configuration::get('PS_SHOP_TITLE')}<br />
            &nbsp; {Configuration::get('PS_SHOP_ADDRESS1')}<br />
            &nbsp; {Configuration::get('PS_SHOP_CODE')} {Configuration::get('PS_SHOP_CITY')}
        </td>
        <td style="width: 25%">
            &nbsp;{Configuration::get('PS_SHOP_TYPE')}<br />
            &nbsp; Siret {Configuration::get('PS_SHOP_SIRET')}<br />
            &nbsp; <b>{Configuration::get('PS_SHOP_PHONE')}</b><br />
            &nbsp; {Configuration::get('PS_SHOP_EMAIL')}
        </td>
        <td style="width: 25%; text-align: right;">
            <table style="width: 100%">
                <tr>
                    <td style="font-weight: bold; font-size: 14pt; color: #444; width: 100%;">{if isset($header)}{$header|escape:'html':'UTF-8'|upper}{/if}</td>
                </tr>
                <tr>
                    <td style="font-size: 14pt; color: #9E9F9E">{$date|escape:'html':'UTF-8'}</td>
                </tr>
                <tr>
                    <td style="font-size: 14pt; color: #9E9F9E">{$title|escape:'html':'UTF-8'}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>