{foreach from=$orders item=$order name=orders}

	{include file="./preparation-slip.tpl"}
	{if !$smarty.foreach.orders.last}
		<br pagebreak="true"/>
	{/if}
	
{/foreach}