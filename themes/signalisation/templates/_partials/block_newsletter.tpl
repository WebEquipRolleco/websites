<input type="hidden" id="newsletter_url" value="{$link->getPageLink('newsletter')}">
<div class="col-xs-12 col-lg-4 margin-top-sm">
    <h3 class="margin-top-15"><i class="fa fa-envelope-open-text"></i> {l s="Newsletter"}</h3>

    <div>
	   	<form method="post" class="form-inline text-center margin-top-15" name="register_newsletter">
	   		<input type="text" class="form-control" name="email" placeholder="{l s='Mon e-mail...'}" autocomplete="off" required>
	   		<button type="submit" class="btn">{l s='OK'}</button>
	  	</form>
	</div>
	<div id="ajax_modal_newsletter" class="iziModal">
	    <i class="fa fa-spinner fa-spin"></i>
	    {l s="Un instant, nous vous inscrivons à la newsletter..."}
	</div>

</div>