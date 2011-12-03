<div id="viewer" class="streams">				
	<?php  
		echo $this->element('river',
				array(
					'streamsList' => $streamsList,
					'selectStreamId' => $selectStreamId, 
				)
			);
	?>
</div>
