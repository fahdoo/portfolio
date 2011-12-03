<?php 
$originalBlurb = $content['blurb'][0];
//$blurb = __parseTextForUrl($originalBlurb);


		$tokens = explode(" ", $originalBlurb);
		$blurb = "";
		foreach($tokens as $token){
			$token = preg_replace("#((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#ie",
		  "'<a href=\"$1\" target=\"_blank\">$3</a>$4'", $token);
		  	$blurb.= $token." ";		
		}
		
?>
<div id="<?php echo $container; ?>_blurb" style="width: 500px; margin:auto;">
	<?php echo $blurb; ?>
</div>

<?php 
	//render edit
	echo $this->element('/thought/edit/render',
		array(
			'permission'	=> $edit['permission'],
			'id'			=> $container,
			'hyperlinkTitle'=> "Edit blurb",
			'cssClass'		=> '',
			'field_name'	=> 'blurb',
			'content'		=> $originalBlurb, 
			'type'			=> $type['type'],
			'comment_id'	=> $comment_id,
			'text'			=> 'Edit Blurb'
		)
	);
?>