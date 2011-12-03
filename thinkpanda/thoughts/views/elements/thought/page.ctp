<?php 
//debug($content);
$url = $content['url'][0];
$title = $content['websiteTitle'][0];

$blurb = '';
if (!empty($content['blurb'][0]))
	$blurb = $content['blurb'][0];

$pageType = $type['class'];
//debug($pageType);
?>
<?php 
if($pageType == 'webpage'):
	$matchPages = array(
		array('class'=> 'etherpad', 'match'=>'piratepad.net/'),
		array('class'=> 'etherpad', 'match'=>'ietherpad.com/'),		
		array('class'=> 'etherpad', 'match'=>'etherpad.com/'),	
		array('class'=> 'gdocs', 'match'=>'docs.google.com/'),
		array('class'=> 'gdocs', 'match'=>'spreadsheets.google.com/'),
	);	
	foreach($matchPages AS $k => $matchPage){
		$pageTypesArray[$k] = $matchPage['match'];
	}

	$pageTypes = implode("\b|", $pageTypesArray);
	$pageTypes = str_replace('?', '\?', $pageTypes);
	$pageTypes = str_replace('.', '\.?', $pageTypes); 
	$pageTypes = str_replace('/', '\/', $pageTypes);
	preg_match('/'.$pageTypes.'/i', $url, $matches);
			//debug($matches);
			
	if (count($matches) > 0)
	{
		foreach ($pageTypesArray as $key => $value)
		{
			if ($matches[0] == $value || '.'.$matches[0] == $value)
			{
				$pageType = $matchPages[$key]['class'];
				//debug($pageType);
			}
		}
	}
endif;
?>

<div class="permalink">
	<?php 
		echo $this->element('/thought/render/'.$pageType,
			array(
				'url' => $url,
				'title' => $title,
				'container' => $container,
				'type'	=> $pageType
			)
		);
		
		//render edit
		echo $this->element('/thought/edit/render',
			array(
				'permission'	=> $edit['permission'],
				'id'			=> $container,
				'hyperlinkTitle'=> "Edit link title",
				'cssClass'		=> '',
				'field_name'	=> 'websiteTitle',
				'content'		=> $title, 
				'type'			=> $type['type'],
				'comment_id'	=> $comment_id,
				'text'			=> 'Edit Link Title'
			)
		);
	?>
</div>

<div style="width: 500px; margin:auto;">
	<span id="<?php echo $container; ?>_blurb">
		<?php 
		if (empty($blurb))
			echo "";
		else
			echo $blurb; 
		?>
	</span>
	<?php 
		//render edit
		echo $this->element('/thought/edit/render',
			array(
				'permission'	=> $edit['permission'],
				'id'			=> $container,
				'hyperlinkTitle'=> "Edit blurb",
				'cssClass'		=> '',
				'field_name'	=> 'blurb',
				'content'		=> $blurb, 
				'type'			=> $type['type'],
				'comment_id'	=> $comment_id,
				'text'			=> 'Edit Blurb'
			)
		);
	?>
</div>
