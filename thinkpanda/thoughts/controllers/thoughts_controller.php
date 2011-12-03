<?php
class ThoughtsController extends ThoughtsAppController {

	var $name = 'Thoughts';
	var $helpers = array('Html', 'Form', 'Javascript');
	var $components = array('Email'); 
	//var $uses = array(); // Doesn't have a model
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('filtered', 'replies', 'search'); // Guest permissions
		 
		$widget_id = 1; // Every widget must set their Widget.id	
		$widget = $this->getWidget($widget_id);
			
		$this->core['widget'] = $widget;
		$this->pageTitle = $widget['widget'];
    }
	
	function view()
	{
		if ($this->RequestHandler->isAjax()){
			$this->layout = 'ajax';		
		}
		
		$streamsTag = NULL;
		$options = $this->setOptions($this->params['url']);
		$pagination = array();
		$paging = array();
		if(empty($this->core['my'])){
			$this->core['my'] = $this->initGuest();
		}
		
		if(empty($options['user_id']) && empty($options['stream_id']))
			$options['user_id'] = $this->core['my']['id'];
			
		if(empty($options['focus'])){
			if(!empty($options['user_id']) && $options['user_id'] == $this->core['my']['id'])
				$options['focus'] = 'global';
			else if(!empty($options['user_id']) && $options['user_id'] != $this->core['my']['id'])
				$options['focus'] = 'thinker';
			else	
				$options['focus'] = 'projects';
		}

		$thoughts = $this->Thought->Comment->get($this->core, $options);

		
		if($thoughts === false){
			$msg = 'You do not have permission to view this Collection';
			$this->set(compact('msg'));
			$this->render('/elements/global/ajaxMessage');
		}else if(empty($thoughts)){
			$thoughts['message'] = 'No thoughts found.';
		}else{	
			
			// Get Tags
			if(isset($options['stream_id'])){
				$streamsTag = $this->Thought->getTags($options['stream_id']);
			}else if(isset($options['user_id']) && false){
				$streams = $this->Thought->Comment->Stream->getStreamsForTagViewer($options['user_id'], $this->core['my']['id'],  $this->core['permissions']);
				$streamsTag = $this->Thought->getTags($streams);
			}
				
			//$pagination = $this->Thought->Comment->getPagination($this->core, $options);
		}
		//debug($thoughts);
		$handler =  "'/thoughts/thoughts/filtered/'";
		$updateID = "'#thoughtList'";
		if(count($thoughts) == $options['pageSize'])
			$paging = array('handler'=> $handler,'updateID'=>"'#paginateResults'");
		$this->set(compact('thoughts', 'pagination', 'paging', 'streamsTag', 'options', 'handler', 'updateID'));
	}
	
	function filtered(){
		if(empty($this->core['my'])){
			$this->core['my'] = $this->initGuest();
		}
		$paging = array();
		$options = $this->setOptions($this->params['url']);
		if(empty($options['focus'])){
			if(!empty($options['user_id']) && $options['user_id'] == $this->core['my']['id'])
				$options['focus'] = 'global';
			else if(!empty($options['user_id']) && $options['user_id'] != $this->core['my']['id'])
				$options['focus'] = 'thinker';
			else	
				$options['focus'] = 'projects';
		}

		//debug($options);
		$thoughts = $this->Thought->Comment->get($this->core, $options);
		$this->layout = 'ajax';
		if($thoughts === false){
			$msg = 'You do not have permission to view this Collection';
			$this->set(compact('msg'));
			$this->render('/elements/global/ajaxMessage');
		}else if(empty($thoughts)){
			$msg = 'No thoughts found.';
			$this->set(compact('msg'));
			$this->render('/elements/global/ajaxMessage');
		}else{ // Allow additional filters here
			$handler =  "'/thoughts/thoughts/filtered/'";
			$updateID =	"'#paginateResults'";
			if(count($thoughts) == $options['pageSize'])
				$paging = array('handler'=> $handler,'updateID'=> $updateID);
			$this->set('data', array('thoughts' => $thoughts, 'paging'=>$paging));
			$this->render('/elements/thoughts'); 
		}
	}
	
	function replies(){
		if(empty($this->core['my'])){
			$this->core['my'] = $this->initGuest();
		}	
		$options = $this->setOptions($this->params['url']);
		//debug($options);
		$thoughts = $this->Thought->Comment->get($this->core, $options);
		
		$this->layout = 'ajax';
		if($thoughts === false){
			$message = 'You do not have permission to view these comments';
			$this->set(compact('message', 'filter_type', 'activities'));
			$this->render('/activities/activity');
		}else if(!empty($thoughts)){				
			$this->set(compact('thoughts'));
			$this->render('/elements/replies');	
		}else{
			$this->set('message', 'No replies yet.');
			$this->render('/elements/error'); 
		}	
	}

	
	function search()
	{
		if(empty($this->core['my'])){
			$this->core['my'] = $this->initGuest();
		}	
		//debug($this->params['form']);
		/*Array
		(
			[user_id] => 16
			[limit] => 20
			[timestamp] => 1264796039377
		)
		*/
		
		//debug($this->data);
		/*Array
		(
			[Search] => Array
			(
				[terms] => css
			)
		)*/
		$this->layout = 'ajax';		
		if ( (!empty($this->data['Search']['terms']) && !empty($this->params['form']) ) || !empty($this->params['url']['search_terms'])) 
		{
			if(!empty($this->params['form'])){
				$this->params['url'] = $this->params['form'];		
			}
			$this->Thought->clean($this->params['url']['search_terms']);

			$options = $this->setOptions($this->params['url']);	
					
			$thoughts = $this->Thought->Comment->get($this->core, $options);
			//debug($thoughts);
			$paging = array();

			if($thoughts === false){
				$msg = 'You do not have permission to view these Thoughts';
			}else if(empty($thoughts)){
				$msg =  'No thoughts found matching "'.$this->data['Search']['terms'].'".';
			}else{ // Allow additional filters here
				$handler =  "'/thoughts/thoughts/search/'";
				$updateID =	"'#paginateResults'";
				if(count($thoughts) == $options['pageSize'])
					$paging = array('handler'=> $handler,'updateID'=> $updateID);
				$this->set('data', array('thoughts' => $thoughts, 'paging'=>$paging));
				$this->render('/elements/thoughts'); 
				return;
			}
		}
		else
		{
			$msg = 'Search something to find a Thought';
		}
		$this->set(compact('msg'));
		$this->render('/elements/global/ajaxMessage');
	}
	
	function add()
	{
		if (!empty($this->data)) 
		{
			//debug($this->params['form']);
			if (!empty($this->params['form'])) //add reply
			{
				if (!empty($this->params['form']['stream_id']))
					$this->data['Stream']['id']  = $this->params['form']['stream_id'];
			}
			
			if(isset($this->data['Comment']['parent_id']))
				$parent_id = $this->data['Comment']['parent_id'];
					

			/*if(isset($this->data['Field']['blurb'][0]))
			{
				//$this->data['Field']['blurb'][0] = $this->Thought->Comment->__trimBrTags($this->data['Field']['blurb'][0]);
				//$this->data['Field']['blurb'][0] = $this->Thought->Comment->process($this->data['Field']['blurb'][0]);
				$this->data['Field']['blurb'][0] = $this->Thought->clean($this->data['Field']['blurb'][0]);
			}*/
				
			// Better for performance but assumes no change in dB
			$this->data['Type']['id'] = 44; //type_id = 44 is for Note
			$this->data['Type']['type'] = 'Note'; 	
			$this->data['Type']['class'] = 'note'; 
			//$this->Thought->Comment->Type->recursive = -1; 
			//$type = $this->Thought->Comment->Type->findById($this->data['Type']['id']);
			$options = $this->setOptions($this->params['form']);
			//debug($this->data['Tag']);																				
			$thoughts = $this->Thought->Comment->add($this->data, $this->core, $options);
			//debug($thoughts);
		}
		else
		{
			$message = 'No data was passed';
		}
			
		$this->layout = 'ajax';
		
		if ($thoughts)
		{
			if(isset($parent_id)){
				$this->set(compact('thoughts'));
				$this->render('/elements/replies');				
			}else{
				$this->set('data', array('thoughts' => $thoughts));
				$this->render('/elements/thoughts');
			}
		}
		else
		{
			$this->set(compact('message'));
			$this->render('/elements/error');	
		}
	}
	
	function addPage()
	{
		//$this->loadModel('Comment');
		//$comment = ClassRegistry::init('Comment'); 
				
		if(isset($this->params['url']['page']) && isset($this->params['url']['blurb'])) //this add page call was submitted from ?
		{
			//debug($this->params['url']);
			$this->data['Stream']['id']= $this->params['url']['stream_id']; // from GET
			$this->data['Filter']['type'] = $this->params['url']['filter_type'];
			$this->data['Filter']['id']  = $this->params['url']['filter_id'];	
			$this->data['Field']['blurb'][0] = $this->params['url']['blurb'];	
			$this->data['Page']['page'] = $this->params['url']['page'];	
			$this->data['Page']['title'] = $this->params['url']['title'];	
		}
		
		if (!empty($this->data)) 
		{
			//debug($this->data);					
			$_page = trim($this->data['Field']['url'][0]);
			if (!empty($_page)) 
			{
				define("FIELD_NAME_URL", 'url');
				define("FIELD_NAME_WEBSITE_TITLE", 'websiteTitle');
				define("FIELD_NAME_FAVICON", 'favicon');
				define("FIELD_NAME_BLURB", 'blurb');
				
				$_page = $this->Thought->__urlResolve($_page);
				//debug($_page);
				$this->data['Field']['url'][0] = $_page;
				
				$_favicon = $this->Thought->__getFavicon($_page);
				//debug($_favicon);
				$this->data['Field'][FIELD_NAME_FAVICON][] = $_favicon;
				
				$_html = $this->Thought->__getHTML($_page);
				if (!isset($this->data['Field'][FIELD_NAME_WEBSITE_TITLE][0]))
				{
					$_title = '';
					if(empty($this->data['Page']['title']))
						$_title = $this->Thought->__urlTitle($_html);
					else
						$_title = mysql_real_escape_string($this->data['Page']['title']);
					$this->data['Field'][FIELD_NAME_WEBSITE_TITLE][] = $_title;
					//debug($_title);
				}
				
				if(empty($this->data['Page']['type_id'])){
					$_pageType = $this->Thought->__getType($_page, $_html);
				}else{
					$_pageType = $this->data['Page']['type_id'];							
				}
				//debug($_pageType);
				$this->data['Comment']['type_id'] = $_pageType;
				
				$this->Thought->Type->recursive = -1;
				$type = $this->Thought->Type->findById($this->data['Comment']['type_id']);
				//debug($type);
				$this->data['Type'] = $type['Type'];
				
				$options = $this->setOptions($this->params['form']);
				$thoughts = $this->Thought->Comment->add($this->data, $this->core, $options);
				//debug($thoughts);
				if (!$thoughts)
					$message = 'Thought could not be saved at this time';
			}
			else
			{
				$message = 'You have not entered a valid link';
			}
		}
		else
		{
			$message = 'No data was passed';
		}
		
		$this->layout = 'ajax';
		//Configure::write('debug',0); //When output Json. 
		if ($thoughts)
		{
			if($this->data['Comment']['type_id'] == 9){ //9 is for RSS feeds
				$feedsList = $thoughts;
				$articles = $this->Thought->__getRssArticlesList($feedsList);
				$this->set('feeds', $feedsList);
				$this->set('articles', $articles);
				$this->render('/elements/activity/feeds'); 					
			}else{
				$this->set('data', array('thoughts' => $thoughts));
				$this->render('/elements/thoughts');
			}
		}
		else
		{
			$this->set(compact('message'));
			$this->render('/activities/error');	
		} 
	}

	function addEtherpad()
	{
		//$this->loadModel('Comment');
		//$comment = ClassRegistry::init('Comment'); 
				
		if(isset($this->params['url']['page']) && isset($this->params['url']['blurb'])) //this add page call was submitted from ?
		{
			//debug($this->params['url']);
			$this->data['Stream']['id']= $this->params['url']['stream_id']; // from GET
			$this->data['Filter']['type'] = $this->params['url']['filter_type'];
			$this->data['Filter']['id']  = $this->params['url']['filter_id'];	
			$this->data['Field']['blurb'][0] = $this->params['url']['blurb'];	
			$this->data['Page']['page'] = $this->params['url']['page'];	
			$this->data['Page']['title'] = $this->params['url']['title'];	
		}
		
		if (!empty($this->data)) 
		{
			define("FIELD_NAME_URL", 'url');
			define("FIELD_NAME_WEBSITE_TITLE", 'websiteTitle');
			define("FIELD_NAME_FAVICON", 'favicon');
			define("FIELD_NAME_BLURB", 'blurb');
			//debug($this->data['Field']);					
			$this->data['Field'][FIELD_NAME_WEBSITE_TITLE][0] = h($this->data['Field'][FIELD_NAME_WEBSITE_TITLE][0]);
			if (!empty($this->data['Field'][FIELD_NAME_WEBSITE_TITLE][0])) 
			{
				$slug = $this->slug($this->data['Field'][FIELD_NAME_WEBSITE_TITLE][0]);
				$slug = substr($slug, 0, 30).'-'.time();
				$rand = rand(1,6);
				switch ($rand){
					 case 1:
						$_page = 'http://piratepad.net/'.$slug;
				        break;
				    case 2:
						$_page = 'http://typewith.me/'.$slug;
				        break;
				    case 3:
						$_page = 'http://ietherpad.com/'.$slug;
				        break;
 					case 4:
						$_page = 'http://openetherpad.org/'.$slug;
				        break;
				    case 5:
						$_page = 'http://titanpad.com/'.$slug;
				        break;
				    case 6:
						$_page = 'http://meetingwords.com/'.$slug;
				        break;
				}
				//$this->Thought->__urlResolve($_page);
				//debug($_page);
				$this->data['Field']['url'][0] = $_page;
				
				//$_favicon = $this->Thought->__getFavicon($_page);
				//debug($_favicon);
				$this->data['Field'][FIELD_NAME_FAVICON][] = 'http://thinkpanda.com/img/icons/etherpad.png';
				
				$_pageType = 74;
				/*if(empty($this->data['Page']['type_id'])){
					 = $this->Thought->__getType($_page, $_html);
				}else{
					$_pageType = $this->data['Page']['type_id'];							
				}*/
				//debug($_pageType);
				$this->data['Comment']['type_id'] = $_pageType;
				
				//$this->Thought->Type->recursive = -1;
				//$type = $this->Thought->Type->findById($this->data['Comment']['type_id']);
				//debug($type);
				$this->data['Type'] = array('id' => 74,
	            'type' => 'EtherPad',
	            'class' => 'etherpad',
	            'match_criteria' => NULL);
					
				$options = $this->setOptions($this->params['form']);
				$thoughts = $this->Thought->Comment->add($this->data, $this->core, $options);
				//debug($thoughts);
				if (!$thoughts)
					$message = 'EtherPad could not be saved at this time';
			}
			else
			{
				$message = 'You have not entered a Title';
			}
		}
		else
		{
			$message = 'No data was passed';
		}
		
		$this->layout = 'ajax';
		//Configure::write('debug',0); //When output Json. 
		if ($thoughts)
		{
			$this->set('data', array('thoughts' => $thoughts));
			$this->render('/elements/thoughts');
		}
		else
		{
			$this->set(compact('message'));
			$this->render('/activities/error');	
		} 
	}	
		
	function edit($id = NULL)
	{
		$json = array (
			'message'	=> 'Thought not saved.',
			'status' 	=> 'failed',
		);
		if($id && !empty($this->params['url']))
		{
			$field_id = $this->params['url']['field_id'];
			$field_name = substr($field_id, strrpos($field_id, "_") + 1);
			
			$this->data['Comment']['id'] = $id;
			$this->data['Field'] = $this->params['url']['data']['Field'];
			//debug($this->data);
			
			$options = $this->setOptions($this->params['form']);			
			$thoughts = $this->Thought->Comment->add($this->data, $this->core, $options);
			if ($thoughts)
			{
				$json['status'] = 'success';
				$json['message'] = 'Thought edited.';
			}
			else
			{
				$json['error'] = 'Edit cannot be saved at this time.  Please try again later.';
			}
		}
		else
		{
			$json['error'] = 'Empty id or this->params[url]';
		}
		$this->layout = 'ajax';
		Configure::write('debug',0); //When output Json. 
		echo json_encode($json);
		exit();
	}
	
	function delete($comment_id = NULL) 
	{
		$json = array (
			'message' => 'Thinkpanda is having difficulties forgetting this thought at this time.  Please try again later.', 
			'status' => 'failed',
		);
		
		if (!$comment_id) 
			$json['message'] = 'Invalid id for Thought';
		else 
		{
			$message = $this->Thought->Comment->delete($comment_id, $this->core);
			
			if ($message === 0) //delete successful
			{
				$json['message'] = 'The thought has been forgotten';
				$json['status'] = 'success';
			}
			else if ($message === 2)
				$json['message'] = 'You are not allowed to forget this thought!';
			else
				$json['message'] = 'The thought cannot be forgotten at this time.  Please try again later.';
		}
		$this->layout = 'ajax';
		Configure::write('debug',0); //When output Json. 
		echo json_encode($json);
		exit();
	}

	//source: http://bakery.cakephp.org/articles/view/calling-controller-actions-from-cron-and-the-command-line
	function sendEmails()//$password)
	{
		$comments = array();
		
		$timestamp = time() - (900); //15 minutes before
		$fifteenMinutesEarlier = "2010-02-28 18:50:58"; //date("Y-m-d H:i:s", $timestamp); //
		$comments = $this->Thought->Comment->find("all", array(
			"recursive"		=> -1,
			"conditions"	=> array(
				"Comment.created >="	=> $fifteenMinutesEarlier,
				"Comment.delete_user_id IS NULL"
			)
		));
		//debug($comments);
		
		$mode = 'add';
		foreach ($comments as $comment)
		{
			/* Matthew - no need for sending notifcations for thought edits
			//calculate the time difference in seconds
			$timeDifference = strtotime($comment['Comment']['modified']) - strtotime($comment['Comment']['created']);
						
			//check to see if the comment was modified more than 15 minutes ago
			//this is needed because we send emails in 15 minute intervals
			//if the user edited the entry within 15 minutes
			if ($timeDifference > (15*60))
				$mode = 'edit';*/
			
			if ($comment['Comment']['stream_id'] != 2) //don't send notification for global sandbox collection
			{
				if (!is_null($comment['Comment']['parent_id']) && !empty($comment['Comment']['parent_id'])) //this is a reply
					$this->__sendReplyEmails($comment['Comment'], $fifteenMinutesEarlier);
				else //this is a thought
					$this->__sendThoughtEmails($comment['Comment'], $fifteenMinutesEarlier, $mode);
			}
		}
		
		$this->set(compact("comments"));
	}
	
	/** __sendReplyEmails
	 * 	sends email notification to users within the thought where a reply was added
	 */
	function __sendReplyEmails($comment, $fifteenMinutesEarlier, $mode = "reply")
	{
		//convert the comment data into associative array
		$data = json_decode($comment['data'], true);
		
		//get the project details
		$stream = $this->Thought->User->StreamsUser->Stream->find("first", array(
			"recursive"	=> -1,
			"conditions"=> array(
				"Stream.id"	=> $comment['stream_id'],
				"Stream.delete_user_id IS NULL"
			)					
		));
			
		//send the email to the originator of the Thought
		$originalThought = $this->Thought->Comment->find("first", array(
			"recursive"		=> -1,
			"conditions"	=> array(
				"Comment.id"	=> $comment["parent_id"],
				"Comment.delete_user_id IS NULL"
			)
		));
		
		if ($originalThought)
		{
			//get the user details
			$this->Thought->User->recursive = -1;
			$user = $this->Thought->User->findById($originalThought['Comment']['user_id']);
			
			if ($user && $stream)
			{
				$emailSubject = $data['User']['fullname']." replied to your Thought in the \"".$stream['Stream']['stream']."\" collection";
				$plugin = $data['Widget']['class'];
				$this->__sendEmail($comment, $user, $stream, $emailSubject, $plugin, $mode);
			}
		}
		
		//get the comments with the same parent_id as the incoming comment
		$user_ids = $this->Thought->Comment->find("list", array(
			"recursive"		=> -1,
			"conditions"	=> array(
				"Comment.parent_id"		=> $comment["parent_id"],
				"Comment.user_id !="	=> $comment["user_id"],
				"Comment.delete_user_id IS NULL"
			),
			"fields"		=> array("Comment.user_id")
		));
		
		//extract the user_id's from the comments with the same parent_id as the incoming comment
		$user_ids = array_unique($user_ids);
		
		if ($stream)
		{
			foreach($user_ids as $user_id)
			{
				//send notification to those users for the incoming comment
				$this->Thought->User->recursive = -1;
				$user = $this->Thought->User->findById($user_id);
				
				if ($user)
				{
					$emailSubject = $data['User']['fullname']." replied to your Thought in the \"".$stream['Stream']['stream']."\" collection";
					
					$plugin = $data['Widget']['class'];
					$this->__sendEmail($comment, $user, $stream, $emailSubject, $plugin, $mode, $originalThought);
				}
			}
		}
	}
	
	/** __sendThoughtEmails
	 * 	sends email notification to users within the project where a thought was added
	 */
	function __sendThoughtEmails($comment, $fifteenMinutesEarlier, $mode)
	{
		//get those unread comments_users entries that were created within the last 15 minutes
		$unread = 0;
		$read = 1;
		$comments_users = $this->Thought->Comment->CommentsUser->find("all", array(
			"recursive"	=> -1,
			"conditions"=> array(
				"CommentsUser.comment_id" 	=> $comment['id'],
				"CommentsUser.delete_user_id IS NULL"
			),
		));
		//debug($comments_users);
		/*Array
		(
			[0] => Array
			(
				[CommentsUser] => Array
				(
					[id] => 62
					[comment_id] => 1962
					[user_id] => 13
					[stream_id] => 482
					[widget_id] => 1
					[is_favourite] => 
					[delete_user_id] => 
					[created] => 2010-02-23 16:45:09
					[modified] => 
					[deleted] => 
					[views] => 0
					[status] => 0
				)
			)
			[1] => Array () etc...
		)*/
		
		if ($comments_users)
		{
			foreach ($comments_users as $comments_user)
			{
				//only send notification if it's an unread new thought or if it's a read thought that has been edited
				if (($mode == 'add' && $comments_user['CommentsUser']['status'] == $unread) 
					|| ($mode == 'edit' && $comments_user['CommentsUser']['status'] == $read))
				{
					$user = $this->Thought->User->findById($comments_user['CommentsUser']['user_id']);
					$stream = $this->Thought->User->StreamsUser->Stream->find("first", array(
						"recursive"	=> -1,
						"conditions"=> array(
							"Stream.id"	=> $comments_user['CommentsUser']['stream_id'],
							"Stream.delete_user_id IS NULL"
						)					
					));
					if ($user && $stream)
					{
						$data = json_decode($comment['data'], true);
						
						if ($mode == 'edit')
							$emailSubject = $data['User']['fullname']." edited a Thought in the \"".$stream['Stream']['stream']."\" collection";
						else
							$emailSubject = $data['User']['fullname']." added a new Thought in the \"".$stream['Stream']['stream']."\" collection";
						
						$plugin = $data['Widget']['class'];
						//$this->__sendEmail($comment, $user, $stream, $emailSubject, $plugin, $mode);
					}
				}
			}
		}
	}
	
	function __sendEmail($comment, $user, $stream, $emailSubject, $plugin, $mode, $originalThought = NULL)
	{
		//reference at http://bakery.cakephp.org/articles/view/brief-overview-of-the-new-emailcomponent
		$this->Email->to = "mcschan@gmail.com";//$user['User']['email'];
		$this->Email->subject = $emailSubject;
		$this->Email->replyTo = "no-reply@thinkpanda.com";
		$this->Email->from = "Thinkpanda <notifications@thinkpanda.com>";
		$this->Email->sendAs = 'html'; //Send as 'html', 'text' or 'both' (default is 'text') 
		//$this->Email->_debug = true;
		
		//Set the body of the mail as we send it.
		//Note: the text can be an array, each element will appear as a seperate line in the message body.
		//also we can use templates
		$this->Email->template = 'notification/thought'; //using the template in views/elements/email/html/.ctp
		
		//set view variables for rendering
		//the way we set these variables in shells is not the same as normal controllers
		//http://bakery.cakephp.org/articles/view/emailcomponent-in-a-cake-shell
		$this->set(compact('comment', 'user', 'stream', 'plugin', 'mode', 'originalThought')); 
		
		if($this->Email->send())
			// write to some log file
			;
		else
			// write to some log file
			;
		$this->Email->reset();
	}
}
?>