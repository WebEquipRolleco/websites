{extends file="helpers/view/view.tpl"}
{block name="override_tpl"}
	{if !$shop_context}
		<div class="alert alert-warning">{l s='You have more than one shop and must select one to configure payment.' d='Admin.Payment.Notification'}</div>
	{else}
		<div class="alert alert-info">
			{l s='This is where you decide what payment modules are available for different variations like your customers\' currency, group, and country.' d='Admin.Payment.Help'}
			<br />
			{l s='A check mark indicates you want the payment module available.' d='Admin.Payment.Help'}
			{l s='If it is not checked then this means that the payment module is disabled.' d='Admin.Payment.Help'}
			<br />
			{l s='Please make sure to click Save for each section.' d='Admin.Payment.Help'}
		</div>

		<div class="panel">
			<h3><i class="icon-cogs"></i> {l s='Configuration'}</h3>
			<form method="post" class="form-horizontal">
				<div class="form-group">
					<label class="control-label col-lg-3">{l s='Délai de paiement'}</label>
					<div class="col-lg-9">
						<input type="number" min="0" class="form-control" name='PAYMENT_TIME_LIMIT' value="{$PAYMENT_TIME_LIMIT}">
						<p class="help-block pull-right">{l s="Si vide ou égal à 0, le paiement est considéré comme devant être effectué le jour de la facturation."}</p>
					</div>
				</div>
				<div class="panel-footer text-right">
					<button type="submit" class="btn btn-default">
						<i class="process-icon-save"></i> {l s='Enregistrer' d='Admin.Actions'}
					</button>
				</div>
			</form>
		</div>

		{if $display_restrictions}
			{foreach $lists as $list}
				{include file='controllers/payment_preferences/restrictions.tpl'}
			{/foreach}
		{else}
			<div class="alert alert-warning">{l s='No payment module installed' d='Admin.Payment.Notification'}</div>
		{/if}
	{/if}
{/block}
