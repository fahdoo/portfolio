<?php if (count($thinkerBoardUsers) > 0):?>
	<div id="thinkerBoardUsers" class="focusItem">
		<div  id="thinkerBoardUsersList">
			<ul>
				<?php foreach ($thinkerBoardUsers as $thinkerBoardUser):?>
					
					<li id="thinkerBoardUser_<?php echo $thinkerBoardUser['User']['id'];?>">
						<?php
							echo $this->element('/users/userbox', array(
									'thinker'=> $thinkerBoardUser,
									'plugin' => 'thinkers'
								)
							);
						?>
					</li>
				<?php endforeach; ?>
				<div class="clearfix"></div>
			</ul>
		</div>				
	</div>						
<?php endif;?>