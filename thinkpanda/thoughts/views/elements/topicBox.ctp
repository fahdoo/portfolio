<?php $inputText = 'add to topic';?>
<div id="tags_<?php echo $data['Comment']['id']; ?>" class="thought_action tags">
	<?php if($my['id'] != 0):?>
	<form id="tagbox_<?php echo $data['Comment']['id']; ?>"  class="tagbox" onsubmit="thinkPanda.addTag(this, '#tags_<?php echo $data['Comment']['id']; ?> ul', '#thoughts_<?php echo $data['Comment']['id']; ?>'); event.returnValue = false; return false;" method="post" action="/comments_tags/add">
		<div class="input text">
			<input id="TagTags"name="data[Tag][tags]" type="text"  onfocus ="JavaScript:thinkPanda.setFieldValue(this, '<?php echo $inputText;?>', 'focus');" onblur= "JavaScript:thinkPanda.setFieldValue(this, '<?php echo $inputText;?>', 'blur');" value="<?php echo $inputText;?>" autocomplete="off"/>
		</div>
		<input id="CommentId" type="hidden" value="<?php echo $data['Comment']['id']; ?>" name="data[Comment][id]"/>		
		<input id="StreamId" type="hidden" value="<?php echo $data['Stream']['id']; ?>" name="data[Stream][id]"/>		

		<div class="submit">
			<input type="submit" value="+ topic"/>
		</div>
		<img id="loading_tagbox_<?php echo $data['Comment']['id']; ?>" src="/img/icons/black-ajax-loader.gif" style="visibility:hidden" />
	</form>
	<a style="display:none;" onclick="thinkPanda.unselectAllTags();">Unselect All</a>
	<?php else:?>
		Add to Topic: <?php echo $this->element('global/guestCallToSignup');?>	
	<?php endif;?>
	
	<ul>
		<?php 	echo $this->element('tags/renderCommentsTag', array('comment_id' => $data['Comment']['id'], 'tags'=>$data['Tag'], 'stream_id' =>$data['Stream']['id']));?>
		<div class="clearfix"></div>
	</ul>
</div>
