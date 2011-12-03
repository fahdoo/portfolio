<?php echo $html->css('/thinkers/css/thinkers.css?'.time()); ?>

<?php echo $javascript->link('/thinkers/js/thinkers.js?'.time()); ?>

<div id="thinkersPanel" class="panel">
	<div class="">
		
		<!-- SIDEBAR -->
		
		<div id="stream_sidebar" class="sidebar">
			<script>widget_thinkers.getRelatedUsers();</script>
			<form id="formAddUser" class="filter_users" action="/streams_users/add" method="post" onsubmit="widget_thinkers.addUser(this); event.returnValue = false; return false;">
			<?php
				echo $form->input('UserFilter.fullname', array('name'=>'data[StreamsUser][fullname]', 'label'=>'', 'value' => 'add a participant or email', 'onfocus' => "Javascript:thinkPanda.setFieldValue(this, 'add a participant or email', 'focus');", 'onblur' => "Javascript:thinkPanda.setFieldValue(this, 'add a participant or email', 'blur');"));
				echo $form->input('UserFilter.fullnameId', array('type'=>'hidden', 'value' => '', 'name'=>'data[StreamsUser][tagged_user_id]'));
				echo $form->input('UserFilter.fullnameVerify', array('type'=>'hidden', 'value' => '', 'name'=>'data[StreamsUser][fullnameVerify]'));
				
				echo $form->submit('+');
			?>		
				<img id="loading_formAddUser" src="/img/icons/black-ajax-loader.gif" style="visibility:hidden" />
			</form>		
			<?php if($stream['Stream']['user_id'] == $my['id']):?>
			<?php
				echo $this->element('/streams/usersToApprove',
					array('plugin' => 'thinkers', 'streamsUserToApprove' => $streamsUserToApprove, 'stream' => $stream)
				);
			?>	
			<?php endif ?>
		</div>	
		<div class="clearfix"></div>

	
		<!-- CONNECTIONS -->
		<div id="stream_connections" class="connections">	
			<div id="thinkers">
				<?php
					echo $this->element('/streams/thinkers',
						array('plugin' => 'thinkers', 'streamsUser' => $streamsUser, 'stream' => $stream)
					);
				?>							
			</div>
		</div>	
		
		<div class="clearfix"></div>
	</div>

</div>

<div id="invite_user_popup" style="display:none" class="popup">
	<div class="popup_base"></div>
	<form id="form_invite_user_popup" action="/thinkers/thinkers/invite" method="post" onsubmit="widget_thinkers.invite(this); event.returnValue = false; return false;">
		<?php
		echo $form->input('Invite.emails', array('type'=>'textarea', 'label' => 'To: (separate emails with commas)', 'rows' => 2));
		echo $form->input('Invite.message', array('type'=>'textarea', 'label' => 'Message: (Optional)', 'rows' => 2));
		?>
		<br/>
		<input type="button" name="btnCancel" value="Cancel" onclick="javascript:widget_thinkers.hide_invite(); event.returnValue = false; return false;"/>
		<input type="submit" value="Invite" />
		<img id="loading_form_invite_user_popup" src="/img/icons/black-ajax-loader.gif" style="visibility:hidden" />
	</form>		
</div>