<?php $src = $url.'?fullScreen=1&displayName='.$my['fullname'];?>
<?php $framename = 'etherpad_'.$container; ?>
	<?php echo 'Share this real-time document: ';?>
	<a class="title content_text" target="_blank" href="<?php echo $url; ?>" title="<?php echo $url; ?>">
		<?php echo $url; ?>
	</a>
<?php if($my['id'] != 0):?>
	<iframe id="<?php echo $framename;?>" width="100%" height="500px" frameborder="0" src="" style="display:none;" />
	<span id="<?php echo $container; ?>_websiteTitle">
		<p><b><?php echo $title;?></b></p>
	</span>
	<!--ordinary web pages, show page title -->
	<script>
		jQuery('#<?php echo $container;?> .glimpse').bind('click.iframe', function(e){
				var frame = jQuery('#<?php echo $framename;?>');
				frame.show().attr('src', '<?php echo $src;?>');
				jQuery(this).unbind(e);
			}
		);
	</script>
	<?php echo $this->element('/global/resizeFrame', array('framename' => $framename));?>
<?php endif;?>