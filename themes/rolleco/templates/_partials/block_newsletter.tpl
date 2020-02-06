<input type="hidden" id="newsletter_url" value="{$link->getPageLink('newsletter')}">
<div id="newsletter_box" class="col-lg-6">
	<h3 class="section-title">
		<i class="fa fa-envelope-open-text"></i>
	   	{l s="Newsletter"}
	</h3>
	<p class="text-center">
	  	{l s="Inscrivez vous à la newsletter Rolleco et gagnez 5%% sur votre prochaine commande"}
	</p>
	<div>
	   	<form method="post" class="form-inline text-center" name="register_newsletter">
	   		<input type="text" class="form-control" name="email" placeholder="{l s='Mon e-mail...'}" autocomplete="off" required>
	   		<button type="submit" class="btn btn-primary">{l s='OK'}</button>
	  	</form>
	</div>
	<div id="ajax_modal_newsletter" class="iziModal">
	    <i class="fa fa-spinner fa-spin"></i>
	    {l s="Un instant, nous vous inscrivons à la newsletter..."}
	</div>
</div>