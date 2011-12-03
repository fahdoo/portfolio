<?php if (count($usersToApprove) > 0) :?>
<div id="usersToApprove" class="focusItem">
	<h3><span class="total new"><?php echo $paginate['total'];?></span>Connection Requests</h3>
	<div  id="usersToApproveList">
		<ul>
			<?php foreach ($usersToApprove as $userToApprove):?>
				<li id="userToApprove_<?php echo $userToApprove['User']['id'];?>">
					<div class="userbox">
						<div class="picture">
							<?php 
								$imageLink = $userToApprove['User']['picture'];
									
								echo $html->link(
									$html->image($imageLink, array("alt" => $userToApprove['User']['fullname'])),
									array('plugin' => NULL, 'controller'=> 'users', 'action'=>'dashboard', $userToApprove['User']['id']),
									null, null, false
								);
							?>
						</div>
						<div class="content">
							<div class="actions">
								<a class="accept" id="approveUser_<?php echo $userToApprove['User']['id']; ?>" onclick="widget_thinkers.approveUser('<?php echo $userToApprove['User']['id']; ?>', '#approveUser_<?php echo $userToApprove['User']['id']; ?>');" title="Approve User">Approve</a>	
							</div>	
							<?php echo $html->link(__($userToApprove['User']['fullname'], true), array('plugin' => NULL, 'controller'=> 'users', 'action'=>'dashboard', $userToApprove['User']['id']), array('class' => 'fullname')); ?>
							<div class="clearfix"></div>
						</div>
						<div class="clearfix"></div>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>	
	<?php
		echo $pagination = $this->element('/global/paginate',
			array(
				'paginate' 	=> $paginate,
				'handler'	=> '/thinkers/thinkers/get_usersToApprove',
				'updateID'	=> 'usersToApprove',
				'function'	=> 'loadUser'
			)
		);
	?>			
</div>

<?php endif;?>