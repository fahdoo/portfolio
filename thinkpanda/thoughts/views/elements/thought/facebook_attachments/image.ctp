<?php 
$url = $thought['Content']['url'][0];
$imageURL = $url;
$type = $thought['Type']['class'];
$title = $thought['Content']['websiteTitle'][0];

preg_match("/pages\/document_view/i", $url, $matches);
if (!empty($matches))
{
	$lastSlashPosition = strrpos($url, "/");
	$fileName = substr($url, $lastSlashPosition + 1);
	
	if ($type == 'png' || $type == 'jpg') 
		$imageURL = "/files/$type/$fileName.$type";
	else
	{
		$s = substr($url, 0, $lastSlashPosition);
		$secondLastSlashPosition = strrpos($s, "/");
		$fileExtension = substr($s, $secondLastSlashPosition + 1, $lastSlashPosition - $secondLastSlashPosition);
		$imageURL = "/files/$fileExtension/$fileName.$fileExtension";
	}
}

if ($title == "Untitled")
	$title = $url;

$blurb = $thought['Content']['blurb'][0];
$blurb = str_replace("<br />", " ", $blurb);
$blurb = str_replace("\n", " ", $blurb);
$blurb = str_replace("\t", " ", $blurb);
$blurb = str_replace("'", "\'", $blurb);
?>

{ 'name': '<?php echo $title; ?>', 'href': '<?php echo $url; ?>', 'caption': '<?php echo htmlentities($blurb, ENT_QUOTES); ?>', 'description': '<?php echo $thought['User']['fullname']; ?> added a new image to the <b><?php echo $project; ?></b> collection', 'media': [{ 'type': 'image', 'src': '<?php echo $imageURL; ?>', 'href': '<?php echo $url; ?>'}] }