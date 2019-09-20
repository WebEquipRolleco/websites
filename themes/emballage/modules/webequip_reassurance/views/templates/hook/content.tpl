{if !empty($reassurances)}
	<div id="reassurance_bottom" class="row margin-top-sm">
		<div class="col-lg-3 reassurance title">
			<span>{l s="Vos avantages %s" sprintf=[Configuration::get('PS_SHOP_NAME')] mod="webequip_reassurance"}</span>
		</div>
		<div class="col-lg-9">
			<ul>
				{foreach from=$reassurances item=reassurance}
					<li>
						{if $reassurance->link}<a href="{$reassurance->link}">{/if}
			        	{$reassurance->icon nofilter}
			        	{$reassurance->text nofilter}
			      		{if $reassurance->link}</a>{/if}
					</li>
				{/foreach}
			</ul>
		</div>
	</div>
{/if}