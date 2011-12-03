<?php 
/*debug($user);
debug($stream);
debug($data);
debug($plugin);*/
?>
<?php echo $data['User']['fullname']; ?> added a new <?php echo $data['Type']['type']?> link to the &quot;<?php echo $stream['Stream']['stream']; ?>&quot; collection
<br /><br />
----------
<br /><br />
<?php echo $this->element('/email/html/render/webpage', array(
	'data'		=> $data,
	'version'	=> $comment['version'],
	'plugin'	=> $plugin
));?>
<br /><br />
<?php echo $this->element('/email/html/render/blurb', array(
	'data'		=> $data,
	'version'	=> $comment['version'],
	'plugin'	=> $plugin
));?>
----------