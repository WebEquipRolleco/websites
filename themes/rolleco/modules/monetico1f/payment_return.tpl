<p style="margin-left:20px;">
	{l s='Your order on' mod='monetico1f'} <span class="bold">{$shop_name}</span> {l s='is complete.' mod='monetico1f'}
	<br /><br />
	{l s='We registered your payment of ' mod='monetico1f'} <span class="bold">{$order_total}</span> {l s='regulated by' mod='monetico1f'} <span class="bold">{$order_payment}</span> {l s='for your order number' mod='monetico1f'} <span class="bold">{$order_ref}</span>
	<br /><br />
    {l s='For any questions or for further information, please contact our' mod='monetico1f'} <a href="{$order_contact}">{l s='customer support' mod='monetico1f'}</a>.
    {*<br /><br />
    {l s='To view and print your invoice, click on' mod='monetico1f'} <a target="_blank" href="{$lien_facture_pdf}" title=" {l s='Invoice' mod='monetico1f'} ">
    <span class="bold">{l s='here' mod='monetico1f'}</span><img src="{$lien_img_pdf}" title=" {l s='Invoice' mod='monetico1f'} " class="icon" /></a>.*}
</p>