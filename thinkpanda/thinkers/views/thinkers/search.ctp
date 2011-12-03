<?php 
$users['display'] = "block";
$users['category'] = "active";	
?>
<div class="search index">
	<div id="query"><?php echo 'You searched Thinkpanda for: <b>'. $q.'</b>';?></div>
	<div id="browseUsers" class="users connections searchResults" style="display:<?php echo $users['display']; ?>;">
		<?php
			echo $this->element('/users/browseUsers',
				array('users' => $users)
			);
		?>	
	</div>
	<div class="clearfix"></div>
</div>
