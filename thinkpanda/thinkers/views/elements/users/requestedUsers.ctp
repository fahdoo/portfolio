<?php if (count($requestedUsers) > 0) :?>
<div id="requestedUsers" class="sidebar_box">
	<h3><span class="total"><?php echo $paginate['total'];?></span>Waiting for Approval</h3>
	<div  id="requestedUsersList">
		<ul>
			<?php foreach ($requestedUsers as $requestedUser):?>
				<li id="requestedUser_<?php echo $requestedUser['User']['id'];?>">
					<div class="userbox">
						<div class="picture">
							<?php 
								$imageLink = $requestedUser['User']['picture'];
									
								echo $html->link(
									$html->image($imageLink, array("alt" => $requestedUser['User']['fullname'])),
									array('plugin' => NULL, 'controller'=> 'users', 'action'=>'dashboard', $requestedUser['User']['id']),
									null, null, false
								);
							?>
						</div>
						<div class="content">
							<?php echo $html->link(__($requestedUser['User']['fullname'], true), array('plugin' => NULL, 'controller'=> 'users', 'action'=>'dashboard', $requestedUser['User']['id']), array('class' => 'fullname')); ?>
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
				'handler'	=> '/thinkers/thinkers/get_requested',
				'updateID'	=> 'requestedUsers',
				'function'	=> 'loadUser'
			)
		);
	?>		
</div>						
<?php endif;?>
		
