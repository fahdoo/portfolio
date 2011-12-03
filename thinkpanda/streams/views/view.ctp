<?php //debug($this->viewVars);?>
<!-- Start of Entity View -->
<div class="streams view">
	<table class="entities">
		<tr>
			<td width="50%" height="100%" align="left">
				<table class="entities">
					<tr>
						<td>
							<span id='entity_favourite'>
								<?php if ($stream['User']['tagged_user_id'] == $my['id'] && $stream['User']['is_favourite'] == 1): ?>
									<a class="favorite" onclick="JavaScript:thinkPanda.ajaxFavouriteClicked('<?php echo $my['id'];?>', '<?php echo $stream['Stream']['id'];?>', '<?php echo $stream['User']['id'];?>', '0', '/streams_users/favourite', '');" title="Click to unfavourite this stream..."></a>
								<?php else: ?>
									<a class="unfavorite" onclick="JavaScript:thinkPanda.ajaxFavouriteClicked('<?php echo $my['id'];?>', '<?php echo $stream['Stream']['id'];?>', '<?php echo $stream['User']['id'];?>', '1', '/streams_users/favourite', '');" title="Favourite this stream!"></a>
								<?php endif; ?>
							</span>
							<h1>
								<?php echo $stream['Stream']['stream']; ?> - <?php echo $stream['Stream']['description']; ?>
							</h1>			
							<span>
								Created by 
								<a href="users/view/<?php echo $stream['CreationUser']['id']; ?>">
									<?php echo $stream['CreationUser']['fullname']; ?>
								</a> 
								on <?php echo $stream['Stream']['created']; ?>
							</span>
							<br />
							<span class="views">
								<b><?php echo $stream['Stream']['views']; ?></b> views
							</span>
						</td>
					</tr>
				</table>
			</td>
			<td width="30%" height="100%" align="right">
				<table class="entities">
					<tr>
						<td align="right">
							<div id="ratings">
								<?php echo $stream['Stream']['good_rating']; ?>
								<a class="rateup" onclick="JavaScript:thinkPanda.ajaxRatingsClick('1', '<?php echo $my['id'];?>', '<?php echo $stream['Stream']['id'];?>', '<?php echo $stream['User']['id'];?>', '/streams/rating', '');">
									+
								</a>
								<a class="ratedown" onclick="JavaScript:thinkPanda.ajaxRatingsClick('-1', '<?php echo $my['id'];?>', '<?php echo $stream['Stream']['id'];?>', '<?php echo $stream['User']['id'];?>', '/streams/rating', '');">
									-
								</a>
								<?php echo $stream['Stream']['bad_rating']; ?>
							</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>
<!-- End of streams view -->

<div id="viewer"  class="streams">
	<form id="entityInfo">		
	<?php
			echo $form->input('Entity.id', array('type'=>'hidden', 'value' => $entityOn['id']));
			echo $form->input('Entity.controller', array('type'=>'hidden', 'value' => $entityOn['controller']));
			echo $form->input('Entity.action', array('type'=>'hidden', 'value' => $entityOn['action']));
	?>
	</form>
	<?php  	// Using CakePHP Elements to make the views modular 
			// See: /views/elements/river.ctp
			// http://book.cakephp.org/view/97/Elements
		
		echo $this->element('river',
				array(
					'streamsList' => $streamsList,
					'usersList' => $usersList,
					'tagsList' => $tagsList,
					'entityOn' => $entityOn,
					'selectStreamId' => $selectStreamId, 
					'selectFilterType' => $selectFilterType, 
					'selectFilterId' => $selectFilterId
				)
			);
	?>
</div> 
<!-- End of viewer -->

