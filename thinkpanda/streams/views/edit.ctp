<div class="streams edit">
	<div id="streamSettings" class="form">
		<h2><?php echo '"'.$this->data['Stream']['stream'].'" Stream Settings';?></h2>
		<?php echo $form->create('Stream');?>
			<p>The <?php echo '"'.$this->data['Stream']['stream'].'"';?> stream was created by <a href="/users/view/<?php echo $this->data['CreationUser']['id']; ?>"><?php echo $this->data['CreationUser']['fullname']; ?></a></p>
			<br/>
			<?php echo $form->input('Stream.stream'); ?>
			<?php echo $form->input('Stream.description'); ?>
			<br/>
			<div class="access"><span class="access_label">Access Level</span>
				<?php echo $form->select('Stream.access', $accessLevels, $this->data['Stream']['access'], array(), false); ?>
				<div class="infobox">
					<p><b>Hidden: </b>Hidden from your dashboard and any search results. Visible only to participants on their own dashboard.</p>
					<p><b>Closed: </b>Thoughts and Topics are hidden from everyone, except for Participants. Other Thinkers can view the description and can request access.</p>
					<p><b>Open: </b>Anyone on Thinkpanda can view and add to the Thoughts without joining the Stream.</p>
				</div>
			</div>
			<br/>
			<div class="streamsUsers_permission">
				<label class="streamsUsers_permission_label">User Permissions</label>
				<table>
				<?php foreach ($streamsUsers as $streamsUser) : ?>
					<tr>
						<td>
							<span id="fullname_<?php echo $streamsUser['StreamsUser']['id'];?>"><a href="/users/view/<?php echo $streamsUser['StreamsUser']['tagged_user_id'];?>" target="_blank"><?php echo $streamsUser['TaggedUser']['fullname']; ?></a></span>
						</td>
						<td>
							<?php if ($streamsUser['StreamsUser']['permissions'] == 3) : ?>
								<span id="invited_<?php echo $streamsUser['StreamsUser']['id'];?>"><?php echo 'Invited' ?></span>
								<!-- Revoke Invite link -->
								<a id="revoke_<?php echo $streamsUser['StreamsUser']['id'];?>" title="Revoke Invite" onclick="JavaScript: if(confirm('Are you sure you want to revoke the invite from <?php echo $streamsUser['TaggedUser']['fullname'];?> to this stream?')){jQuery('#fullname_<?php echo $streamsUser['StreamsUser']['id'];?>, #invited_<?php echo $streamsUser['StreamsUser']['id'];?>').attr('style', 'text-decoration: line-through'); jQuery('#revoke_<?php echo $streamsUser['StreamsUser']['id'];?>').hide(); jQuery('#undoRevoke_<?php echo $streamsUser['StreamsUser']['id'];?>').show(); jQuery('#StreamsUserRevokeInvite<?php echo $streamsUser['StreamsUser']['id'];?>').val(1);}">X</a>
								<!-- Undo Revoke Invite link -->
								<a id="undoRevoke_<?php echo $streamsUser['StreamsUser']['id'];?>" title="Undo Revoke Invite" onclick="JavaScript: jQuery('#fullname_<?php echo $streamsUser['StreamsUser']['id'];?>, #invited_<?php echo $streamsUser['StreamsUser']['id'];?>').attr('style', 'text-decoration: none'); jQuery('#undoRevoke_<?php echo $streamsUser['StreamsUser']['id'];?>').hide(); jQuery('#revoke_<?php echo $streamsUser['StreamsUser']['id'];?>').show(); jQuery('#StreamsUserRevokeInvite<?php echo $streamsUser['StreamsUser']['id'];?>').val(0);" style="display:none">Undo</a>
								
								<!-- hidden field to indicate if invite has been revoked -->
								<?php echo $form->input('StreamsUser.revokeInvite_'.$streamsUser['StreamsUser']['id'], array('type'=>'hidden', 'value' => '0')); ?>
							<?php elseif ($streamsUser['StreamsUser']['permissions'] == 4): ?>
								<?php echo $form->select('StreamsUser.permissions_'.$streamsUser['StreamsUser']['id'], $permissions, $streamsUser['StreamsUser']['permissions'], array(), false); ?>	
							<?php else: ?>
								<?php echo $form->select('StreamsUser.permissions_'.$streamsUser['StreamsUser']['id'], $memberPermissions, $streamsUser['StreamsUser']['permissions'], array(), false); ?>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach;?>
				</table>
			</div>
			<div style="text-align:right; margin:20px 5px 5px; color:#666;border-top:1px solid #ccc;padding:5px 3px 0px;">To save the new settings, please press "Update" below</div>
		<?php echo $form->end('Update');?>
	</div>
	
	<!-- TODO: add user management of streams here -->
	
	<!-- end TODO -->
</div>
