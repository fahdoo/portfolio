<script type="text/javascript">
	//http://dev.fckeditor.net/ticket/4348
	//http://docs.cksource.com/CKEditor_3.x/Developers_Guide/Specifying_the_Editor_Path
	//Matthew - must do this everytime you want to load ckeditor as this is a bug with their code
	var CKEDITOR_BASEPATH = '/vendors/ckeditor/';
</script>
<?php echo $javascript->link('/vendors/ckeditor/ckeditor'); ?>

<?php if($my['id'] != 0):?>
	
	<div class="activityAddTypes" id="addComments">
		<form id="formComment" action="/thoughts/thoughts/add" method="post" onsubmit=" thinkPanda.addForm(this, '#thoughtList', -1, '#thoughtList p.fetchError'); event.returnValue = false; return false;"> <!-- /comments_streams/add -->
			<textarea class="ckeditor" id="CommentComment" rows="1" name="data[Field][blurb][0]"  onkeyup="thinkPanda.dynamicSize(this);"  onclick="thinkPanda.dynamicSize(this);"></textarea>
			<?php
			echo $form->submit('+ note');
			?>	
	
			<script type="text/javascript">
				if (typeof(CKEDITOR.instances["CommentComment"]) != "undefined")
				{
					CKEDITOR.remove(CKEDITOR.instances["CommentComment"]);
				}
				CKEDITOR.replace("CommentComment"); 
				CKEDITOR.add(CKEDITOR.instances["CommentComment"]);
			</script>	
				
		<img id="loading_formComment" src="/img/icons/black-ajax-loader.gif" style="visibility:hidden" />	
		</form>
	</div>
	
	<div class="activityAddTypes" id="addPages" style="display:none">
		<form id="formPage" action="/thoughts/thoughts/addPage" method="post" onsubmit=" thinkPanda.addForm(this, '#thoughtList', -1, '#thoughtList p.fetchError'); event.returnValue = false; return false;">
		<?php
			echo $form->input('Field.url.0', array('type'=>'text', 'label' => 'Link'));										
			// Scrollbar rendering bug
			echo '<label for="CommentBlurbPage">Summary</label>';
			echo '<textarea id="CommentBlurbPage" class="ckeditor optional" rows="1" name="data[Field][blurb][0]"  onkeyup="thinkPanda.dynamicSize(this);"  onclick="thinkPanda.dynamicSize(this);"></textarea>';		
			
			echo $form->submit('+ link');
		?>
			<script type="text/javascript">
				if (typeof(CKEDITOR.instances["CommentBlurbPage"]) != "undefined")
				{
					CKEDITOR.remove(CKEDITOR.instances["CommentBlurbPage"]);
				}
				CKEDITOR.replace("CommentBlurbPage"); 
				CKEDITOR.add(CKEDITOR.instances["CommentBlurbPage"]);
			</script>
			<img id="loading_formPage" src="/img/icons/black-ajax-loader.gif" style="visibility:hidden" />
		</form>
	</div>
	
	
	<div class="activityAddTypes" id="addEtherpad" style="display:none">
		<form id="formEtherpad" action="/thoughts/thoughts/addEtherpad" method="post" onsubmit=" thinkPanda.addForm(this, '#thoughtList', -1, '#thoughtList p.fetchError'); event.returnValue = false; return false;">
		<?php
			// Scrollbar rendering bug
			echo $form->input('Field.websiteTitle.0', array('type'=>'text', 'label' => 'Title'));		
			echo '<label for="CommentBlurbEtherpad">Summary</label>';
			echo '<textarea id="CommentBlurbEtherpad" class="ckeditor optional" rows="1" name="data[Field][blurb][0]"  onkeyup="thinkPanda.dynamicSize(this);"  onclick="thinkPanda.dynamicSize(this);"></textarea>';							
			echo $form->submit('+ create real-time document');
		?>
			<script type="text/javascript">
				if (typeof(CKEDITOR.instances["CommentBlurbEtherpad"]) != "undefined")
				{
					CKEDITOR.remove(CKEDITOR.instances["CommentBlurbEtherpad"]);
				}
				CKEDITOR.replace("CommentBlurbEtherpad"); 
				CKEDITOR.add(CKEDITOR.instances["CommentBlurbEtherpad"]);
			</script>
			<img id="loading_formEtherpad" src="/img/icons/black-ajax-loader.gif" style="visibility:hidden" />
		</form>
		<span>Disclaimer: These real-time documents are Etherpads hosted by a third party and are publicly accessible if someone knows the URL since they are hosted outside of thinkpanda.com</span>
	</div>
	
	<?php echo $this->element('activityForm', array('plugin'=>'documents'));?>
	
	<div class="clearfix"></div>
	
	<?php 
		if(isset($options['stream_id'])){
			echo $this->element('/tags/renderActivityAddTags', array('options' => $options, 'streamsTag' => $streamsTag, 'permissions' => $permissions, 'handler' => $handler, 'updateID' => $updateID, 'tagMode' => 'streamsTagAdd'));
		}
	?>
	
<?php else:?>

	<br/>
	<?php echo $this->element('global/guestCallToSignup');?>
	
<?php endif;?>


