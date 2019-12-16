{if $quotation->getCustomer() and $quotation->getCustomer()->isNewFromQuotation()}
	<tr>
		<td class="box" style="border:1px solid #D6D4D4;background-color:#f8f8f8;padding:7px 0">
			<table class="table" style="width:100%">
				<tr>
					<td width="10" style="padding:7px 0">&nbsp;</td>
					<td style="padding:7px 0">
						<font size="2" face="Open-sans, sans-serif" color="#555454">
							<span style="text-align:center; color:#777">
								<div style="color:red">{l s="Afin de facilité votre démarche, nous avons également pris la liberté de vous créer un compte sur notre site. Vous pourrez ainsi vous connecter avec les identifiants suivant et retrouver ce devis directement dans votre compte."}</div>
								<br />
								<div><b>Identifiant</b></div>
								<div>{$quotation->getCustomer()->email}</div>
								<br />
								<div><b>Mot de passe</b></div>
								<div>{$quotation->reference}</div>
							</span>
						</font>
					</td>
					<td width="10" style="padding:7px 0">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="space_footer" style="padding:0!important">&nbsp;</td>
	</tr>
{/if}