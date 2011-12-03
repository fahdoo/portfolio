<div id="add-citation-fields-wrapper">
	<div id="list-citation-fields" class="citation-fields">
		<?php
			if(isset($type)):		
				echo $form->input('Type.id', array('type'=>'hidden', 'value' => $type['id'])); 
				echo $form->input('Type.type', array('type'=>'hidden', 'value' =>  $type['type'])); 
				echo $form->input('Type.class', array('type'=>'hidden', 'value' =>  $type['class'])); 
			endif;

			if(isset($fields)){
				$const = 0;
				foreach($fields AS $field){
					switch($field['Field']['format_id']){
						case 1: // text
							echo $form->input('Field.'.$field['Field']['class'].'.'.$const, array('label' => $field['Field']['field'], 'class'=> 'optional'));
							break;
						case 2: // textarea
							echo $form->input('Field.'.$field['Field']['class'].'.'.$const, array('label' => $field['Field']['field'], 'class'=> 'optional', 'type'=> 'textarea'));	
							break;
					}
				}
			}
		?>
	</div>
	<div class="submit">
		<input type="submit" value="add citation"/>
	</div>
	<img id="loading_formCitation" src="/img/icons/black-ajax-loader.gif" style="visibility:hidden" />	
</div>