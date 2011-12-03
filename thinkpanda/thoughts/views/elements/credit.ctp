<?php 
if(isset($stream)):
	if($stream['id'] == 2 || $stream['id'] == 604){
		$project = stripslashes($stream['stream']);
	}else if(isset($stream['access']) && $stream['access'] == 4){
		$project = stripslashes($stream['description']);
	}else if(isset($stream['stream'])){
		$project = stripslashes($stream['stream']);
	}else{
		$project = NULL;
	}
endif;
?>

<span class="thought_credit">
	<?php echo $type; ?> / <?php echo  $widget; ?>
	<?php if(isset($stream) && isset($stream['streamname'])  && isset($project)):?>
	/<b><a href="/p/<?php echo $stream['streamname'];?>" title="<?php echo strip_tags(h(stripslashes($stream['description']))); ?>" onclick="thinkPanda.Context.setStream('#stream_<?php echo $stream['id']; ?>', '.contextItem', <?php echo $stream['id']; ?>); return false;"><?php echo $project; ?></a></b>
	<?php endif; ?>
	<span class="tags_credit">
		<?php if(isset($tags)):?>
			<?php foreach($tags AS $tag):?>
				<span id="commentTag_<?php echo $comment_id;?>_<?php echo $tag['id'];?>" class="commentTagCredit">&nbsp;/&nbsp;<?php echo $tag['tag'];?></span>
			<?php endforeach; ?>
		<?php endif; ?>
	</span>
</span>
