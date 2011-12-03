Hi <?php echo $firstname; ?>!
<br /><br />
I would like to invite you to the &quot;<?php echo $stream['stream']; ?>&quot; collection on Thinkpanda and start collaborating.
<br /><br />

<?php if (!empty($stream['description'])): ?>
	Collection description:
	<br/>
	&quot;<i><?php echo $stream['description']; ?></i>&quot;
	<br /><br />
<?php endif; ?>

To accept my invite, go to <a href="http://www.thinkpanda.com/users/dashboard">your dashboard</a> to accept the &quot;<?php echo $stream['stream']; ?>&quot; collection.
<br /><br />
Look forward to thinking with you on Thinkpanda!
<br />
<em><a href="http://www.thinkpanda.com/users/dashboard/<?php echo $myId; ?>"><?php echo $myFirstname; ?></a></em>