<?php $thoughts = $data['thoughts'];?>
<?php //debug($thoughts); ?>

<?php if(isset($thoughts['message'])):?>
	<p class="fetchError"><?php echo $thoughts['message']; ?></p>
<?php else:
	foreach ($thoughts as $thought) : 
		$showReplies = "thinkPanda.showReplies('#ul_replybox_".$thought['Comment']['id']."', ".$thought['Comment']['id'].", ".$thought['Comment']['reply_count'].");";
		$showTags = $thought['Stream']['id'].',0,0,'.$thought['Comment']['id'].', 1';	
		$revealThought = "thinkPanda.revealThought('#".$thought['container']."', ".$thought['Comment']['id'].", '/comments_users/markAs/', '#stream_".$thought['Stream']['id']."');".$showReplies."event.returnValue = false; return false;";
		$markAs = "thinkPanda.markAs('#".$thought['container']."', ".$thought['Comment']['id'].", '/comments_users/markAs/', '#stream_".$thought['Stream']['id']."');event.returnValue = false; return false;";				
		 ?>	
	
		<li id="<?php echo $thought['container'];?>" class="<?php echo $thought['cssClass']?>">
	
			<div class="thoughtbox">
				<div class="glimpse">
					<div style="position:absolute; height:100%; width:100%; z-index: 0; top:0px;" onclick="<?php echo $revealThought;?>"></div>
					<div class="picture">
						<?php if(false): // Taking out till this is OPTIMIZED?>
						<a href="/users/dashboard/<?php echo $thought['User']['id']; ?>" target="_blank">
							<img src="<?php echo $thought['User']['picture']; ?>" width="25px">
						</a>		
						<?php endif;?>
					</div>
					<div class="postedby">
						<span class="author">
							<a href="/users/dashboard/<?php echo $thought['User']['id']; ?>" target="_blank" title="Dashboard: <?php echo $thought['User']['fullname'];?>">
								<span class="fullname"><?php echo $thought['User']['fullname'];?></span>
								<span class="username"><?php echo $thought['User']['username'];?></span>
							</a>
						</span>
					</div>
					<div class="previewline"  onclick="<?php echo $revealThought;?>">
						<span class="glimpse_blurb">
							<emphasis>
								<?php 
									echo $this->element('preview', array(
										'data'		=> array('thought' => $thought), 
										'plugin' 	=> $thought['Widget']['class']
									));
								?>
							</emphasis>
						</span>						
					</div>	
					<?php 
					// Fahd: Should be able to share to FB regardless if you made it or not (4/4/2010)
					if (($thought['Widget']['widget'] == "Thoughts" || $thought['Widget']['widget'] == "Documents") && ($thought['Edit']['permission'] || true)): ?>
						<div class="thought_share glimpse_action" style="">
							<?php
								$blurb = $thought['Content']['blurb'][0];
								$blurb = str_replace("<br />", " ", $blurb);
								$blurb = str_replace("\n", " ", $blurb);
								$blurb = str_replace("\t", " ", $blurb);
								$blurb = str_replace("'", "\'", $blurb);
							?>
						</div>
					<?php endif; ?>	
				</div>
				<div class="thought" id ="reveal_<?php echo $thought['container']; ?>">
					<div class="thought_toolbar">				
						<a onclick="thinkPanda.toggleSelf('#tags_<?php echo $thought['Comment']['id'];?>');">Add to a Topic</a>
						<?php if($my['id']!=0):?>	
							<span class="bullet">&bull;</span>
							<a id="<?php echo $thought['container'];?>_markAs" onclick="<?php echo $markAs;?>">
								<?php if($thought['new'] == true):?>
									Mark as Read
								<?php else:?>
									Mark as Unread
								<?php endif;?>
							</a>
							<?php
							echo $deleteLink = $this->element('delete',
								array(
									'permission'=> $thought['Delete']['permission'],
									'container'	=> $thought['container'], //this is the container id
									'id'		=> $thought['Comment']['id'] //this is the comment id
								)
							);
							?>	
						<?php endif;?>				
					</div>
					
					<?php 
						echo $this->element('/topicBox',
							array(
								'data'	=> $thought, 
								'plugin' => 'thoughts'
							)
						);
					?>	
					<div id="show_<?php echo $thought['container']; ?>" class="blurb">
						<span class="content_text">
							<?php 
								echo $this->element('thought', array(
									'data'		=> array('thought' => $thought), 
									'plugin' 	=> $thought['Widget']['class']
								));
							?>
						</span>
					</div>
					
					
					<?php 
						echo $this->element('/replyBox',
							array(
								'data'	=> $thought, 
								'plugin' => 'thoughts'
							)
						);
					?>	
				</div>

				<?php if(true): //enable action menu: true ?>
				<div class="action_menu">

					<?php if(false):
						echo $thoughtFavourite = $this->element('/activity/links/favourite',
							array(
								'container_id'		=> $thought['container'], 
								'entity_id'			=> $thought['Comment']['id'],
								'entity_user_id'	=> $thought['User']['id'],
								'is_favourite'		=> $thought['CommentsUser']['is_favourite'], 
								'my'				=> $my,
							)
						);
						endif;
					?>
					<span class="created time">
						<?php echo $thought['modified_time']; ?>		
					</span>
					
					<div class="clearfix"></div>
					<div id="action_menu_items" class="comments_count items">
						<?php 
							echo $this->element('credit',
								array(
									'type'	=> $thought['Type']['type'], 
									'widget'=> $thought['Widget']['widget'],
									'stream'=> $thought['Stream'], 
									'tags'	=> $thought['Tag'],
									'comment_id' => $thought['Comment']['id'],
									'plugin' => 'thoughts', 
									'type_class' => $thought['Type']['class'], 
									'thought' => $thought
								)
							);
						?>	
						<?php if ($thought['modified_time'] != $thought['created_time'] && false) :?>
							<span class="modified_timestamp">
							 on
								<?php echo $thought['created_time']; ?>
							</span>
						<?php endif;?>						
					</div>

					<div class="glimpse_action comments_count">
						<a class="reply_text" onclick="<?php echo $revealThought;?>">
							+ Reply 
							<span id="reply_<?php echo $thought['Comment']['id']; ?>_count" class="count" ><?php echo $thought['Comment']['reply_count']; ?></span>
							<span class="notification"></span>
						</a>
					</div>
					<?php if(false){
						echo $thoughtRating = $this->element('/activity/links/rating',
							array(
								'container_id'		=> $thought['container'], 
								'entity_id'			=> $thought['Comment']['id'],
								'entity_user_id'	=> $thought['User']['id'],
								'my'				=> $my,
								'Comment'			=> strtolower($thought['Type']['type']),
								'good_rating'		=> $thought['Comment']['good_rating'],
								'bad_rating'		=> $thought['Comment']['bad_rating']
							)
						);
						}
					?>
					
				</div>
				<?php endif;?>
				<div class="clearfix"></div>
			</div>
		</li>
	<?php endforeach;?>
	<?php if(!empty($data['paging'])):?>
		<div id="paginateResults" class="paginateResults"><a class="more" onclick="thinkPanda.Filters.setPaginate('paginate', 1, <?php echo $data['paging']['handler']; ?>,   <?php echo $data['paging']['updateID']; ?>);thinkPanda.Filters.loadPaginate(<?php echo $data['paging']['handler']; ?>,   <?php echo $data['paging']['updateID']; ?>, 0);">More</a></div>
	<?php endif;?>
<?php endif;?>
