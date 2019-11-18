{extends file='page.tpl'}

{*block name='page_title'}
	{l s="SAV N°"} {$sav->reference}
{/block*}

{block name='page_content_container'}

	<table class="table combinations-table">
		<thead>
			<tr>
				<th colspan="2">{l s="SAV N°"} {$sav->reference}</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="bold">{l s="Statut"}</td>
				<td class="text-center">
					<span class="label-{$sav->getStatusClass()} bold">
						{$sav->getStatusLabel()}
					</span>
				</td>
			</tr>
			<tr>
				<td class="bold">{l s="Date de création"}</td> 
				<td class="text-center">{$sav->date_add|date_format:'d/m/Y'}</td>
			</tr>
			<tr>
				<td class="bold">{l s="Commande concernée"}</td>
				<td class="text-center">{$sav->getOrder()->reference}</td>
			</tr>
			<tr>
				<td class="bold">{l s="Produits concernés"}</td>
				<td class="text-center">
					{foreach from=$sav->getProductDetails() item=details}
						<div>
							{if $details->product_reference}<b>{$details->product_reference}</b> - {/if}
							<em class="text-muted">{$details->product_name}</em>
						</div>
					{/foreach}
				</td>
			</tr>
			<tr>
				<td colspan="2" class="text-center">
					{if $sav->notice_on_delivery}
						<span class='text-success'>
							<i class="fa fa-check-square"></i> {l s="J'ai déclaré le SAV sur le bon du transporteur"}
						</span>
					{else}
						<span class='text-danger'>
							<i class="fa fa-times"></i> {l s="Je n'ai pas déclaré le SAV sur le bon du transporteur"}
						</span>
					{/if}
				</td>
			</tr>
		</tbody>
	</table>

	<div class="row">

		<div class="col-xs-12 col-lg-8">
			<table class="table combinations-table">
				<thead>
					<tr>
						<th class="bg-blue">{l s="Messages"}</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$sav->getMessages(true) item=message}
						<tr>
							<td>
								<div class="bold">
									<i class="fa fa-user"></i> &nbsp; 
									{$message->getSender()->firstname} {$message->getSender()->lastname}
									- <em class="text-muted">{$message->date_add|date_format:'d/m/Y à H:i'}</em>
								</div>
								<div class="text-muted inner-space" style="padding:10px">{$message->message}</div>
							</td>
						</tr>
					{/foreach}
					{if $sav->isEditable()}
						<tr>
							<td>
								<form method="post">
									<textarea rows="5" name="new_message" style="width:100%" required></textarea>
									<button type="submit" class="btn btn-success btn-block bold">
										{l s="Ajouter"}
									</button>
								</form>
							</td>
						</tr>
					{/if}
				</tbody>
			</table>
		</div>

		<div class="col-xs-12 col-lg-4">
			<table class="table combinations-table">
				<thead>
					<tr>
						<th class="bg-blue">{l s="Photos"}</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$sav->getPictures() item=name}
						<tr>
							<td class="text-center">
								<a href="{$sav->getDirectory()}{$name}" target="_blank">
									<img src="{$sav->getDirectory()}{$name}" class="col-lg-12" />
								</a>
								{if $sav->isEditable()}
									<div class="col-lg-12">
										<a href="{$link->getPageLink('AfterSales')}?sav={$sav->reference}&remove={$name}" class="btn btn-block btn-danger">
											<i class="fa fa-trash"></i>
										</a>
									</div>
								{/if}
							</td>
						</tr>
					{/foreach}
					{if $sav->isEditable()}
						<tr>
							<td>
								<form method="post" enctype='multipart/form-data'>
									<input type="file" class="form-control" name="new_file" style="background: white">
									<button type="submit" class="btn btn-success btn-block bold">
										{l s="Ajouter"}
									</button>
								</form>
							</td>
						</tr>
					{/if}
				</tbody>
			</table>
		</div>

	</div>

{/block}