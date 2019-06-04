{if $rating}
	<span class="awesome-star-rating">
		{for $x=1 to $rating}
			<span class="fa-stack fa-1x awesome-star rating-{$rating}">
			    <i class="fa fa-square fa-stack-2x"></i>
			    <i class="fa fa-star fa-stack-1x"></i>
			</span>
		{/for}
		{if $rating < 5}
			{for $x=$rating to 4}
				<span class="fa-stack fa-1x awesome-star grey-star">
			    	<i class="fa fa-square fa-stack-2x"></i>
			    	<i class="fa fa-star fa-stack-1x"></i>
				</span>
			{/for}
		{/if}
	</span>
{/if}