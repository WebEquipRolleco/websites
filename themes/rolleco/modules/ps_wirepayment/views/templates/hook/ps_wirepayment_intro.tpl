<section class="margin-bottom-15">

    {l s='Please transfer the invoice amount to our bank account. You will receive our order confirmation by email containing bank details and order number.' d='Modules.Wirepayment.Shop'}
    {l s='Goods will be reserved %s days for you and we\'ll process the order immediately after receiving the payment.' sprintf=[$bankwireReservationDays] d='Modules.Wirepayment.Shop'}
    <br /><br />
    {l s="Le vivrement bancaire est à effectuer sur le compte suivant :" mod="webequip_payment_invoice"} <br />
    <b>{l s="Société : "}</b> {Configuration::get('PS_SHOP_TITLE')} {Configuration::get('PS_SHOP_ADDR1')} {Configuration::get('PS_SHOP_CODE')} {Configuration::get('PS_SHOP_CITY')} <br />
    <b>{l s="RIB : " mod="webequip_payment_invoice"} {Configuration::get('PS_SHOP_RIB')}</b> <br />
    <b>{l s="IBAN : " mod="webequip_payment_invoice"} {Configuration::get('PS_SHOP_IBAN')} - {l s="BIC : " mod="webequip_payment_invoice"} {Configuration::get('PS_SHOP_BIC')}</b>

</section>