<?php $thought = $data['thought'];?>
<div id="show_<?php echo $thought['container']; ?>" class="blurb">
	<span class="content_text">
		<?php 
			$articles = $this->requestAction('/feeds/feeds/articles/', array('feeds' => array($thought)));
			echo $this->element('articles', array('plugin' => $thought['Widget']['class'], 'feed_id' => $thought['container'], 'articles' =>  $articles[$thought['Comment']['id']]));
 		?>
	</span>
</div>

