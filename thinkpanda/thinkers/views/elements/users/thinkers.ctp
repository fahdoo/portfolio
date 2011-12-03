<?php if($user['User']['id'] == $my['id']) $belongsTo = "your"; else $belongsTo = "this" ?>
<div id="thinkersNetwork" class="focusItem active">
	<ul id="thinkersList">
		<h3><span class="total"><?php echo $users['paginate']['total'];?></span>Thinkers Followed</h3>
		<?php if(isset($users['users']) && $users['paginate']['total'] > 0):?>
			<?php foreach ($users['users'] as $user):?>
				<li id="thinker_<?php echo $user['User']['id'];?>">
					<?php
						echo $this->element('/users/userbox', array(
								'thinker'=> $user,
								'plugin' => 'thinkers'
							)
						);
					?>
				</li>
			<?php endforeach; ?>
			<div class="clearfix"></div>
			<?php
				echo $pagination = $this->element('/global/paginate',
					array(
						'paginate' 	=> $users['paginate'] ,
						'handler'	=> $users['handler'],
						'updateID'	=> 'thinkersNetwork',
						'function'	=> 'loadUser'
					)
				);
			?>	
		<?php else: ?>
			<div class="noResults">Nothing here...yet.</div>
		<?php endif?>
	</ul>	
</div>