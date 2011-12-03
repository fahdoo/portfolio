<?php if ($permission == true || $permission == 'true') {
	//render the edit link
	echo $this->element('/thought/edit/link',
		array(
			'id'			=> $id,
			'hyperlinkTitle'=> $hyperlinkTitle,
			'cssClass'		=> $cssClass,
			'field_name'	=> $field_name,
			'text'			=> $text
		)
	);
	
	//render the edit form
	echo $this->element('/thought/edit/form',
		array(
			'id'			=> $id, 
			'content'		=> $content, 
			'type'			=> $type,
			'field_name'	=> $field_name,
			'comment_id'	=> $comment_id
		)
	);
} ?>