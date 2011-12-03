<?php if(empty($thinker['User']['fullname'])):
	$thinker['User']['fullname'] = $thinker['User']['email'];
endif;?>
<div class="userbox">
	<div class="picture">
		<?php 
			$imageLink = $thinker['User']['picture'];
			
			echo $html->link(
				$html->image($imageLink, array("alt" => $thinker['User']['fullname'])),
				array('plugin' => NULL, 'controller'=> 'users', 'action'=>'dashboard', $thinker['User']['id']),
				null, null, false
			);
		?>
	</div>
	<div class="content">
		<?php echo $html->link(__($thinker['User']['fullname'], true), array('plugin' => NULL, 'controller'=> 'users', 'action'=>'dashboard', $thinker['User']['id']), array('class' => 'fullname')); ?>

		<div class="actions">	
			<?php if($my['id'] != 0):?>
				<?php if($thinker['User']['id']!=$my['id']):?>					
					<?php if(empty($thinker['UserRelation']['isApproved'])):?> 
						<a class="accept" id="connectUser_<?php echo $thinker['User']['id']; ?>" onclick="thinkPanda.connectUser('<?php echo $thinker['User']['id']; ?>', 'connectUser_<?php echo $thinker['User']['id']; ?>');">Follow</a>
					<?php else:?>		
						<a id="connectUser_<?php echo $thinker['User']['id']; ?>" onclick="thinkPanda.unconnectUser('<?php echo $thinker['User']['id']; ?>', 'connectUser_<?php echo $thinker['User']['id']; ?>');">Unfollow</a>								
					<?php endif;?>
				<?php endif;?>	
			<?php endif;?>
			<?php if(isset($thinker[0]['streams_count'])):?>
				<span style="font-weight:bold; color:#555;background-color:transparent;"><?php echo $thinker[0]['streams_count'];?> collections</span>
			<?php endif;?>
			<div class="clearfix"></div>
		</div>								
		<div class="about"><?php echo $thinker['User']['about'];?></div>
		<div class="status"><?php echo $thinker['User']['status'];?></div>
								
	</div>
	<div class="clearfix"></div>
</div>