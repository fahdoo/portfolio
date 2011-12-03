<div id="feedsPanel" class="panel">
	<div id="actionbarItems">
		<div class="activityAdd actionbarItem" id="addFeeds">
			<form id="formAddFeeds" class="forms_view feeds-form" action="/feeds/feeds/add" method="post" onsubmit="thinkPanda.addForm(this, '#feedsList', -1); event.returnValue = false; return false;">
			<?php
				echo $form->input('Field.url.0', array('type'=>'text', 'label' => 'Feed Link(RSS/Atom)'));								
				echo $form->input('Field.websiteTitle.0', array('type'=>'text', 'label' => 'Title'));								
				
				echo $form->submit('+ feed');
			?>
				<img id="loading_formRss" src="/img/icons/black-ajax-loader.gif" style="visibility:hidden" />
			</form>
		</div>
	</div>
	<ul id="feedsList">
		<?php 	echo $this->element('thoughts', array('data'=>array('feeds' => $feeds, 'articles' => $articles)));?>
	</ul>
</div>
