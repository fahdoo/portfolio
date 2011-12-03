<?php $thought = $data['thought'];?>
	<div id="show_<?php echo $thought['container']; ?>" class="blurb">
		<?php 
			echo $edit = $this->element('/activity/links/edit',
				array(
					'permission'	=> $thought['Edit']['permission'],
					'id'			=> 'show_'.$thought['container'],
					'title'			=> "Edit ".$thought['Type']['type'], 
					'cssClass'		=> ''
				)
			);
		?>
		<span class="content_text">
			<?php 
				// TODO: go to thought rendering specific to each type
				if(isset($thought['Content'])): 
					foreach($thought['Content'] AS $field => $fieldArray):
						foreach($fieldArray AS $content):
							echo $field.': '.$content.'<br/><br/>';
						endforeach;
	 				endforeach; 
	 			endif; 
	 		?>
		</span>
	</div>
	
	<?php 
		if(false):
		echo $thoughtEdit = $this->element('/activity/links/activityEdit',
			array(
				'containerId'	=> 'reveal_'.$thought['container'], 
				'controller'	=> $thought['Edit']['blurb_controller'],
				'content'		=> $thought['Content'], 
				'type'			=> $thought['Type']['type'],
				'permission'	=> $thought['Edit']['permission']
			)
		);
		endif;
	?>

