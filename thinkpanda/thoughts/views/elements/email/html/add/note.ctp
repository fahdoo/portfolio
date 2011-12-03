<?php 
/*debug($user);
debug($stream);
debug($data);
debug($plugin);*/
?>
<?php echo $data['User']['fullname']; ?> added a new Note to the &quot;<?php echo $stream['Stream']['stream']; ?>&quot; collection
<br /><br />
----------
<br /><br />
<?php echo $this->element('/email/html/render/blurb', array(
	'data'		=> $data,
	'version'	=> $comment['version'],
	'plugin'	=> $plugin
));?>
----------