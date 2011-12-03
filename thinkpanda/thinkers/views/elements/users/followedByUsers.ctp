<?php if (count($followedByUsers) > 0):?>
	<div id="followedByUsers" class="focusItem">
		<h3><span class="total"><?php echo $paginate['total'];?></span>Followers</h3>
		<div  id="followedByUsersList">
			<ul>
				<?php foreach ($followedByUsers as $followedByUser):?>
					
					<li id="followedByUser_<?php echo $followedByUser['User']['id'];?>">
						<?php
							echo $this->element('/users/userbox', array(
									'thinker'=> $followedByUser,
									'plugin' => 'thinkers'
								)
							);
						?>
					</li>
				<?php endforeach; ?>
				<div class="clearfix"></div>
			</ul>
		</div>
		<?php
			echo $pagination = $this->element('/global/paginate',
				array(
					'paginate' 	=> $paginate,
					'handler'	=> '/thinkers/thinkers/get_followedby',
					'updateID'	=> 'followedByUsers',
					'function'	=> 'loadUser'
				)
			);
		?>					
	</div>						
<?php endif;?>