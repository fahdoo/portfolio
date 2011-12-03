<?php 
$thought = $data['thought'];
//debug($thought);

$file = 'page';
if (isset($thought['Type']['type']) && $thought['Type']['type'] == 'Note') 
	$file = 'note';

$content = $thought['Content'];
if (array_key_exists($thought['Comment']['version'], $thought['Content']))
	$content = $thought['Content'][$thought['Comment']['version']];

echo $this->element('thought/'.$file, array(
	'content'	=> $thought['Content'], 
	'type'		=> $thought['Type'],
	'edit'		=> $thought['Edit'],
	'container'	=> $thought['container'],
	'comment_id'=> $thought['Comment']['id']
));
?>