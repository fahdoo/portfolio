<?php 
	//$javascript->link('/vendors/ckeditor/ckeditor', false); 
	
?>

<div id="replies_<?php echo $data['Comment']['id']; ?>" class="thought_action replies">
	<a style="display:none;" class="hide" href="Javascript:thinkPanda.toggleSelf('#replies_<?php echo $data['Comment']['id']; ?>')">
		Hide Replies
	</a>
	<?php if($my['id'] != 0):?>
	<form id="replyform_<?php echo $data['Comment']['id']; ?>"  class="replyform" onsubmit=" thinkPanda.addReply(this); event.returnValue = false; return false;" method="post" action="/thoughts/thoughts/add">
		
		<!--script type="text/javascript">
			if (typeof(CKEDITOR.instances["reply_<?php //echo $data['Comment']['id']; ?>_CommentComment"]) != "undefined")
			{
				CKEDITOR.remove(CKEDITOR.instances["reply_<?php //echo $data['Comment']['id']; ?>_CommentComment"]);
			}
		</script-->
		<div class="input textarea">
			<label for="CommentComment"/>
			<textarea id="reply_<?php echo $data['Comment']['id']; ?>_CommentComment" class="ckeditor" rows="1" cols="30" name="data[Field][blurb][0]" onkeyup="thinkPanda.dynamicSize(this);" onclick="thinkPanda.dynamicSize(this);"></textarea>
			<!--script type="text/javascript">
				CKEDITOR.replace("reply_<?php //echo $data['Comment']['id']; ?>_CommentComment"); 
				CKEDITOR.add(CKEDITOR.instances["reply_<?php //echo $data['Comment']['id']; ?>_CommentComment"]);
			</script-->
		</div>
		
		<!--input id="EntityReply" type="hidden" value="true" name="data[Entity][reply]"/>
		<input id="CommentParentId" type="hidden" value="<?php //echo $data['Comment']['id']; ?>" name="data[Comment][parent_id]"/-->
		
		<div class="submit">
			<input type="submit" value="+ reply"/>
		</div>
		
		<img id="loading_replyform_<?php echo $data['Comment']['id']; ?>" src="/img/icons/black-ajax-loader.gif" style="visibility:hidden" />
	</form>
	<?php else:?>
		Replies: <?php echo $this->element('global/guestCallToSignup');?>	
	<?php endif;?>
	<ul id="ul_replybox_<?php echo $data['Comment']['id']; ?>" ></ul>
</div>
