<?php if (count($unrelatedUsers) > 0) :?>
	<div id="unrelatedUsers" class="focusItem">
		<h3><span class="total"><?php echo $paginate['total'];?></span>New Thinkers</h3>
		<div  id="unrelatedUsersList">
			<ul>
				<?php foreach ($unrelatedUsers as $unrelatedUser):?>
					<li id="unrelatedUser_<?php echo $unrelatedUser['User']['id'];?>">
						<?php
							echo $this->element('/users/userbox', array(
									'thinker'=> $unrelatedUser,
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
					'handler'	=> '/thinkers/thinkers/get_unrelated',
					'updateID'	=> 'unrelatedUsers',
					'function'	=> 'loadUser'
				)
			);
		?>					
	</div>						
<?php endif;?>