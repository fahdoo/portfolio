<ul id="thinkersList">
	<?php if(isset($streamsUser)):?>
		<?php foreach ($streamsUser as $users):?>
			<h3><span class="total"><?php echo count($users);?></span>Thinkers Participating in this Collection</h3>
			<?php foreach ($users as $user):?>
				<li id="thinker_<?php echo $user['User']['id'];?>">
					<div class="userbox">
						<div class="picture">
							<?php 
								$imageLink = $user['User']['picture'];
								
								echo $html->link(
									$html->image($imageLink, array("alt" => $user['User']['fullname'])),
									array('plugin'=> null, 'controller'=> 'users', 'action'=>'dashboard', $user['User']['id']),
									null, null, false
								);
							?>
						</div>
						<div class="content">
							<?php echo $html->link(__($user['User']['fullname'], true), array('plugin'=> null, 'controller'=> 'users', 'action'=>'view', $user['User']['id']), array('class' => 'fullname')); ?>
							<div class="about"><?php echo $user['User']['about'];?></div>
							<div class="actions">
								<?php echo $html->link('Send Email', array('plugin'=> null, 'controller'=> 'users','action'=>'email', $user['User']['id']));?>
								<div class="clearfix"></div>
							</div>							
						</div>
						<div class="clearfix"></div>
					</div>
				</li>
			<?php endforeach; ?>
		<?php endforeach; ?>
	<?php else: ?>
		<div class="noResults">No Thinkers participating yet.</div>
	<?php endif?>
</ul>	
