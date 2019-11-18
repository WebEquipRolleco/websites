{extends file='page.tpl'}

{block name='page_title'}
	{l s="Demande de SAV"}
{/block}

{block name='page_content_container'}
	<form method="post" enctype='multipart/form-data'>

		<div class="row">

			<div class="col-xs-12 col-lg-6">
				
				<div class="well">
					<i id="check_step_1" class="fa fa-check-square text-success" style="display:none"></i>
					<b>{l s="Etape 1 - Choix de la commande concernée."}</b>
				</div>

				<div class="form-group margin-top-15">
					<select class="form-control" id="selected_order" name="form[id_order]" required>
						<option value="0">-</option>
						{foreach from=$orders item=order}
							<option value="{$order->id}" {if $order->id == $id_order}selected{/if}>
								{$order->reference} - {$order->date_add|date_format:'d/m/Y'}
							</option>
						{/foreach}
					</select>
				</div>

				{foreach from=$orders item=order}
					<table id="details_{$order->id}" class="table table-details" style="display:none">
						<tbody>
							{foreach from=$order->getDetails() item=$detail}
								{if $detail->product_id or $detail->id_quotation_line}
									<tr>
										<td width="30px">
											<input type="checkbox" class="selected_details" name="form[id_detail][]" value="{$detail->id}" style="height:20px; width:20px">
										</td>
										<td>
											{if $detail->product_reference}
												<b>{$detail->product_reference}</b> - 
											{/if}
											<em class="text-muted">{$detail->product_name}</em>
										</td>
									</tr>
								{/if}
							{/foreach}	
						</tbody>
					</table>
				{/foreach}
		
			</div>

			<div class="col-xs-12 col-lg-6">

				<div class="well">
					<i id="check_step_2" class="fa fa-check-square text-success" style="display:none"></i>
					<b>{l s="Etape 2 - Vérifiez vos coordonnées."}</b>
				</div>

				<div class="form-group margin-top-15">
					<input type="text" id="email" class="form-control" name="form[email]" value="{$customer->email}">
				</div>

			</div>

		</div>

		<div class="well top-space">
			<i id="check_step_3" class="fa fa-check-square text-success" style="display:none"></i>
			<b>{l s="Etape 3 - Saisissez l'objet de votre demande."}</b>
			<em class="text-muted pull-right">{l s="Vous pourrez compléter votre demande par la suite."}</em>
		</div>

		<div class="row">
			<div class="col-xs-12 col-lg-12">
				<div class="form-group margin-top-15">
					<textarea rows="7" id="message" name="form[message]" class="form-control" required></textarea>
					<div id="lenght_help" class="text-muted text-right"></div>
				</div>
			</div>
			<div class="col-xs-12 col-lg-6">
				<div class="form-group">
					<label>{l s="Ajouter des pièces jointes à votre demande"} - <em class="text-muted">{l s="facultatif"}</label>
					<input type="file" multiple="multiple" name="attachments[]" class="form-control">
				</div>
			</div>
			<div class="col-xs-12 margin-top-10">
				<input type="checkbox" id="notice_on_delivery" name="form[notice_on_delivery]" value="1">
				<label for="notice_on_delivery">{l s="J'ai déclaré le SAV sur le bon du transporteur"}</label>
			</div>
		</div>

		<div class="well top-space text-center">
			<button type="submit" id="submit_request" class="btn btn-success" disabled>
				<b>{l s="J'ai vérifié mes informations et je confirme ma demande"}</b>
			</button>
		</div>

	</form>
{/block}

{block name="custom_js"}
	<script>

		$(document).ready(function() {
			checkSteps();

			$('#selected_order').on('change', function() {

				$('.table-details').hide();
				$('.selected_details').prop('checked', false);
				$('#details_'+$(this).val()).show();

				checkSteps();
			});
			$('#selected_order').trigger('change');
			
		});

		$('.selected_details').on('change', function() {
			checkSteps(); 
		});

		$('#email').on('keyup', function() {
			checkSteps();
		});

		$('#message').on('keyup', function() {
			checkSteps();
		});

		function checkSteps() {

			var step_1 = $('.selected_details:checked').size();
			if(step_1)
				$('#check_step_1').show();
			else
				$('#check_step_1').hide();

			var step_2 = isEmail($('#email').val());
			if(step_2)
				$('#check_step_2').show();
			else
				$('#check_step_2').hide();

			var min_char = 100;
			var length = $('#message').val().trim().length;

			var step_3 = length >= min_char;
			if(step_3) {
				$('#check_step_3').show();
				$('#lenght_help').html('');
			}
			else {
				$('#check_step_3').hide();
				$('#lenght_help').html(length + " / " + min_char + " minimum");
			}

			if(step_1 && step_2 && step_3)
				$('#submit_request').prop('disabled', false);
			else
				$('#submit_request').prop('disabled', true);
		}

		{literal}
			function isEmail(email) {
			  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			  return regex.test(email);
			}
		{/literal}

	</script>
{/block}