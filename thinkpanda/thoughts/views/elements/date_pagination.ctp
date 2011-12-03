<?php //debug($this->viewVars); ?>
<?php //debug($pagination); ?>

<?php if (!empty($pagination)) : ?>
<div class="pagination_date">
	<div id="pagination_year">
		<ul>
		<?php if (false)://count($pagination) == 1) : ?>
			<li class="year active" id="year_<?php echo key($pagination); ?>">
				<b><?php echo key($pagination).' ('.reset(reset($pagination)).')'; ?></b>
			</li>
		<?php else: ?>
			<?php $i = 0; foreach ($pagination as $year => $data) : ?>
				<?php if ($i > 0) : ?>
					
				<?php endif; ?>
				<li class="year <?php if ($i == count($year)-1){echo 'active';} ?>" id="year_<?php echo $year?>">
					<a id="year_<?php echo $year; ?>_link"  class="pagination_link"
						onclick="Javascript:
							jQuery('#pagination_year ul li a').show(); 
							jQuery('#pagination_year ul li span').hide(); 
							jQuery('#pagination_month ul').hide(); 
							jQuery('#pagination_year li').removeClass('active');
							jQuery('#year_<?php echo $year; ?>').addClass('active');
							jQuery('#pagination_month .pagination_link').show(); 
							jQuery('#pagination_month .pagination_text').hide(); 
							jQuery('#year_<?php echo $year; ?>_link, #year_<?php echo $year; ?>_text').toggle();
							jQuery('#year_<?php echo $year?>_months').show();" style="display:<?php if ($i == count($year)-1){echo 'none';}else{echo 'inline';}?>">							
						<?php echo $year; ?>
					</a>
					<span id="year_<?php echo $year; ?>_text"  class="pagination_text" style="display:<?php if ($i == count($year)-1){echo 'inline';}else{echo 'none';}?>">
						<b><?php echo $year; ?></b>
					</span> 
					<span class="pagination_count"><?php echo $data['count']; ?> thoughts</span>
				</li>
			<?php ++$i; endforeach; ?>
		<?php endif; ?>
		</ul>
	</div>
	<div id="pagination_month">
		<?php $i = 0; foreach ($pagination as $year => $months) : ?>
			<ul id="year_<?php echo $year?>_months" style="display:<?php if ($i == count($year)-1) {echo 'inline';} else {echo 'none';}?>">
			<?php $j = 0; foreach ($months['months'] as $month => $data) : ?>
				<?php if ($j > 0) : ?>
					
				<?php endif; ?>
				<li id="year_<?php echo $year; ?>_month_<?php echo $month?>" class="month <?php if ($j == count($months['months'])-1){echo 'active'; }?>">
					<a id="year_<?php echo $year; ?>_month_<?php echo $month?>_link"  class="pagination_link"
						onclick="Javascript:
							jQuery('#year_<?php echo $year?>_months .pagination_link').show(); 
							jQuery('#year_<?php echo $year?>_months .pagination_text').hide(); 
							jQuery('#pagination_day ul').hide(); 
							jQuery('#pagination_day ul .pagination_link').show(); 
							jQuery('#pagination_day ul .pagination_text').hide(); 
							jQuery('#pagination_month li').removeClass('active');
							jQuery('#year_<?php echo $year; ?>_month_<?php echo $month?>').addClass('active');
							jQuery('#year_<?php echo $year; ?>_month_<?php echo $month; ?>_link').hide(); 
							jQuery('#year_<?php echo $year; ?>_month_<?php echo $month; ?>_text').show();
							jQuery('#year_<?php echo $year; ?>_month_<?php echo $month; ?>_days').show();" 
						style="display:<?php if ($i == count($year)-1 && $j == count($months['months'])-1){echo 'none';}else{echo 'inline';}?>">							
						<b><?php echo date("M",mktime(0, 0, 0, $month, 1, $year));?></b>
					</a>
					<span id="year_<?php echo $year; ?>_month_<?php echo $month?>_text"  class="pagination_text" style="display:<?php if ($i ==  count($year)-1 && $j == count($months['months'])-1){echo 'inline';}else{echo 'none';}?>">
						<b><?php echo date("M",mktime(0, 0, 0, $month, 1, $year));?></b>
					</span> 
					<span class="pagination_count"><?php echo $data['count']; ?></span>
				</li>
			<?php ++$j; endforeach;?>
			</ul>
		<?php ++$i; endforeach;?>
	</div>
	<div id="pagination_day">
		<?php foreach ($pagination as $year => $months) : ?>
			<?php $i = 0; foreach ($months['months'] as $month => $days) : ?>
				<ul id="year_<?php echo $year; ?>_month_<?php echo $month; ?>_days" style="display:<?php if ($i == count($months['months'])-1) {echo 'inline';} else {echo 'none';}?>">
				<?php $j = 0; foreach ($days['days'] as $day => $data) : ?>
					<?php if ($j > 0) : ?>
						
					<?php endif; ?>
					<li id="year_<?php echo $year; ?>_month_<?php echo $month?>_day_<?php echo $day; ?>" class="day <?php if ($i == count($days['days'])-1){echo 'active'; }?>">
						<a id="year_<?php echo $year; ?>_month_<?php echo $month; ?>_day_<?php echo $day; ?>_link" class="pagination_link"
							onclick="
							thinkPanda.Filters.setSingle('date', '<?php echo $data['date']?>');
							thinkPanda.Filters.load('/thoughts/thoughts/view/', '#thoughtList', 0);
							jQuery('#year_<?php echo $year; ?>_month_<?php echo $month; ?>_days .pagination_link').show(); 
							jQuery('#year_<?php echo $year; ?>_month_<?php echo $month; ?>_days .pagination_text').hide(); 
							jQuery('#year_<?php echo $year; ?>_month_<?php echo $month; ?>_day_<?php echo $day; ?>_link, #year_<?php echo $year; ?>_month_<?php echo $month; ?>_day_<?php echo $day; ?>_text').toggle();">
							<!-- thinkPanda.loadActivity('<?php //echo $data['stream_id']; ?>', '<?php //echo $data['filter_id']; ?>', '<?php //echo $data['filter_type']; ?>', '<?php //echo $data['date']?>');  -->
							<?php echo $day; ?>
						</a>
						<span id="year_<?php echo $year; ?>_month_<?php echo $month?>_day_<?php echo $day; ?>_text" class="pagination_text" style="display:none">
							<b><?php echo $day; ?></b>
						</span> 
						<span class="pagination_count"><?php echo $data['count']; ?></span>
					</li>
				<?php ++$j; endforeach; ?>
				</ul>
			<?php ++$i; endforeach;?>
		<?php endforeach;?>
	</div>
</div>
<?php endif; ?>