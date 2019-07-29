<div class="row">

	<div class="col-lg-3">

		{if $sav->getOrder()}
			{assign var=order value=$sav->getOrder()}
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-shopping-cart"></i> &nbsp; {l s="Commande"}
				</div>
				<b>{$order->reference}</b>
				- <em class="text-muted">{$order->date_add|date_format:'d/m/Y'}</em>
				<br />
				{if $order->getState()->paid}
					<span class="label label-success">
						<i class="icon-check-square"></i> {l s="Commande payée"}
					</span>
				{else}
					<span class="label label-danger">
						<i class="icon-times"></i> {l s="Commande non payée"}
					</span>
				{/if}
				<hr />
				<b>{l s="Produits concernés :"}</b>
				{foreach from=$sav->getProductDetails() item=details}
					<div> -
						{if $details->product_reference}<b>{$details->product_reference}</b> : {/if}
						<em class="text-muted">{$details->product_name}</em>
					</div>
				{/foreach}
			</div>
		{/if}

	</div>

	<div class="col-lg-6">

		<div class="panel">
			<div class="panel-heading">
				<i class="icon-envelope"></i> &nbsp; {l s="Messages"}
			</div>
			{foreach from=$sav->getMessages() item=message}
				<div class="well" {if $message->isNewToMe()}style="background-color:lightyellow"{/if}>
					<b>{$message->getSender()->firstname} {$message->getSender()->lastname}</b>
					- <em class="text-muted">{$message->date_add|date_format:'d/m/Y à H:i'}</em>
					<span class="pull-right">
						{if $message->isNewToMe()}
							<a href="{$link->getAdminLink('AdminAfterSales')}&id_after_sale={$sav->id}&read={$message->id}&updateafter_sale" class="label label-warning" title="{l s='Marquer comme lu'}">
								<i class="icon-check-square"></i>
							</a>
						{/if}
						{if !$message->display}
							<span class="label label-default" title="{l s='Non visible pour le client'}">
								<i class="icon-eye-slash"></i>
							</span>
						{/if}
					</span>
					<hr />
					{$message->message}
				</div>
			{/foreach}
		</div>

		<div class="panel">
			<div class="panel-heading">
				<i class="icon-envelope"></i> &nbsp; {l s="Ajouter un commentaire"}
			</div>
				<form method="post">
					<div class="form-group">
						<label>{l s="Visibilité pour le client"}</label>
						<span class="switch prestashop-switch fixed-width-lg" style="margin-bottom:20px">
							<input type="radio" name="display" id="display_on" value="1" checked>
							<label for="display_on">{l s='Affiché' d='Shop.Theme.Labels'}</label>
							<input type="radio" name="display" id="display_off" value="0">
							<label for="display_off">{l s='Caché' d='Shop.Theme.Labels'}</label>
							<a class="slide-button btn"></a>
						</span>
					</div>
					<div class="form-group">
						<textarea rows="5" name="new_message" required></textarea>
					</div>
					<div class="form-group text-right">
						<button type="submit" class="btn btn-success">
							<b>{l s="Ajouter"}</b>
						</button>
					</div>
				</form>

		</div>

	</div>

	<div class="col-lg-3">

		{if $sav->getCustomer()}
			{assign var=customer value=$sav->getCustomer()}
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-user"></i> &nbsp; {l s="Client"}
				</div>
				<b>{$customer->firstname} {$customer->lastname}</b>
				{if $customer->getAccountType()} - <em class="text-muted">{$customer->getAccountType()->name}</em>{/if}
				<br />
				<a href="mailto:{$customer->email}">{$customer->email}</a>
			</div>
		{/if}

		<div class="panel">
			<div class="panel-heading">
				<i class="icon-picture"></i> &nbsp; {l s="Images"}
			</div>
			<div class="text-center">
				{foreach from=$sav->getPictures() item=file_name}
					<a href="{$sav->getDirectory()}{$file_name}" target="_blank">
						<img src="{$sav->getDirectory()}{$file_name}" style="margin-bottom:15px; padding:5px; border:2px solid lightgrey; max-width:100%" />
					</a>
				{/foreach}
			</div>
		</div>

	</div>

</div>