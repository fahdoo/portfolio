<?php if (count($suggestedUsers) > 0):?>
	<div id="suggestedUsers" class="focusItem">
		<h3><span class="total"><?php echo $paginate['total'];?></span>Suggested Thinkers</h3>
		<div  id="suggestedUsersList">
			<ul>
				<?php foreach ($suggestedUsers as $suggestedUser):?>
					
					<li id="suggestedUser_<?php echo $suggestedUser['User']['id'];?>">
						<?php
							echo $this->element('/users/userbox', array(
									'thinker'=> $suggestedUser,
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
					'handler'	=> '/thinkers/thinkers/get_suggested',
					'updateID'	=> 'suggestedUsers',
					'function'	=> 'loadUser'
				)
			);
		?>					
	</div>						
<?php endif;?>