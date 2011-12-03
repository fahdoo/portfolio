<div id="citationPanel" class="panel">
	<div id="actionbarItems">
		<div class="activityAdd actionbarItem" id="activityAdd">
			<form id="formLoadCitation" onsubmit="thinkPanda.addForm(this, '#add-citation-fields', 0, false); event.returnValue=false; return false;"  action="/citations/citations_renders/fields/" method="post">
					<?php
					
					echo $form->input('Citation.type_id',array(
						'label'	=> 'Select Citation Type',
						'title'	=> 'Citation Type (Required)',
						'type'	=> 'select',
						'options'=>$types,
						'div'	=> false,
						'name'	=> 'data[Citation][type_id]', 
						'empty'	=> false,
						'error'	=> array('required' => __('You must select a Citation Type', true)),
						//'onChange' => 'thinkPanda.addForm(this, "#add-citation-fields", 0, false); event.returnValue=false; return false;'
					));
			
					echo $form->submit('load citation type');
					?>
				<img id="loading_citation_type" src="/img/icons/black-ajax-loader.gif" style="visibility:hidden" />
			</form>
			<div id="addCitation">
				<form id="formAddCitation" class="forms_view citation-form" action="/citations/citations_renders/add" method="post" onsubmit="thinkPanda.addForm(this, '#citationList', -1, '#add-citation-fields-wrapper'); event.returnValue = false; return false;">
					<div id="add-citation-fields"></div>		
				</form>		
			</div>
		</div>
	</div>
	<ul id="citationList">
		<?php 	echo $this->element('thoughts', array('data'=>array('thoughts' => $thoughts)));?>
	</ul>
</div>
