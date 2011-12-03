<?php foreach($analytics['admin'] AS $key => $set):?>
	 <?php if($key != 'total'):?>
	 	<?php foreach($set AS $type => $array):?>
	 		<div class="tp_panel" style="margin:10px;">
	 			<h2><?php echo $key.' | '.$type;?></h2>
		 		<div id="<?php echo 'div_'.$key.'_'.$type;?>" style="width:600px; height:250px;"></div>
		 	</div>
	 	<?php endforeach;?>
	 <?php else:?>
	 	<div class="tp_panel" style="margin:10px;">
		 	<h2><?php echo $key;?></h2>
		 	<p>Analytics in most cases ignore Fahd, Gabriel, Matthew and Lucas's activity</p>
	 	<?php foreach($set AS $type => $array):?>
		 		<h3><?php echo $array['total'].'&nbsp;<span style="color:#999;">'.$type.'</span>';?></h3>
	 	<?php endforeach;?>	 	
	 	</div>
	 <?php endif;?>
<?php endforeach;?>


<?php unset($analytics['admin']['total']);?>

<script type="text/javascript">
  // Load the Visualization API and the piechart package.

  // Set a callback to run when the Google Visualization API is loaded.
  //google.setOnLoadCallback(drawChart);
  drawChart();

  function drawChart(){
  		<?php if(true):?>
	   	<?php foreach($analytics['admin'] AS $key => $set):?>
			<?php foreach($set AS $type => $array):?>
				var <?php echo $key.$type;?> = new google.visualization.DataTable();
      			<?php echo $key.$type;?>.addColumn('date', 'Date');         
        		<?php echo $key.$type;?>.addColumn('number', '<?php echo $key;?>');				
				<?php echo $key.$type;?>.addRows([
					<?php $vizRow = array();foreach($array AS $row):?>
			        	<?php 
			        		
			        			if(isset($row['x']) && isset($row['y'])){
			        				$vizRow[] = '[new Date('.$row['x'].'),'.$row['y'].']';
			        			}
			        		
			        	?>
					<?php endforeach;?>
					<?php echo implode(',', $vizRow);?>
				]);  
				<?php if(true):?>
					var <?php echo 'chart'.$key.$type;?> = new google.visualization.AnnotatedTimeLine(document.getElementById('<?php echo 'div_'.$key.'_'.$type;?>'));
		       		<?php echo 'chart'.$key.$type;?>.draw(<?php echo $key.$type;?>, {displayAnnotations: true});			
		       	<?php else:?>
					var <?php echo 'chart'.$key.$type;?> = new google.visualization.Table(document.getElementById('<?php echo 'div_'.$key.'_'.$type;?>'));
		       		<?php echo 'chart'.$key.$type;?>.draw(<?php echo $key.$type;?>, {displayAnnotations: true});					       	
		       	<?php endif;?>
			<?php endforeach;?>
		<?php endforeach;?>
		<?php endif;?>
		
	}
</script>

