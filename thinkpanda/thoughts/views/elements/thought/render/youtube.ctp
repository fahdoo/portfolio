<!--youtube video, embed it -->
			
<!--original link looks like: http:<!--www.youtube.com/watch?v=RcYv5x6gZTA&feature=fvhl -->
<!--we want the embed to look like: http:<!--www.youtube.com/v/RcYv5x6gZTA -->
<?php 
	$v_key = strpos($url, '?v=') ;
	$v_id = substr($url, $v_key+3, $v_key+11);
	$embedLink = 'http://www.youtube.com/v/'.$v_id; 
?>
<object width="350" height="276">
	<param name="movie" value="<?php echo $embedLink; ?>"></param>
	<param name="allowFullScreen" value="true"></param>
	<param name="allowscriptaccess" value="always"></param>
	<embed src="<?php echo $embedLink; ?>" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="350" height="276"></embed>
</object>

<br />
<?php 
	echo $this->element('/thought/render/webpage',
		array(
			'container'	=> $container,
			'title'		=> $title,
			'url'		=> $url
		)
	);
?>

