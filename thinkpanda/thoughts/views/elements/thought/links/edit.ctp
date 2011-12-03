<?php if ($permission == true || $permission == 'true') :?>
	<a class="edit icons <?php echo $cssClass; ?>" title="<?php echo $title; ?>" onclick="Javascript:thinkPanda.showEdit('<?php echo $id; ?>_<?php echo $field; ?>');">
	</a>
<?php endif; ?>