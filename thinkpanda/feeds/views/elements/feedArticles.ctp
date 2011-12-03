<?php foreach($articles AS $key => $article):?>
	<?php $article_id = 'article_'.$key.'_'.$feed_id;?>
	<li id="<?php echo $article_id;?>" class="article">
		<div class="picture"><a class="url" target="_blank" href="<?php echo $article['permalink'];?>"><img src="<?php echo $article['favicon'];?>" width="16px" height="16px"/></a></div>
		<span class="time" onclick="thinkPanda.toggleSelf('<?php echo '#abstract_'.$article_id; ?>');"><?php echo $article['date'];?></span>
		<span class="article_add" >
			<a id="save_<?php echo $article_id;?>" onclick="thinkPanda.saveArticle('save_<?php echo $article_id;?>', '<?php echo htmlentities(addslashes($article['permalink']));?>', '<?php echo htmlentities(addslashes(substr($article['description'],0,250)));?>', '<?php echo htmlentities(addslashes($article['title']));?>');">Keep</a>
		</span>
		<span class="article_box">
			<span class="article_desc"><a target="_blank" class="article_title" href="<?php echo $article['permalink']; ?>">
				<?php echo substr($article['title'],0, 100);?>
			</a></span>
			<span class="article_desc" onclick="thinkPanda.toggleSelf('<?php echo '#abstract_'.$article_id; ?>');"><?php echo $article['description'];?></span>
		</span>
    	<div id="<?php echo 'abstract_'.$article_id; ?>" class="article_abstract" style="display:none;">
    		<p><big><?php echo $article['title']; ?></big></p>
			<br/>
    		<p><?php echo $article['description']; ?></p>
    		<br/>
 			<p><a href="<?php echo $article['permalink']; ?>" target="_blank"><?php echo $article['permalink']; ?></a></p>
    	</div>
		<div class="clearfix"></div>
	</li>						
<?php endforeach; ?>
						
