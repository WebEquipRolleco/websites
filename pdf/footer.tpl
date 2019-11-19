{assign var=title value=Configuration::getForShop('PS_SHOP_TITLE', $shop)}
{assign var=name value=Configuration::getForShop('PS_SHOP_NAME', $shop)}
{assign var=type value=Configuration::getForShop('PS_SHOP_TYPE', $shop)}
{assign var=address value=Configuration::getForShop('PS_SHOP_ADDR1', $shop)}
{assign var=zipcode value=Configuration::getForShop('PS_SHOP_CODE', $shop)}
{assign var=city value=Configuration::getForShop('PS_SHOP_CITY', $shop)}
{assign var=email value=Configuration::getForShop('PS_SHOP_EMAIL', $shop)}
{assign var=phone value=Configuration::getForShop('PS_SHOP_PHONE', $shop)}
{assign var=RCS value=Configuration::getForShop('PS_SHOP_RCS', $shop)}
{assign var=SIRET value=Configuration::getForShop('PS_SHOP_SIRET', $shop)}

<div style="text-align:center; font-size:6px;">
	{$name} - {$address}, {$zipcode} {$city} <br />
	{l s="Pour toute assistance, merci de nous contacter par mail %s ou par téléphone au %s" sprintf=[$email, $phone] d='Shop.Pdf' pdf='true'} <br />
	{$title} - {$type} - {$RCS} - {$SIRET}
</div>