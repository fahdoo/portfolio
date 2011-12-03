<!-- Start of Entity View -->
<div class="discover index">
	<p id="intro_1">Thinkpanda is your all-in-one research <a title="Your Dashboard" href="<?php echo '/users/view/'.$my['id']?>">dashboard</a></p>
	<p id="intro_1" style="display:none;">Thinkpanda helps you <b>search, discover, discuss, organize and stay current</b>
across all of your collections.</p>
<p id="intro_2">Use Thinkpanda to help you <b>search, discover, discuss, organize and stay current</b> on collections that are either time-sensitive or long term, critical or interest-focused, collaborative or requiring controlled access.</p>	
	<p class="statline"><span class="stats"  id="stream_stats"><?php echo $stats['num_streams'];?></span> streams created by <span class="stats"  id="user_stats"><?php echo $stats['num_users'];?></span> thinkers spanning <span class="stats" id="tag_stats"><?php echo $stats['num_tags'];?></span> topics: <span  id="page_stats" class="stats"><?php echo $stats['num_pages'];?></span> links and <span  id="comment_stats" class="stats"><?php echo $stats['num_comments'];?></span> thoughts collected</p>
	<p>Here's a quick look at the most interesting, active and open-access <b>research streams</b> across Thinkpanda:</p>
</div>
<!-- End of pages view -->

<div id="viewer"  class="discover">
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
		
		/*echo $this->element('river',
				array(
					'entityOn' => $entityOn
				)
			);*/
		
		echo $this->element('river-server-render',
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

