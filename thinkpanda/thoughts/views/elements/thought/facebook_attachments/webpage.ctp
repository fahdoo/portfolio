<?php
preg_match("/documents\/documents\/download/i", $thought['Content']['url'][0], $matches);
if (!empty($matches))
	$thought['Content']['url'][0] = "http://www.thinkpanda.com".$thought['Content']['url'][0];
?>
{'name':'<?php echo $thought['Content']['websiteTitle'][0]; ?>', 'href':'<?php echo $thought['Content']['url'][0]; ?>', 'description':'This is a Thought in the <b><?php echo $project; ?></b> collection.  What else is <?php echo $thought['User']['fullname']; ?> thinking about?  Find out on Thinkpanda!'}