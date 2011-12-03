<?php //debug($thoughts); ?>

<?php if(count($thoughts) == 0):?>
	<span class="fetchError">No replies yet.</span>
<?php endif;?>
<?php foreach ($thoughts as $thought) : ?>
<?php $markAs = "thinkPanda.markAs('#".$thought['container']."', ".$thought['Comment']['id'].", '/comments_users/markAs/', '#stream_".$thought['Stream']['id']."');event.returnValue = false; return false;"; ?>
<li id="<?php echo $thought['container'];?>" class="reply <?php  echo $thought['cssClass']?>">

	<div class="commentbox">
		<div class="content">
			<div class="picture" style="display:none;">
				<a href="/users/dashboard/<?php echo $thought['User']['id']; ?>">
					<img src="<?php echo $thought['User']['picture']; ?>" width="16px">
				</a>
			</div>
			<div class="by">
				<a class="author" href="/users/dashboard/<?php echo $thought['User']['id']; ?>"><?php echo $thought['User']['fullname'].' ('. $thought['User']['username'].')'; ?></a> replied on <?php echo $thought['created_time']; ?>
				<?php if($thought['User']['id'] == $my['id']):?>
					<span class="bullet">&bull;</span>
					<?php
					echo $deleteLink = $this->element('delete',
						array(
							'permission'=> $thought['Delete']['permission'],
							'container'	=> $thought['container'], //this is the container id
							'id'		=> $thought['Comment']['id'], //this is the comment id
							'plugin' 	=> $thought['Widget']['class']
						)
					);
					?>
				<?php endif;?> 
			</div>
			
			<div id="show_<?php echo $thought['container']; ?>_blurb" style="display:block;" class="blurb">
				<span class="content_text">
					<div id="<?php echo $thought['container']; ?>_blurb">
						<?php echo $thought['Content']['blurb'][0]; ?>
					</div>
					
					<?php if(false): //render edit
						echo $this->element('/thought/edit/render',
							array(
								'permission'	=> $thought['Edit']['permission'],
								'id'			=> $thought['container'],
								'hyperlinkTitle'=> "Edit Reply",
								'cssClass'		=> '',
								'field_name'	=> 'blurb',
								'content'		=> $thought['Content']['blurb'][0], 
								'type'			=> $thought['Type']['type'],
								'comment_id'	=> $thought['Comment']['id'],
								'text'			=> "Edit Reply"
							)
						);
						endif;
					?>
				</span>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>

</li>
<?php endforeach;?>