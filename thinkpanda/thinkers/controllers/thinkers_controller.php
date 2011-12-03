<?php
class ThinkersController extends ThinkersAppController {
	var $name = 'Thinkers';
	var $helpers = array('Html', 'Form', 'Javascript');
	var $components = array('Email'); 
	
	var	$pageSize = 18;
	var $pageSizeRequested = 18;
	var	$pageSizeUnrelated = 18;
	var	$pageSizeApprove = 18;
	var $pageSizeSuggested = 18;
	var $pageSizeFollowedBy = 18;
	var $pageSizeThinkerBoard = 20;

			
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('lite', 'search');

		$widget_id = 4; // Every widget must set their Widget.id	
		$widget = $this->getWidget($widget_id);
			
		$this->core['widget'] = $widget;
		$this->pageTitle = $widget['widget'];
				

    }
	
	function view(){
		if(empty($this->core['my'])){
			$this->core['my'] = $this->initGuest();
		}
		$my = $this->core['my'];
		$options = $this->setOptions($this->params['url']);
		if(isset($options['user_id'])){
			$id = $options['user_id'];
			$rs = $this->user($id);
			$unrelatedUsers = $rs['unrelatedUsers'];
			$user = $rs['user'];
			$users = $rs['users'];
			$followedByUsers = $rs['followedByUsers'];
			$thinkerBoardUsers = $rs['thinkerBoardUsers'];
			$paginationUnrelatedUsers = $rs['paginationUnrelatedUsers'];
			$paginationFollowedByUsers = $rs['paginationFollowedByUsers'];
			$paginationThinkerBoardUsers = $rs['paginationThinkerBoardUsers'];
			$connectAction = $rs['connectAction'];
			$this->set(compact('unrelatedUsers', 'user', 'users', 'followedByUsers', 'thinkerBoardUsers', 'paginationUnrelatedUsers', 'paginationFollowedByUsers', 'paginationThinkerBoardUsers', 'connectAction'));			$this->render('user');			
		}else if(isset($options['stream_id'])){
			$id = $options['stream_id'];
			$rs = $this->stream($id);
			$stream = $rs['stream'];
			$streamsUser = $rs['streamsUser'];
			$streamsUserToApprove = $rs['streamsUserToApprove'];
			$this->set(compact('stream', 'streamsUser', 'streamsUserToApprove'));
			$this->render('stream');			
		}
	}
	
	function lite(){
		if(empty($this->core['my'])){
			$this->core['my'] = $this->initGuest();
		}
		$my = $this->core['my'];
				
		$options = $this->setOptions($this->params['url']);
		if(isset($options['stream_id'])){
			$id = $options['stream_id'];
			$rs = $this->stream($id);
			$stream = $rs['stream'];
			$streamsUser = $rs['streamsUser'];
			$streamsUserToApprove = $rs['streamsUserToApprove'];
			if(!empty($streamsUser))
				$streamsCount = count($streamsUser[$options['stream_id']]);
			else
				$streamsCount = 0;
			if($streamsCount >= 100){
				$streamsCount = '100+';
			}
			$this->set(compact('stream', 'streamsUser', 'streamsUserToApprove', 'streamsCount'));
		}else{
			$this->layout = 'ajax';
		}
	}
		
	function user($id){
		if(empty($this->core['my'])){
			$this->core['my'] = $this->initGuest();
		}
		$my = $this->core['my'];
		$streamIDs = false;
		$connectAction = false;
		$paginationUnrelatedUsers = NULL; $paginationSuggestedUsers = NULL; $paginationThinkerBoardUsers = NULL;
		$unrelatedUsers = NULL;
		$thinkerBoardUsers = NULL;
		
		if($id != $my['id']){
			$this->Thinker->User->recursive = -1;
			$user = $this->Thinker->User->read(null, $id);
			if(!$this->Thinker->User->isConnected($id, $my['id'])){
				$connectAction = array('Connect' => array('id'=>'action_connect', 'link'=>'/users/connect/'.$id, 'class'=>'connectAction'));				
			}
		}else{
			$user['User'] = $my;
			$streams = array_keys($this->core['permissions'], 1) + array_keys($this->core['permissions'], 2) + array_keys($this->core['permissions'], 5);
			$streamIDs = implode(',', $streams);	
		}			
		//get related users
		$relatedUsers = $this->Thinker->User->getRelatedUsers($id, 1, $this->pageSize, $my['id']);
		$totalCount_relatedUsers = $this->Thinker->User->countRelatedUsers($id);
		//debug($totalCount_relatedUsers);
			
		//get followers
		$followedByUsers = $this->Thinker->User->getFollowedByUsers($id, 1, $this->pageSizeFollowedBy, $my['id']);
		$totalCount_followedByUsers = $this->Thinker->User->countFollowedByUsers($id);
		//debug($totalCount_requestedUsers);
						
		if($id == $my['id']){
			//get unrelated users
			$unrelatedUsers = $this->Thinker->User->getUnrelatedUsers($my['id'], 1, $this->pageSizeUnrelated);
			$totalCount_unrelatedUsers = $this->Thinker->User->countUnrelatedUsers($my['id']);
			//debug($totalCount_unrelatedUsers);

			//get suggested users
			$thinkerBoardUsers = $this->Thinker->User->getThinkerBoardUsers($my['id'], 1, $this->pageSizeThinkerBoard);
		}				
		
		//pagination logic
		$paginationColleagues = $this->Thinker->User->getUsersPagination($totalCount_relatedUsers, $this->pageSize, 'Colleagues'); 
		$paginationFollowedByUsers = $this->Thinker->User->getUsersPagination($totalCount_followedByUsers, $this->pageSizeFollowedBy, 'FollowedByUsers');
					
		if($id == $my['id']){		
			$paginationUnrelatedUsers = $this->Thinker->User->getUsersPagination($totalCount_unrelatedUsers, $this->pageSizeUnrelated, 'UnrelatedUsers');
			
			$paginationThinkerBoardUsers['total'] = count($thinkerBoardUsers);
		}
		$users['users'] = $relatedUsers;
		//debug($users);
		$users['paginate'] = $paginationColleagues;
		$users['handler'] = '/thinkers/thinkers/get_related';
		return array(
			'unrelatedUsers' => $unrelatedUsers,
			'user' 	=> $user,
			'users' => $users,
			'followedByUsers' 	=> $followedByUsers, 
			'thinkerBoardUsers' => $thinkerBoardUsers, 
			'paginationUnrelatedUsers'	=> $paginationUnrelatedUsers, 
			'paginationFollowedByUsers'	=> $paginationFollowedByUsers, 
			'paginationThinkerBoardUsers' => $paginationThinkerBoardUsers, 
			'connectAction'	=> $connectAction		
		);		
	}
	
	function stream($id){
		if(empty($this->core['my'])){
			$this->core['my'] = $this->initGuest();
		}
		$my = $this->core['my'];
		$this->Thinker->User->Stream->recursive = -1;
		$stream = $this->Thinker->User->Stream->read(null, $id);
		
		$userFilters = $this->Thinker->getParticipants($id, $my);
		//debug($userFilters);
		$usersList = $this->Thinker->User->getUsersList_server($userFilters, $my);
		//debug($usersList);
		$streamsUser = $usersList['Normal'];
		$streamsUserToApprove =  $usersList['Requested'];
		return array(
			'stream' => $stream,
			'streamsUser' => $streamsUser,
			'streamsUserToApprove' => $streamsUserToApprove
		);
	}
	
	function approveStreamsUser($id = NULL){
		if(isset($id)){
			$this->Thinker->User->StreamsUser->id = $id;
			if($this->Thinker->User->StreamsUser->saveField('permissions', 2))
				$json = true;
		}else{
			$json = false;
		}
		$this->layout = 'ajax';
		Configure::write('debug',0); //When output Json. 
		echo json_encode($json);
		exit();
	}

	function approveUser($id = null){
		$my = $this->core['my'];
		if(isset($id)){
			if($this->Thinker->User->UserRelation1->approveUserRelations($my['id'], $id))
				$json = true;
		}else{
			$json = false;
		}
		$this->layout = 'ajax';
		Configure::write('debug',0); //When output Json. 
		echo json_encode($json);
		exit();
	}

	function connectUser($id = null){
		$my = $this->core['my'];
		if(isset($id)){
			if($this->Thinker->User->UserRelation1->follow($my['id'], $id)){
				$this->Thinker->User->recursive = -1;
				$user = $this->Thinker->User->read(null, $id);
				$firstname = $this->__deriveFirstname($user['User']['fullname']);
				$myFirstname = $this->__deriveFirstname($my['fullname']);
				
				//reference at http://bakery.cakephp.org/articles/view/brief-overview-of-the-new-emailcomponent
				$this->Email->to = $user['User']['email'];
				$this->Email->subject = $my['fullname'].' is now following you on Thinkpanda';
				$this->Email->replyTo = $my['email'];
				$this->Email->from = $my['fullname'].'<'.$my['email'].'>';
				$this->Email->sendAs = 'html'; //Send as 'html', 'text' or 'both' (default is 'text') 
				//$this->Email->_debug = true;
				
				//Set the body of the mail as we send it.
				//Note: the text can be an array, each element will appear as a seperate line in the message body.
				//also we can use templates
				$this->Email->template = 'follow'; //using the template in views/elements/email/html/.ctp
				$this->set('firstname', $firstname);
				$this->set('myFirstname', $myFirstname); 
				$this->set('my', $my);

				$this->Email->send();

				$json = true;
			}
		}else{
			$json = false;
		}
		$this->layout = 'ajax';
		Configure::write('debug',0); //When output Json. 
		echo json_encode($json);
		exit();
	}

	function unconnectUser($id = null){
		$my = $this->core['my'];
		if(isset($id)){
			if($this->Thinker->User->UserRelation1->unfollow($my['id'], $id)){
				$json = true;
			}
		}else{
			$json = false;
		}
		$this->layout = 'ajax';
		Configure::write('debug',0); //When output Json. 
		echo json_encode($json);
		exit();
	}
	
	function get_related()
	{
		$options = $this->setOptions($this->params['url']);
		$json = array (
			'message'	=> 'Related user cannot be retrieved.',
			'status' 	=> 'failed',
			'timestamp' => $this->params['url']['timestamp']
		);
		$my = $this->Session->read('Auth.User');
		
		$currentPage = null;
		$pageSize = $this->pageSize;
		if (!empty($this->params['url']['paginate']))
		{
			$currentPage = $this->params['url']['paginate'];
		}
		if(empty($options['user_id'])){
			$uid = $my['id'];
		}else{
			$uid = $options['user_id'];
		}
		
		$relatedUsers = $this->Thinker->User->getRelatedUsers($uid, $currentPage, $pageSize, $my['id']);
		//debug($relatedUsers);
		
		if (!empty($relatedUsers))
		{
			$json['message'] = 'Related users retrieved';
			$json['status'] = 'success';
			
			if (!empty($this->params['url']['paginate']))
			{
				//pagination logic
				$totalCount = $this->Thinker->User->countRelatedUsers($uid);
				//debug($totalCount_relatedUsers);

				$paginate = $this->Thinker->User->getUsersPagination($totalCount, $pageSize,'RelatedUsers', $currentPage);
				$json['paginate'] = $paginate;
				//debug($json);
			}
			else
			{
				$json['relatedUsers'] = $relatedUsers;
			}
		}
		$this->layout = 'ajax';
		Configure::write('debug',0); //When output Json.
		 
		if (!empty($this->params['url']['paginate'])){
			$users['users'] = $relatedUsers;
			$users['paginate'] = $paginate;
			$users['handler'] = '/thinkers/thinkers/get_related';
			$this->set(compact('users'));
			$this->render('/elements/users/thinkers'); 		
		}else{
			echo json_encode($json);
			exit();
		}
	}
	
	function get_unrelated()
	{
		$json = array (
			'message'	=> 'Unrelated user cannot be retrieved.',
			'status' 	=> 'failed',
			'timestamp' => $this->params['url']['timestamp']
		);
		$my = $this->Session->read('Auth.User');
		
		$currentPage = null;
		$pageSize = $this->pageSizeUnrelated;
		if (!empty($this->params['url']['paginate']))
		{
			$currentPage = $this->params['url']['paginate'];
		}
		
		$unrelatedUsers = $this->Thinker->User->getUnrelatedUsers($my['id'], $currentPage, $pageSize);
		
		if (!empty($unrelatedUsers))
		{
			$json['message'] = 'Unrelated users retrieved';
			$json['status'] = 'success';
			$json['relatedEntities']['UnrelatedUsers'] = $unrelatedUsers;
			
			//pagination logic
			$totalCount = $this->Thinker->User->countUnrelatedUsers($my['id']);
			//debug($totalCount_unrelatedUsers);

			$paginate = $this->Thinker->User->getUsersPagination($totalCount, $pageSize,'UnrelatedUsers', $currentPage);

			$json['pagination'] = $paginate;
			//end pagination logic
		}
		
		$this->set(compact('unrelatedUsers', 'paginate'));
		$this->layout = 'ajax';
		$this->render('/elements/users/unrelatedUsers'); 
		Configure::write('debug',0); //When output Json. 
		//echo json_encode($json);
		//exit();
	}

	function get_followedby()
	{
		$json = array (
			'message'	=> 'Thinkers cannot be retrieved.',
			'status' 	=> 'failed',
			'timestamp' => $this->params['url']['timestamp']
		);
		$my = $this->Session->read('Auth.User');
		$options = $this->setOptions($this->params['url']);
		
		$currentPage = null;
		$pageSize = $this->pageSizeFollowedBy;
		if (!empty($this->params['url']['paginate']))
		{
			$currentPage = $this->params['url']['paginate'];
		}
		
		$followedByUsers = $this->Thinker->User->getFollowedByUsers($options['user_id'], $currentPage, $pageSize, $my['id']);
		
		if (!empty($followedByUsers))
		{
			$json['message'] = 'Thinkers retrieved';
			$json['status'] = 'success';
			$json['relatedEntities']['FollowedByUsers'] = $followedByUsers;
			
			//pagination logic
			$totalCount = $this->Thinker->User->countFollowedByUsers($options['user_id']);
			//debug($totalCount_unrelatedUsers);

			$paginate = $this->Thinker->User->getUsersPagination($totalCount, $pageSize, 'FollowedByUsers', $currentPage);

			$json['pagination'] = $paginate;
			//end pagination logic
		}
		
		$this->set(compact('followedByUsers', 'paginate'));
		$this->layout = 'ajax';
		$this->render('/elements/users/followedByUsers'); 
		Configure::write('debug',0); //When output Json. 
		//echo json_encode($json);
		//exit();
	}
	
	function get_suggested()
	{
		$json = array (
			'message'	=> 'Suggested users cannot be retrieved.',
			'status' 	=> 'failed',
			'timestamp' => $this->params['url']['timestamp']
		);
		$my = $this->Session->read('Auth.User');
		
		$currentPage = null;
		$pageSize = $this->pageSizeSuggested;
		if (!empty($this->params['url']['paginate']))
		{
			$currentPage = $this->params['url']['paginate'];
		}
		
		$suggestedUsers = $this->Thinker->User->getSuggestedUsers($my['id'], $currentPage, $pageSize);
		
		if (!empty($suggestedUsers))
		{
			$json['message'] = 'Suggested users retrieved';
			$json['status'] = 'success';
			$json['relatedEntities']['SuggestedUsers'] = $suggestedUsers;
			
			//pagination logic
			$totalCount = $this->Thinker->User->countSuggestedUsers($my['id']);
			//debug($totalCount_unrelatedUsers);

			$paginate = $this->Thinker->User->getUsersPagination($totalCount, $pageSize,'SuggestedUsers', $currentPage);

			$json['pagination'] = $paginate;
			//end pagination logic
		}
		
		$this->set(compact('suggestedUsers', 'paginate'));
		$this->layout = 'ajax';
		$this->render('/elements/users/suggestedUsers'); 
		Configure::write('debug',0); //When output Json. 
		//echo json_encode($json);
		//exit();
	}
		
	function get_requested()
	{
		$json = array (
			'message'	=> 'Requested user cannot be retrieved.',
			'status' 	=> 'failed',
			'timestamp' => $this->params['url']['timestamp']
		);
		$my = $this->Session->read('Auth.User');
		
		$currentPage = null;
		$pageSize = null;
		if (!empty($this->params['url']['paginate']))
		{
			$currentPage = $this->params['url']['paginate'];
			$pageSize = $this->pageSizeRequested;
		}
		
		$requestedUsers = $this->Thinker->User->getRequestedUsers($my['id'], $currentPage, $pageSize);
		//debug($requestedUsers);
		
		if (!empty($requestedUsers))
		{
			$json['message'] = 'Requested users retrieved';
			$json['status'] = 'success';
			$json['relatedEntities']['RequestedUsers'] = $requestedUsers;
			
			//pagination logic
			$totalCount = $this->Thinker->User->countRequestedUsers($my['id']);

			$paginate = $this->Thinker->User->getUsersPagination($totalCount, $pageSize,'RequestedUsers', $currentPage);

			$json['pagination']  = $paginate;
			//debug($paginate);
			//end pagination logic
		}
		
		$this->set(compact('requestedUsers', 'paginate'));
		$this->layout = 'ajax';
		$this->render('/elements/users/requestedUsers'); 
		Configure::write('debug',0); //When output Json. 
		//echo json_encode($json);
		//exit();
	}
	
	function get_usersToApprove()
	{
		$json = array (
			'message'	=> 'Users to approve cannot be retrieved.',
			'status' 	=> 'failed',
			'timestamp' => $this->params['url']['timestamp']
		);
		$my = $this->Session->read('Auth.User');
		
		$currentPage = null;
		$pageSize = null;
		if (!empty($this->params['url']['paginate']))
		{
			$currentPage = $this->params['url']['paginate'];
			$pageSize = $this->pageSizeApprove;
		}
		
		$usersToApprove = $this->Thinker->User->getUsersToApprove($my['id'], $currentPage, $pageSize);
		//debug($usersToApprove);
		
		if (!empty($usersToApprove))
		{
			$json['message'] = 'Users to approve retrieved';
			$json['status'] = 'success';
			$json['relatedEntities']['UsersToApprove'] = $usersToApprove;
			
			//pagination logic
			$totalCount = $this->Thinker->User->countUsersToApprove($my['id']);
			//debug($totalCount_usersToApprove);

			$paginate = $this->Thinker->User->getUsersPagination($totalCount, $pageSize,'UsersToApprove', $currentPage);

			$json['pagination'] = $paginate;
			//debug($json);
			//end pagination logic
		}
		
		$this->set(compact('usersToApprove', 'paginate'));
		$this->layout = 'ajax';
		$this->render('/elements/users/usersToApprove'); 
		Configure::write('debug',0); //When output Json. 
		//echo json_encode($json);
		//exit();
	}
	
	function getUsersToApproveCount()
	{
		$json = array (
			'message'	=> 'Users to approve count cannot be retrieved.',
			'status' 	=> 'failed',
		);
		$my = $this->Session->read('Auth.User');
		$totalCount_usersToApprove = $this->Thinker->User->countUsersToApprove($my['id']);
		//debug($totalCount_requestedUsers);
		//filter out the array structure to get to the count number
		$totalCount_usersToApprove = (int)$totalCount_usersToApprove[0][0]['COUNT(*)'];
		
		if($totalCount_usersToApprove)
		{
			$json['message'] = 'Requested user count retrieved.';
			$json['status'] = 'success';
			$json['usersToApproveCount'] = $totalCount_usersToApprove;
		}
		$this->layout = 'ajax';
		Configure::write('debug',0); //When output Json. 
		echo json_encode($json);
		exit();
	}
	
	function search($q = NULL){
		if(empty($this->core['my'])){
			$this->core['my'] = $this->initGuest();
		}
		$my = $this->core['my'];	
		if(empty($q)){
			if(isset($this->data['Search']['query'])){
				$q = $this->data['Search']['query'];
				//$this->redirect(array('action'=> 'q', $q)); 
			}else if(isset($this->params['query'])) {
				$q = $this->params['query'];
			}	
		} 
		$clean = new Sanitize();
		$q = $clean->html(trim($q));
		$users = null;
		if(isset($q)){
			$users = $this->searchUsers($q);							
		}else{
			$q = "You haven't searched for anything yet!";
		}
			   
		//debug($users);
		
		$this->set(compact('q', 'users'));
	}
	
	function searchUsers($q = NULL){
		if(empty($this->core['my'])){
			$this->core['my'] = $this->initGuest();
		}
		$my = $this->core['my'];	
		if(isset($this->params['pass'][0]))
			$q = $this->params['pass'][0];
		if($q){
			$pageSize = 20;
			if (isset($this->passedArgs['page'])) {
			 	$currentPage = $this->passedArgs['page'];
			}else if(isset($this->params['url']['paginate'])){
			 	$currentPage = $this->params['url']['paginate'];
			}else{
				$currentPage = 1;
			}
					
			$array = $this->Thinker->User->searchUsers($q, $my, $currentPage, $pageSize, false);
			$count = $this->Thinker->User->searchUsers($q, $my, $currentPage, $pageSize, true);	
			$count = $count[0][0]['count'];
			$paginate = $this->getPaginate($currentPage, $count, $pageSize);
			
			$users = array(
				'users' => $array,
				'paginate' => $paginate,
				'handler' => '/thinkers/thinkers/searchUsers/'.$q,
			);			
		}else{
			$users = array(
				'users' => null,
				'paginate' => 0,
				'handler' => null
			);	
		}

		if($this->params['isAjax'] == 1){	
			$this->layout = 'ajax';
			Configure::write('debug',0);
			$this->set(compact('users'));
			$this->render('/elements/users/browseUsers'); 	
		}
		return $users;
	}
	
	function invite()
	{
		//debug($this->params['url']);
		/*Array
        (
            [ext] => html
            [url] => thinkers/thinkers/invite
            [data] => Array
			(
				[Invite] => Array
				(
					[emails] => mcschan@gmail.com, abc@abc.com
					[message] => 
				)
				[Stream] => Array
				(
					[id] => 38
				)
			)
            [stream_id] => 38
            [pageSize] => 10
            [paginate] => 1
            [timestamp] => 1266538652367
        )*/
		
		$json = array(
			'status'	=> 'failed',
			'message'	=> ''
		);
		
		if (!empty($this->params['url']['data']['Invite']['emails']) && !empty($this->params['url']['stream_id']))
		{
			$stream = $this->Thinker->User->StreamsUser->Stream->find("first", array(
				"recursive"	=> -1,
				"conditions"=> array(
					"Stream.id"	=> $this->params['url']['stream_id']
				),
				"fields"	=> array("Stream.id", "Stream.stream", "Stream.description")
			));
			//debug($stream);
			
			define("PERMISSION_INVITED", 3);
			
			$invitesNotSent = array();
			
			$message = $this->Thinker->clean($this->params['url']['data']['Invite']['message']);
			$emails = explode(",", $this->Thinker->clean($this->params['url']['data']['Invite']['emails']));
			foreach($emails as $index => $email)
			{
				$email = trim($email);
				$username = trim(str_replace(array("@", "."), array("_", "_"), $email));
				
				$isUserRegistered = false;
				$userCreated = $this->Thinker->User->find("first", array(
					"recursive"	=> -1,
					"conditions"=> array(
						"OR"	=> array(
							"User.email"	=> $email,
							"User.username"	=> $username
						),
						"User.delete_user_id IS NULL"
					),
					"fields"	=> array("User.id", "User.username", "User.fullname", "User.email", "User.signupCode")
				));
				//debug($userCreated);
				
				if (!$userCreated)
				{
					//generate a sign-up code
					$signupCode = substr(md5($email.time().rand()), 0, 30); //this is because the column is set as varchar(30) 
					
					//create the users entry
					$params = array(
						"signupCode"		=> $signupCode,
						"email"				=> $email,
						"username"			=> $username,
						"creation_user_id"	=> $this->core['my']['id']
					);
					$this->Thinker->User->create();
					$userCreated = $this->Thinker->User->save($params, false);
				}
				else
				{ 
					if (is_null($userCreated['User']['fullname']) || empty($userCreated['User']['fullname'])) 
					{
						//this is the case where the user tries to reinvite the same user
						if (!is_null($userCreated['User']['signupCode']) && !empty($userCreated['User']['signupCode']))
							$signupCode = $userCreated['User']['signupCode'];
						else
							//generate a sign-up code
							$signupCode = substr(md5($email.time().rand()), 0, 30); //this is because the column is set as varchar(30)
					}
					else
					{
						//user tries to invite a user that is already registered
						$isUserRegistered = true;
						$fullname = $userCreated['User']['fullname'];
					}
					
					$this->Thinker->User->id = $userCreated['User']['id'];
				}
				
				if ($userCreated)
				{
					$user_id = $this->Thinker->User->id;
					
					//check if the user_relations entry existed
					$userRelationCreated = $this->Thinker->User->UserRelation1->find("first", array(
						"recursive"	=> -1,
						"conditions"=> array(
							"UserRelation1.user1_id"	=> $user_id,
							"UserRelation1.user2_id"	=> $this->core['my']['id'],
							"UserRelation1.delete_user_id IS NULL"
						)
					));
					if (!$userRelationCreated) //user_relations entry doesn't exist - create it!
					{
						$userRelationCreated = $this->Thinker->User->UserRelation1->follow($this->core['my']['id'], $user_id);
						$this->Thinker->User->UserRelation1->follow($user_id, $this->core['my']['id']);
						
						if (!$userRelationCreated)
							$invitesNotSent[$index] = $email;
					}
					
					//check if the streams_users entry existed
					$streamsUserCreated = $this->Thinker->User->StreamsUser->find("all", array(
						"recursive"	=> -1,
						"conditions"=> array(
							"StreamsUser.tagged_user_id"	=> $user_id,
							"StreamsUser.stream_id"			=> $this->params['url']['stream_id'],
							"StreamsUser.delete_user_id IS NULL"
						)
					));
					if (!$streamsUserCreated) //streams_users entry doesn't exist, create it!
					{
						$params = array(
							"tagged_user_id"	=> $user_id,
							"stream_id"			=> $this->params['url']['stream_id'],
							"user_id"			=> $this->core['my']['id'],
							"permissions"		=> PERMISSION_INVITED
						);
						$this->Thinker->User->StreamsUser->create();
						$streamsUserCreated = $this->Thinker->User->StreamsUser->save($params);
						if (!$streamsUserCreated)
							$invitesNotSent[$index] = $email;
					}
					
					//send email only if all the entries are created
					if ($userCreated && $userRelationCreated && $streamsUserCreated)
					{
						//send email to thinker to notify of the invite
						$myFirstname = $this->__deriveFirstname($this->core['my']['fullname']);
						
						//reference at http://bakery.cakephp.org/articles/view/brief-overview-of-the-new-emailcomponent
						$this->Email->to = $email;
						$this->Email->replyTo = $this->core['my']['email'];
						$this->Email->from = $this->core['my']['fullname'].'<'.$this->core['my']['email'].'>';
						$this->Email->sendAs = 'html'; //Send as 'html', 'text' or 'both' (default is 'text') 
						//$this->Email->_debug = true;
						
						//Set the body of the mail as we send it.
						//Note: the text can be an array, each element will appear as a seperate line in the message body.
						//also we can use templates
						//using the template in views/elements/email/html/.ctp
						$this->set('myId', $this->core['my']['id']);
						$this->set('stream', $stream['Stream']);
						
						if (!$isUserRegistered)
						{
							//invite to join thinkpanda + join project
							$this->Email->subject = $this->core['my']['fullname'].' invited you to join Thinkpanda and work on the "'.$stream['Stream']['stream'].'" collection together!';
							$this->Email->template = 'invite'; 
							$this->set(compact('signupCode', 'message', 'myFirstname'));
						}
						else
						{
							//invite to join project
							$firstname = $this->__deriveFirstname($fullname);
							
							$this->Email->subject = $this->core['my']['fullname'].' invited you to work on the "'.$stream['Stream']['stream'].'" collection together!';
							$this->Email->template = 'invite_project'; 
							$this->set(compact('firstname', 'myFirstname'));
						}
						
						if(!$this->Email->send())
							$invitesNotSent[$index] = $email;
						
						$this->Email->reset();
					}
				}
				else
					$invitesNotSent[$index] = $email;
			}
		}
		
		if (count($invitesNotSent) > 0)
			$json['message'] .= "Something went wrong as we invite the following thinkers: ".$this->__arrayToString($invitesNotSent).".<br/><br/>Perhaps try again?";
		else
		{
			$json['status'] = "success";
			$json['message'] = "All invites sent!";
		}
		
		$this->layout = 'ajax';
		Configure::write('debug',0);
		echo json_encode($json);
		exit();
	}
}
?>