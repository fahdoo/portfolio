<?php $search_text = "search thinkers..."; ?>

<div id="taskbar" class="taskbar">
	<div id="focusbar" class="focusbar">
		<a id="focusbarTabThinkers" class="focusbarTab active" 
			onclick="thinkPanda.toggleBox('#'+this.id, '.focusbarTab', '#thinkersNetwork', '.focusItem');"
			>
			Following <span class="count"><?php echo $following;?></span>
		</a>

		<a id="focusbarTabFollowers" class="focusbarTab" 
			onclick="thinkPanda.toggleBox('#'+this.id, '.focusbarTab', '#followedByUsers', '.focusItem');"
			>
			Followers <span class="count"><?php echo $followers;?></span>
		</a>
		<?php if($user['User']['id'] == $my['id']):?>
	
		<span class="bullet">&bull;</span>


		<a id="focusbarTabThinkerBoard" class="focusbarTab" 
			onclick="thinkPanda.toggleBox('#'+this.id, '.focusbarTab', '#thinkerBoardUsers', '.focusItem');"
			>
			Thinker Board <span class="count"><?php echo $thinkerBoard;?></span>
		</a>		


		<a id="focusbarTabNew" class="focusbarTab" 
			onclick="thinkPanda.toggleBox('#'+this.id, '.focusbarTab', '#unrelatedUsers', '.focusItem');"
			>
			New <span class="count"><?php echo $new;?></span>
		</a>
		<?php endif;?>
				
	</div>
	
	<div id="actionbar"  class="actionbar">
		<a id="actionbarTabSearch" class="actionbarTab active" 
			onclick="thinkPanda.setActive('#actionbarTabSearch', '.actionbarTab');thinkPanda.toggleGroupSlide('#activitySearch', '.actionbarItem');"
			>
			<img src="/img/icons/search_color.png" height="10px" width="10px"/>
			Find Thinkers
		</a>
	</div>	
	<div class="clearfix"></div>
</div>

<div id="actionbarItems">
	<div id="activitySearch" class="actionbarItem">
		<div class="wrapper">
			<!--form id="SearchQueryForm" action="/thinkers/thinkers/search/" method="post" onsubmit="thinkPanda.addForm(this, '#usersResults', 0);thinkPanda.toggleBox('#actionbarTabSearch', '.focusbarTab', '#usersResults', '.focusItem'); event.returnValue = false; return false;"-->
			<form id="SearchQueryForm" action="/thinkers/thinkers/search/" method="post" onsubmit="widget_thinkers.searchUser(this); thinkPanda.toggleBox('#actionbarTabSearch', '.focusbarTab', '#usersResults', '.focusItem'); event.returnValue = false; return false;">
				<div class="input text">
					<input id="SearchQuery" type="text" value="<?php echo $search_text; ?>" name="data[Search][query]" onfocus ="JavaScript:thinkPanda.setFieldValue(this, '<?php echo $search_text; ?>', 'focus');" onblur="JavaScript:thinkPanda.setFieldValue(this, '<?php echo $search_text; ?>', 'blur');" />
				</div>
			</form>
			<div class="clearfix"></div>
		</div>			
	</div>
</div>