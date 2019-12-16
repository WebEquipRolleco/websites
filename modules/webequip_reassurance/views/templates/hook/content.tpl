<h3 class="section-title top-space">
	{l s="Vos avantages Rolléco.fr" mod="webequip_reassurance"}
</h3>
{foreach from=$reassurances item=reassurance}
	{if $reassurance->link}<a href="{$reassurance->link}">{/if}
    <div class="rassurance-product">
    	{$reassurance->icon nofilter}
    	{$reassurance->text|strip_tags:true}
    </div>
    {if $reassurance->link}</a>{/if}
{/foreach}