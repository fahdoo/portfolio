<?php echo $html->css('/thinkers/css/thinkers.css?'.time()); ?>

<?php echo $javascript->link('/thinkers/js/thinkers.js?'.time()); ?>

<div id="thinkersPanel" class="panel">
	<?php
		echo $this->element('taskbar',
			array('plugin' => 'thinkers', 
				'following' => $users['paginate']['total'],
				'followers' => $paginationFollowedByUsers['total'], 
				'thinkerBoard' => $paginationThinkerBoardUsers['total'],
				'new' => $paginationUnrelatedUsers['total'],
		));
	?>	
	<div class="focusItems">
		<!-- CONNECTIONS -->
		<div id="user_connections" class="connections">		
			<div id="usersResults" class="focusItem"></div>	
			<div id="thinkers">
				<?php
					echo $this->element('/users/thinkers',
						array('plugin' => 'thinkers', 'users' => $users)
					);
				?>							
			</div>
			<?php
				echo $this->element('/users/followedByUsers',
					array('plugin' => 'thinkers', 'followedByUsers' => $followedByUsers, 'paginate' => $paginationFollowedByUsers)
				);
			?>	
			<?php if($user['User']['id'] == $my['id']): ?>					
				<?php
					echo $this->element('/users/thinkerBoard',
						array('plugin' => 'thinkers', 'thinkerBoardUsers' => $thinkerBoardUsers, 'paginate' => NULL)
					);
				?>				
				<?php
					echo $this->element('/users/unrelatedUsers',
						array('plugin' => 'thinkers', 'unrelatedUsers' => $unrelatedUsers, 'paginate' => $paginationUnrelatedUsers)
					);
				?>	
			<?php endif;?>		
		</div>	
		
		<div class="clearfix"></div>
	</div>

</div>
