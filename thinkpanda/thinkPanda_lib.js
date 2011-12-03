var thinkPanda = function()
{
	var active_stream_id; //stream that the user has selected
	var active_filter_id; //filter that the user has selected
	var active_filter_type; //filter type that the user has selected
	var filter_source_last; // So that we know what Activity stream to empty
	
	var feedback_id;
	
	var is_chrome;
	
	// Resize bugfix
	var resizebug = 0;
	
	// Notifications Variables
	var docTitle;
	var timestamp; //keeps track of when requests where made by the user
	var timezoneOffset; // figure out the timezone from the users computer time
	var n_start = 1*30; //notification seconds
	var n_seconds = n_start;
	var n_max = 5*60;// 10 minutes
	var n_delay = 1.1;
	var notifications = new Array();
	var n_count = 0;
	var that = this;
	
	var FiltersList =  new Array(); // Generic variable to store cached list for quick filtering etc

	var Guest = false;	
	var Widget = false; 
	var Context = false;
	var Filters = false;
	
	var splash = false;
	var thinkingIndicator = false;

	function init()
	{
		//Public Variables // RIVER3 - keeps track of UI state - active interface elements
		Widget = new objects.Widget(); 
		Context = new objects.Context();
		Filters = new objects.Filters();
					
		feedback_id = 0;
		
		active_filter_type = 'Filter_Tags';
		filter_source_last = 'Filter_Tags';
		
		is_chrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
		
		timestamp = new Date().getTime();
		jQuery(document).ready(function() { 
			docTitle = document.title;
			//splash = jQuery('#splash');
			thinkingIndicator = jQuery('#thinking');
			if(jQuery('#river').length > 0){
				Widget.init();
				Context.init();
				//Widget.load();
				//thinkingIndicator.fadeIn('slow');
				//setTimeout(function(){utils.resizeWindow();splash.fadeOut(500);}, 200);
				//setTimeout(function(){utils.resizeWindow();}, 2000);
				//utils.resizeWindow();}
				//jQuery('#river').attr('style', 'visibility:visible;');					
				//jQuery(window).bind("resize", utils.resizeWindow);
			}

			if(jQuery('body#guest').length > 0 || jQuery('body#landing').length > 0){
				Guest = true;
			}
			if(!Guest) load.loadNotifications(true);
			core.bindAutoCompletes();
		}); 
	}

	var objects = function(){			
		function Widget(){
			// Private
			var classname = false;
			var workspace = false;
			var that = this;
			
			//Public	
			this.clear = function(){
				classname = false;
				workspace = false;
			};
			
			this.init = function(){
				var selectButton = jQuery('#river_widgets li a.active');	// [widget_button_thoughts]	
				var selectWorkspace = jQuery('#river_workspace .workspace.active');	// [widget_button_thoughts]			
				if(selectButton.length > 0 ){
					var split = selectButton[0].id.split('_'); // [widget][button][thoughts]
					var arrayLength = split.length;
					classname = split[arrayLength - 1]; // 38					
				}
				if(selectWorkspace.length > 0){
					workspace = '#' + selectWorkspace[0].id; //				
				}				
				if(classname != false && workspace != false){
					that.set(selectButton[0], '.widget_button', workspace, classname);
				}
			};	
			
			this.getClass = function(){
				return classname;
			};

			this.getWorkspace = function(){
				return workspace;
			};
						
			// User selects Widget	
			this.set = function(btnID, btnClass, updateID, widgetClass){
				// CONTROLLER: Set the Global variable
				classname = widgetClass;
				workspace = updateID;
				
				//reset filters
				utils.resetAllFilters();
				
				utils.setActive(btnID, btnClass);
				return true;
			};
			
			this.load = function(){
				utils.setWorkspace();
				// MODEL: Call the Global Dispatcher to make Ajax call
				load.dispatcher();	
				return true;
			};
			
			return true;
		}
		
		function Context(){
			// Private
			id = false;
			context = false;
			classname = false;
			var that = this;
			
			//Public	
			this.clear = function(){
				id = false;
				context = false;
				classname = false;
			};
			
			this.init = function(){
				var select = jQuery('#context_container .contextItem.active');	// [stream_38]	
				if(select.length > 0){
					var split = select[0].id.split('_'); // [stream][38]
					var arrayLength = split.length;
					id = split[arrayLength - 1]; // 38
					if(select.hasClass('stream')){
						context = 'stream';
					}else{
						context = 'user';
						that.load();
					}
										
				}		
			};
			
			this.getID = function(){
				return id;
			};
			
			this.getContext = function(){
				return context;
			};
			
			this.setUser = function(btnID, btnClass, user_id){
				var load flag = !(id == user_id && context == 'user');
				// CONTROLLER: Set the Global variable
				id = user_id;
				context = 'user';
				
				if(load flag){
					//jQuery('.streamList').hide();
					that.load();
				}
								
				//reset filters
				utils.resetAllFilters();
				
				utils.setActive(btnID, btnClass);
				return true;
			};		
			
			this.setStream = function(btnID, btnClass, stream_id, parent_id){
				var loadflag = !(id == stream_id && context == 'stream');
				// CONTROLLER: Set the Global variable
				id = stream_id;
				context = 'stream';
				classname = btnClass;
				
				if(load flag){
					//if(typeof(parent_id)!='undefined')
						//jQuery('#stream_'+parent_id).removeClass('active').addClass('trail');
						
					// Toggle all lists off
					//jQuery(btnClass).hide();
					//jQuery(btnID).show();
					if(jQuery(btnID).length == 0){
						getStream();
					}
					//getSubStreams(); 
					that.load();
				}else{
					that.load();
				}
								
				//reset filters
				utils.resetAllFilters();
				
				utils.setActive(btnID, btnClass);
				return true;
			};
			
			function getStream(){
				handler = '/streams/getStream/';
				var container = '#streamListUser';
				//var listID = '#stream_'+id;
				//var streamBoxID = 'streamBox_'+id;
				//var streamListID = 'streamList_'+id;
				var updateID = '#stream_'+id;//'#streamListUser';
		
				// Create UI Element for New List	
				if(true){
					jQuery(container).prepend('<li id="stream_'+id+'" class="streamThinking"><span class="link">Thinking…</span></li>');	
					
					// Fill List with Streams
					var params = utils.getParams();																
					var request = handler + params;
					jQuery.ajax({
					  url: request,
					  cache: false,
					  success: function(html){
					  	var updateElem = jQuery(updateID);
					  	if(updateElem.length > 0) 
					    	updateElem.replaceWith(html).fadeIn('slow');
					    utils.setActive(updateID, classname);
					  }
					});		
				}	
			}
			
			function getSubStreams(){
				// CONTROLLER: Set the Global variable
				//jQuery(classname + '.stream:not(.active)').hide();
				//jQuery(id).show();
				handler = '/streams/getSubStreams/';
				var container = '#streams_container';
				var listID = '#stream_'+id;
				var streamBoxID = 'streamBox_'+id;
				var streamListID = 'streamList_'+id;
				var updateID = listID;//'#streamListUser';
		
				
				// Create UI Element for New List	
				if(true){
					//jQuery(container).append('<li id="streamLoad"></li>');	
					
					// Fill List with Streams
					var params = utils.getParams();																
					
					var request = handler + params;
					jQuery.ajax({
					  url: request,
					  cache: false,
					  success: function(html){
					  	var updateElem = jQuery(updateID);
					  	if(updateElem.length > 0) 
					    	updateElem.after(html).fadeIn('slow');
					  }
					});		
				}else{
					if(jQuery('#'+streamBoxID).hasClass('contextClosed')){
						jQuery('#'+streamBoxID).appendTo(container);
						jQuery('#'+streamListID).show();
					}
				}	
				jQuery('#'+streamBoxID).fadeIn();
				
				return true;			
			}		

			function getLinkedStreams(){
				// CONTROLLER: Set the Global variable
				//jQuery(classname + '.stream:not(.active)').hide();
				//jQuery(id).show();
				handler = '/streams/getLinkedStreams/';
				var container = '#streams_container';
				var listID = '#stream_'+id;
				var streamBoxID = 'streamBox_'+id;
				var streamListID = 'streamList_'+id;
				var updateID = '#'+streamListID;
		
				
				// Create UI Element for New List	
				if(jQuery('#'+streamBoxID).length == 0){
					if(id == 604){
						var streamListName = 'TP Feedback';				
					}else{
						var streamListName = jQuery(listID + ' .link a:first').text();					
					}
					var streamSelect = 'thinkPanda.Context.setStream(\'#'+streamBoxID+'\', \'.contextBox\', '+id+'); thinkPanda.toggleSelf(\'#'+streamListID+'\'); ';
					var streamList = '<div id="'+streamBoxID+'" class="contextBox streamBox" style="display:none;">'
										+'<a class="streamBoxToggle" onclick="'+streamSelect+'">'
											+'Collection: '+streamListName
											+'<span class="streamCount" style="display:none;">0</span>'
										+'</a>'
										+'<a onclick="jQuery(\'#'+streamBoxID+'\').remove().addClass(\'contextClosed\');" class="closeContext">'
											+'<img src="/img/icons/close.png"  onmouseover="this.src=\'/img/icons/close_hover.png\'" onmouseout="this.src=\'/img/icons/close.png\'" height="16px" width="16px"/>'
										+'</a>'
										+'<ul id="'+streamListID+'" class="streamList"></ul>'
									+'</div>';
					jQuery(container).append(streamList);	
					
					// Fill List with Streams
					var params = utils.getParams();																
					
					var request = handler + params;
					jQuery.ajax({
					  url: request,
					  cache: false,
					  success: function(html){
					  	var updateElem = jQuery(updateID);
					  	if(updateElem.length > 0) 
					    	updateElem.html(html).fadeIn('slow');
					  }
					});		
				}else{
					if(jQuery('#'+streamBoxID).hasClass('contextClosed')){
						jQuery('#'+streamBoxID).appendTo(container);
						jQuery('#'+streamListID).show();
					}
				}	
				jQuery('#'+streamBoxID).fadeIn();
				
				return true;
			}
			
			this.load = function(){
				utils.setWorkspace();
				// MODEL: Call the Global Dispatcher to make Ajax call
				load.dispatcher();	
				return true;
			};
			
			return true;
		}

		function Filters(){
			// Private
			var filter = new Array();
			var timeoutID = false;
			var timeout = 0;
			var that = this;			
					
			function init(){
				//that.setType('limit');
				that.setSingle('pageSize', 10);
				that.setSingle('paginate', 1);
			}

			function checkResets(type){
				if(type == 'tag_id'){
					that.resetPaginate();
				}
			}
						
			//Public	
			
			this.setStack = function(type, value){ 
				that.setType(type);
				checkResets(type);
				if(type in filter){
					var pos = filter[type].join(",").indexOf(value);
					if(pos < 0){
						filter[type].push(value); 
						if(type == 'tag_id'){ // CUSTOM CODE FOR TOPICS
							utils.counter('#actionbarTabTopics .count', 1);
						}
						//console.log('set', type, value);
					}else{
						filter[type].splice(pos, 1);
						if(type == 'tag_id'){ // CUSTOM CODE FOR TOPICS
							utils.counter('#actionbarTabTopics .count', -1);
						}
						//console.log('splice', type, value);
					}
				}
				return true;
			};

			this.unset = function(type){ 
				//that.setType(type);
				if(type in filter){
					delete filter[type];
				}
				return true;
			};
			
			this.setSingle = function(type, value){checkResets(type);that.unset(type);that.setType(type);filter[type].push(value); return true;};

			this.setPaginate = function(type, value, handler, updateID){
				that.setType(type);
				var page = parseInt(filter[type]) + parseInt(value);
				that.setSingle(type, page);
				/*var page = parseInt(filter[type]) + parseInt(value);
				that.setSingle(type, page);
				var updateID = '#paginateResults_'+(page-1);
				paginateUpdateID = 'paginateResults_'+page;
				var paginateUpdate = '<div id="'+paginateUpdateID+'" class="paginateResults"></div>';
				jQuery(updateID).append(paginateUpdate);
				thinkPanda.Filters.load(handler, paginateUpdateID, 0);*/
			};

			this.resetAddTags = function(tagMode){
				that.unset('tag_id');
				var elems = jQuery(tagMode+' .streamTag.active');
				elems.each(function(){jQuery('#'+this.id).removeClass('active');});
			}
						
			this.resetTags = function(tagMode){
				that.unset('tag_id');
				var elems = jQuery('.streamTag.active');
				elems.each(function(){jQuery('#'+this.id).removeClass('active');});
				jQuery('#actionbarTabTopics .count').text(0).hide();
			}
			
			this.resetPaginate = function(){
				that.setSingle('pageSize', 10);
				that.setSingle('paginate', 1);
			}
			
			this.setType = function(type){
				if(!(type in filter)){
					filter[type] = new Array();
				}
				return true;
			};
						
			this.get = function(){
				var params = '';
				//console.log('b4get', filter, params);
				for(type in filter){
					if(filter[type].length > 0){
						//alert(filter[type]);
						params+= '&'+type+'='+filter[type].join(',');
					}
				}
				//console.log('a4get', filter, params);
				return params;
			};

			this.load = function(handler, updateID, t){
				if(timeoutID){
					clearTimeout(timeoutID);
				}
				if(typeof(t)!='undefined')
					timeout = t;	
				
				timeoutID = setTimeout(function(){	
					thinkingIndicator.addClass('active');
					thinkingIndicator.fadeIn('slow');
					load.loadAjaxView(handler, updateID, 0);
				}, timeout);
			
				return true;
			};
			
			this.loadPaginate = function(handler, updateID){
				jQuery(updateID).html('<span class="thinking">Thinking…</span>');				
				var params = utils.getParams();																
				var request = handler + params;
				jQuery.ajax({
				  url: request,
				  cache: false,
				  success: function(html){
				  	var updateElem = jQuery(updateID);
				  	if(updateElem.length > 0) 
				    	updateElem.replaceWith(html);
				  }
				});				
			
			};			
			
			this.resetAll = function(){
				filter = new Array();
				init();
			};

			
			init();			
			return true;			
		}
		
		return{
			Widget:Widget,
			Context:Context,
			Filters:Filters
		};
	}();
	
	var load = function() 
	{
		function dispatcher(){ // RIVER3
			if(Widget.getClass() != false && Widget.getWorkspace() != false){
				var paginate = 1;
				var updateID = Widget.getWorkspace();
				var plugin = '/' + Widget.getClass();
				var controller = '/' + Widget.getClass();
				var view = '/'+'view' + '/';
				var handler = plugin + controller + view; // The standard link to each Widget
				thinkingIndicator.addClass('active');
				thinkingIndicator.fadeIn('slow');
				loadAjaxView(handler, updateID, paginate);
				setTimeout(function(){logger();}, 2000);
			}
		}


		function logger(){
			jQuery.ajax({
			  url: '/analytics/contextLogger/'+Context.getID()+'/'+Context.getContext()+'/'+Widget.getClass(),
			  cache: false,
			  success: function(html){
			  	return;
			  }
			});
		}
		/*
			function loadAjaxView
			generic Ajax function to load HTML views
		*/
		function loadAjaxView(handler, updateID, paginate)
		{
			if(updateID.charAt(0) != '#'){
				updateID = '#' + updateID;
			}
			//console.log(updateID);
			
			var updateElem = jQuery(updateID);
			updateElem.fadeTo('fast', 0.6);
			

			var params = utils.getParams();																
			
			var request = handler + params;
			jQuery.ajax({
			  url: request,
			  cache: false,
			  success: function(html){
			  	if(thinkingIndicator.hasClass('active')){
  				  	thinkingIndicator.fadeOut('slow').removeClass('active');
			  	}
				updateElem.html(html);
			  	updateElem.fadeTo('fast', 1);
			  }
			});
		}
		
				
		function loadNotifications(initial){
			extra = "";
			if(initial == true){
				timestamp = new Date().getTime();
				timezoneOffset = new Date().getTimezoneOffset() / 60;
				var extra = timestamp+"/"+timezoneOffset;
			}
				
			jQuery.ajax({url:"/streams_users/getNotifications/"+extra, dataType:'json', timeout:7*1000, 
				success:function(data){
					render.afterAJAX(data, data.status);
				},
				error: function( o, e ){
 					setTimeout(function(){load.loadNotifications(initial)}, 5*1000);
 					o.abort();
				}
			});	
		}
		
		
		//mark for deletion by Matthew - Dec 11, 2009
		/*function loadTagFilter(stream_id, filter_type)
		{
			jQuery('#filterTagsNone').hide();
			
			events.hideAllAjaxLoadingIndicators(filter_type);
			events.toggleAjaxLoadingIndicator(stream_id, '', 'Streams', 'show');
			
			timestamp = new Date().getTime();
			jQuery.getJSON("/streams_tags/get_tags/"+stream_id, {timestamp: timestamp}, function(data){
				events.toggleAjaxLoadingIndicator(stream_id, '', 'Streams', 'hide');
				render.afterAJAX(data, data.status);
				
				if(data.status == 'failed')
					jQuery('#filterTagsNone').show();
				//else
					//events.showFilter(stream_id); // This is to show the tag for when a new stream is added 				
			});
		}
		
		function loadUserFilter(stream_id, filter_type)
		{
			jQuery('#filterUsersNone').hide();
			
			events.hideAllAjaxLoadingIndicators(filter_type);
			events.toggleAjaxLoadingIndicator(stream_id, '', 'Streams', 'show');
			
			timestamp = new Date().getTime();
			jQuery.getJSON("/streams_users/get_users/"+stream_id, {timestamp: timestamp}, function(data){
				events.toggleAjaxLoadingIndicator(stream_id, '', 'Streams', 'hide');
				render.afterAJAX(data, data.status);
				
				if(data.status == 'failed')
					jQuery('#filterUsersNone').show();
			});	
		}*/
		//end of mark for deletion by Matthew - Dec 11, 2009
				
		//function loadActivity(stream_id, filter_id, filter_type, date)
		function loadActivity(stream_id, date)
		{
			//events.hideActivityContent();
			events.hideAllAjaxLoadingIndicators(active_filter_type);
			events.toggleAjaxLoadingIndicator(stream_id, active_filter_id, active_filter_type, 'show');
			
			var extraParameters = '';
			var pageId = jQuery('[name*=page_id]:first').val();
			
			if (pageId != null){
				extraParameters += "&page_id=" + pageId;	
			}
			
			var dateQueryString = "";
			var loadPagination = true;
			if(date != '') { //load activity through pagination
				dateQueryString = "&date="+date;
				loadPagination = false; //don't load date pagination again
			}
			timestamp = new Date().getTime();
			
			jQuery.post("/comments/get/?stream_id="+stream_id+"&filter_id="+active_filter_id+"&type="+active_filter_type+dateQueryString+"&timestamp="+timestamp+extraParameters, function(data){
    			//alert("Data Loaded: " + data);
				var updateID = '';
				var type = '';
				if (jQuery('#select_tag').hasClass('active')) //update activity list for tags
				{
					type = 'Filter_Tags';
					if (loadPagination)
						updateID = '#activityListTagsWithPagination';
					else
					{
						updateID = '#activityListTags';
						//jQuery('#activityListTagsPagination').html('');
					}
				}
				else //update activity list for users
				{
					type = 'Filter_Users';
					if (loadPagination)
						updateID = '#activityListUsersWithPagination';
					else
					{
						updateID = '#activityListUsers';
						//jQuery('#activityListUsersPagination').html('');
					}
				}
				//alert('loadActivity > updateId = ' + updateId);
				events.hideActivityContent();
				var activityMessageDiv = jQuery('#activityMessage');
				activityMessageDiv.html('');
				activityMessageDiv.hide();
				
				jQuery(updateID).html(data);
				
				events.toggleAjaxLoadingIndicator(stream_id, active_filter_id, active_filter_type, 'hide');
				//jQuery('#'+displayDivId).show();
				events.toggleList(type, true);
				
				/*
				TO DELETE
				
				Notifications to work in Activity Stream
				
				 var data = new Array();
				data['count'] = new Array();
				data['count'] = 0;
				
				
				jQuery(updateID+' .activityComment').each(function(){
					render.renderNotificationActivity('Comments', this.id, data, "user");
				});	
				jQuery(updateID+' .comments_count').each(function(){
					render.renderNotificationActivity('Replies', this.id, data, "user");
				});		
				*/	
			}, 'html');
		}

		function loadFeeds(stream_id, filter_id, filter_type)
		{
			events.hideActivityContent();
			events.hideAllAjaxLoadingIndicators(filter_type);
			events.toggleAjaxLoadingIndicator(stream_id, filter_id, filter_type, 'show');
			
			timestamp = new Date().getTime();
			var params = "stream_id="+stream_id+"&filter_id="+filter_id+"&type="+filter_type+"&timestamp="+timestamp;
			jQuery.post("/pages/get_feeds/?"+params, function(data){
				events.toggleAjaxLoadingIndicator(stream_id, filter_id, filter_type, 'hide');
				jQuery('#feedList').html(data);	
			}, 'html');
		}
				
		function loadReplies(stream_id, filter_id, filter_type, comment_id, paginate)
		{
			var extraParameters = '';
						
			if(filter_id == 0)
				filter_id = active_filter_id;
			if(filter_type == 0)
				filter_type = active_filter_type;
			
			var isSearch = utils.isSearch();
			if (isSearch)
				extraParameters += "&isSearch=1";

			timestamp = new Date().getTime();
			jQuery.post("/thoughts/thoughts/view/?stream_id="+stream_id+"&filter_type="+filter_type+"&filter_id="+filter_id+"&parent_id="+comment_id+"&paginate="+paginate+"&timestamp="+timestamp+extraParameters, function(data)																																   			{
				jQuery('#replies_'+comment_id +' ul').html(utils.eliminatePHPSpeedyCode(data));
				
				// TO DO: count li's instead?
				//var id = '#reply_'+comment_id+'_'+active_filter_type.toLowerCase()+'_'+filter_id+'_stream_'+stream_id; 
				var id = '#reply_'+comment_id; 
				var notification = jQuery(id + ' span.notification');
				var count = jQuery(id +' span.count');
				var total = jQuery('#replies_'+comment_id+' li.reply').length;
				var newCount = parseInt(notification.text());
				//count.text(parseInt(count.text())+parseInt(notification.text()));
				count.text(total);
				notification.text('');
				notification.fadeOut('slow');
				if(newCount == '')
					newCount = 0;
					
				if(newCount > 0){
					jQuery('#replies_'+comment_id+' li.reply').each(function(){
						if(newCount > 0){
							jQuery('#'+this.id+' div.by:first').addClass('new');					
						}
						newCount--;	
					});	
				}

				/*jQuery('#replies_'+comment_id +' ul .comments_count').each(function(){
					render.renderNotificationActivity('Replies', this.id, data, "user");
				});*/
			}, 'html');
		}

		function loadTags(stream_id, filter_id, filter_type, comment_id, paginate)
		{
			var extraParameters = '';
			
			var updateID = '#tags_'+comment_id+ ' ul';
			
			if(filter_id == 0){
				filter_id = active_filter_id;
			}
			if(filter_type == 0){
				filter_type = active_filter_type;
			}
			
			timestamp = new Date().getTime();
								
			jQuery.post("/comments_streams/get_tags/?stream_id="+stream_id+"&filter_id="+filter_id+"&filter_type="+filter_type+"&comment_id="+comment_id+"&paginate="+paginate+"&timestamp="+timestamp+extraParameters, function(data){
    			//console.log("Data Loaded: " + data);
				
				jQuery(updateID).html(utils.eliminatePHPSpeedyCode(data));
			}, 'html');			
		}
						
		function loadPages(stream_id, tag_id, paginate)
		{
			timestamp = new Date().getTime();
			jQuery.getJSON("/pages_streams/get_pages/?stream_id="+stream_id+"&tag_id="+tag_id+"&paginate="+paginate+"&timestamp="+timestamp, function(data){
				render.afterAJAX(data, data.status);
			});
		}
		
		/*
			function loadWebSearch (to be replaced by loadAjaxView)
			used by search/results.ctp to paginate through results
		*/
		function loadWebSearch(handler, updateID, paginate)
		{
			timestamp = new Date().getTime();
			jQuery('#'+updateID).fadeOut('slow').html('<span class="thinking">(Thinking…)</span>').fadeIn('slow');
			var request = handler+"&paginate="+paginate+"&timestamp="+timestamp;
			jQuery.ajax({
			  url: request,
			  cache: false,
			  success: function(html){
			    jQuery('#'+updateID).html(html);
			  }
			});
		}		
		
		/*
			function loadUser (to be replaced by loadAjaxView)
			used by users/index.ctp to retrieve colleagues/contacts, including pagination
		*/
		function loadUser(handler, updateID, paginate)
		{
			timestamp = new Date().getTime();
			jQuery('#'+updateID).fadeOut('slow').html('<span class="thinking">(Thinking…)</span>').fadeIn('slow');
			Filters.setSingle('paginate', paginate);
			var params = utils.getParams();	
			var request = handler+params+"&timestamp="+timestamp;
			jQuery.ajax({
			  url: request,
			  cache: false,
			  success: function(html){
			  	jQuery('#'+updateID).replaceWith(html);
			    jQuery('#'+updateID).addClass('active');
			  }
			});
		}

		function saveEdit(field_id, controller)
		{
			//console.log(controller);
			events.toggleAjaxLoadingIndicator('', 'form_'+field_id, 'Forms', 'show');
			
			CKEDITOR.instances['field_'+field_id].updateElement();
			var field = jQuery('#field_'+field_id);
			field.attr('readonly', true);
			
			//var editedField = field_id.substr(field_id.lastIndexOf("_") + 1);
			//alert(editedField);
			
			timestamp = new Date().getTime();
			var params = jQuery("#form_"+field_id).serialize() + "&field_id=" + field_id + "&timestamp=" + timestamp;
			
			jQuery.getJSON(controller, params, function(data)
			//jQuery.post(controller, {key: editedField, value: field.val(), stream_id: active_stream_id}, function(data)
			{
				render.jsonMessage(data);
				
				if(data.status == 'success')
				{
					//jQuery('#show_'+field_id+' .content_text:first').html(field.val());
					jQuery('#'+field_id).html(utils.nl2br(field.val()));
					CKEDITOR.instances['field_'+field_id].setData(utils.nl2br(field.val()));
					//alert(jQuery('#'+field_id).html());
				}
				
				//CKEDITOR.instances['field_'+field_id].setData('');
				events.toggleAjaxLoadingIndicator('', 'form_'+field_id, 'Forms', 'hide');
				events.hideEdit(field_id);
				field.attr('readonly', false);
			});
			
			return false;
		}
	
					
		return {
			//private functions accessible within this library
			//loadTagFilter:loadTagFilter,
			//loadUserFilter:loadUserFilter,
			dispatcher:dispatcher,
			loadFeeds:loadFeeds,
			loadReplies:loadReplies,
			loadTags:loadTags,
			loadNotifications:loadNotifications,
			
			//functions accessible both within this library and to the public
			loadActivity:loadActivity,
			//publicly accessible functions
			loadWebSearch:loadWebSearch,
			loadAjaxView:loadAjaxView,
			loadUser:loadUser,
			saveEdit:saveEdit
		};
	}();
	
	var render = function() 
	{
		//renders and displays the json message from the server
		function jsonMessage(data)
		{
			var message = data.message;
			if (message.length != 0)
				alerts.displayAjaxMessage(message, 'feedback_box', 10000);
		}
		
		// post-submit callback 
		function afterAJAX(responseText, statusText)  { 
			// for normal html responses, the first argument to the success callback 
			// is the XMLHttpRequest object's responseText property 
		 
			// if the ajaxSubmit method was passed an Options Object with the dataType 
			// property set to 'xml' then the first argument to the success callback 
			// is the XMLHttpRequest object's responseXML property 
		 
			// if the ajaxSubmit method was passed an Options Object with the dataType 
			// property set to 'json' then the first argument to the success callback 
			// is the json data object returned by the server 
		 
			// alert('status: ' + statusText + '\n\nresponseText: \n' + responseText + 
			//    '\n\nThe output div should have already been updated with the responseText.'); 
			
			var jsonText = responseText; //JSON.parse(responseText)
			var message = jsonText.message;
			var status = jsonText.status;
			var error = jsonText.error;
			var redirect = jsonText.redirect;
			
			if(typeof(redirect)!="undefined"){
				alerts.displayAjaxMessage(message, 'feedback_box', 5000);
				alert(redirect);
				//setTimeout(function(){window.location = redirect;}, 5*1000);
			}else{
				if (utils.isCurrentRequest(jsonText.timestamp))
				{
					if (status == 'success')
					{					
						alerts.clearAjaxMessages(feedback_id-1); // This doesn't work but it would be great if it did :(
		
						if (jsonText.relatedEntities)
							update(jsonText.relatedEntities);
					}
					
					if (message.length != 0)
						alerts.displayAjaxMessage(message, 'feedback_box', 10000);	
				}
			}
	
		} 
		
		/*
			Function:	update
			Purpose:	Updates the screen by calling another handler to handle the specific operations based on the given type
			Parameter:	entities = json of entities to update on the screen
			Output:		None
		*/
		function update(entities)
		{
			jQuery.each(entities, function(entitiesKey, entitiesValue)
			{
				// entitiesKey represents the type of the entity that we are modifying, which may be one of:
				// Users, Tags, Comments, Pages, Favourite, Ratings, Spam, or Sharing
				if (entitiesKey == "Notifications"){
					if(entitiesValue['Count']==0){
						if(n_seconds < n_max)
							n_seconds = n_seconds * n_delay;
					}else{
						n_seconds = n_start;
					}
					setTimeout(function(){load.loadNotifications(false)}, n_seconds*1000);
					jQuery.each(entitiesValue, function(objectKey, objectValue)
					{
						renderNotifications(entitiesKey, objectKey, objectValue);
					});
				}else if (entitiesKey == "Notifications_Tags"){
					jQuery.each(entitiesValue, function(objectKey, objectValue)
					{
						renderNotificationsTags(entitiesKey, objectKey, objectValue);
					});
				}
			});
		}
				
		function shorten(text, start, limit){
			if(text.length > limit){
				text = text.substr(start, limit)+'(...)';
			}
			return text;
		}
		
		/* These 2 methods are used for formating the results from the add user autocomplete field in the filters list */
		function formatItem(row) {
			var img = '<img class="ac_picture" height="16px" width="16px" src="' + row[2] +'"/>';
			var text = '<span class="ac_text">'+row[0]; + " (<strong>id: " + row[1] + "</strong>)"+'</span>';
			return img + text;
		}
		
		function formatResult(row) {
			return row[0].replace(/(<.+?>)/gi, '');
		}
		/**********/
	
		/*----------- NOTIFICATIONS -----------*/
		
		function renderNotifications(entityKey, type, data)
		{		
			//console.log(entityKey, type, data);
			if((type == 'Count' || type == 'Thinkers') && data > 0){
				renderNotificationCount(type, data);
			}else if(type =='Streams'){
				jQuery.each(data, function(id, value)
				{			
					renderNotificationStreams(id, value['count']);
				});			
			}
		}

		function renderNotificationCount(type, value, modify){
			var count = 0;
			if(type == 'Count' ){
				if(modify == true)
					n_count+= parseInt(value);
				else
					n_count = parseInt(value);
				count = n_count;
				var sitenav = jQuery('#mydashboard a');
				var barnav = jQuery('.self #notificationThoughts');
			}else if(type == 'Thinkers'){
				//var sitenav= jQuery('#mythinkers a');
				var sitenav = '';	
				var barnav = jQuery('.self #notificationThinkers');	
				count = value;			
			}
			if(count < 0){
				count = 0;
			}
			if(barnav.length > 0){
				//barnav.fadeOut('slow');
				if(count > 0){
					//if(parseInt(barnav.children('span.notification').html()) != count)
						barnav.children('span.notification').fadeOut('slow').html(count).fadeIn('slow');
				}else{
					barnav.children('span.notification').fadeOut('slow');			
				}
			}
			if(sitenav.length > 0){
				if(count > 0){
					sitenav.addClass('new').attr('title', count);	
					if(jQuery('#container.self').length > 0)
						document.title = '('+count+') '+docTitle;						
				}else{
					sitenav.removeClass('new').attr('title', 'Dashboard');
					document.title = docTitle;
				}
					
			}		
		}
		
		function renderNotificationStreams(id, value, modify){
			var count = 0;
			var n_box = jQuery(id+' span.notification');
			if(modify == true){
				if(n_box.text()!=''){
					count = parseInt(n_box.text());						
				}
			}
			count+= parseInt(value);
			if(count <= 0){
				n_box.text(0).fadeOut('slow');
			}else if(count != parseInt(n_box.text())){
				n_box.text(count).fadeOut('slow').fadeIn('slow');
			}
		}
		
		function renderNotificationsTags(entityKey, object, data)
		{		
			/*var elements = jQuery('.filter_tag span.notification');
			elements.each(function(){
				if(jQuery(this).attr('id'))
				jQuery(this).addClass('unread').fadeIn('slow');
			});*/
			jQuery.each(data, function(id, value)
			{
				id = '#filter_tags_'+value['tag_id']+'_stream_'+value['stream_id'];
				jQuery(id).removeClass('unread');
				//jQuery(id+' span.notification').fadeIn('slow');
			});
		}
				
		return {
			jsonMessage:jsonMessage,
			afterAJAX:afterAJAX,
			update:update,
			renderNotificationCount:renderNotificationCount,
			renderNotificationStreams:renderNotificationStreams,
			formatItem:formatItem,
			formatResult:formatResult
		};
	}();
	
	var events = function()
	{
		function resizeFrame(e, updateID){
			var container = jQuery('#river_workspace');
			var frame = jQuery(updateID);
			var h = container.height();	
			var w = container.width();
			extra_w = 2;
			extra_h = 0;
			// WIDTH 
			// frame.css("width", w - extra_w);
			
			// HEIGHT 
			//console.log(frame);
			frame.css("height", h - extra_h);
		}	
		//start of click
		/*
			Function:	favouriteClicked
			Purpose:	onClick event handler for the "Favourite" link 
			Parameter:	userId = id of the current user wanting to favourite the entity
						entityId = unique id of the entity where favourite is applied
						entityUserId = unique id of the entity_users or user_entity table where favourite is saved
						isFavourite = either 1 (meaning to favourite) or 0 (meaing to unfavourite)
						URL = string that specifies the controller and the view to call
			Output:		None
		*/
		function ajaxFavouriteClicked(userId, entityId, entityUserId, isFavourite, URL, fromId)
		{
			var entity_user_id = entityUserId;
			jQuery.ajax({
				type: 		"POST",
				url: 		URL,
				data:		"entity_id="+entityId+"&user_id="+userId+"&entity_user_id="+entity_user_id+"&is_favourite="+isFavourite+"&fromId="+fromId,
				dataType: 	"html",
				success:	function(data, textStatus)
				{
					alerts.clearAjaxMessages();

					var dataDiv = jQuery('#ajaxReturnData');
					dataDiv.html(data);
					//alert(data);
					
					var msg = dataDiv.children('.message').remove();
					if (msg.length > 0)
						alerts.displayAjaxMessage(msg.html(), 'feedback_box', 5000);
						
					var favouriteLinks = dataDiv.children('.favouriteHTML').children();
					
					if (favouriteLinks != null && favouriteLinks.length > 0)
					{
						var favouriteSpan = jQuery('#entity_favourite' + fromId);
						favouriteSpan.empty();
						favouriteLinks.each(function(){
							jQuery(this).appendTo('#entity_favourite' + fromId);
						});
					}
					dataDiv.children('.favouriteHTML').remove();
				}
			});
		}
		
		function ajaxRatingsClick(rating, userId, entityId, entityUserId, URL, fromId)
		{
			var entity_user_id = entityUserId;
			jQuery.ajax({
				type: 		"POST",
				url: 		URL,
				data:		"entity_id="+entityId+"&user_id="+userId+"&rating="+rating+"&entity_user_id="+entity_user_id,
				dataType: 	"html",
				success:	function(data, textStatus)
				{
					alerts.clearAjaxMessages();
			
					var dataDiv = jQuery('#ajaxReturnData');
					dataDiv.html(data);
					
					var msg = dataDiv.children('.message').remove();
					if (msg.length > 0)
						alerts.displayAjaxMessage(msg.html(), 'feedback_box', 5000);
					
					var ratingHTML = dataDiv.children('.ratingHTML').html();
					
					if (ratingHTML != null && ratingHTML != "")
						jQuery('#ratings' + fromId).html(ratingHTML);
					dataDiv.children('.ratingHTML').remove();
				}
			});
		}
		
		function ajaxSpamClick(userId, entityId, actionType, entityUserId, URL)
		{
			alerts.clearAjaxMessages();
			var entity_user_id = entityUserId;
			jQuery.ajax({
				type: 		"POST",
				url: 		URL,
				data:		"entity_id="+entityId+"&reporter_user_id="+userId+"&action_type_id="+actionType+"&entity_user_id="+entity_user_id,
				dataType: 	"json",
				success:	function(data, textStatus)
				{
					
				}
			});
		}
		
		function approveStreamRequestClicked(streams_users_id, stream_id, user_id)
		{
			alerts.clearAjaxMessages();
			jQuery.ajax({
				type: 		"POST",
				url: 		"/streams_users/approve_serverRender/",
				data:		"streams_users_id="+streams_users_id+"&stream_id="+stream_id+"&user_id="+user_id,
				dataType: 	"html",
				success:	function(data, textStatus)
				{
					var dataDiv = jQuery('#ajaxReturnData');
					dataDiv.html(data);
					
					var msg = dataDiv.children('.filterMessage').remove();
					if (msg.length > 0)
						alerts.displayAjaxMessage(msg.html(), 'feedback_box', 5000);
										
					var filterUsers = dataDiv.children('.add_filter_users').children();
					//alert("filterUsers length = " + filterUsers.length);
					
					if (filterUsers != null && filterUsers.length > 0) 
					{
						var filter_id = "filter_users_"+user_id+"_stream_"+stream_id;
						
						var requestedUsersList = jQuery('#stream_'+active_stream_id+'_users_requested');
						if (requestedUsersList.children().length == 1)
						{
							requestedUsersList.parent().parent().fadeOut("slow", function () {
								requestedUsersList.children('#'+filter_id).remove();
							});
						}
						else
						{
							requestedUsersList.children('#'+filter_id).fadeOut("slow", function () {
								jQuery(this).remove();
							});
						}
						
						filterUsers.each(function(){
							if (jQuery('#stream_'+active_stream_id+'_users li#'+this.id).length == 0)
								jQuery(this).prependTo('#stream_'+active_stream_id+'_users');
						});						
					}
					dataDiv.children('.add_filter_users').remove();//clears the innerHTML of the <div id="ajaxReturnData">
				}
			});
		}
		//end of click
		

		
		
		//start of show
		function showFilter(stream_id)
		{
			//hide the individual filter entries
			if (active_filter_type == 'Filter_Users')
			{
				if (active_stream_id != stream_id)
				{
					jQuery('#streamsUsersList ul#stream_'+active_stream_id+'_users').hide();
					jQuery('#streamsUsersListRequested ul#stream_'+active_stream_id+'_users').hide();
					jQuery('#streamsUsersListRequested').parent().hide();
				}
				jQuery('#filterUsersNone').hide(); //hide the no participant message
			}
			else if (active_filter_type == 'Filter_Tags')
			{
				if (active_stream_id != stream_id)
					jQuery('#streamsTagsList ul#stream_'+active_stream_id+'_tags').hide();
				jQuery('#filterTagsNone').hide(); //hide the no tag message
			} 
			
			active_stream_id = stream_id;
			
			//show the filters of the selected stream
			events.showFilterListItems(stream_id, active_filter_type);
						
			jQuery('.streams .active').removeClass('active'); //remove hightlight for the presently active stream
			jQuery('#stream_'+stream_id).addClass('active'); //highlight the selected stream
			
			//clear activity lists
			clearActivity('Streams');
			jQuery('#activityMessage').hide();
			
			// Show the Filters and hide Activity
			jQuery('#stream_guide').fadeOut('slow');
			jQuery('#filter_guide').fadeIn('slow');
			jQuery('#river_activity').addClass('tp-invisible');
			jQuery('#river_filter').removeClass('tp-invisible');
		}

		function showActivity(stream_id, filter_id, filter_type)
		{
			// Set Active
			active_filter_id = filter_id;
			active_filter_type = filter_type;
			
			//toggleForms(false); 
			if (filter_type == "Filter_Users") {
				var filter_hide = jQuery('#filter_users .active');
				var filter_show = 'filter_users_'+filter_id+'_stream_'+stream_id;
			}
			else if (filter_type == "Filter_Tags") {
				var filter_hide = jQuery('#filter_tags .active');
				var filter_show = 'filter_tags_'+filter_id+'_stream_'+stream_id;
				if (filter_id == -1)
					filter_show = 'filter_tags_favourites_stream_'+stream_id;
			}
			filter_hide.each(function(){
				jQuery(this).removeClass('active');
			});
			jQuery('#'+filter_show).addClass('active');
						
			if(jQuery('#activityPanel_select').hasClass('active')){ //#panelActivity
				load.loadAjaxView('/thoughts/thoughts/view/', 'activityPanel', 0);
			}else if (jQuery('#feedPanel_select').hasClass('active')){ //#panelFeeds
				load.loadFeeds(stream_id, filter_id, filter_type);			
			}
				
			// Show Activity Panel
			jQuery('#river_activity').removeClass('tp-invisible');
			jQuery('#filter_guide').fadeOut('slow');
			
			//added Oct 11, 2009 for favourites tag, by Matthew
			toggleTopApps(filter_type, filter_id);
			
			// For clearing Notifications
			if (!(filter_type == "Filter_Tags" && filter_id == -1))
			{	//only do these if it's not getting favourites entry
				jQuery('#'+filter_show).removeClass('unread');			
				setTimeout(function(){
					var stream_container = jQuery('#stream_'+stream_id+' span.notification');
					var filter_container = jQuery('#'+filter_show+' span.notification');
					var barnav = jQuery('#nav_my_dashboard span.notification');
					var filter_count = parseInt(filter_container.text());				
					filter_container.fadeOut('slow').text('');						
					if(utils.isInteger(filter_count)){
						// Reduce Navigation on the Stream
						var stream_count = parseInt(stream_container.text()) - filter_count;
						if(stream_count < 0){
							stream_count = 0;
						}
						stream_container.text(stream_count);
						if(stream_count == 0){
							stream_container.fadeOut('slow').text('');
						}	
						
						// Reduce the Count on your Dashboard navigation
						if(barnav.length > 0){
							var barnav_count = parseInt(barnav.text()) - filter_count;
							if(barnav_count < 0)
								barnav_count = 0;
							if(barnav_count == 0){
								barnav.fadeOut('slow').remove();
							}		
						}								
					}
				}, 10000);
			}
		}
		
		function showFavouriteActivity(stream_id)
		{
			showActivity(stream_id, -1, "Filter_Tags");
		}
		
		function showReplies(updateID, comment_id, count){
			var replylist = jQuery(updateID+' li');
			//jQuery('#replies_'+comment_id).hide();
			var box = jQuery('div[id="replies_'+comment_id+'"]');
			box.each(function(){jQuery(this).fadeIn('slow');});
			if(count > 0 && replylist.length == 0)
			{
				handler = '/thoughts/thoughts/replies/?parent_id='+comment_id;
				paginate = 0;
				load.loadAjaxView(handler, updateID, paginate);			
			}
			else if(count == 0){
				//jQuery(updateID).html('<p class="fetchError">No replies yet.</p>');			
			}
		}

		function showTags(stream_id, filter_id, filter_type, comment_id, paginate){
			var replylist = jQuery('div[id="tags_'+comment_id+'"] ul');
			jQuery('#tags_'+comment_id).hide();
			var box = jQuery('div[id="tags_'+comment_id+'"]');
			box.each(function(){jQuery(this).fadeIn('slow');});
			if(paginate > 0){
				if(!(jQuery('div[id="tags_'+comment_id+'"] ul li:first-child').length > 0)){
					replylist.each(function(){		
						jQuery(this).empty();
					});
					/*
					19-12-2009: not sure what this was for...
					var count = jQuery('#tag_'+comment_id+'_'+active_filter_type.toLowerCase()+'_'+active_filter_id+'_stream_'+active_stream_id+' span.count');
					count.text('0');*/
					load.loadTags(stream_id, filter_id, filter_type, comment_id, paginate);					
				}				
			}
		}
					
		function showFilterListItems(stream_id, filter_type)
		{
			var filterList = '';
			
			//this is only used for users filters to get the list of users that requested access to the stream
			var requestedUsersList = ''; 
			
			if(filter_type == 'Filter_Tags')
				filterList = jQuery('#stream_'+stream_id+"_tags");
			else if (filter_type == 'Filter_Users')
			{
				filterList = jQuery('#stream_'+stream_id+"_users");
				requestedUsersList = jQuery('#stream_'+stream_id+"_users_requested");
			}
			
			if(typeof(filterList) == 'undefined' || filterList == '' || filterList.children().length == 0)
			{
				if(filter_type == 'Filter_Tags')
				{
					jQuery('#filterTagsNone').show();
					alerts.displayAjaxMessage('No tags found in this stream.<br />Create one!', 'feedback_box', 5000);	
				}
				else if (filter_type == 'Filter_Users')
				{
					jQuery('#filterUsersNone').show();
					alerts.displayAjaxMessage('No user is associated with this stream.', 'feedback_box', 5000);	
				}	
				/*
				var activityMessageDiv = jQuery('#activityMessage');
				activityMessageDiv.html("No Activities found");
				activityMessageDiv.show();*/
			}
			else
			{
				filterList.show();
				filterList.children('li').show();
				
				if (filter_type == 'Filter_Users' && requestedUsersList.length > 0)
				{
					requestedUsersList.show();
					requestedUsersList.children('li').show();
					
					//show the div containing the requested users, which includes the user request toggle link
					requestedUsersList.parent().parent().show();
				}
			}
		}
		
		function showEdit(id)
		{
			//convert <br> to new line
			var fields = jQuery('#form_'+id+" :input");
			fields.each(function()
			{
				jQuery(this).val(utils.br2nl(jQuery(this).val()));
				//alert(jQuery(this).val());
			});
			
			jQuery('#'+'edit_'+id).show();
			jQuery('#'+id).hide();
		}
		
		function showCustomPopup(type, stream_id, filter_type, filter_id)
		{
			var html = '<div class="popup_base"></div>';
			if (type == 'export')
				html += getExportPopup(stream_id, filter_type, filter_id);
			else if (type == 'filterURL')
				html += getFilterURLPopup(stream_id, filter_type, filter_id);
			
			popupDiv = jQuery('#customPopup');
			popupDiv.html(html);
			popupDiv.fadeIn('slow');
				
			toggleMenu('#filter_'+filter_type.toLowerCase()+'s_'+filter_id+'_stream_'+stream_id);
		}
		
		function getFilterURLPopup(stream_id, filter_type, filter_id)
		{
			var urlFilterType = 'tag';
			if (filter_type == 'User')
				urlFilterType = 'participant';
			var url = "http://www.thinkpanda.com/streams/view/"+stream_id+"/"+urlFilterType+"/"+filter_id;
			
			var html = '<form id="formUrlShare">';
			html += '		<p>Copy the URL to share the thoughts in this Topic with your stream-mates!</p>';
			html += '		<p>(more options for sharing are in the works)</p>';
			html += '		<input type="text" id="urlShare_url" name="data[Page][page]" value="'+url+'" />';
			html += '		<input type="button" value="Hide" onclick="JavaScript:thinkPanda.hideCustomPopup(\'formUrlShare\');" />';
			html += '	</form>';
			
			return html;
		}
		
		function getExportPopup(stream_id, filter_type, filter_id)
		{
			var html = '<form id="formExport" method="post" action="/streams/export" method="post" onsubmit="thinkPanda.deleteExportFile(this, \'/streams/deleteExportFile\'); setTimeout(function(){thinkPanda.hideCustomPopup(\'formExport\');},500);">'; //onsubmit="thinkPanda.exportActivity(this, '+stream_id+', \''+filter_type+'\', '+filter_id+'); event.returnValue = false; return false;"
			html += '		Format:';
			html += '		<select id="exportFormat" name="data[exportFormat]">';
			html += '			<option value="csv" SELECTED>CSV</option>';
			html += '			<option value="html" SELECTED>HTML</option>';
			html += '			<option value="txt" SELECTED>Text</option>';
			html += '			<option value="xml" SELECTED>XML</option>';
			html += '		</select>';
			html += '		<input type="hidden" name="data[stream_id]" value="'+stream_id+'" />';
			html += '		<input type="hidden" name="data[filter_type]" value="'+filter_type+'" />';
			html += '		<input type="hidden" name="data[filter_id]" value="'+filter_id+'" />';
			html += '		<input type="hidden" name="data[start_date]" value="" />';
			html += '		<input type="hidden" name="data[end_date]" value="" />';
			html += '		<input type="submit" value="Export" />';
			html += '		<img id="loading_formExport" src="/img/icons/black-ajax-loader.gif" style="visibility:hidden" />';
			html += '	</form>';
			
			return html;
		}
		// end of show
		
		//start of hide
		function hideActivityContent()
		{
			clearActivity('Remove');
			toggleList(active_filter_type, false);
		}
		
		function hideEdit(id)
		{
			//jQuery('#show_'+id).show();
			jQuery('#'+id).show();
			jQuery('#edit_'+id).hide();
			
			//reset the value of the edit textarea back to the original
			var value = jQuery('#'+id).html();
			value = value.replace('<span class="edit_blurb">Edit blurb</span>', '');
			value = value.replace("<span class='edit_blurb'>Edit blurb</span>", '');
			
			var field = jQuery('#field_'+id);
			field.val(jQuery.trim(value));	
			//CKEDITOR.instances['field_'+id].setData(value);
		}
		
		function hideCustomPopup(id)
		{
			var popupDiv = jQuery('#customPopup');
			popupDiv.fadeOut('slow');
			//popupDiv.children('#'+id).remove();
			popupDiv.html('');
		}
		//end of hide
		
		// start of toggling
		function togglePanel(id)
		{
			var elems = jQuery('.panels li a');
			elems.each(function(){
				jQuery(this).removeClass('active');
			});
			jQuery(id+'_select').addClass('active'); 
			jQuery('.select_action_links, .panel').hide(); 
			jQuery('#activityAdd').show(); // Here cause of new citations workflow, eventually Activity Adds should load within the Apps
			if (jQuery('#feedsPanel_select').hasClass('active')){
				jQuery('#addFeedsEntryLinks').show();
				togglePanelAction('#select_add_rss', 'addRss');
				load.loadAjaxView('/feeds/feeds_renders/view/', 'feedsPanel', 0);
				//showActivity(active_stream_id, active_filter_id, active_filter_type);
			}else if (jQuery('#activityPanel_select').hasClass('active')){
				jQuery('#addActivityEntryLinks').show();	
				load.loadAjaxView('/thoughts/thoughts/view/', 'activityPanel', 0);
				togglePanelAction('#select_add_comments', 'addComments');	
				//showActivity(active_stream_id, active_filter_id, active_filter_type);
			}else if (jQuery('#searchPanel_select').hasClass('active')){
				jQuery('#addSearchEntryLinks').show();	
				load.loadAjaxView('/research/research/view/', 'researchPanel', 0);
				togglePanelAction('#select_add_citation', 'addCitation'); // to remove
 			}else if(jQuery('#citationPanel_select').hasClass('active')){
				jQuery('#activityAdd').hide();
				load.loadAjaxView('/citations/citations/view/', 'citationPanel', 0);
				togglePanelAction('#select_add_citation', 'addCitation'); // to remove
			} 
			jQuery(id).show();
		}
		
		function togglePanelAction(id, action)
		{
			jQuery('.activityAddTypes').hide();
			var elems = jQuery('.select_action_links a');
			elems.each(function(){
				jQuery(this).removeClass('active');
			});
			jQuery(id).addClass('active');
			jQuery('#'+action).show();	
		}

		function toggleAction(active, select, show, hide){
			jQuery('#'+hide).hide();
			jQuery('#'+show).show();

			var elems = jQuery('#'+select+' a');
			elems.each(function(){
				jQuery(this).removeClass('active');
			});
			jQuery(active).addClass('active');
		}
				
		/*
			Function:	toggleActivity
			Purpose:	Shows the controls to add a related entity
			Parameter:	type - string of the related entity to show
							can be one of: "All", "Tags", "Comments", "Pages", "Users"
			Output:		None
		*/
		function toggleActivity(type)
		{
			var formsToShow;
			var formsToHide;
			
			switch(type)
			{   
				case "All":
					formsToShow = jQuery('.add');
					break;
				default:
					formsToShow = jQuery('#add' + type);
					formsToHide = jQuery('.add');
			}
			//deselect all headings
			var headings = jQuery('[id^=related_entities_]');
			headings.removeClass('active');
				
			if (formsToHide != null)
			{
				formsToHide.removeClass('active');
				formsToHide.hide();
			}
			if (formsToShow != null)
			{
				formsToShow.addClass('active');
				formsToShow.show();
				
				var heading_show = jQuery('#related_entities_' + type);
				heading_show.addClass('active');
				//heading_show.show();
			}
		}
		
		function toggleFilter(source_type)
		{
			filter_source_last = active_filter_type;
			active_filter_type = source_type;
			
			var filtersToShow = 'Tag';
			var filtersToHide = 'User';
			
			//only show the add page/comment links when tag filter is selected
			var visibility = 'visible';	
			
			//variable denotes whether user can add comments/pages/feeds/etc...
			var isActivityReadOnly = false; //false means user can add, true means user not allowed to add
			
			if (source_type == 'Filter_Users')
			{
				filtersToShow = 'User';
				filtersToHide = 'Tag';
				visibility = 'hidden'; 
				isActivityReadOnly = true;
			}
			
			//hide the no filter message
			jQuery('#filter'+filtersToShow+'sNone').hide();
			
			//toggle filter_tags / filter_users lists
			//these are lists that contains the filter lists for specific streams
			jQuery('#filter_'+filtersToShow.toLowerCase()+'s').show(); //filter_tags and filter_users are 
			jQuery('#filter_'+filtersToHide.toLowerCase()+'s').hide();
			
			//hide the opposite filter list for the active stream
			jQuery('#stream_'+active_stream_id+'_'+filtersToHide.toLowerCase()+'s').hide();
			
			toggleFilterSearchAdd(filtersToShow, filtersToHide);
						
			//make visible/hidden the select action links, which includes the Add Thought, Add Page, Upload File, etc. links
			jQuery('.select_action_links').css('visibility', visibility); 
			
			//toggle the respective activity lists
			jQuery('#activityMessage').hide();
			jQuery('#activityList'+filtersToShow+'sWithPagination').show();
			jQuery('#activityList'+filtersToHide+'sWithPagination').hide();
			
			toggleTopApps(active_filter_type, -100); //the 2nd parameter does not matter in this case
			toggleForms(isActivityReadOnly); //shows the forms for adding thoughts, pages, entities, etc.
			
			if (jQuery('#stream_'+active_stream_id+'_'+filtersToShow.toLowerCase()+'s').children().length == 0) 
			{
				//this is the case where no filter list items are found inside the filter list
				//meanning there is no filter in this stream
				jQuery('#filter'+filtersToShow+'sNone').show();
			}
			else
				events.showFilterListItems(active_stream_id, active_filter_type);
		}
		
		//toggles between the users filter search bar or the tags filter search bar
		function toggleFilterSearchAdd(showType, hideType)
		{
			//toggle the respective add form
			jQuery('#formAdd'+showType).show();
			jQuery('#formAdd'+hideType).hide();
			
			//select the tag/partiicipant heading
			jQuery('#select_'+showType.toLowerCase()).addClass('active');
			jQuery('#select_'+hideType.toLowerCase()).removeClass('active');
		}
		
		function toggleForms(isReadOnly)
		{
			var form = jQuery('#activityAdd');
			if (isReadOnly)
				form.hide();
			else
				form.show();
		}
		
		function toggleList(filterType, toShow)
		{
			var element = '';
			switch(filterType)
			{
				case 'Filter_Users':
					element = 'activityUsers'
					break;
				case 'Filter_Tags':
					element = 'activityTags'
					break;
				case 'Colleagues':
					element = 'colleagues'
					break;
				case 'UnrelatedUsers':
					element = 'unrelatedUsers'
					break;
				case 'RequestedUsers':
					element = 'requestedUsers'
					break;
				case 'UsersToApprove':
					element = 'usersToApprove'
					break;
				case 'SearchThoughts':
					element = 'activitySearch'
					break;
			}
			if (toShow)
			{
				if (filterType == 'Filter_Users' || filterType == 'Filter_Tags' || filterType == 'SearchThoughts')
					jQuery('#'+element).show();
				else
					jQuery('#'+element).attr('style', 'visibility:visible');
			}
			else
			{
				if (filterType == 'Filter_Users' || filterType == 'Filter_Tags' || filterType == 'SearchThoughts')
					jQuery('#'+element).hide();
				else
					jQuery('#'+element).attr('style', 'visibility:hidden');
			}
		}

		//hides the menu of the stream/filter list item
		function toggleMenu(s_id)
		{
			var menu = jQuery(s_id+' .menubox');
			if(!menu.is(":hidden")){
				menu.hide(); //menu.slideUp('fast');
				jQuery('.preview').attr('style', 'z-index:9000;');
				jQuery(s_id+' .dropup').hide();
				jQuery(s_id+' .dropdown').show();				
			}else{
				jQuery('.preview').attr('style', 'z-index:-1;');
				jQuery('.menubox').hide();
				jQuery('.dropup').hide();
				jQuery('.dropdown').show();
				jQuery(s_id+' .dropdown').hide();	
				jQuery(s_id+' .dropup').show();
				menu.show(); //menu.slideDown('fast');
				//jQuery(menu).bind('mouseleave', {s_id:s_id}, toggleMenu);				
			}
		}
				
		function toggleSelf(id)
		{
			var elements = jQuery(id);
			elements.each(function(){		
				jQuery(this).toggle();
			});	
		}
		
		function revealThought(id, comment_id, handler, stream_container){
			toggleSelf(id + ' .thought:first');
			if(jQuery(id).hasClass('revealed')){
				jQuery(id).removeClass('revealed');
			}else{
				jQuery(id).addClass('revealed');
				if(jQuery(id).hasClass('new')){
					markAs(id, comment_id, handler, stream_container);
				}
			}
		}
		
		function markAs(id, comment_id, handler, stream_container){
			var markAsLink = jQuery(id+'_markAs');
			if(jQuery(id).hasClass('new')){
				var status = 1;
				// Mark as Read					
				jQuery.getJSON(handler, {comment_id: comment_id, status:status}, function(data){
					if(data){
						jQuery(id).removeClass('new');
						jQuery(id).addClass('just-read');
						render.renderNotificationStreams(stream_container, -1, true);
						render.renderNotificationCount('Count', -1, true);
						if(markAsLink.length > 0){
							markAsLink.text('Mark As Unread');
						}
					}
				});
			}else{
				var status = 0;
				// Mark as UNRead					
				jQuery.getJSON(handler, {comment_id: comment_id, status:status}, function(data){
					if(data){
						jQuery(id).removeClass('just-read');
						jQuery(id).addClass('new');
						render.renderNotificationStreams(stream_container, 1, true);
						render.renderNotificationCount('Count', 1, true);
						if(markAsLink.length > 0){
							markAsLink.text('Mark As Read');
						}
					}
				});
			}
				
	
		}		
				
		function hideAllAjaxLoadingIndicators(filter_type)
		{
			var id = '';
			if (filter_type == 'Streams')
			{
				id = "loading_stream_";
			}
			else if (filter_type == 'Filter_Tags' || filter_type == 'Filter_Users')
			{
				id = "loading_"+filter_type.toLowerCase();
			}
			
			jQuery('[id^='+id+']').attr('style', 'visibility: hidden;');
		}
		
		function toggleAjaxLoadingIndicator(stream_id, filter_id, filter_type, operation)
		{
			var id = '';
			if (filter_type == 'Streams')
			{
				id = "loading_stream_"+stream_id;
			}
			else if (filter_type == 'Filter_Tags' || filter_type == 'Filter_Users')
			{
				if (filter_type == 'Filter_Tags' && filter_id == -1)
					id = "loading_filter_tags_favourites_stream_"+stream_id;
				else
					id = "loading_"+filter_type.toLowerCase()+"_"+filter_id+"_stream_"+stream_id;
			}
			else if (filter_type == 'Forms')
			{
				id = "loading_"+filter_id;
			}
			
			var indicatorObject = jQuery('#'+id);
			if (operation == 'show')
				indicatorObject.attr('style', 'visibility: visible;');
			else
				indicatorObject.attr('style', 'visibility: hidden;');
		}
		
		// 	top apps include Thoughts, Web Search, Feeds, Cite as of Dec 10, 2009
		// 	by Matthew
		function toggleTopApps(filter_type, filter_id)
		{
			if ((filter_type == "Filter_Tags" && filter_id == -1) || filter_type == "Filter_Users")
			{
				jQuery('#activityAdd').hide(); //hide the add forms
				jQuery('#searchPanel_select').parent().hide(); //hide the Search button
				jQuery('#feedPanel_select').parent().hide(); //hide the Feeds button
			}
			else
			{
				jQuery('#activityAdd').show();
				jQuery('#searchPanel_select').parent().show();
				jQuery('#feedPanel_select').parent().show();
				
				events.clearSearchTerm();
			}
		}
		//end of toggling
		
		//start of clear/delete/remove
		function clearActivity(type)
		{
			if(jQuery('#feedPanel_select').hasClass('active')){
				var elements = jQuery('#feedPanel ul');
			}else{
				// Empty the existing activities (TODO: we can keep the data we have already fetched eventually)
				if(type == 'Streams'){
					var elements = jQuery('#activityMessage, #activityListTags, #activityListUsers, #activityListUsersPagination, #activityListTagsPagination, #activityListSearch');
				}else if(active_filter_type == 'Filter_Tags' && (filter_source_last == 'Filter_Tags' || type == 'Remove')){
					var elements = jQuery('#activityListTags, #activityListSearch');
				}else if (active_filter_type == 'Filter_Users' && (filter_source_last == 'Filter_Users' || type == 'Remove')){
					var elements = jQuery('#activityListUsers, #activityListSearch');
				}else{
					return;
				}
			}
			elements.each(function(){
				jQuery(this).empty();
			});	
		}
		
		function removeElement(elements){
			elements.each(function(){		
				jQuery(this).empty();
			});		
		}
				
		function clearList(id){
			jQuery('#'+id).empty();
		}
		
		//delete
		function forget(id, url, deleteMessage)
		{
			if (confirm(deleteMessage))
			{
				jQuery.getJSON(url, function(data)
				{ 
					render.jsonMessage(data);
					
					if (data.status == 'success')
					{
						jQuery('[id="'+id+'"]').each(function(){
							jQuery(this).fadeOut("slow", function () {
								jQuery(this).remove();
							});
						});
					}
				});
			}
		}
		
		function streamBoxCount(box, operation, classname){
			var streamBox = jQuery(box);
			var streamCount = jQuery(box+' '+classname);
			var count = parseInt(streamCount.text());
			if(operation == 'add'){
				count++;
			}else{
				if(count > 0)
					count--;
			}
			streamCount.text(count);
			if(count <= 0){
				streamBox.fadeOut('slow');
			}
		}
		
		function muteStream(id, url, message){		
			events.toggleMenu(id);
			
			jQuery.getJSON(url, function(data){ 
				render.jsonMessage(data);

				if (data.status == 'success'){
					//show the unarchive link from menu
					jQuery(id+' .menubox .mute').show();
					//hide the archive link from menu
					jQuery(id+' .menubox .unmute').hide();	

					//add muted classname
					jQuery(id).addClass('muted');
				}
			});
		}
		
		function unmuteStream(id, url, message){
			events.toggleMenu(id);
			
			jQuery.getJSON(url, function(data){ 
				render.jsonMessage(data);

				if (data.status == 'success'){
					//show the unarchive link from menu
					jQuery(id+' .menubox .unmute').show();
					//hide the archive link from menu
					jQuery(id+' .menubox .mute').hide();	

					//add muted class
					jQuery(id).removeClass('muted');
				}
			});
		}
		
		function archiveStream(id, url, message){
			events.toggleMenu('#'+id);
			
			jQuery.getJSON(url, function(data)
			{ 
				render.jsonMessage(data);

				if (data.status == 'success')
				{
					//show the unarchive link from menu
					jQuery('#'+id+' .unarchive').show();
					//hide the archive link from menu
					jQuery('#'+id+' .archive').hide();	
					//decrease the count of the streams list where the stream to be archived was
					streamBoxCount('#'+data.streamBox, 'minus', '.streamCount');
				
					//move the stream item over to the archive streams list
					jQuery('#'+id).prependTo('#streamListArchive');
					//increase the count of the archive streams list
					streamBoxCount('#streamBoxArchive', 'add');
					//show the Archive toggle link so that the user can show/hide the archive streams list
					jQuery('#streamBoxArchive').show();
				}
			});
			
		}
		
		function unarchiveStream(id, url, message)
		{
			if (confirm(message))
			{
				events.toggleMenu('#'+id);
				
				jQuery.getJSON(url, function(data)
				{ 
					render.jsonMessage(data);

					if (data.status == 'success')
					{
						//hide the unarchive link from menu
						jQuery('#'+id+' .unarchive').hide();
						//show the archive link from menu
						jQuery('#'+id+' .archive').show();
	
						//move the stream item over to the closed streams list
						jQuery("#"+id).appendTo('#streamListActive');
						//increase the count of the closed streams list
						streamBoxCount('#'+data.streamBox, 'add', '.streamCount');
						
						//decrease the count of the archive streams list
						streamBoxCount('#streamBoxArchive', 'minus');
						//show the Archive toggle link so that the user can show/hide the archive streams list
						jQuery('#streamBoxArchive').show();
					}
				});
				
			}
		}
		
		function deleteExportFile(form, url)
		{
			var data = jQuery("#"+form.id).serialize();
			
			setTimeout(function()
			{
				jQuery.post(url, data, function(data, textStatus){
    				//alert("Data Loaded: " + data);
					//alert("textStatus: " + textStatus);
				}, 'html');	
			},30000);
		}
		//end of clear/remove/delete
				
		//start of join methods
		function joinStream(stream_id, stream_name, picture, user_id)
		{		
			jQuery.ajax({
				type: 		"POST",
				url: 		"/streams_users/join_stream/"+stream_id,
				dataType: 	"json",
				success:	function(data, textStatus)
				{
					render.jsonMessage(data);

					if(data.status != 'failed')
					{
						var thinkerImage = '<li class="left" id="thinker_'+user_id+'">'
							+'<div class="usergrid">'
								+'<div class="picture">'
									+'<a title="Fahd Butt" href="/users/dashboard/'+user_id+'"><img height="25px" alt="" src="'+picture+'"></a>'						
								+'</div>'
								+'<div class="clearfix"></div>'
							+'</div>'
						+'</li>';
						jQuery('#stream_'+stream_id).addClass(data.cssClass);
						if(jQuery('#thinker_'+user_id).length == 0)
							jQuery('#thinkersGrid').prepend(thinkerImage);
						utils.counter('#thinkersPanel .total', 1);		
						//remove the child <span class="join"></span>
						jQuery('#stream_'+stream_id+' .streamJoin').remove();
						jQuery("li#stream_"+stream_id).fadeTo(100, 1);
						
						jQuery('#followStream_'+stream_id).hide();
						jQuery('#unfollowStream_'+stream_id).show();
					}
				}
			});
		}
		
		//end of join methods

		function removeStream(stream_id, url, deleteMessage, user_id)
		{
			if (confirm(deleteMessage))
			{
				jQuery.getJSON(url, function(data)
				{ 
					render.jsonMessage(data);

					if (data.status == 'success')
					{
						if(jQuery('#thinker_'+user_id).length > 0)
							jQuery('#thinker_'+user_id).remove();
						utils.counter('#thinkersPanel .total', -1);	
						if(data.streamBox != 'streamBoxInvited' && data.streamBox != 'streamBoxRequested')	
							data.streamBox = 'streamBoxArchive';			
						streamBoxCount('#streamCountBox', 'minus', '.streamCount');
						
						clearActivity('Remove');
						jQuery("li#stream_"+stream_id).fadeTo(500, 0.5);
						jQuery('#unfollowStream_'+stream_id).hide();	
						jQuery('#followStream_'+stream_id).show();

					}
				});
			}
		}
				
		//start of accept methods
		function acceptStream(stream_id)
		{
			jQuery.getJSON("/streams_users/accept_stream/"+stream_id, function(data)
			{
				render.jsonMessage(data);

				if(data.status == 'success')
				{
					var stream_object = jQuery('#stream_'+stream_id);
					stream_object.removeClass('invited');
					stream_object.addClass(data.cssClass);
					
					//remove the 
					jQuery('#stream_'+stream_id+' a.streamAccept').remove();
					
					//move the list item from streamListInvited to streamList 
					//stream_object.prependTo('#'+data.streamList);
					streamBoxCount('#streamCountBox', 'add', '.streamCount');
					streamBoxCount('#streamCountBox', 'minus', '.streamInviteCount');
					jQuery('#unfollowStream_'+stream_id).hide();
					jQuery('#followStream_'+stream_id).show();					
				}
			});
		}
		//end of accept methods

		function connectUser(user_id, updateID){
			elem = jQuery('[id='+updateID+']');
			elem.each(function(){jQuery(this).text("...")});
			timestamp = new Date().getTime();
			jQuery.post("/thinkers/thinkers/connectUser/"+user_id+"/", {timestamp:timestamp}, function(data){
				if(data == 'true'){
					elem.each(function(){
						jQuery(this).replaceWith('<a class="unfollow" id="connectUser_'+user_id+'" onclick="thinkPanda.unconnectUser('+user_id+', \''+updateID+'\');">Unfollow</a>');
					});
				}else{
					elem.each(function(){
						jQuery(this).html('Try Again');
					});
				}
					
			});
		}	

		function unconnectUser(user_id, updateID){
			elem = jQuery('[id='+updateID+']');			
			elem.each(function(){jQuery(this).text("...")});
			timestamp = new Date().getTime();
			jQuery.post("/thinkers/thinkers/unconnectUser/"+user_id+"/", {timestamp:timestamp}, function(data){
				if(data == 'true'){
					elem.each(function(){
						jQuery(this).replaceWith('<a class="follow" id="connectUser_'+user_id+'" onclick="thinkPanda.connectUser('+user_id+', \''+updateID+'\');">Follow</a>');
					});
				}else{
					elem.each(function(){
						jQuery(this).html('Try Again');
					});
				}					
			});
		}	
						
		//start of save methods
		function saveArticle(id, page, blurb, title)
		{
			/*var stream_name = jQuery('#stream_'+active_stream_id+' .link a').text();
			var tag_name = jQuery('#'+active_filter_type.toLowerCase()+'_'+active_filter_id+'_stream_'+active_stream_id+' .link a').text();*/
			
			var params = utils.getAddParams();
			var postData = "data[Field][blurb][0]="+blurb+"&data[Field][url][0]="+page+"&data[Field][websiteTitle][0]="+title+params;
			jQuery('#'+id).text("...");
			jQuery.post("/thoughts/thoughts/addPage", postData, function(data){
				jQuery('#'+id).text("Kept").addClass("saved").attr("onclick", "");
			});
		}
		
		function saveEmailNotificationSetting(selection)
		{
			events.toggleAjaxLoadingIndicator('', selection.id, 'Forms', 'show');
			
			jQuery.getJSON("/users/edit_notification_settings", selection.name+"="+selection.value, function(msg)
			{
				alerts.displayAjaxMessage(msg, 'feedback_box', 5000);
				events.toggleAjaxLoadingIndicator('', selection.id, 'Forms', 'hide');
			});
		}
		//end of save methods

		//start of add methods
		function addReply(form)
		{
			if (utils.validateAjaxForm(form))
			{
				events.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'show');
				
				var parent_id = form.id.substring(form.id.indexOf("_")+1);
				//var parent_id = jQuery('#'+form.id+' #CommentParentId').val();
				
				var formData = jQuery("#"+form.id).serialize()+utils.getParams()+"&data[Comment][parent_id]="+parent_id; 
								//data[Stream][stream_id]="+active_stream_id+"&data[Filter][type]="+active_filter_type+"&data[Filter][id]="+active_filter_id+"&data[Comment][parent_id]="+parent_id;
				
				var extraParam = '?';
				var isSearch = utils.isSearch();
				if (isSearch)
					extraParam = '?isSearch=1';

				jQuery.post(form.action+extraParam, formData, function(data)
				{
					if (jQuery('#ul_replybox_'+parent_id+" ul p.fetchError").length > 0)
						jQuery('#ul_replybox_'+parent_id).html(utils.eliminatePHPSpeedyCode(data));
					else
						jQuery('#ul_replybox_'+parent_id).prepend(utils.eliminatePHPSpeedyCode(data));
					
					var textarea = jQuery('#'+form.id+' textarea');
					textarea.val(""); 
					//CKEDITOR.instances[textarea.attr('id')].setData('');
					
					utils.enableFields(form);	
					if (isSearch)
						var count = jQuery('#reply_search_'+parent_id+'_'+active_filter_type.toLowerCase()+'_'+active_filter_id+'_stream_'+active_stream_id+' span.count');
					else
						var count = jQuery('#reply_'+parent_id+'_count');
						//var count = jQuery('#reply_'+parent_id+'_'+active_filter_type.toLowerCase()+'_'+active_filter_id+'_stream_'+active_stream_id+' span.count');
					//alert(count.text());
					count.html(parseInt(count.html())+1);
					events.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'hide');			
				}, 'html');	
			}
		}
		
		function addRSS(form)
		{
			if (utils.validateAjaxForm(form))
			{
				events.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'show');
				
				var parameters = "&data[Stream][stream_id]="+active_stream_id+"&data[Filter][type]="+active_filter_type+"&data[Filter][id]="+active_filter_id;
				
				jQuery.post(form.action, jQuery("#"+form.id).serialize()+parameters, function(data)
				{
					jQuery('#feedList').prepend(utils.eliminatePHPSpeedyCode(data));
					
					var textarea = jQuery('#'+form.id+' textarea');
					textarea.val(""); 
					CKEDITOR.instances[textarea.attr('id')].setData('');
					
					jQuery('#'+form.id+' input[type=text]').val(""); 
					utils.enableFields(form);		
					events.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'hide');			
				}, 'html');	
			}
		}
				
		function addActivity(form)
		{
			if (utils.validateAjaxForm(form))
			{
				events.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'show');
				var parameters = "&data[Stream][stream_id]="+active_stream_id+"&data[Filter][type]="+active_filter_type+"&data[Filter][id]="+active_filter_id;
				
				jQuery.post(form.action, jQuery("#"+form.id).serialize()+parameters, function(data)
				{
					data = utils.eliminatePHPSpeedyCode(data);
					if (data.indexOf('div class="fetchError">') >= 0) //there is an error message
					{
						var msg = data.substring(data.indexOf(">")+1, data.indexOf("</"));
						if (msg.length > 0)
							alerts.displayAjaxMessage(msg, 'feedback_box', 5000);
					}
					else
					{
						jQuery('#thoughtList .fetchError').fadeOut('slow').remove();
						jQuery('#thoughtList').prepend(data); 
						
						var textarea = jQuery('#'+form.id+' textarea');
						textarea.val(""); 
						CKEDITOR.instances[textarea.attr('id')].setData('');
										
						jQuery('#'+form.id+' input[type=text]').val(""); 
					}
					
					utils.enableFields(form);
					events.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'hide');						
				}, 'html');	
			}
		}

		function addForm(form, updateID, appendType, removeID)
		{				
			if (utils.validateAjaxForm(form))
			{
				events.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'show');
				params = utils.getAddParams();	
				var data = jQuery("#"+form.id).serialize()+params;
				
				jQuery.post(form.action, data, function(data)
				{
					if(jQuery(updateID).length > 0){
						if(appendType == 0){
							jQuery(updateID).html(data);	
						}else if(appendType == -1){
							jQuery(updateID).prepend(data);	
						}else if(appendType == 1){
							jQuery(updateID).append(data);
						}
					}

					jQuery('#'+form.id+' input[type=text]').val("");
					var textarea = jQuery('#'+form.id+' .ckeditor');
					if(textarea.length > 0){
						textarea.val("");
						CKEDITOR.instances[textarea.attr('id')].setData('');
					}
					
					if(removeID != false){
						jQuery(removeID).fadeOut('slow').remove(); 
					}
					
					utils.enableFields(form);
					events.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'hide');						
				}, 'html');	
			}
		}
		
		function addStream(form)
		{
			if (utils.validateAjaxForm(form))
			{
				
				events.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'show');
				var params = utils.getAddParams();
							
				var formData = jQuery("#"+form.id).serialize()+params;

							
				jQuery.post(form.action, formData, function(data, textStatus){
					//clears the value of the add stream input field
					var inputs = jQuery('#'+form.id+' input');
					inputs.each(function(){
						jQuery(this).val("");
					});					
					jQuery('ul.streamList li').show();
					
					
					var dataDiv = jQuery('#ajaxReturnData');
					dataDiv.html(data);
					
					var msg = dataDiv.children('.addStreamTagUserMessage').remove();
					if (msg.length > 0)
						alerts.displayAjaxMessage(msg.html(), 'feedback_box', 5000);
										
					var streams = dataDiv.children('.add_stream').children();
					//var filterTags = dataDiv.children('.add_filter_tags').children();
					//var filterUsers = dataDiv.children('.add_filter_users').children();
					
					var isStreamAdded = false;
					if (streams != null && streams.length > 0) {
						// Reset FiltersList cache
						//FiltersList['ObjectCache'][form.id] = undefined;
						isStreamAdded = true;
						
						streams.each(function(){
							new_stream_id = this.id.substring("stream_".length);
	
							//Append after the Add Form
							jQuery('#streamListUser').prepend(this);
							Context.setStream('#stream_'+new_stream_id, '.contextItem', new_stream_id);
							/*
							var mylist = jQuery('#streamListUser');
							var listitems = mylist.children('li').get();
							listitems.sort(function(a, b) {
							   var compA = jQuery(a).text().toUpperCase();
							   var compB = jQuery(b).text().toUpperCase();
							   return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
							});
							jQuery.each(listitems, function(idx, itm) { mylist.append(itm); });
							*/
							/*
							var containerID = '#streamListActive';
							if (jQuery(containerID + ' li#'+this.id).length == 0){
								jQuery('#'+this.id).clone(true).insertAfter(containerID + ' form');
							}
							*/
						});

					}
					dataDiv.children('.add_stream').remove();
				
					events.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'hide');
					utils.enableFields(form);
				}, 'html');	
			}
		}
				
		function addTag(form, updateID, containerID)
		{
			if (utils.validateAjaxForm(form))
			{
				
				events.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'show');
				var data = jQuery("#"+form.id).serialize()+utils.getAddParams();
				
				jQuery.post(form.action, data, function(data, textStatus)
				{
					if(typeof(data.status)=='undefined' && data.status=='failed'){
						console.log(data.status);
					}else{
						jQuery(containerID+' #TagTags').val('');
						jQuery(updateID).prepend(data);
						var ct = jQuery(updateID + ' li');
						ct.each(function(){
							var ctExists = jQuery(containerID+' .tags_credit'+' #'+this.id);
							if(ctExists.length == 0){
								var tag = jQuery('#'+this.id+' .tag_content').text();
								var data2 = '<span id="'+this.id+'" class="commentTagCredit">&nbsp;/&nbsp;'+tag+'</span>';
								jQuery(containerID+' .tags_credit').prepend(data2);
							}

						});
						
						events.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'hide');
						utils.enableFields(form);
					}
				}, 'html');	
			}
		}
		
		
		// TO DELETE: Moved to Thinkers
		function addUser(form)
		{
			if (utils.validateAjaxForm(form))
			{
				
				events.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'show');
				
				jQuery.post(form.action, jQuery("#"+form.id).serialize()+"&data[StreamsUser][stream_id]="+active_stream_id, function(data, textStatus)
				{
    				//alert("Data Loaded: " + data);
					//alert("textStatus: " + textStatus);
					
					var dataDiv = jQuery('#ajaxReturnData');
					dataDiv.html(data);
					
					var msg = dataDiv.children('.filterMessage').remove();
					if (msg != null && msg != '')
						alerts.displayAjaxMessage(msg.html(), 'feedback_box', 5000);
					
					var filterUsers = dataDiv.children('.add_filter_users').children();
					
					if (filterUsers != null && filterUsers.length > 0)
					{
						//this means that we are adding filter tags in the filter list
						filterUsers.each(function(){
							if (jQuery('#stream_'+active_stream_id+'_users li#'+this.id).length == 0)
								jQuery(this).prependTo('#stream_'+active_stream_id+'_users');
						});
						
						showFilter(active_stream_id);
					}
					else
					{
						jQuery('#stream_'+active_stream_id+'_users li').show();
					}
					
					dataDiv.children('.add_filter_users').remove(); //clears the innerHTML of the <div id="ajaxReturnData">
					
					jQuery('#UserFilterFullname, #UserFilterFullnameId').val(""); //clears the value of the add user input field
					
					events.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'hide');
					utils.enableFields(form);
				}, 'html');	
			}
		}
		
		function addProjectOnSignup(form)
		{
			var projectName = jQuery("#projectNameOnSignup");
			if (projectName.val() != "" && projectName.val() != "Enter a collection name")
			{
				return true;
			}
			alert("Oops you forgot to enter a collection name");
			projectName.focus();
			return false;
		}
		//end of add
		
		//start of upload methods
		function uploadFile(form, fileFieldId, fileExtensionsAllowed)
		{
			var fileFieldValue = jQuery('#'+fileFieldId).val().toLowerCase();
			var fileExtension = fileFieldValue.substr(fileFieldValue.lastIndexOf('.'));
			if (jQuery('#FileDocument').val() != "")
			{
				if (fileExtensionsAllowed.indexOf(fileExtension) >= 0)
				{
					//starting setting some animation when the ajax starts and completes
					events.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'show');
					
					utils.disableFields(form);
									
					/*
					prepareing ajax file upload
					url: the url of script file handling the uploaded files
					fileElementId: the file type of input element id and it will be the index of  $_FILES Array()
					dataType: it support json, xml
					secureuri:use secure protocol
					success: call back function when the ajax complete
					error: callback function when the ajax failed
					
					*/
					
					var parameters = utils.getParams();
					
					var textarea = jQuery('#'+form.id+' textarea');
					CKEDITOR.instances[textarea.attr('id')].updateElement();
					//"&data[Stream][stream_id]="+active_stream_id+"&data[Filter][type]="+active_filter_type+"&data[Filter][id]="+active_filter_id;
				
					jQuery.ajaxFileUpload
					(
						{
							url: 			form.action+'/?'+jQuery("#"+form.id).serialize()+parameters,
							secureuri:		false,
							fileElementId:	fileFieldId,
							dataType: 		'html',
							success: function (data, status)
							{
								//console.debug("Here is data: %o", data);
								//alert(data);
								
								var dataDiv = jQuery('#ajaxReturnData');
								dataDiv.html(utils.eliminatePHPSpeedyCode(data));
								
								var msg = dataDiv.children('.message').remove();
								if (msg != null && msg != '' && msg.length > 0)
									alerts.displayAjaxMessage(msg.html(), 'feedback_box', 5000);
														
								var doc = dataDiv.children('.docEntry').children();
						
								if (doc != null && doc.length > 0)
								{
									if(jQuery('#thoughtList').length > 0){
										var updateID = '#thoughtList';
									}else{
										var updateID = '#documentList';
									}
									
									//this means that we are adding filter tags in the filter list
									doc.each(function(){
										jQuery(this).prependTo(updateID);
									});
								}
								dataDiv.children('.docEntry').remove(); //clears the innerHTML of the <div id="ajaxReturnData">
								
								//clears the value of the upload file input fields
								jQuery("#"+form.id+' input:not(:submit)').val("");
								textarea.val(""); 
								CKEDITOR.instances[textarea.attr('id')].setData('');
											
								events.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'hide');
								utils.enableFields(form);
							},
							error: function (data, status, e)
							{
								alert(e);
								events.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'hide');
								utils.enableFields(form);
							}
						}
					);
				}
				else
				{
					alert("Documents currently does not support this file type: " + fileExtension);
					jQuery("#"+form.id+' input:not(:submit)').val("");
					var textarea = jQuery('#'+form.id+' textarea');
					textarea.val(""); 
					CKEDITOR.instances[textarea.attr('id')].setData('');
				}
			}
			else
			{
				alert("You forgot to select a file!");
			}
			return false;
		}
		//end of upload methods
		
		
		//start of search
		function searchThoughts(form)
		{
			if (utils.validateAjaxForm(form))
			{
				
				events.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'show');
				
				var searchTermsField = jQuery('#SearchTerms');
				
				var parameters = "&data[Stream][stream_id]="+active_stream_id+"&data[Filter][type]="+active_filter_type+"&data[Filter][id]="+active_filter_id;
				
				jQuery.post(form.action, jQuery("#"+form.id).serialize()+parameters, function(data, textStatus)
				{
    				//alert("Data Loaded: " + data);
					//alert("textStatus: " + textStatus);
					
					events.hideActivityContent(); //clears the activity lists
										
					var activityMessageDiv = jQuery('#activityMessage');
					activityMessageDiv.html('');
					activityMessageDiv.hide();
					
					var updateID = '#activityListSearch';
					var searchList = jQuery(updateID);
					
					searchList.html(data);
					
					events.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'hide');
					events.toggleList('SearchThoughts', true); //show search activity list
					
					var searchKeywordSpan = jQuery('#searchKeyword');
					searchKeywordSpan.children('span').html(searchTermsField.val());
					searchTermsField.val('');
					searchKeywordSpan.show();
										
					utils.enableFields(form);
				}, 'html');	
			}
		}
		
		function clearSearch()
		{
			clearSearchTerm();
			load.loadActivity(active_stream_id, "");
		}
		
		function clearSearchTerm()
		{
			//hide the search keyword span
			var searchKeywordSpan = jQuery('#searchKeyword');
			searchKeywordSpan.children('span').html('');
			searchKeywordSpan.hide();
			
			//clear the search field
			jQuery('#SearchTerms').val('search thoughts');
		}
		
		function searchWeb(form)
		{
			//console.log(jQuery("#"+form.id).serialize());
			if (utils.validateAjaxForm(form))
			{
				
				events.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'show');
				
				jQuery.post(form.action, jQuery("#"+form.id).serialize(), function(data, textStatus)
				{
					jQuery('#researchResults').html(data);
					
					events.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'hide');
					//jQuery('#'+form.id+' input#SearchTerm').val("search...");
					utils.enableFields(form);
				}, 'html');	
			}
		}
		//end of search
		
		//discover
		function discover(form)
		{
			//console.log(jQuery("#"+form.id).serialize());
			if (utils.validateAjaxForm(form))
			{
				events.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'show');
				
				jQuery.post(form.action, jQuery("#"+form.id).serialize(), function(data, textStatus)
				{
					jQuery('#discover_content').html(data);
					
					events.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'hide');
					//jQuery('#'+form.id+' input#SearchTerm').val("search...");
					utils.enableFields(form);
				}, 'html');	
			}
		}
		//discover
		
		//check methods
		
		var eventTimeoutID = false; //used for submission throttling
		
		function checkUniqueUsername(field)
		{
			if(eventTimeoutID){
				clearTimeout(eventTimeoutID);
			}
			
			eventTimeoutID = setTimeout(function()
			{	
				if (field.value != "")
				{
					//make ajax call to check for duplicate user names
					jQuery.getJSON("/users/isUsernameUnique", "data[User][username]="+field.value, function(isUnique)
					{
						if (isUnique)
						{
							//turn the field back to normal
							field.style.backgroundColor = "#FFFBE8";
							
							//hide the username taken text
							jQuery("#usernameNotUnique").hide();
							
							//enable the submit button
							jQuery("#UserEditForm input:submit").attr("disabled", false);
						}
						else
						{
							//turn the field into red
							field.style.backgroundColor = "red";
							
							//show the username taken text
							jQuery("#usernameNotUnique").show();
							
							//disable the submit button
							jQuery("#UserEditForm input:submit").attr("disabled", true);
						}
					});	
				}
				else
				{
					//turn the field back to normal
					field.style.backgroundColor = "#FFFBE8";
					
					//hide the username taken text
					jQuery("#usernameNotUnique").hide();
					
					//enable the submit button
					jQuery("#UserEditForm input:submit").attr("disabled", true);
				}
			}, 200);
		
			return true;
		}
		//end of check methods
		
		return {
			//private functinos within this library
			toggleForms:toggleForms,
			toggleList:toggleList,
			clearList:clearList,
			toggleAjaxLoadingIndicator:toggleAjaxLoadingIndicator,
			showFilterListItems:showFilterListItems,
			hideActivityContent:hideActivityContent,
			hideAllAjaxLoadingIndicators:hideAllAjaxLoadingIndicators,
			clearSearchTerm:clearSearchTerm,
			
			//publicly accessible functions
			resizeFrame:resizeFrame,
			ajaxFavouriteClicked:ajaxFavouriteClicked,
			ajaxRatingsClick:ajaxRatingsClick,
			ajaxSpamClick:ajaxSpamClick,
			approveStreamRequestClicked:approveStreamRequestClicked,
			showFilter:showFilter,
			showActivity:showActivity,
			showFavouriteActivity:showFavouriteActivity,
			showReplies:showReplies,
			showTags:showTags,
			showEdit:showEdit,
			showCustomPopup:showCustomPopup,
			hideEdit:hideEdit,
			hideCustomPopup:hideCustomPopup,
			togglePanel:togglePanel,
			toggleActivity:toggleActivity,
			toggleAction:toggleAction,
			togglePanelAction:togglePanelAction,
			toggleFilter:toggleFilter,
			toggleMenu:toggleMenu,
			toggleSelf:toggleSelf,
			revealThought:revealThought,
			markAs:markAs,
			forget:forget,
			muteStream:muteStream,
			unmuteStream:unmuteStream,
			removeStream:removeStream,
			archiveStream:archiveStream,
			unarchiveStream:unarchiveStream,
			deleteExportFile:deleteExportFile,
			joinStream:joinStream,
			acceptStream:acceptStream,
			connectUser:connectUser,
			unconnectUser:unconnectUser,
			saveArticle:saveArticle,
			saveEmailNotificationSetting:saveEmailNotificationSetting,
			addReply:addReply,	
			addRSS:addRSS,		
			addActivity:addActivity,
			addForm:addForm,
			addStream:addStream,
			addTag:addTag,
			addUser:addUser,
			addProjectOnSignup:addProjectOnSignup,
			uploadFile:uploadFile,
			searchThoughts:searchThoughts,
			clearSearch:clearSearch,
			searchWeb:searchWeb,
			discover:discover,
			checkUniqueUsername:checkUniqueUsername
		};
	}();
	//end of events
		
	var utils = function()
	{
		function toggleGroupSlide(id, classname){
			var activeID = false;
			jQuery(classname).each(function(){
				if(jQuery(this).hasClass('active')){
					activeID = '#'+this.id;
				}
			});
			if(activeID == false || id == activeID){ // is active or active not set
				toggleActive(id);
				jQuery(id).slideToggle('slow');
			}else{ // not active
				toggleActive(activeID);
				jQuery(activeID).slideToggle('fast', function(){
					toggleActive(id);
					jQuery(id).slideToggle('slow');
				});	
			}
		}
		
		function toggleActive(id, classname){
			var elem = jQuery(id);
			if(elem.hasClass('active')){
				elem.removeClass('active');
			}else{
				if(typeof(classname) != 'undefined')
					jQuery(classname).removeClass('active');
				elem.addClass('active');
			}				
		}
		
		function toggleBox(tab_id, tab_class, content_id, content_class){
			jQuery(tab_class).removeClass('active');
			jQuery(tab_id).addClass('active');
			jQuery(content_class).removeClass('active').hide();
			jQuery(content_id).addClass('active').show();
		}
					
		function setActive(id, classname){			
			jQuery(classname).removeClass('active');
			jQuery(id).addClass('active');
		}
		
		function setWorkspace(){
			jQuery('.workspace.active').removeClass('active');
			jQuery(Widget.getWorkspace()).addClass('active');
		}
		
		function getParams(){
			var params = '';
			timestamp = new Date().getTime();
			
				
			// What is selected in the Context Pane?
			if(Context.getContext() != false && Context.getID() != false){
				params += '&'+Context.getContext()+'_id='+ Context.getID(); // e.g. &user_id = 1 OR &stream_id = 249
			}
			
			params += Filters.get();	
			/*if(Filters.getDates() != false){
				params += '&date='+ Filters.getDates();			
			}
						
			if(Filters.getUsers() != false){
				params += '&filter_user_ids='+ Filters.getUsers();
			}
	
			if(Filters.getPaginate() != false){
				params += '&paginate='+ Filters.getPaginate();			
			}*/
					
			params += "&timestamp="+timestamp;	
			return params;			
		}

		// RIVER3
		function getAddParams(){
			var params = '';
			timestamp = new Date().getTime();
			// What is selected in the Context Pane?
			if(Context.getContext() == 'stream' && Context.getID() != false){
				params += '&data[Stream][id]='+ Context.getID();
			}
			//var tags = jQuery('#formStreamsTagAdd #TagTags').val();
			//params += '&data[Tag][tags]='+tags;
			params += getParams(); // This is so we can send the context elements as well		
			//params += "&timestamp="+timestamp;	
			return params;			
		}
		
		// RIVER3
		function resetAllFilters()
		{
			Filters.resetAll();
		}
						
		function resizeWindow(e){
			var h = jQuery(window).height();	
			var w = jQuery(window).width();
			
			var context = jQuery('#river_context');
			var workspace =  jQuery('#river_workspace');
			var widgets = jQuery('#river_widgets');
			
			var container =  jQuery('#container');
			var river = jQuery('#river');

			var border = 10;
			var margin = 10;
			
			// WIDTH 
			var river_ow = river.outerWidth();
			var river_iw = river.width();
			
			var context_w = context.outerWidth();
			//var workspace_w = workspace.outerWidth();	
			//var widgets_w = widgets.outerWidth();	
			
			if(is_chrome != -1){
				var extra_w = 20;			
			}else{
				var extra_w = 20;
			}
			//console.log(w, river_w, context_w)
			workspace.css("width", river_ow - context_w - extra_w );
			widgets.css("width", river_ow - context_w - extra_w );
			
			// HEIGHT 
			
			var container_h = container.height();
			var river_h = river.height();	
			var widgets_h = widgets.outerHeight();
			var extra_h = 5;	

			var new_h = river_h + (h - container_h) - extra_h;

			if(new_h < 400)
				new_h = 400;
				
			context.css("height", new_h - 5);
			workspace.css("height", new_h - widgets_h);
			
			river.removeClass('tp-invisible');
		}	
		
		function toggleDock(){
			if(jQuery('#river_widgets ul').hasClass('active')){
				jQuery('#river_widgets').css('height', 0);
				jQuery('#river_widgets ul').hide().removeClass('active');
			}else{
				jQuery('#river_widgets').css('height', 57);
				jQuery('#river_widgets ul').show().addClass('active');
			}
			resizeWindow();setTimeout(function(){resizeWindow();}, 500);
		}	
		
		function riverSlider(){
			jQuery('.slider').each(function(){
				switch(this.id){
					case 'streams_slider':
						var scroll_id = '#streams_scroll';
						var container_id = '#streams_container';
						break;
					case 'filters_slider':
						var scroll_id = '#filters_scroll';
						var container_id = '#filters_container';
						break;
					case 'activity_slider':
						var scroll_id = '#activity_scroll';
						var container_id = '#activity_container';
						break;
				}
				var slide = jQuery('#'+this.id);
				slide.slider({
					orientation: "vertical",
					value: 100,
					max:100,
					min:0,
					slide: function(e, ui) {handleSliderSlide(scroll_id, e, ui)},
					change: function(e, ui){handleSliderChange(scroll_id, e, ui)}
				});			
				
				jQuery(scroll_id + ', #'+this.id).mousewheel(function(event, delta) {
					var value = slide.slider('value');
					var move = 2*delta;
					slide.slider('value', value+move);
				});
				/*var e = new Array();
				var e['data'] = new Array();
				var e['data']['s_id'] = scroll_id;
				var e['data']['c_id'] = container_id;
				var e['data']['slide'] = slide;
				scrollCheck(e);*/
				//jQuery(container_id).bind('mousemove', {s_id:scroll_id, c_id:container_id, slide: slide}, scrollCheck);
				jQuery(container_id).bind('click', {s_id:scroll_id, c_id:container_id, slide: slide}, scrollCheck);

				//.bind('mouseover', {s_id:scroll_id, c_id:container_id, slide: slide}, scrollCheck);			
			});

		}
		
		function scrollCheck(e){
			var s = jQuery(e.data.s_id); // scroll
			var c = jQuery(e.data.c_id); // container within scroll
			var slide = e.data.slide;		
			setTimeout(function(){
				if(c.height() <= s.height())
					slide.slider('disable').css('visibility', 'hidden');			
				else
					slide.slider('enable').css('visibility', 'visible');
			
			},50);
		}
		
		function handleSliderChange(id, e, ui)
		{
		  var maxScroll = jQuery(id).attr("scrollHeight") -jQuery(id).height();
		  jQuery(id).attr({scrollTop: (100-ui.value) * (maxScroll / 100) }, 1000);
		}
		
		function handleSliderSlide(id, e, ui)
		{
		  var maxScroll = jQuery(id).attr("scrollHeight") - jQuery(id).height();
		  jQuery(id).attr({scrollTop: (100-ui.value) * (maxScroll / 100) });
		}
		
		//mark for deletion by Matthew - Dec 11, 2009
		// non-ajax forms pre-submit 
		/*function validateNonAjaxForms(form) 
		{
			for (var i=0; i < form.length; i++) 
			{ 
				if (form[i].value == '' && form[i].name != "data[EntityUser][id]" && form[i].name != "data[Comment][blurb]" && form[i].name != "data[Page][title]") 
				{
					alerts.showValidateMessage(form[i].name);
					return false; 
				} 
			}
			return true;
		}*/
		//end mark for deletion by Matthew - Dec 11, 2009
				
		// ajax forms pre-submit callback 
		function validateAjaxForm(form)
		{
			//returns the strings "true" or "false" instead of the boolean true or false 
			//because as soon as we return true, the form submits, therefore, not an ajax form submit...
			for (var i=0; i < form.length; i++) 
			{
				if (validateField(form[i]))
					disableField(form[i]);
				else
					return false;
			}
			return true;
		}
		
		function validateField(field)
		{
			if (field.type != 'hidden' && field.id != 'CommentIsChild' && field.id != 'CommentParentId' && (String(field.id).toLowerCase().indexOf('comment') > -1 || String(field.id).indexOf('reply_') > -1))
			{
				//alert(field.id);
				if(String(field.id).toLowerCase().indexOf('reply_')==-1)
					CKEDITOR.instances[field.id].updateElement();
				if (jQuery.trim(field.value) == '<br />')
					field.value = '';
			}
			if(		(field.value == '' 
					&& field.name != "data[EntityUser][id]" 
					&& field.name != "data[Comment][blurb]" 
					&& field.className.indexOf("optional") == -1
					)
					|| field.value == 'add a collection' 
					|| field.value == 'add collection' 
					|| field.value == 'add/filter collection' 
					|| field.value == 'add a topic' 
					|| field.value == 'add a participant'
			  ) 
			{
				//console.log(field.value, field.className);
				alerts.showValidateMessage(field.name);
				return false; 
			} 
			return true;
		}
		
		function disableFields(form)
		{
			for (var i=0; i < form.length; i++) 
				disableField(form[i]);
		}
		
		function disableField(field)
		{
			//alert('fieldObject = '+field+', id = '+field.id + ', type = ' + field.type + ', ');
			if (field.type == 'text' || field.type == 'hidden' || field.type == 'textarea')
				field.readOnly = true;
			else if (field.type == 'submit')
				field.disabled = true;
		}
		
		function enableFields(form)
		{
			for (var i = 0; i < form.length; ++i)
			{
				if (form[i].type == 'text' || form[i].type == 'hidden' || form[i].type == 'textarea')
					form[i].readOnly = false;
				else if (form[i].type == 'submit')
					form[i].disabled = false;
			}
		}
		
		function setFieldValue(fieldObject, originalValue, action)
		{
			if (fieldObject.value == originalValue && action == "focus") {
				fieldObject.value = '';
			}
			else if (fieldObject.value == '' && action == "blur") {
				fieldObject.value = originalValue;
			}
		}
		
		// TODO: JS Cache of key-value that will be searched and then the resulting view updated
		// Instead of parsing the DOM every key-up
		function filter(field, listIds, isSubmit){		
			if(FiltersList[field.id] == undefined || FiltersList['ObjectCache'][field.id] == undefined){
				var filterListObject = jQuery(listIds.join(", "));			
				var items = filterListObject.children("li");
				FiltersList['ObjectCache'] = new Array();
				FiltersList['ObjectCache'][field.id] = items;
				
				FiltersList[field.id] = new Array();
				jQuery.each(items, function(){
					var content = this.textContent;
					content = content.trim();
					content = content.toLowerCase();
					content = content.replace(/configure|archive|unarchive|forget|mute|unmute/g, '');
					content = content.replace(/\n/g, '');
					FiltersList[field.id][this.id] = content; // Must add to this array for new entries
				});
			}else{
				items = FiltersList['ObjectCache'][field.id];
			}
			if (field.value  == '' || isSubmit){
				items.show();
			}
			else
			{
				items.hide();
			
				//var matchedItems = filterListObject.children('li').children('span.link:Contains("'+field.value +'")').parent();
				//matchedItems.show();
				
				for(key in FiltersList[field.id]){
					var found = FiltersList[field.id][key].search(field.value.toLowerCase());
					if(found != -1){
						jQuery('#'+key).show();
					}
				}
			}
		}
				
		function isCurrentRequest(t)
		{
			if (t == undefined)
				return true;
			var timestampReturned = new Date(t);
			return t >= timestamp;
		}
		
		function isInteger(s) {
			return (s.toString().search(/^-?[0-9]+$/) == 0);
		}

		function dynamicSize(t) {
			var width = t.offsetWidth;
			a = t.value.split('\n');
			b=1;
			for (x=0;x < a.length; x++) {
			 if (a[x].length >= t.cols) b+= Math.floor(a[x].length/width);
			}
			b+= a.length;
			if (b > t.rows)t.rows = b;
			if(a.length <= 1){
				if(a[0].length <= 1)
					t.rows = 2;
			}
		}
		
		function isSearch()
		{
			var searchDiv = jQuery('#activitySearch');
			return (searchDiv.attr('style') == '' || searchDiv.attr('style') == 'display: block;');
		}
		
		function eliminatePHPSpeedyCode(data)
		{
			if (data.indexOf("?>") >= 0)
				return data.substr(data.indexOf("?>") + 2); //to filter out php code from PHPSpeedy from AJAX calls
			else
				return data;
		}
		
		function br2nl(string)
		{
			var result = string.replace(/<br(\s*\/)?>(\n)*/gi, '\n');
			return result;
		}
		
		function nl2br(string)
		{
			var result = string.replace(/\n/g, "<br>");
			result = result.replace(/<br(\s*\/)?>\n/gi, '<br>');
			return result;
		}
		
		function counter(id, value){
			var elem = jQuery(id);
			var count = parseInt(jQuery(id).text());
			var total = count + value;
			elem.text(total);
			if(total == 0){
				elem.hide();
			}else{
				elem.show();
			}
		}
		
		return {
			//private functions accessible within this library
			isCurrentRequest:isCurrentRequest,
			isInteger:isInteger,
			disableFields:disableFields,
			enableFields:enableFields,
			isSearch:isSearch,
			eliminatePHPSpeedyCode:eliminatePHPSpeedyCode,
			resetAllFilters:resetAllFilters,
			nl2br:nl2br,
			counter:counter,
			
			//publicly accessible functions
			//validateNonAjaxForms:validateNonAjaxForms,
			setFieldValue:setFieldValue,
			filter:filter,
			dynamicSize:dynamicSize,
			resizeWindow:resizeWindow,
			toggleDock:toggleDock,
			riverSlider:riverSlider,
			setActive:setActive,
			setWorkspace:setWorkspace,
			getParams:getParams,
			getAddParams:getAddParams,
			validateAjaxForm:validateAjaxForm,
			br2nl:br2nl,
			toggleActive:toggleActive,
			toggleGroupSlide:toggleGroupSlide,
			toggleBox:toggleBox
		};
	}();
	
	var alerts = function()
	{
		/*
			Function:	clearAjaxMessages
			Purpose:	Gets all element objects that belong to the "ajaxMsg" class
						clear their innerHTML values and hide them by setting their style.display property to "none"
			Parameter:	None
			Output:		None
		*/
		function clearAjaxMessages(fid)
		{
			if(typeof(fid)=="undefined"){
				fid = feedback_id-1;
			}
			
			jQuery("#"+fid).fadeOut("slow");			
		}
		
		function displayAjaxMessage(message, id, clearTime)
		{
			var fid = 'feedback_'+feedback_id;
			fid = 'feedback_0';
			//feedback_id = feedback_id + 1;
			
			jQuery('#'+id).html('<div class="feedback" id="'+fid+'">'+message+'</div>');	
			setTimeout('thinkPanda.clearAjaxMessages("'+fid+'")', clearTime);
			jQuery('#'+fid).click(function () {
			  jQuery('#'+fid).fadeOut("slow");
			});
			jQuery('#'+fid).fadeIn("slow");
		}
		
		function showValidateMessage(fieldName)
		{
			var message = "";
			switch(fieldName)
			{
				case "data[Stream][stream]":
					message = 'Please enter a value into the "add a collection" field';
					break;
				case "data[Tag][tags]":
					message = 'Please enter valid topic/s';
					break;
				case "data[StreamsUser][fullname]":
					message = 'Please enter a value into the "add a participant" field';
					break;
				case "data[Comment][comment]":
					message = 'Please enter a value into the comment box';
					break;
				case "data[Page][page]":
					message = 'Please enter a value into the link box';
					break;
				case "data[StreamsTag][stream_id]":
					message = 'Please select a stream first. Create one if there is none!';
					break;
				case "data[StreamsUser][stream_id]":
					message = 'Please select a stream first. Create one if there is none!';
					break;
				case "data[Stream][stream_id]":
					message = 'Please select a stream first. Create one if there is none!';
					break;
				case "data[Filter][type]":
					message = 'Please select a filter first. Create one if there is none!';
					break;
				case "data[Filter][id]":
					message = 'Please select a filter first. Create one if there is none!';
					break;
				case "data[File][document]":
					message = 'Please select a document by pressing the Browse button';
					break;
				case "data[File][image]":
					message = 'Please select a picture by pressing the Browse button';
					break;
				default:
					message = 'Please fill out the appropriate fields before submitting';
					break;
			}
			alert(message);
		}
		
		function showFormSubmitMessage(form)
		{
			var message = 'Adding...';
			var id = '';
			var clearTime = '';
			
			var idSpecified = false;
			
			switch(form.id)
			{
				case "formStreamsUser": //add stream form - from river.ctp
					message = 'Adding stream...';
					id = 'streamFeedback';
					clearTime = 3000;
					idSpecified = true;
					break;
				case "formStreamsUser2": //add user to stream form - from filterFormUser.ctp, filterFormTag.ctp, filterFormPage.ctp, and filterFormComment.ctp
					message = 'Adding user...';
					id = 'filterUsersFeedback';
					clearTime = 3000;
					idSpecified = true;
					break;
				case "formStreamsTag": //add tag to stream form - from filterFormUser.ctp, filterFormTag.ctp, filterFormPage.ctp, filterFormComment.ctp
					message = 'Adding tag...';
					id = 'filterTagsFeedback';
					clearTime = 3000;
					idSpecified = true;
					break;
				case "formComment": //add comment to activity on Dashboard - from activityFormUser.ctp, activityFormComment.ctp
					message = 'Adding comment...';
					break;
				case "formPage": //add page to activity on Dashboard - from activityFormUser.ctp
					message = 'Adding page...';
					break;
				case "formCommentsTag": //add comment to activity on tags view - from activityFormTag.ctp
					message = 'Adding comment...';
					break;
				case "formPagesTag": //add page to activity on tags view - from activityFormTag.ctp
					message = 'Adding page...';
					break;
				case "formPageComment": //add comment to activity on pages view - from activityFormPage.ctp
					message = 'Adding comment...';
					break;
			}
			if(idSpecified)
				displayAjaxMessage(message, id, clearTime);
			else
				displayAjaxMessage(message);
		}
		
		return {
			//functions accessible both publicly and privately within this library
			clearAjaxMessages:clearAjaxMessages,
			displayAjaxMessage:displayAjaxMessage,
			showValidateMessage:showValidateMessage,
			showFormSubmitMessage:showFormSubmitMessage
		};
	}();
	
	var core = function()
	{
		// TO DELETE: Moved to Thinkers
		function getRelatedUsers()
		{
			var userFields = new Array(new Array("shareUserFullname", false), new Array("UserFilterFullname", false));
			var userFieldExist = false;
			
			for (var i = 0; i < userFields.length; ++i)
			{
				if(jQuery('#'+userFields[i][0]).length > 0)
				{
					userFields[i][1] = true;
					userFieldExist = true;
				}
			}
			if (userFieldExist)
			{
				jQuery.getJSON("/users/get_related/?timestamp="+timestamp, function(data){
					if(data.status == 'success')
					{
						//alert("success");
						var users = new Array();
						jQuery.each(data.relatedUsers, function(userKey, userValue)
						{
							users.push(new Array(userValue.User.fullname, userValue.User.id));
							//alert("i = " + (users.length-1) + ", users = " + users[users.length-1]);
						});
						for (var i = 0; i < userFields.length; ++i)
						{
							if (userFields[i][1])
								bind_strictAutoComplete(userFields[i][0], users);
						}
					}
				});
			}
		}
		
		function getUsersToApproveCount()
		{
			var usersTab = jQuery('#nav_my_thinkers');
			//alert(usersTab.length);
			if(usersTab.length > 0)
			{
				jQuery.ajax({
					type: 		"POST",
					url: 		"/users/getUsersToApproveCount",
					dataType: 	"json",
					success:	function(data, textStatus){
						if(data.status == 'success')
						{
							usersTab.children('a').html(usersTab.children('a').html() + " <span class='notification'>"+data.usersToApproveCount+"</span>");
							//usersTab.append('<span class="requests">'+data.requestedCount+'</span>');
						}
					}
				});
			}
		}
		
		/*********************************
				AutoComplete code	
		**********************************/
		
		function bindAutoCompletes()
		{
			var o_autoCompleteFields = jQuery('.autoComplete');
			
			//See http://docs.jquery.com/Plugins/Autocomplete/autocomplete#url_or_dataoptions for explanation of options
			var minChars, delay, matchSubset, matchContains, mustMatch, multiple;
			
			for (var i = 0; i < o_autoCompleteFields.length; ++i)
			{
				//alert("id = " + o_autoCompleteFields[i].id + " title = " + o_autoCompleteFields[i].title);
				if ((o_autoCompleteFields[i].title).indexOf("autoCompleteTag") > -1) {
					//alert("in autoCompleteTag, id = " + o_autoCompleteFields[i].id);
					minChars = 1;
					delay = 100;
					matchSubset = false;
					matchContains = false;
					mustMatch = false;
					multiple = true;
				}
				else
				{
					minChars = 3;
					delay = 200;
					matchSubset = true;
					matchContains = true;
					mustMatch = false;
					multiple = false;
				}
				bind_autoComplete(o_autoCompleteFields[i].id, jQuery('#'+o_autoCompleteFields[i].id+"Autocomplete").val(), minChars, delay, matchSubset, matchContains, mustMatch, multiple);
			}
		}
		
		//TO DELETE: Moved to Thinkers
		function bind_strictAutoComplete(id, relatedUsers)
		{
			var minChars = 1;
			var delay = 10;
			var matchSubset = true;
			var matchContains = true;
			var mustMatch = true;
			var multiple = false;
			bind_autoComplete(id, relatedUsers, minChars, delay, matchSubset, matchContains, mustMatch, multiple);
		}
		
		function bind_autoComplete(id, url, chars, ms, isMatchSubset, isMatchContains, isStrict, isMultiple)
		{
			jQuery('#' + id).autocomplete(url,
			{  
				minChars: chars,  
				delay: ms,
				matchSubset: isMatchSubset,
				matchContains: isMatchContains,
				mustMatch: isStrict,
				multiple: isMultiple,
				selectFirst: false,
				formatItem: render.formatItem,
				formatResult: render.formatResult
			});
			jQuery("#" + id).result(function(event, data, formatted) {
				var hidden = jQuery("#" + id + "Id");//jQuery(this).parent().next().find(">:input");
				
				//the line below sets the hidden field to "#;#;#;#", where # being an id in our case
				//hidden.val( (hidden.val() ? hidden.val() + ";" : hidden.val()) + data[1]);
				
				hidden.val(data[1]); //sets the value of the hidden field to the last id selected
			});
		}
		
		return {
			bindAutoCompletes:bindAutoCompletes,
			getRelatedUsers:getRelatedUsers,
			getUsersToApproveCount:getUsersToApproveCount,
			bind_autoComplete:bind_autoComplete
		};
	}();

	// INITIALIZE
	init();

	return {	
		//Objects
		Widget:Widget,
		Context:Context,
		Filters:Filters,
		
		//load methods
		loadActivity:load.loadActivity, //used for activity pagination
		loadUser:load.loadUser, //used for users/index.ctp pagination
		loadWebSearch:load.loadWebSearch,
		loadAjaxView:load.loadAjaxView,
		saveEdit:load.saveEdit, //used for editing page titles in the activity feed
		
		//events methods
		resizeFrame:events.resizeFrame,
		ajaxFavouriteClicked:events.ajaxFavouriteClicked,
		ajaxRatingsClick:events.ajaxRatingsClick,
		ajaxSpamClick:events.ajaxSpamClick,
		approveStreamRequestClicked:events.approveStreamRequestClicked,
		showFilter:events.showFilter,
		showActivity:events.showActivity,
		showFavouriteActivity:events.showFavouriteActivity,
		showReplies:events.showReplies,
		showTags:events.showTags,
		showEdit:events.showEdit,
		showCustomPopup:events.showCustomPopup,
		hideEdit:events.hideEdit,
		hideCustomPopup:events.hideCustomPopup,
		toggleSharing:events.toggleSharing,
		togglePanel:events.togglePanel,
		toggleActivity:events.toggleActivity,
		toggleAction:events.toggleAction,
		togglePanelAction:events.togglePanelAction,
		toggleMenu:events.toggleMenu,
		toggleFilter:events.toggleFilter,
		toggleSelf:events.toggleSelf,
		revealThought:events.revealThought,
		markAs:events.markAs,
		forget:events.forget,
		removeStream:events.removeStream,
		muteStream:events.muteStream,
		unmuteStream:events.unmuteStream,
		archiveStream:events.archiveStream,
		unarchiveStream:events.unarchiveStream,
		deleteExportFile:events.deleteExportFile,
		joinStream:events.joinStream,
		acceptStream:events.acceptStream,
		connectUser:events.connectUser,
		unconnectUser:events.unconnectUser,
		saveArticle:events.saveArticle,
		saveEmailNotificationSetting:events.saveEmailNotificationSetting,
		addReply:events.addReply,
		addRSS:events.addRSS,		
		addActivity:events.addActivity,
		addForm:events.addForm,
		addStream:events.addStream,
		addTag:events.addTag,
		addUser:events.addUser,
		addProjectOnSignup:events.addProjectOnSignup,
		uploadFile:events.uploadFile,
		searchThoughts:events.searchThoughts,
		clearSearch:events.clearSearch,
		searchWeb:events.searchWeb,
		toggleAjaxLoadingIndicator:events.toggleAjaxLoadingIndicator,
		discover:events.discover,
		checkUniqueUsername:events.checkUniqueUsername,
				
		//utils methods
		validateNonAjaxForms:utils.validateNonAjaxForms,
		setFieldValue:utils.setFieldValue,
		filter:utils.filter,
		dynamicSize:utils.dynamicSize,
		getParams:utils.getParams,
		getAddParams:utils.getAddParams,
		validateAjaxForm:utils.validateAjaxForm,
		enableFields:utils.enableFields,
		br2nl:utils.br2nl,
		setActive:utils.setActive,
		toggleActive:utils.toggleActive,
		toggleGroupSlide:utils.toggleGroupSlide,
		toggleBox:utils.toggleBox,
		toggleDock:utils.toggleDock,
		
		//alerts methods
		displayAjaxMessage:alerts.displayAjaxMessage,
		clearAjaxMessages:alerts.clearAjaxMessages,
		
		//core methods
		bind_autoComplete:core.bind_autoComplete,
		
		//render
		jsonMessage:render.jsonMessage
	};
}();