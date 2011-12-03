<div id="usersToApprove" class="sidebar_box">
	<div  id="usersToApproveList">
		<ul>
			<?php if (count($streamsUserToApprove) > 0) :?>
				<?php foreach ($streamsUserToApprove as $users):?>
					<h3><span class="total new"><?php echo count($users);?></span>Thinkers Requesting Access</h3>
					<?php foreach ($users as $user):?>
						<li id="userToApprove_<?php echo $user['User']['id'];?>">
							<div class="userbox">
								<div class="picture">
									<?php 
										$imageLink = $user['User']['picture'];
											
										echo $html->link(
											$html->image($imageLink, array("alt" => $user['User']['fullname'])),
											array('plugin' => NULL, 'controller' => 'users', 'action'=>'dashboard', $user['User']['id']),
											null, null, false
										);
									?>
								</div>
								<div class="content">
									<div class="actions">
										<a class="accept" id="approveStreamsUser_<?php echo $user['StreamsUser']['id']; ?>" onclick="widget_thinkers.approveStreamsUser('<?php echo $user['StreamsUser']['id']; ?>', '#approveStreamsUser_<?php echo $user['StreamsUser']['id']; ?>');" title="Approve stream membership">Approve</a>									</div>	
									<?php echo $html->link(__($user['User']['fullname'], true), array('plugin' => NULL, 'controller' => 'users', 'action'=>'dashboard', $user['User']['id']), array('class' => 'fullname')); ?>
									<div class="clearfix"></div>
								</div>
								<div class="clearfix"></div>
							</div>
						</li>
					<?php endforeach; ?>
				<?php endforeach; ?>
			<?php endif;?>
		</ul>
	</div>			
</div>
