var widget_thinkers = function(){
		init();
		
		function init(){
			//getRelatedUsers();
		}

		function getRelatedUsers(){
			var timestamp = new Date().getTime();
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
					bind_autoComplete("UserFilterFullname", users);
				}
			});
		}
					
		function relatedUserAutocompleteItem(row) {
			var img = '<img class="ac_picture" height="16px" width="16px" src="' + row[2] +'"/>';
			var text = '<span class="ac_text">'+row[0]+'</span>';
			return img + text;
		}
		
		function relatedUserAutocompleteResult(row) {
			return row[0].replace(/(<.+?>)/gi, '');
		}
		
		function bind_autoComplete(id, relatedUsers)
		{
			jQuery('#' + id).autocomplete(relatedUsers,
			{  
				minChars: 1,  
				delay: 10,
				matchSubset: true,
				matchContains: true,
				mustMatch: false,
				multiple: false,
				selectFirst: true,
				formatItem: relatedUserAutocompleteItem,
				formatResult: relatedUserAutocompleteResult
			});
			jQuery("#" + id).result(function(event, data, formatted) {
				jQuery("#" + id + "Id").val(data[1]); //sets the value of the hidden field to the last id selected			
				jQuery("#" + id + "Verify").val(data[0]); //sets the value of the hidden field to the last id selected
			});
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

		function approveUser(user_id, updateID){
			jQuery(updateID).text("...");
			timestamp = new Date().getTime();
			jQuery.post("/thinkers/thinkers/approveUser/"+user_id+"/", {timestamp:timestamp}, function(data){
				if(data == 'true'){
					jQuery(updateID).replaceWith('<span>Connected</span>');
				}else{
					jQuery(updateID).html('Try Again');
				}
					
			});
		}
		
		function searchUser(form)
		{
			var userSearchField = jQuery("#SearchQuery");
			if (userSearchField.val() != "" && userSearchField.val() != "search thinkers...")
			{
				userSearchField.attr("readonly", true);
				
				thinkPanda.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'show');
				params = thinkPanda.getAddParams();	
				var data = jQuery("#"+form.id).serialize()+params;
				
				jQuery.post(form.action, data, function(data)
				{
					jQuery('#usersResults').html(data);
					
					userSearchField.attr("readonly", false);
					
					thinkPanda.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'hide');						
				}, 'html');	
			}
		}
		
		function addUser(form)
		{
			var user_fullname = jQuery("#UserFilterFullname").val();
			var user_id = jQuery("#UserFilterFullnameId").val();
			var user_fullnameVerify = jQuery("#UserFilterFullnameVerify").val();
			if (user_fullname != "" && user_fullname != "add a participant or email")
			{
				if (user_id == "" || (user_id != "" && user_fullname != user_fullnameVerify))
				{
					//this means that the user is adding a new user to Thinkpanda
					//show the invite user popup
					show_invite(user_fullname);
				}
				else if (user_id != "" && user_fullname == user_fullnameVerify)
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
							
							//clears the value of the add user input field
							jQuery("#UserFilterFullname").val("");
							jQuery("#UserFilterFullnameId").val("");
							jQuery("#UserFilterFullnameVerify").val(""); 
							
							thinkPanda.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'hide');
							thinkPanda.enableFields(form);
						});	
					}
				}
			}
			else
				alert("Oops you forgot to enter a name!");
		}
		
		function show_invite(emails)
		{
			jQuery("#invite_user_popup").show();
			
			jQuery("#InviteEmails").val(emails);
		}
		
		function hide_invite()
		{
			jQuery("#invite_user_popup").hide();
			jQuery("#InviteEmails, #InviteMessage").val("");
		}
		
		function invite(form)
		{
			var userEmails = jQuery("#InviteEmails");
			if (userEmails.val() != "")
			{
				var inputs = jQuery("#"+form.id+":input");
				inputs.attr("readonly", true);
				
				thinkPanda.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'show');
				params = thinkPanda.getAddParams();	
				var data = jQuery("#"+form.id).serialize()+params;
				
				jQuery.getJSON(form.action, data, function(data)
				{
					thinkPanda.displayAjaxMessage(data.message, 'feedback_box', 5000);
					
					if (data.status == "success")
					{
						jQuery("#UserFilterFullname").val("");
						hide_invite();
					}
					
					inputs.attr("readonly", false);
					thinkPanda.toggleAjaxLoadingIndicator('', form.id, 'Forms', 'hide');						
				});	
			}
			else
				alert("Oops you forgot to enter an email address!");
		}
				
	return{
		addUser:addUser,
		getRelatedUsers:getRelatedUsers,
		approveStreamsUser:approveStreamsUser,
		approveUser:approveUser,
		searchUser:searchUser,
		invite:invite,
		hide_invite:hide_invite
	};
}();		