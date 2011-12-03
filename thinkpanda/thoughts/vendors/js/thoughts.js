var widget_thoughts = function(){
		init();
		
		function init(){
			//getRelatedUsers();
		}

		function addStreamsTag(form){
			if (thinkPanda.validateAjaxForm(form)){			
				thinkPanda.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'show');
				var data = jQuery("#"+form.id).serialize()+thinkPanda.getAddParams();
				
				jQuery.post(form.action, data, function(html, textStatus){
					//console.log(html.status, typeof(html.status));
					if(typeof(html.status)!='undefined'){
						thinkPanda.displayAjaxMessage(html.message, 'feedback_box', 5000);
					}else{
						jQuery("#"+form.id+' #TagTags').val('');				
	    				//jQuery('ul#streamsTag').prepend(html);	
	    				jQuery('ul#streamsTagAdd').prepend(html);
						thinkPanda.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'hide');
						thinkPanda.enableFields(form);

					}
    			}, 'html');	
			}
		}
		
		/*
		function addUser(form)
		{
			if (thinkPanda.validateAjaxForm(form))
			{
				
				thinkPanda.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'show');
				var params = thinkPanda.getAddParams();
				jQuery.post(form.action, jQuery("#"+form.id).serialize()+params, function(data, textStatus)
				{
    				//alert("Data Loaded: " + data);
					//alert("textStatus: " + textStatus);
					
					var dataDiv = jQuery('#ajaxReturnData');
					dataDiv.html(data);
					
					var msg = dataDiv.children('.filterMessage').remove();
					if (msg != null && msg != '')
						thinkPanda.displayAjaxMessage(msg.html(), 'feedback_box', 5000);

					var filterUsers = dataDiv.children('.add_filter_users').children();
					
					if (filterUsers != null && filterUsers.length > 0)
					{
						filterUsers.each(function(){
							//jQuery(this).prependTo('#usersToApproveList');
						});
					}

					
					dataDiv.children('.add_filter_users').remove(); //clears the innerHTML of the <div id="ajaxReturnData">
					
					jQuery('#UserFilterFullname, #UserFilterFullnameId').val(""); //clears the value of the add user input field
					
					thinkPanda.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'hide');
					thinkPanda.enableFields(form);
				});
			}
		}
				
		function getRelatedUsers(){
			var timestamp = new Date().getTime();
			var userFields = new Array(new Array("UserFilterFullname", false));
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
				jQuery.getJSON("/thinkers/thinkers/get_related/?timestamp="+timestamp, function(data){
					if(data.status == 'success')
					{
						//alert("success");
						var users = new Array();
						jQuery.each(data.relatedUsers, function(userKey, userValue)
						{
							users.push(new Array(userValue.User.fullname, userValue.User.id, userValue.User.picture));
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
					
		function bind_strictAutoComplete(id, relatedUsers){
			var minChars = 1;
			var delay = 10;
			var matchSubset = true;
			var matchContains = true;
			var mustMatch = false;
			var multiple = false;
			thinkPanda.bind_autoComplete(id, relatedUsers, minChars, delay, matchSubset, matchContains, mustMatch, multiple);
		}
		
		function approveStreamsUser(streams_users_id, updateID){
			jQuery(updateID).text("...");
			timestamp = new Date().getTime();
			jQuery.post("/thinkers/thinkers/approveStreamsUser/"+streams_users_id+"/", {timestamp:timestamp}, function(data){
				if(data == 'true'){
					jQuery(updateID).replaceWith('<span>Approved</span>');
				}else{
					jQuery(updateID).html('Try Again');
				}
					
			});
		}
		*/
		
		// copied from thinkPanda.js by Matthew - Fri, Jan 29, 2010 
		function searchThoughts(form, updateID)
		{
			thinkPanda.Filters.resetPaginate();
			if (jQuery("#"+form.id+" input#SearchTerms").val() != '')
			{
				jQuery("#"+form.id+" input#SearchTerms").attr('readonly', true);
				
				thinkPanda.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'show');
				
				var search_terms = jQuery('#SearchTerms').val();
				thinkPanda.Filters.setSingle('search_terms', search_terms);
				
				var parameters = thinkPanda.getParams();
				
				jQuery.post(form.action, jQuery("#"+form.id).serialize()+parameters, function(data, textStatus)
				{
    				//alert("Data Loaded: " + data);
					//alert("textStatus: " + textStatus);
					
					jQuery(updateID).html(data);
					
					/*events.hideActivityContent(); //clears the activity lists
										
					var activityMessageDiv = jQuery('#activityMessage');
					activityMessageDiv.html('');
					activityMessageDiv.hide();
					
					var updateID = '#activityListSearch';
					var searchList = jQuery(updateID);
					
					searchList.html(data);*/
					
					thinkPanda.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'hide');
					/*events.toggleList('SearchThoughts', true); //show search activity list
					
					var searchKeywordSpan = jQuery('#searchKeyword');
					searchKeywordSpan.children('span').html(searchTermsField.val());
					searchTermsField.val('');
					searchKeywordSpan.show();
										
					utils.enableFields(form);*/
					jQuery("#"+form.id+" input#SearchTerms").attr('readonly', false);
				}, 'html');	
			}
			else
			{
				alert("Oops you forgot to enter some keywords to search!");
			}
		}
		
		function resetSearchThoughts()
		{
			jQuery('#SearchTerms').val(''); 
			thinkPanda.Filters.unset('search_terms'); 
			thinkPanda.Filters.resetPaginate();
			
			var parameters = thinkPanda.getParams();
			
			jQuery.get("/thoughts/thoughts/view", parameters, function(data, textStatus)
			{
				//alert("Data Loaded: " + data);
				//alert("textStatus: " + textStatus);
				
				jQuery("#workspace_thoughts").html(data);
			}, 'html');	
		}
		
	return{
		addStreamsTag:addStreamsTag,
		searchThoughts:searchThoughts,
		resetSearchThoughts:resetSearchThoughts
	};
}();		