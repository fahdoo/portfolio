<?php foreach($feeds AS $feed):?>
	<li id="<?php echo $feed['entity']['container']; ?>" class="feed activity<?php echo $feed['entity']['type']; ?>">		
		<div class="pagebox">
			<?php 
				echo $deleteLink = $this->element('/activity/links/delete',
					array(
						'delete' 	=> $feed['delete']
					)
				);
			?>	
			<div class="picture">
				<a class="url" href="<?php echo $feed['entity']['source']; ?>" title="Source: <?php echo $feed['entity']['source']; ?>" target="_blank">
				<img height="16px" width="16px" src="<?php echo $feed['entity']['favicon']; ?>"/>
				</a>
			</div>
			<div class="content">
				<div class="entity">
					<div class="permalink">
						<?php 
							echo $permaLink = $this->element('/activity/links/perma',
								array(
									'entity' => $feed['entity'],
									'edit' => $feed['edit']
								)
							);
						?>	
					</div>
												
					<div class="feedbox clearfix">
					<?php if(array_key_exists('page_'.$feed['entity']['id'], $articles)): ?>
						<?php 
							echo $this->element('/activity/feedArticles',
								array(
									'feed_id' => $feed['entity']['container'],
									'articles' => $articles['page_'.$feed['entity']['id']],
								)
							);
						?>						
					<?php endif;?>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
	</li>
<?php endforeach; ?>