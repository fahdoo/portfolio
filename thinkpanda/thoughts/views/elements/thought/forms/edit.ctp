<?php 
	$javascript->link('/vendors/ckeditor/ckeditor', false); 
?>

<?php if($permission == true || $permission == 'true') : ?>
	<!-- console.log($containerId, $controller, $content, type, permission) -->
	<?php 
		$content = str_replace('<br/>', '\n', $content);
		$content = str_replace("<span class='edit_blurb'>Edit blurb</span>","", $content);
		$num_br = count(split("<br>", $content)) + count(split("<br/>", $content));
		$num_chars = strlen($content);
		$height = round($num_br + $num_chars/100);
		//debug($num_chars.' c:'.$content);
	?>
	
	<div id="edit_<?php echo $containerId; ?>" style="display:none;" class="edit_container">
		<form id="form_<?php echo $containerId; ?>" onsubmit="Javascript:thinkPanda.saveEdit('<?php echo $containerId; ?>', '<?php echo $controller; ?>');event.returnValue = false; return false;">
			<script type="text/javascript">
				if (typeof(CKEDITOR.instances["field_<?php echo $containerId; ?>"]) != "undefined")
				{
					CKEDITOR.remove(CKEDITOR.instances["field_<?php echo $containerId; ?>"]);
					//CKEDITOR.instances["field_<?php //echo $containerId; ?>"].destroy();
				}
			</script>
			<textarea class="ckeditor txt_edit" id="field_<?php echo $containerId; ?>" rows="<?php echo $height; ?>"  onkeyup="thinkPanda.dynamicSize(this);"  onclick="thinkPanda.dynamicSize(this);"><?php echo $content; ?></textarea>
			<script type="text/javascript">
				CKEDITOR.replace("field_<?php echo $containerId; ?>"); 
				//CKEDITOR.appendTo("field_<?php //echo $containerId; ?>"); 
				CKEDITOR.add(CKEDITOR.instances["field_<?php echo $containerId; ?>"]);
			</script>
			<input class="btn_update" type="submit" value="Update" />
			<input class="btn_cancel" type="button" name="btnCancel" value="Cancel" onclick="javascript:thinkPanda.hideEdit('<?php echo $containerId; ?>');"/>
			<img id="loading_edit_<?php echo $containerId; ?>" src="/img/icons/black-ajax-loader.gif" style="visibility:hidden" />
		</form>
	</div>
<?php endif; ?>