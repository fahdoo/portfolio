<?php if(isset($stream)):?>
<?php echo $html->css('/thinkers/css/thinkers.css?'.time()); ?>

<?php echo $javascript->link('/thinkers/js/thinkers.js?'.time()); ?>

<div id="thinkersPanel" class="panel lite">
	<div class="">
		<h3><span class="total"><?php echo $streamsCount;?></span>Thinkers following this Collection</h3>
				
		<!-- SIDEBAR -->
		<?php if($my['id']!=0):?>

			<div class="actions" style="float:left; margin:8px 5px;">
				<?php if(empty($streamsUser)||!array_key_exists($my['id'], $streamsUser[$stream['Stream']['id']])):	
					$followDisplay = 'block';
					$unfollowDisplay = 'none';
				else:	
					$followDisplay = 'none';
					$unfollowDisplay = 'block';
				endif;?>		
					<a id="followStream_<?php echo $stream['Stream']['id'];?>"  class="accept" href="JavaScript:thinkPanda.joinStream('<?php echo $stream['Stream']['id']; ?>', '<?php echo $stream['Stream']['stream']; ?>', '<?php echo $my['picture']; ?>','<?php echo $my['id']; ?>');" style="display:<?php echo $followDisplay;?>">
						Follow
					</a>					
					<a id="unfollowStream_<?php echo $stream['Stream']['id'];?>" class="delete" style="display:<?php echo $unfollowDisplay;?>"href="Javascript:thinkPanda.removeStream('<?php echo $stream['Stream']['id']; ?>', '/streams_users/delete/<?php echo $stream['Stream']['id']; ?>/Stream/streamBox', 'Are you sure you want to unfollow this collection &#8220;<?php echo $stream['Stream']['stream']; ?>&#8221;?','<?php echo $my['id']; ?>');" >

						Unfollow
					</a>								
				
			</div>
		<?php endif;?>
		<div id="streamusers_add" class="lite_adduser">
			<?php if($my['id']!=0):?>

				
				<script>widget_thinkers.getRelatedUsers();</script>
				<form id="formAddUser" class="filter_users" action="/streams_users/add" method="post" onsubmit="widget_thinkers.addUser(this); event.returnValue = false; return false;">
				<?php
					$value = 'invite to collection';
					echo $form->input('UserFilter.fullname', array('name'=>'data[StreamsUser][fullname]', 'label'=>'', 'value' => $value, 'onfocus' => "Javascript:thinkPanda.setFieldValue(this, '".$value."', 'focus');", 'onblur' => "Javascript:thinkPanda.setFieldValue(this, '".$value."', 'blur');"));
					echo $form->input('UserFilter.fullnameId', array('type'=>'hidden', 'value' => '', 'name'=>'data[StreamsUser][tagged_user_id]'));
					echo $form->input('UserFilter.fullnameVerify', array('type'=>'hidden', 'value' => '', 'name'=>'data[StreamsUser][fullnameVerify]'));
					
					echo $form->submit('+');
				?>		
					<img id="loading_formAddUser" src="/img/icons/black-ajax-loader.gif" style="visibility:hidden" />
				</form>	
			<?php endif;?>	

			<?php if($stream['Stream']['user_id'] == $my['id'] && false):?>
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
				<ul id="thinkersGrid">

				<?php
					echo $this->element('/lite/thinkers',
						array('plugin' => 'thinkers', 'streamsUser' => $streamsUser)
					);
				?>
					<div class="clearfix"></div>	
				</ul>							
			</div>
		</div>	
		
		<div class="clearfix"></div>
	</div>

</div>

<div id="invite_user_popup" style="display:none" class="popup">
	<div class="popup_base" onclick="jQuery('.popup').fadeOut();"></div>
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
<?php endif;?>