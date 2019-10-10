<div class="alert alert-info">
	<b>{l s="Date de passage : "}</b> {Configuration::get('RECALL_CRON_LAST_DATE', null, null, null, '-')}
	| <b>{l s="Nombre de commandes : "}</b> {Configuration::get('RECALL_CRON_NB_ORDERS', null, null, null, '0')}
</div>

<div class="panel">
	<div class="panel-heading">Gestion de la tâche CRON</div>
	<table class="table" width="100%" cellspacing="0">
		<thead>
			<tr>
				<th colspan="3" class="bg-primary">
					<b>Actions possibles</b>
				</th>
			</tr>
		</thead>
		<tbody>
			{foreach $cron_informations as $key => $information}
				<tr>
					<td><b>{$key}</b></td>
					<td>{$information}</td>
					<td class="text-right">
						<a href="https://{$cron_url}&actions={$key}" class="btn btn-success">
							<b><i class="icon-play"></i> &nbsp; lancer</b> 
						</a>
					</td>
				</tr>
			{/foreach}	
		</tbody>
	</table>
</div>

<form method="post">
	<div class="panel">
		<div class="panel-heading">{l s="Objets des mails" mod="webequip_recall"}</div>
		<div class="alert alert-info">
			{l s="Objets des mails de rappel de paiement envoyés aux clients" mod="webequip_recall"}
		</div>
		<div class="form-group">
			<label for="RECALL_OBJECT_1">{l s="Objet mail de rappel 1 (J-10)" mod="webequip_recall"}</label>
			<input type="text" id="RECALL_OBJECT_1" name="RECALL_OBJECT_1" value="{$RECALL_OBJECT_1}">
		</div>
		<div class="form-group">
			<label for="RECALL_OBJECT_2">{l s="Objet mail de rappel 2 (J+7)" mod="webequip_recall"}</label>
			<input type="text" id="RECALL_OBJECT_2" name="RECALL_OBJECT_2" value="{$RECALL_OBJECT_2}">
		</div>
		<div class="form-group">
			<label for="RECALL_OBJECT_3">{l s="Objet mail de rappel 3 (J+17)" mod="webequip_recall"}</label>
			<input type="text" id="RECALL_OBJECT_3" name="RECALL_OBJECT_3" value="{$RECALL_OBJECT_3}">
		</div>
		<div class="form-group">
			<label for="RECALL_OBJECT_4">{l s="Objet mail de rappel 4 (J+25)" mod="webequip_recall"}</label>
			<input type="text" id="RECALL_OBJECT_4" name="RECALL_OBJECT_4" value="{$RECALL_OBJECT_4}">
		</div>
		{literal}
		<div style="margin-top:10px">
			<b>Variables acceptées :</b> 
			<ul style="margin-top:10px">
				<li>- {civility}</li>
				<li>- {firstname}</li>
				<li>- {lastname}</li>
				<li>- {order_reference}</li>
				<li>- {order_date}</li>
				<li>- {order_date}</li>
				<li>- {invoice_reference}</li>
				<li>- {shop_name}</li>
			</ul>
		</div>
		<div style="margin-top:10px">
			<b>Exclusivement pour l'envoi des BC/BL :</b> 
			<ul style="margin-top:10px">
				<li>- {supplier_name}</li>
			</ul>
		</div>
		{/literal}
		<div class="panel-footer text-right">
			<button type="submit" class="btn btn-success">
				<i class="process-icon-save"></i> <b>{l s="Save" d='Shop.Theme.Actions'}</b>
			</button>
		</div>
	</div>
</form>

<form method="post">
	<div class="panel">
		<div class="panel-heading">{l s="Gestion des copies de mails cachées" mod="webequip_recall"}</div>
		<div class="alert alert-info">
			{l s="Destinataire caché des mails de rappel à J+7, J+17 et J+25" mod="webequip_recall"}
		</div>
		<div class="form-group">
			<label for="RECALL_HIDDEN_MAIL">{l s="E-mail" mod="webequip_recall"}</label>
			<input type="text" id="RECALL_HIDDEN_MAIL" name="RECALL_HIDDEN_MAIL" value="{$RECALL_HIDDEN_MAIL}">
		</div>
		<div class="panel-footer text-right">
			<button type="submit" class="btn btn-success">
				<i class="process-icon-save"></i> <b>{l s="Save" d='Shop.Theme.Actions'}</b>
			</button>
		</div>
	</div>
</form>

