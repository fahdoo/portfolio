<?php $framename = 'pptx_'.$container; ?>

<iframe  id="<?php echo $framename;?>" src="http://docs.google.com/viewer?url=<?php echo $url; ?>&embedded=true" width="100%" height="500px" frameborder="0" ></iframe>

<!--ordinary web pages, show page title -->
<span id="<?php echo $container; ?>_websiteTitle">
	<a class="title content_text" target="_blank" href="<?php echo $url; ?>" title="<?php echo $url; ?>">
		<?php 
			if(empty($title) || $title == "Untitled")
				echo $url;
			else
				echo $title; 
		?>
	</a>
</span>
<?php echo $this->element('/global/resizeFrame', array('framename' => $framename));?>