<?php 
/*debug($user);
debug($stream);
debug($data);
debug($plugin);*/
?>

<?php $originalData = json_decode($originalThought['Comment']['data'], true); ?>

<?php echo $data['User']['fullname']; ?> replied to the following Thought in the &quot;<?php echo $stream['Stream']['stream']; ?>&quot; collection
<br /><br />
----------
<br /><br />
<span style="text-decoration:underline;">Thought:</span>
<br />
<div style="padding-left:30px;">
<?php echo $this->element('/email/html/render/blurb', array(
	'data'		=> $originalData,
	'version'	=> $originalThought['Comment']['version'],
	'plugin'	=> $plugin
));?>
</div>

<span style="text-decoration:underline;"><?php echo $data['User']['fullname']; ?>&rsquo;s Reply:</span>
<br />
<div style="padding-left:30px;">
	<?php echo $this->element('/email/html/render/blurb', array(
		'data'		=> $data,
		'version'	=> $comment['version'],
		'plugin'	=> $plugin
	));?>
</div>
----------