<form method="post">
	<div class="panel">
		<legend>{l s="Destinataires des mails d'échéances dépassées" mod="webequip_recall"}</legend>
		<div class="alert alert-info">
			{l s="Les commandes concernées par les rappels sont celles dont l'état actuel n'est pas considéré comme 'payé' et dont le délai entre la date actuelle et la date de facturation renseignée correspond au table ci-dessous." mod="webequip_recall"}
		</div>
		<div class="form-group">
			<table class="table" width="60%" cellpadding="0" cellspacing="0">
				<thead>
					<tr class="bg-primary">
						<th><b>Délai</b></th>
						<th class="text-center"><b>Facturation</b></th>
						<th class="text-center"><b>Nombre de commandes concernées</b></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>10 jours avant échéance</td>
						<td class="text-center">{$date_invoice_recall_1->format('d/m/Y')}</td>
						<td class="text-center">{$nb_orders_recall_1}</td>
					</tr>
					<tr>
						<td>Aujourd'hui</td>
						<td class="text-center">{$date_invoice_recall_2->format('d/m/Y')}</td>
						<td class="text-center">{$nb_orders_recall_2}</td>
					</tr>
					<tr>
						<td>7 jours après l'échéance</td>
						<td class="text-center">{$date_invoice_recall_3->format('d/m/Y')}</td>
						<td class="text-center">{$nb_orders_recall_3}</td>
					</tr>
					<tr>
						<td>17 jours après l'échéance</td>
						<td class="text-center">{$date_invoice_recall_4->format('d/m/Y')}</td>
						<td class="text-center">{$nb_orders_recall_4}</td>
					</tr>
					<tr>
						<td>25 jours après l'échéance</td>
						<td class="text-center">{$date_invoice_recall_5->format('d/m/Y')}</td>
						<td class="text-center">{$nb_orders_recall_5}</td>
					</tr>
					<tr>
						<td>30 jours après l'échéance</td>
						<td class="text-center">{$date_invoice_recall_6->format('d/m/Y')}</td>
						<td class="text-center">{$nb_orders_recall_6}</td>
					</tr>
					<tr>
						<td>45 jours après l'échéance</td>
						<td class="text-center">{$date_invoice_recall_7->format('d/m/Y')}</td>
						<td class="text-center">{$nb_orders_recall_7}</td>
					</tr>
					<tr>
						<td colspan="2">Clients à mettre en OK (commande payée)</td>
						<td class="text-center">{$nb_customers_to_update}</td>
					</tr>
					<tr>
						<td colspan="2">Devis à relancer</td>
						<td class="text-center">{$nb_recall_quotation}</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="form-group">
			<label for="CONFIG_RECALL_MAILS_1">{l s="30 jours après l'échéance" mod="webequip_recall"}</label>
			<select id="CONFIG_RECALL_MAILS_1" name="RECALL_MAILS_1[]" multiple>
				{foreach $employees as $employee}
					<option value="{$employee.id_employee}" {if $employee.id_employee|in_array:$RECALL_MAILS_1}selected{/if}>
						{$employee.firstname} {$employee.lastname}
					</option>
				{/foreach}
			</select>
		</div>
		<div class="form-group">
			<label for="CONFIG_RECALL_MAILS_2">{l s="45 jours après l'échéance (contentieux)" mod="webequip_recall"}</label>
			<select id="CONFIG_RECALL_MAILS_2" name="RECALL_MAILS_2[]" multiple>
				{foreach $employees as $employee}
					<option value="{$employee.id_employee}" {if $employee.id_employee|in_array:$RECALL_MAILS_2}selected{/if}>
						{$employee.firstname} {$employee.lastname}
					</option>
				{/foreach}
			</select>
		</div>
		<div class="panel-footer text-right">
			<button type="submit" class="btn btn-success">
				<i class="process-icon-save"></i> <b>{l s="Save" d='Shop.Theme.Actions'}</b>
			</button>
		</div>
	</div>
</form>

<form method="post">
	<div class="panel">
		<div class="panel-heading"><b>{l s="Rappels des factures" mod="webequip_recall"} ({$nb_no_facturation} à mettre à jour)</b></div>
		<div class="alert alert-info">
			{l s="Gestion des mails de rappel en cas d'absence de numéro ou date de facture pour une commande" mod="webequip_recall"}
		</div>
		<div class="form-group">
			<label for="CHECK_INVOICE_DAYS">{l s="Nombre de jours avant rappel" mod="webequip_recall"}</label>
			<input type="text" id="CHECK_INVOICE_DAYS" name="CHECK_INVOICE_DAYS" value="{$CHECK_INVOICE_DAYS}">
		</div>
		<div class="form-group" style="margin-top:10px">
			<label for="CHECK_INVOICE_STATE">{l s="Etat déclencheur de vérification" mod="webequip_recall"}</label>
			<select id="CHECK_INVOICE_STATE" name="CHECK_INVOICE_STATE">
				<option value="">{l s="Choisir un état" mod="webequip_recall"}</option>
				{foreach $states as $state}
					<option value="{$state.id_order_state}" {if $state.id_order_state == $CHECK_INVOICE_STATE}selected{/if}>
						{$state.name}
					</option>
				{/foreach}
			</select>
		</div>
		<div class="form-group" style="margin-top:10px">
			<label for="CHECK_INVOICE_EMPLOYEE">{l s="Destinataire du mail de rappel" mod="webequip_recall"}</label>
			<select id="CHECK_INVOICE_EMPLOYEE" name="CHECK_INVOICE_EMPLOYEE">
				<option value="">{l s="Choisir un employé" mod="webequip_recall"}</option>
				{foreach $employees as $employee}
					<option value="{$employee.id_employee}" {if $employee.id_employee == $CHECK_INVOICE_EMPLOYEE}selected{/if}>
						{$employee.firstname} {$employee.lastname}
					</option>
				{/foreach}
			</select>
		</div>
		<div class="panel-footer text-right">
			<button type="submit" class="btn btn-success">
				<i class="process-icon-save"></i> <b>{l s="Save" d='Shop.Theme.Actions'}</b>
			</button>
		</div>
	</div>
</form>

<form method="post">
	<div class="panel">
		<div class="panel-heading"><b>{l s="Rappels des SAV" mod="webequip_recall"}</b></div>
		<div class="alert alert-info">
			{l s="Paramètrage du nombre de jours d'inactivité maximum des SAV avant rappel par mail"}
		</div>
		<div class="form-group">
			<label for="RECALL_SAV_NB_DAYS">{l s="Nombre de jours avant rappel" mod="webequip_recall"}</label>
			<input type="text" id="RECALL_SAV_NB_DAYS" name="RECALL_SAV_NB_DAYS" value="{$RECALL_SAV_NB_DAYS}">
		</div>
		<div class="panel-footer text-right">
			<button type="submit" class="btn btn-success">
				<i class="process-icon-save"></i> <b>{l s="Save" d='Shop.Theme.Actions'}</b>
			</button>
		</div>
	</div>
</form>