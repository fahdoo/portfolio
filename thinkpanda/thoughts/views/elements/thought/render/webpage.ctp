<!--ordinary web pages, show page title -->
<a class="title content_text" target="_blank" href="<?php echo $url; ?>" title="<?php echo $url; ?>">
	<span id="<?php echo $container; ?>_websiteTitle">
		<?php 
			if(empty($title) || $title == "Untitled")
				echo $url;
			else
				echo $title; 
		?>
	</span>
</a>