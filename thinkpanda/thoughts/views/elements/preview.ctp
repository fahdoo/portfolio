<?php 
$thought = $data['thought'];
if(isset($thought['Type']['type'])):
	if ($thought['Type']['type'] == 'Note' && isset($thought['Content']['blurb'][0])) 
	{
		echo strip_tags($thought['Content']['blurb'][0]); 
	}
	else
	{
		echo $this->element('thought/preview/page', array(
			'content'	=> $thought['Content'], 
		));
	}
endif;
?>