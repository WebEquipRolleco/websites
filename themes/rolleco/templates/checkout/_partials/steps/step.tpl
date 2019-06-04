<div id="summary_{$identifier}" class="col-lg-3">
	{if $step_is_reachable}
		<a href="#{$identifier}" class="checkout-step display-step {['-current'=>$step_is_current, '-reachable'=>$step_is_reachable, '-complete'=>$step_is_complete, 'js-current-step'=>$step_is_current]|classnames}" data-target="{$identifier}">
			<i class="fa fa-check-square"></i> 
			<span class="step-number">{$position}</span>
			&nbsp; {$title}
		</a>
	{else}
		<span class="checkout-step">
			<span class="step-number">{$position}</span>
			&nbsp; {$title}
		</span>
	{/if}
</div>