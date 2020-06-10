<section>
  <p>{l s='Please send us your check following these rules:' d='Modules.Checkpayment.Shop'}
    <dl>
      <dt>{l s='Amount' d='Modules.Checkpayment.Shop'}</dt>
      <dd>{$checkTotal}</dd>
      <dt>{l s='Payee' d='Modules.Checkpayment.Shop'}</dt>
      <dd>{Configuration::get('PS_SHOP_TITLE')}</dd>
      <dt>{l s='Send your check to this address' d='Modules.Checkpayment.Shop'}</dt>
      <dd>
        {Configuration::get('PS_SHOP_TITLE')} 
        <br /> {Configuration::get('PS_SHOP_ADDR1')} 
        <br /> {Configuration::get('PS_SHOP_CODE')} {Configuration::get('PS_SHOP_CITY')}
      </dd>
    </dl>
  </p>
</section>