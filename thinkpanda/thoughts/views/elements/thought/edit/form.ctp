<?php 
	//echo $javascript->link('/vendors/ckeditor/ckeditor');

	/* prepare the textarea content to be edited */
	$content = str_replace('<br/>', '\n', $content);
	$content = str_replace("<span class='edit_blurb'>Edit blurb</span>","", $content);
	
	/* calculate how many lines to show in the dynamically sized textarea */
	$num_br = count(split("<br>", $content)) + count(split("<br/>", $content));
	$num_chars = strlen($content);
	$height = round($num_br + $num_chars/100);
	//debug($num_chars.' c:'.$content);
	
	/* prepare the id of the edit form/fields */
	$edit_id = $id.'_'.$field_name;
?>

<div id="edit_<?php echo $edit_id; ?>" style="display:none;" class="edit_container">
	<form id="form_<?php echo $edit_id; ?>" onsubmit="Javascript:thinkPanda.saveEdit('<?php echo $edit_id; ?>', '/thoughts/thoughts/edit/<?php echo $comment_id; ?>'); event.returnValue = false; return false;">
		<script type="text/javascript">
			if (typeof(CKEDITOR.instances["field_<?php echo $edit_id; ?>"]) != "undefined")
			{
				CKEDITOR.remove(CKEDITOR.instances["field_<?php echo $edit_id; ?>"]);
				//CKEDITOR.instances["field_<?php //echo $id; ?>"].destroy();
			}
		</script>
		<textarea class="ckeditor txt_edit" id="field_<?php echo $edit_id; ?>" rows="<?php echo $height; ?>"  onkeyup="thinkPanda.dynamicSize(this);"  onclick="thinkPanda.dynamicSize(this);" name="data[Field][<?php echo $field_name; ?>][0]"><?php echo $content; ?></textarea>
		<script type="text/javascript">
			CKEDITOR.replace("field_<?php echo $edit_id; ?>"); 
			//CKEDITOR.appendTo("field_<?php //echo $id; ?>"); 
			CKEDITOR.add(CKEDITOR.instances["field_<?php echo $edit_id; ?>"]);
		</script>
		<input class="btn_update" type="submit" value="Update" />
		<input class="btn_cancel" type="button" name="btnCancel" value="Cancel" onclick="javascript:thinkPanda.hideEdit('<?php echo $edit_id; ?>');"/>
		<img id="loading_form_<?php echo $edit_id; ?>" src="/img/icons/black-ajax-loader.gif" style="visibility:hidden" />
	</form>
</div>