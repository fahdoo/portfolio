	<?php if(!empty($streamsUser) && $streamsUser):?>
		<?php foreach ($streamsUser as $users):?>
			<?php foreach ($users as $user):?>
				<li id="thinker_<?php echo $user['User']['id'];?>" class="left">
					<div class="usergrid">
						<div class="picture">
							<?php 
								$imageLink = $user['User']['picture'];
								
								echo $html->link(
									$html->image($imageLink, array('height'=>'25px')),
									array('plugin'=> null, 'controller'=> 'users', 'action'=>'dashboard', $user['User']['id']),
									array('title'=>  $user['User']['fullname']), null, false
								);
							?>
						</div>
						<?php if(false):?>
						<div class="content">
							<?php echo $html->link(__($user['User']['fullname'], true), array('plugin'=> null, 'controller'=> 'users', 'action'=>'view', $user['User']['id']), array('class' => 'fullname')); ?>
							
								<div class="about"><?php echo $user['User']['about'];?></div>
								<div class="actions">
									<?php echo $html->link('Send Email', array('plugin'=> null, 'controller'=> 'users','action'=>'email', $user['User']['id']));?>
									<div class="clearfix"></div>
								</div>	
						</div>
						<?php endif;?>	
						<div class="clearfix"></div>
					</div>
				</li>
			<?php endforeach; ?>
		<?php endforeach; ?>
	<?php else: ?>
	<?php endif?>
	
