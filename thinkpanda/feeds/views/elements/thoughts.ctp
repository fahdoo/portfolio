<?php $feeds = $data['feeds'];
	  $articles = $data['articles'];
?>
<?php if(isset($feeds['message'])):?>
	<p class="fetchError"><?php echo $feeds['message']; ?></p>
<?php else:
	foreach ($feeds as $feed) : 
		$revealThought = "thinkPanda.revealThought('#".$feed['container']."', ".$feed['Comment']['id'].", '/comments_users/markAs/', '#stream_".$feed['Stream']['id']."'); event.returnValue = false; return false;"; ?>	

		<li id="<?php echo $feed['container'];?>" class="<?php echo $feed['cssClass']?>">
			<div class="thoughtbox">
				<div class="glimpse">
					<div style="position:absolute; height:100%; width:100%; z-index: 0; top:0px;" onclick="<?php echo $revealThought;?>"></div>
					<div class="picture">
						<a href="/users/dashboard/<?php echo $feed['User']['id']; ?>" target="_blank">
							<img src="<?php echo $feed['User']['picture']; ?>" width="25px">
						</a>		
					</div>
					<div class="postedby">
						<span class="author">
							<a href="/users/dashboard/<?php echo $feed['User']['id']; ?>" target="_blank" title="Dashboard: <?php echo $feed['User']['fullname'];?>">
								<span class="fullname"><?php echo $feed['User']['fullname'];?></span>
								<span class="username"><?php echo $feed['User']['username'];?></span>
							</a>
						</span>
					</div>					
					<div class="previewline"  onclick="<?php echo $revealThought;?>">
						<span class="glimpse_blurb">
							<emphasis>
								<?php 
									echo $this->element('preview', array(
										'data'		=> array('thought' => $feed), 
										'plugin' 	=> 'feeds'
									));
								?>
							</emphasis>
						</span>
					</div>	
				</div>
				<?php if(true):?>
				<div class="thought" id ="reveal_<?php echo $feed['container']; ?>">
					<?php 
						echo $this->element('articles',
							array(
								'feed_id' => $feed['container'],
								'articles' => $articles[$feed['Comment']['id']],
							)
						);
					?>		
					<?php 
						//echo $this->element('thought', array('data'=>array('thought' => $feed)));
					?>
				</div>
				<?php endif;?>
				<?php if(true): //enable action menu: true ?>
				<div class="action_menu">
					<?php
					echo $deleteLink = $this->element('delete',
						array(
							'permission'=> $feed['Delete']['permission'],
							'container'	=> $feed['container'], //this is the container id
							'id'		=> $feed['Comment']['id'], //this is the comment id
							'plugin'	=> 'thoughts'
						)
					);
					?>
					<?php if(true):
						echo $feedFavourite = $this->element('/activity/links/favourite',
							array(
								'container_id'		=> $feed['container'], 
								'entity_id'			=> $feed['Comment']['id'],
								'entity_user_id'	=> $feed['User']['id'],
								'is_favourite'		=> $feed['CommentsUser']['is_favourite'], 
								'my'				=> $my,
							)
						);
						endif;
					?>
					<span class="created time">
						<?php echo $feed['created_time']; ?>		
					</span>
					<div id="action_menu_items" class="comments_count items">
						<?php 
							echo $this->element('credit',
								array(
									'type'	=> $feed['Type']['type'], 
									'widget'	=> $feed['Widget']['widget'], 
									'stream'	=> $feed['Stream'], 
									'plugin' => 'thoughts'
								)
							);
						?>	

						<?php if ($feed['modified_time'] != $feed['created_time']) :?>
							<span class="modified_timestamp">
							 on
								<?php echo $feed['created_time']; ?>
							</span>
						<?php endif;?>						
					</div>

					
					<?php 
						if(false):
						echo $replyBox = $this->element('/activity/links/replyBox',
							array(
								'data'	=> $feed, 
							)
						);
						endif;
					?>	
					<?php if(false){
						echo $feedRating = $this->element('/activity/links/rating',
							array(
								'container_id'		=> $feed['container'], 
								'entity_id'			=> $feed['Comment']['id'],
								'entity_user_id'	=> $feed['User']['id'],
								'my'				=> $my,
								'Comment'			=> strtolower($feed['Type']['type']),
								'good_rating'		=> $feed['Comment']['good_rating'],
								'bad_rating'		=> $feed['Comment']['bad_rating']
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
<?php endif;?>
