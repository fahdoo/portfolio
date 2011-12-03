Hi there!
<br /><br />
<?php if (!empty($stream['stream'])): ?>
I would like to invite you to the &quot;<?php echo $stream['stream']; ?>&quot; collection on Thinkpanda and start collaborating.
<br /><br />
<?php endif; ?>

<?php if (!empty($message)): ?>
	&quot;<?php echo $message; ?>&quot;
	<br /><br />
<?php else:?>
	Thinkpanda is a productivity tool for people that work on a lot of different collections - be it for school, research, work or personal interests. We can discuss and organize thoughts, conversations, links, files and any assets related to any collection we are working on.
	<br /><br />
<?php endif; ?>



<?php if (!empty($stream['description'])): ?>
	Collection description:
	<br/>
	&quot;<i><?php echo $stream['description']; ?></i>&quot;
	<br /><br />
<?php endif; ?>

To accept my invite, click this <a href="http://www.thinkpanda.com/users/register/<?php echo $signupCode; ?>">invite link</a>. 
<br /><br />
Look forward to thinking with you on Thinkpanda!
<br />
<em><a href="http://www.thinkpanda.com/users/dashboard/<?php echo $myId; ?>"><?php echo $myFirstname; ?></a></em>