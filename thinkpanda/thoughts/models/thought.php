<?php

class Thought extends ThoughtAppModel {
	var $name = "Thought";
	var $useTable = false;
    
	function __construct() {
        App::import('Model', 'Type');
        $this->Type = new Type();
		
		App::import('Model', 'Comment');
        $this->Comment = new Comment();
        
		App::import('Model', 'Tag');
        $this->Tag = new Tag();
		
		App::import('Model', 'User');
        $this->User = new User();
    }

	function getTags($streamIDs){
		//get the tags associated with the streams
		$q = "SELECT Tag.id, Tag.tag, StreamsTag.id, StreamsTag.stream_id, StreamsTag.pinned
				FROM tags AS Tag 
				JOIN streams_tags AS StreamsTag 
				WHERE StreamsTag.delete_user_id IS NULL AND StreamsTag.stream_id IN (".$streamIDs.") AND StreamsTag.tag_id = Tag.id
				GROUP BY Tag.id
				ORDER BY Tag.tag ASC";
				//LEFT JOIN streams_users AS StreamsUser ON (StreamsUser.stream_id = StreamsTag.stream_id AND StreamsUser.tagged_user_id = ".$my_id.") 

		$tags = $this->Tag->query($q);
		//debug($tagFilters);
		return $this->Tag->getTagsList($tags);
	}

	/* From page.php */
	function __urlResolve($_page)
	{
		// validate the URL format; correct if necessary
		// top level domains from http://data.iana.org/TLD/tlds-alpha-by-domain.txt
		$domains = "com|ca|net|edu|gov|us|cn|ac|ad|ae|aero|af|ag|ai|al|am|an|ao|aq|ar|arpa|as|asia|at|au|aw|ax|az|ba|bb|bd|be|bf|bg|bh|bi|biz|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|cat|cc|cd|cf|cg|ch|ci|ck|cl|cm|co|coop|cr|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|ee|eg|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|in|info|int|io|iq|ir|is|it|je|jm|jo|jobs|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mg|mh|mil|mk|ml|mm|mn|mo|mobi|mp|mq|mr|ms|mt|mu|museum|mv|mw|mx|my|mz|na|name|nc|ne|nf|ng|ni|nl|no|np|nr|nu|nz|om|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|pro|ps|pt|pw|py|qa|re|ro|rs|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tel|tf|tg|th|tj|tk|tl|tm|tn|to|tp|tr|travel|tt|tv|tw|tz|ua|ug|uk|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|xn--0zwm56d|xn--11b5bs3a9aj6g|xn--80akhbyknj4f|xn--9t4b11y5a|xn--deba0ad|xn--g6w251d|xn--hgbk6aj7f53bba|xn--hlcj6aya9esc7a|xn--jxalpdlp|xn--kgbechtv|xn--zckzah|ye|yt|yu|za|zm|zw";
		preg_match("/[.](".$domains.")/i", $_page, $matches);
		if (count($matches) == 0) { //check if there's a top level domain in page URL
			$_page = $_page.'.com'; // add .com as default top level domain
			//debug($_page);
		}
		//$_page = str_replace("www.", "", $_page);
		
		preg_match("/(http:\/\/www.)/i", $_page, $matches);
		//debug($matches);
		if (count($matches) != 0) { //check if there's www. in front of page URL
			return $_page;
		}
		
		preg_match("/(http(s?):\/\/)/i", $_page, $matches);
		//debug($matches);
		if (count($matches) == 0) { //check if there's http:// in front of page URL
			return 'http://'.$_page;
		}	
		
		/*preg_match("/(www.)/i", $_page, $matches);
		//debug($matches);
		if (sizeOf($matches) != 0) { //check if there's www. in front of page URL
			$page = str_replace("www.", "http://www.", $_page);
			return $page;
		}*/
		
		return $_page;
	}
	
	function __getFavicon($url)
	{
	    if(empty($url) || $url == 'http://'){
	        return false;
	    }
	
	    $url_parts = @parse_url($url);
	
	    if (empty($url_parts)) {
	        return '/img/globe.png';
	    }
		$full_url = "http://";
	    if(isset($url_parts['host'])){
	   		$full_url.= $url_parts['host'];
	    }	
	
	    if(isset($url_parts['port'])){
	        $full_url .= ":".$url_parts['port'];
	    }
	
	   	return  $favicon_url = $full_url."/favicon.ico";
	}
	
	// Fetch the HTML from the URL 
	function __getHTML($url)
	{
		if($url!='')
		{
			try 
			{
				$html=@file_get_contents($url); // use html_entity_decode() decode the html code
			}
			catch(Exception $e)
			{}
			return $html;
		}
		return '';
	}
	
	function __urlTitle($html)
	{
		if($html!='')
		{
			preg_match('/<title>(.*?)<\/title>/i', strtolower($html), $matches); //adding a ? after the .* makes this a lazy match rather than a greedy match, meaning it'll stop once a match is made rather than matching the biggest pattern
			//debug($matches);
			if(isset($matches[1])){
				$title = trim($matches[1]);
				return $title =mysql_real_escape_string($title);			
			}

		}
		return 'Untitled';
	}
	
	function __getType($url, $html = NULL, $fileExtension = NULL)
	{
		//$this->loadModel('Type');
		
		if (!is_null($fileExtension)) //this is only used by upload file
		{
			$type = $this->Type->find('first', array(
				'conditions' => array(
					'match_criteria' => $fileExtension
				),
				'fields' => array('id')
			));
			return $type['Type']['id'];
		}
		
		define("PAGETYPE_ID_HTML", 1);
		define("PAGETYPE_ID_RSS", 9);
		//$PAGETYPE_ID_RSS = 9;
		
		preg_match('/<\/rss>/i', $html, $matches);
		if (!empty($matches))
			return PAGETYPE_ID_RSS;
		
		$pageTypesArray = $this->Type->find('list', array(
			'conditions' => array(
				'NOT' => array (
					'Type.id' => PAGETYPE_ID_RSS, //don't get the RSS page type entry
					'Type.match_criteria' => ''
				)
			),
			'fields' => array('Type.match_criteria')
		));
		//debug($pageTypesArray);
		$pageTypes = implode("\b|", $pageTypesArray);
		$pageTypes = str_replace('?', '\?', $pageTypes);
		$pageTypes = str_replace('.', '\.?', $pageTypes); //added "?" after "." so that we can have either 0 or 1 "." therefore, this will match either, for example, ".txt" or "txt" - Matthew Dec 18, 2009
		$pageTypes = str_replace('/', '\/', $pageTypes);
		//debug($pageTypes);
		
		preg_match('/'.$pageTypes.'/i', $url, $matches);
		//debug($matches);
		
		if (count($matches) > 0)
		{
			foreach ($pageTypesArray as $key => $value)
			{
				if ($matches[0] == $value || '.'.$matches[0] == $value)
				{
					return $key; //return the matched key as the type_id
				}
			}
		}
		return PAGETYPE_ID_HTML;
	}
	/* End of from page.php */
	
	function clean($input)
	{
		return parent::clean($input);
	}
}

?>