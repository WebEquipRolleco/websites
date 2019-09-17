{$style_tab}

<div class="footer">
	{if $company.phone || $company.fax}
		{l s='Pour toute assistance, merci de nous contacter :' pdf=true}
		<br />
		{if $company.phone}{l s='Tél :' pdf=true} {$company.phone}{/if}
		{if $company.phone && $company.fax} - {/if}
		{if $company.fax}{l s='Fax :' pdf=true} {$company.fax}{/if}
		<br />
	{/if}
	{$company.details}
	<br />
	{l s='Fiche produit au' pdf=true} {$date}
</div>