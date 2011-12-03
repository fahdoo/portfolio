<?php $thoughts = $data['thoughts'];?>
<?php if(isset($thoughts['message'])):?>
	<p class="fetchError"><?php echo $thoughts['message']; ?></p>
<?php else:
	foreach ($thoughts as $thought) : ?>
		<li id="<?php echo $thought['container'];?>" class="<?php echo $thought['cssClass']?>">
	
			<div class="thoughtbox">
				<div class="glimpse"  onclick="thinkPanda.toggleSelf('#reveal_<?php echo $thought['container']; ?>'); event.returnValue = false; return false;">
					<div class="picture">
						<a href="/users/view/<?php echo $thought['User']['id']; ?>" target="_blank">
							<img src="<?php echo $thought['User']['picture']; ?>" width="25px">
						</a>		
					</div>
					<div class="postedby">
						<span class="author">
							<?php echo $thought['User']['fullname'].' ('. $thought['User']['username'].')'; ?>
						</span>
					</div>
					<div class="previewline">
						<?php $contentLine = array(); if(isset($thought['Content'])): foreach($thought['Content'] AS $field):?>
							<?php 
								foreach($field AS $content):
									$contentLine[] = $content;
								endforeach;
							?>
						<?php endforeach; endif; ?>
						<span class="glimpse_blurb"><?php echo '<b>Posted a '.$thought['Type']['type'].'</b> using <b>'.$thought['Widget']['widget'].'.</b>&nbsp;&nbsp;'; ?><emphasis><?php echo implode(' ', $contentLine); ?></emphasis></span>
					</div>	
				</div>
				<?php if(true):?>
				<div class="thought" id ="reveal_<?php echo $thought['container']; ?>">
					<?php 
						echo $this->element('thought', array('data'=>array('thought' => $thought)));
					?>
				</div>
				<?php endif;?>
				<?php if(true): //enable action menu: true ?>
				<div class="action_menu">
					<?php if(true):
					echo $deleteLink = $this->element('/activity/links/delete',
						array(
							'delete' 	=> $thought['Delete']
						)
					);
						endif;
					?>
					<?php if(true):
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
					<div class="created time">
						<?php echo $thought['created_time']; ?>		
					</div>
					<?php 
						echo $replyBox = $this->element('/activity/links/replyBox',
							array(
								'data'	=> $thought, 
							)
						);
					?>	
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
<?php endif;?>
