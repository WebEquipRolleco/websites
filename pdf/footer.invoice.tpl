{assign var=email value=Configuration::getForOrder('PS_SHOP_EMAIL', $order)}
{assign var=phone value=Configuration::getForOrder('PS_SHOP_PHONE', $order)}

<table width="100%" border="1" cellpadding="2px">
	<tr>
		<td width="100%" colspan="3" style="background-color:beige; text-align:center; font-weight:bold; font-size:7px">
			{l s="INFORMATIONS COMPLEMENTAIRES" pdf=true}
		</td>
	</tr>
	<tr style="background-color:beige; text-align:center; font-size:6px">
		<td width="33%">{l s="Escompte" pdf=true}</td>
		<td width="34%">{l s="Date d'acceptation des CGV" pdf=true}</td>
		<td width="33%">{l s="Taux de pénalité de retard" pdf=true}</td>
	</tr>
	<tr style="text-align:center; font-size:6px">
		<td width="33%">{l s="Néant" pdf=true}</td>
		<td width="34%">{$order->date_add|date_format:'d/m/Y'}</td>
		<td width="33%">{l s="3 fois le taux d'intérêt général" pdf=true}</td>
	</tr>
	<tr style="text-align:center; font-size:6px">
		<td width="100%" colspan="3">{l s="Toute facture implique par elle-même acceptation de nos conditions générales de vente détaillées en piece jointe. Nous nous réservons la propriété marchandises jusqu'au paiement intégral du prix,
mais le client en assumera les risques. Toute contestation, de quelque ordre qu'elle soit sera de la compétence exclusive du Tribunal du siège social de notre société, qui se devra d'appliquer la loi
française. Indemnité forfaitaire minimum pour frais de recouvrement : 40 € (décret n° 2012-115 du 2 octobre 2012)" pdf=true}</td>
	</tr>
	<tr style="text-align:center; font-size:6px">
		<td width="100%" colspan="3">{l s="Pour toute assistance, merci de nous contacter par mail [1] @email@ [/1] ou par téléphone au [1] @phone@ [/1]"|replace:["@email@", "@phone@"]:[$email, $phone] tags=["<b>"] pdf=true}</td>
	</tr>
</table>