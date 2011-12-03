<?php 
	//debug($thought['Content']['url'][0]);
	
	$url = $thought['Content']['url'][0];
	$v_key = strpos($url, '?v=') ;
	$v_id = substr($url, $v_key+3, strpos($url, '&', $v_key+3));
	$embedLink = 'http://www.youtube.com/v/'.$v_id; 
?>
{'media': [{'type': 'flash', 'swfsrc': '<?php echo $embedLink; ?>', 'imgsrc': 'http://www.thinkpanda.com/img/icons/YouTube_logo.gif', 'width': '100', 'height': '80', 'expanded_width': '350', 'expanded_height': '276'}]}

