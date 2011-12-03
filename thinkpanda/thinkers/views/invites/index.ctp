<?php echo $html->css('/thinkers/css/invites.css?'.time()); ?>

<center><h1>Find or Invite your Contacts</h1></center>

<script type="text/javascript">
	var tabs = new Array("email", "facebook", "personal");
	function toggleInviteTabs(tab)
	{
		for (var i = 0; i < tabs.length; i++)
		{
			if (tabs[i] == tab)
			{
				jQuery('#invite-'+tabs[i]).show();
				jQuery('#invite-tab-'+tabs[i]+' a').addClass('active');
			}
			else
			{
				jQuery('#invite-'+tabs[i]).hide();
				jQuery('#invite-tab-'+tabs[i]+' a').removeClass('active');
			}
		}
	}
</script>

<ul id="invite-tabs">
	<li id="invite-tab-email"><a class="<?php if ($tab == "email") {echo "active";} ?>" onclick="JavaScript:toggleInviteTabs('email');">Email Services</a></li>
	<li id="invite-tab-facebook"><a class="<?php if ($tab == "facebook") {echo "active";} ?>" onclick="JavaScript:toggleInviteTabs('facebook');">Facebook</a></li>
	<li id="invite-tab-personal"><a class="<?php if ($tab == "personal") {echo "active";} ?>" onclick="JavaScript:toggleInviteTabs('personal');">Personal Invites</a></li>
</ul>

<div id="invite-email" style="display:<?php if ($tab == "email") {echo "block";} else {echo "none";}?>;">
	<?php if(!empty($followUsers)):?>
	<div class="users edit invite">
		<div class="tp_panel">
			<h3>Thinkers Found!</h3>
			<b>You are now following these Thinkers. You can unfollow them from the Thinkers app on your <a href="/users/dashboard">Dashboard</a>.</b>
			<div style="padding: 5px 10px;">
		<?php foreach($followUsers AS $followUser):?>
			<span style="margin: 2px 5px;">
				<img src="<?php echo $followUser['User']['picture'];?>" height="16px"/>&nbsp;<a href="/users/dashboard/<?php echo $followUser['User']['id'];?>"><?php echo $followUser['User']['fullname'];?></a>
			</span>
		<?php endforeach;?>
			</div>
		</div>
	</div>
	<?php endif;?>
	
	<?php if(isset($services)):?>
	<div class="users edit invite">
		<h2>Step 1 (optional): Import Contacts</h2>
		<div class="important">
			<p>We <b>do not</b> store your login or password information</p>
		</div>
		<?php 
			echo $form->create('formInviteFetch', array('id'=>'formInviteFetch', 'url'=> '/thinkers/invites/'));
				echo $form->input('User.source', array('options' => $services, 'style'=>'width:250px;', 'label'=> 'Service'));
				echo $form->input('User.login', array('style'=>'width:250px;'));
				echo $form->input('User.password', array('style'=>'width:250px;'));
			echo $form->end('Fetch Contacts'); 
		?>
	</div>
	<?php endif;?>
	
	
	<?php if (isset($contacts)): ?>
		<div class="users edit invite">
			<h2>Step 2: Invite</h2>
			<div class="important">
				<b>Are your contacts on Thinkpanda?</b>
				<p><b>If so</b>: we will add them to your network (i.e. "Follow")</p>
				<p><b>If not</b>: we will send them an invitation on your behalf</p>
			</div>
			<?php echo $form->create('formInviteContacts', array('id'=>'formInviteContacts', 'url'=> '/thinkers/invites/'));?>
			<br/>
			<label for="UserContacts">Select Imported Contacts</label>
			<p style="padding: 5px;">
				<a onclick="selectAll('#formInviteContacts');">Select All</a> - <a onclick="unselectAll('#formInviteContacts');">Unselect All</a>
			</p>
			<div style="height:500px; overflow-y:auto; padding: 2px; margin:10px; border: 1px solid #aaa; background-color:#f7f7f7;">
			<?php
				echo $form->input('User.contacts', array('options' => $contacts, 'multiple'=>'checkbox', 'value'=>'checked', 'label' => ''));
				echo '<div class="clearfix"></div>';
			?>
			</div>
			<script>
				selectAll('#formInviteContacts');
				function selectAll(id){
					jQuery(id+' input[type=checkbox]').attr('checked', true)
				}
				function unselectAll(id){
					jQuery(id+' input[type=checkbox]').attr('checked', false)
				}			
			</script>
			
			<?php
				echo $form->input('User.message', array('type'=>'textarea', 'label' => 'Personalized Message', 'rows' => 8, 'value'=> "I'm inviting you to join in sharing your ideas, research and knowledge on Thinkpanda.\n\nIt's great for people that work on a lot of different collections - be it for school, research, work or personal interests. We can discuss and organize thoughts, conversations, links and any assets related to collections we are working on - publicly or privately.\n\nAnd they have a cute Panda logo."));
				echo '<br/><br/>';
			echo $form->end('Invite and Follow'); 	
			?>
		
		</div>
	<?php endif; ?>
</div>
<!-- end of invite-email div -->

<div id="invite-facebook" style="display:<?php if ($tab == "facebook") {echo "block";} else {echo "none";}?>" class="users edit invite">
	<?php if (!empty($usersServices)):?>
		<?php foreach($usersServices as $usersService):?>
			<?php if ($usersService['UsersService']['service_id'] == 1):?>
			
				<?php if (isset($usersService['UsersService']["isLoggedIn"]) && $usersService['UsersService']["isLoggedIn"]): ?>							
					<?php echo $this->element("/services/facebook/forms/request", array(
						"service_key" => $usersService['UsersService']['service_key']
					)); ?>				
				<?php else: ?>					
					<?php echo $this->element("/services/facebook/buttons/login", array()); ?>					
				<?php endif; ?>
			<?php endif;?>
		<?php endforeach;?>
	<?php else: ?>
		<?php echo $this->element("/services/facebook/buttons/connect", array()); ?>
	<?php endif;?>
</div>

<div id="invite-personal" style="display:<?php if ($tab == "personal") {echo "block";} else {echo "none";}?>" class="users edit invite">
	<div class="users edit invite">
		<h2>Personal Invites</h2>
		<div class="important">
			<b>Are your contacts on Thinkpanda?</b>
			<p><b>If so</b>: we will add them to your network (i.e. "Follow")</p>
			<p><b>If not</b>: we will send them an invitation on your behalf</p>
		</div>
		<?php 
		echo $form->create('formInvitePersonalContacts', array('id'=>'formInvitePersonalContacts', 'url'=> '/thinkers/invites/'));
		echo $form->input('User.emails', array('type'=>'textarea', 'label' => 'Emails (use commas to separate multiple emails)', 'rows' => 2, 'value'=> $presetEmails));
		echo $form->input('User.message', array('type'=>'textarea', 'label' => 'Personalized Message', 'rows' => 8, 'value'=> "I'm inviting you to join in sharing your ideas, research and knowledge on Thinkpanda.\n\nIt's great for people that work on a lot of different collections - be it for school, research, work or personal interests. We can discuss and organize thoughts, conversations, links and any assets related to collections we are working on - publicly or privately.\n\nAnd they have a cute Panda logo."));
			echo '<br/><br/>';
		echo $form->end('Invite and Follow'); 	
		?>
	
	</div>
</div>

<div  class="users edit invite" style="text-align:right;"><a href="/users/dashboard">&raquo; Go to your Dashboard</a></div>