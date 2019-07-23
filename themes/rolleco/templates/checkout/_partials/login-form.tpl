{extends file='customer/_partials/login-form.tpl'}

{block name='form_buttons'}
	<div class="text-right">
		<button class="continue btn btn-success bold" name="continue" data-link-action="sign-in" type="submit" value="1">
    		{l s='Continue' d='Shop.Theme.Actions'}
  		</button>
  	</div>
{/block}
