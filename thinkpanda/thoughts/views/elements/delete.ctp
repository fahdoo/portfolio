<?php if ($permission == true || $permission == 'true') : ?>
	<span class="bullet">&bull;</span>
	<a class="remove" title="Delete this Thought" href="Javascript:thinkPanda.forget('<?php echo $container; ?>', '/thoughts/thoughts/delete/<?php echo $id; ?>', 'Are you sure you want to forget this thought? ');">
		Forget
	</a>
<?php endif;?>