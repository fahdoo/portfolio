<?php //echo $html->css('/thoughts/css/thoughts.css?'.time()); ?>
<?php echo $javascript->link('/thoughts/js/thoughts.js'); ?>
<div class="workspace_lite">
	<?php if(isset($options['user_id'])):?>
		<div class="" style="border-bottom: 1px solid #e5e5e5;">
			<?php echo $this->requestAction('/profiles/profiles/lite',  array('return', 'url'=>array('user_id'=> $options['user_id'], 'pageSize' => 10)));?>
		</div>		
	<?php elseif(isset($options['stream_id'])):?>
		<div class="" style="border-bottom: 1px solid #e5e5e5;">
			<?php echo $this->requestAction('/profiles/profiles/lite',  array('return', 'url'=>array('stream_id'=> $options['stream_id'], 'pageSize' => 10)));?>
		</div>
		<div class="" style="border-bottom: 1px solid #e5e5e5;">
			<?php echo $this->requestAction('/thinkers/thinkers/lite',  array('return', 'url'=>array('stream_id'=> $options['stream_id'], 'pageSize' => 10)));?>
		</div>
	<?php endif;?>
	<div class="clearfix"></div>
</div>
<div id="workspace_thoughts">
	<?php echo $this->element('/workspace/taskbar', array('options' => $options, 'streamsTag' => $streamsTag, 'permissions' => $permissions, 'handler' => $handler, 'updateID' => $updateID)); ?>
	<div id="thoughts">
		<ul id="thoughtList">
			<?php echo $this->element('thoughts', array('data'=>array('thoughts' => $thoughts, 'paging' => $paging)));?>
		</ul>
		<?php if (!empty($pagination)) : ?>
			<div id="pagination">
				<?php 	echo $this->element('date_pagination', array('data'=>array('pagination' => $pagination)));?>
			</div>		
		<?php endif; ?>
	</div>
</div>
