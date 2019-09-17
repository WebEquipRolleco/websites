{if !empty($reassurances)}
	<div id="reassurance_bottom" class="row">
		<div class="col-lg-4 reassurance title">
			<span>{l s="Vos avantages %s" sprintf=[Configuration::get('PS_SHOP_NAME')] mod="webequip_reassurance"}</span>
		</div>
		{foreach from=$reassurances item=reassurance}
			<div class="col-lg-4 text-center reassurance">
				{if $reassurance->link}<a href="{$reassurance->link}">{/if}
	        	{$reassurance->icon nofilter}
	        	{$reassurance->text nofilter}
	      		{if $reassurance->link}</a>{/if}
			</div>
		{/foreach}
	</div>
{/if}