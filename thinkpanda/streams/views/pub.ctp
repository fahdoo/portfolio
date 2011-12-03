<div id="viewer" class="streams guest">				
	<?php if(true): 
		echo $this->element('river',
				array(
					'streamsList' => $streamsList,
					'selectStreamId' => $selectStreamId,
					'guest' => true 
				)
			);
		endif;
	?>
</div>
