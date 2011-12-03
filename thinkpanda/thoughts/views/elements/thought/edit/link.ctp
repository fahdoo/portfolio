	<a class="<?php echo $cssClass; ?> editLink" title="<?php echo $hyperlinkTitle; ?>" onclick="Javascript:thinkPanda.showEdit('<?php echo $id; ?>_<?php echo $field_name; ?>');">
		<?php 
			if(isset($text)): echo $text;
			else: echo "Edit";
			endif;
		?>
	</a>
	
	<!-- class="edit icons <?php //echo $cssClass; ?>" -->