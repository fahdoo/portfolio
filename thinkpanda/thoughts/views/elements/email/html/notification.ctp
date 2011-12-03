<?php 
/*debug($user);
debug($stream);
debug($comment);
debug($plugin);*/
?>

<?php 
$data = json_decode($comment['data'], true);

$template = "note";
if ($data['Type']['class'] != "note")
	$template = "webpage";

echo $this->element('/email/html/'.$mode.'/'.$template, array(
	'data'				=> $data,
	'stream'			=> $stream,
	'user'				=> $user,
	'originalThought'	=> $originalThought, //used by replies only
	'plugin'			=> $plugin
));
?>
<br /><br />
To view or reply to this Thought, go to the &quot;<a href="http://www.thinkpanda.com/p/<?php echo $stream["Stream"]["streamname"]; ?>"><?php echo $stream["Stream"]["stream"]; ?></a>&quot; collection or to your <a href="http://www.thinkpanda.com/tp/<?php echo $user["User"]["username"]; ?>">Thinkpanda dashboard</a> and browse to the &quot;<?php echo $stream["Stream"]["stream"]; ?>&quot; collection.