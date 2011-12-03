<!-- png or jpg, show the image from the orginal source page -->
<?php 
$isUploadedImage = false;
preg_match("/pages\/document_view/i", $url, $matches);
if (!empty($matches))
	$isUploadedImage = true;
?>

<a class="image" href="<?php echo $url; ?>">
	<img 
		src="<?php 
			if ($isUploadedImage) 
			{
				$lastSlashPosition = strrpos($url, "/");
				$fileName = substr($url, $lastSlashPosition + 1);
				
				if ($type == 'png' || $type == 'jpg') 
					echo "/files/$type/$fileName.$type";
				else
				{
					$s = substr($url, 0, $lastSlashPosition);
					$secondLastSlashPosition = strrpos($s, "/");
					$fileExtension = substr($s, $secondLastSlashPosition + 1, $lastSlashPosition - $secondLastSlashPosition);
					echo "/files/$fileExtension/$fileName.$fileExtension";
				}
			} 
			else 
				echo $url;
		?>" 
		alt="<?php echo $title; ?>" 
		height="100px"
	/>
</a>
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