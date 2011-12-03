<?php
class StreamsController extends AppController {

	var $name = 'Streams';
	var $helpers = array('Html', 'Ajax', 'Form', 'StrictAutocomplete', 'Javascript', 'PhpSpeedy'); 
	//var $components = array( 'RequestHandler');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('getStream');
		$user['User'] = $this->core['my'];
		$this->pageTitle = 'Collection';
		$this->set(compact('user'));		
    }

// NEW RIVER 3 DASHBOARD
	function dashboard($widget_id = NULL) {
		$clean = new Sanitize();
		$my = $this->core['my'];
		$user = $my;
		$id = $my['id'];
		$streamname = NULL;
		if(isset($this->params['streamname']))
			$streamname = $this->params['streamname'];	
					
		if(empty($my['id'])){
			if(isset($streamname)){
				$this->redirect(array('controller'=> 'streams', 'action'=>'pub', 'streamname' => $streamname));			
			}else{
				$this->Session->setFlash(__('Incorrect collection link, please check the URL you entered', true));
				$this->redirect(array('controller'=> 'hello', 'action'=>'about'));			
			}
		}else if($my['hasConfirmed'] == 0)
			$this->Session->setFlash(__('Please check your inbox to confirm your email address.', 'default', array(), 'error'));


			
		$streamsResults = $this->Stream->getByStreamname($streamname, $this->core['my']['id']);
			
		if(empty($streamsResults)){
			$this->Session->setFlash(__('The Collection was not found. Please check that you entered the correct link.', true));
			$this->redirect(array('controller'=> 'users', 'action'=>'dashboard', 'username'=>$my['username']));		
		}else{
			$stream_id = $streamsResults[0]['Stream']['id'];
			$this->pageTitle = $streamsResults[0]['Stream']['stream'];
		}
		//debug($streamsResults);
		$streamsList = $this->Stream->getStreamsList_server($streamsResults, $my);
		//debug($streamsList);
		
		$this->Stream->getStreamname($streamsResults[0]['Stream']['stream']);
		
		$streamListTypesToAvoid = array("Requested");
		if ($id != $my['id'])
		{
			$streamListTypesToAvoid[] = "Hidden";
			$streamListTypesToAvoid[] = "Closed";
			$streamListTypesToAvoid[] = "Archive";
		}
		
		$getStreamIdsAndActiveStreamIdResults = $this->Stream->__getStreamIdsAndActiveStreamId($stream_id, $streamsList, array("Default", "Invited", "Requested", "Hidden", "Closed", "Open", "Archive"), $streamListTypesToAvoid);

		$selectStreamId = $getStreamIdsAndActiveStreamIdResults['selectStreamId'];
		$streamsIds = $getStreamIdsAndActiveStreamIdResults['streamsIds']; //compiles the list of stream ids that were retrieved
		//debug($selectStreamId);
		
		$widgetsResults = $this->Stream->User->UsersWidget->__getWidgetsForUser($my['id'], $my['id']);
		$widgetsList = $widgetsResults;
		
		$selectWidgetId = $widget_id;
		//debug($widgetsList);

		$contextUser = "thinkPanda.Context.clear();thinkPanda.Context.setUser('#userBox_".$user['id']."', '.contextItem', ".$user['id'].");";
		//."thinkPanda.Widget.load();"
		$shortcuts = array(
			array(
				'id' => "notificationProfiles",
				'onclick'	=> "thinkPanda.Widget.set('#widget_button_profiles', '.widget_button', '#workspace_default', 'profiles');". $contextUser,
				'title' => $user['fullname'].'\'s Profile',
				'image' =>  '/profiles/img/icon.png'
			),
			array(
				'id' => "notificationThinkers",
				'onclick'	=> "thinkPanda.Widget.set('#widget_button_thinkers', '.widget_button', '#workspace_default', 'thinkers');". $contextUser,
				'title' => $user['fullname'].'\'s Thinkers Network',
				'image' =>  '/thinkers/img/icon.png'
			),			
			array(
				'id' => "notificationThoughts",
				'onclick'	=> "thinkPanda.Widget.set('#widget_button_thoughts', '.widget_button', '#workspace_default', 'thoughts');". $contextUser,
				'title' => $user['fullname'].'\'s Thoughts',
				'image' =>  '/thoughts/img/icon.png'
			),
		);

		// SET VARIABLES
		
		//$this->pageTitle = $this->User->user['User']['fullname'];
		//$entityOn = $this->core['entityOn'];
		$this->set(compact('streamsList', 'selectStreamId', 'widgetsList', 'selectWidgetId', 'entityOn', 'shortcuts'));
	}

	function pub($widget_id = NULL) {
		$clean = new Sanitize();
		if(empty($this->core['my'])){
			$this->core['my'] = $this->initGuest();
		}
		$my = $this->core['my'];
		$streamname = NULL;
		
		if(isset($this->params['streamname']))
			$streamname = $this->params['streamname'];	
			
		$streamsResults = $this->Stream->getByStreamname($streamname, $my['id']);
			
		if(empty($streamsResults)){
			$this->Session->setFlash(__('The Collection was not found. Please check that you entered the correct link.', true));
			$this->redirect(array('controller'=> 'hello', 'action'=>'about'));		
		}else{
			$stream_id = $streamsResults[0]['Stream']['id'];
			$this->pageTitle = $streamsResults[0]['Stream']['stream'];
		}
		//debug($streamsResults);
		$streamsList = $this->Stream->getStreamsList_server($streamsResults, $my['id']);
		//debug($streamsList);
		
		$this->Stream->getStreamname($streamsResults[0]['Stream']['stream']);
		
		$streamListTypesToAvoid = array("Requested");
		$streamListTypesToAvoid[] = "Hidden";
		$streamListTypesToAvoid[] = "Closed";
		$streamListTypesToAvoid[] = "Archive";
		
		$getStreamIdsAndActiveStreamIdResults = $this->Stream->__getStreamIdsAndActiveStreamId($stream_id, $streamsList, array("Default", "Invited", "Requested", "Hidden", "Closed", "Open", "Archive"), $streamListTypesToAvoid);

		$selectStreamId = $getStreamIdsAndActiveStreamIdResults['selectStreamId'];
		$streamsIds = $getStreamIdsAndActiveStreamIdResults['streamsIds']; //compiles the list of stream ids that were retrieved
		//debug($selectStreamId);
		
		$widgetsResults = $this->Stream->User->UsersWidget->__getWidgetsForUser($my['id'], $my['id']);
		$widgetsList = $widgetsResults;
		
		$selectWidgetId = $widget_id;
		//debug($widgetsList);
		$shortcuts = NULL;
		
		// SET VARIABLES
		
		//$this->pageTitle = $this->User->user['User']['fullname'];
		//$entityOn = $this->core['entityOn'];
		$this->set(compact('streamsList', 'selectStreamId', 'widgetsList', 'selectWidgetId', 'entityOn', 'shortcuts'));
	}
			
	// Being moved to users/discover
	function index() 
	{
		$my = $this->Session->read('Auth.User');
		
		$stats_q = $this->Stream->query('SELECT COUNT(id) AS num_streams, 
								(SELECT COUNT(id) FROM pages) AS num_pages,
								(SELECT COUNT(id) FROM tags) AS num_tags, 
								(SELECT COUNT(id) FROM users WHERE account_type_id = 3 AND hasConfirmed = 1 AND delete_user_id IS NULL) AS num_users, 
								(SELECT COUNT(id) FROM comments WHERE (parent_id > 0 AND parent_id IS NOT NULL) OR is_child = 0) AS num_comments 
								FROM streams');
		$stats = $stats_q[0][0];
		//debug($stats);
		$this->pageTitle = 'Discover';
		
		$streams = $this->Stream->getStreamsForDiscover($my['id']);
		$streamsList = $this->Stream->getStreamsList_server($streams, $my);
		//debug($streamsList);
		
		$selectStreamId = '';
		$streamIdsArray = array();
		if (array_key_exists("Open", $streamsList))
		{
			$tempStreamsIdArray = array_keys($streamsList['Open']);
			$streamIdsArray[] = implode(",", $tempStreamsIdArray);
			if (is_null($selectStreamId) || strcmp($selectStreamId, '') == 0)
				$selectStreamId = $tempStreamsIdArray[0];
		}
		$streamsIds = implode(", ", $streamIdsArray);
		
		//get the tags associated with the streams
		$tagFilters = $this->Stream->StreamsTag->getTagsForDiscover($streamsIds, $my);
		//debug($tagFilters);
		$tagsList = $this->Stream->Tag->getTagsList_server($tagFilters, $my);
		//debug($tagsList);
		
		/*$condition = array (
			'conditions' => array(
				'StreamsUser.stream_id' => $streamIdsArray, 
				'StreamsUser.delete_user_id IS NULL',
				//'StreamsUser.permissions <=' => 2,			
				'OR'=>array(
					'StreamsUser.stream_id !=' => 2,
					'StreamsUser.permissions =' => 1				
				)
			),
			'fields' => array('StreamsUser.id', 'StreamsUser.stream_id', 'StreamsUser.permissions', 'TaggedUser.id', 'TaggedUser.fullname'),
			'order' => array('StreamsUser.permissions DESC, TaggedUser.fullname DESC'),
		);*/
		$q = "SELECT StreamsUser.id, StreamsUser.stream_id, StreamsUser.permissions, User.id, User.fullname, User.picture, User.about
				FROM streams_users AS StreamsUser 
				LEFT JOIN streams AS Stream ON (StreamsUser.stream_id = Stream.id) 
				LEFT JOIN users AS User ON (StreamsUser.tagged_user_id = User.id) 
				LEFT JOIN permissions AS Permission ON (StreamsUser.permissions = Permission.id) 
				WHERE StreamsUser.stream_id IN (".$streamsIds.")
					AND StreamsUser.delete_user_id IS NULL 
					AND ((StreamsUser.stream_id != 2) OR (StreamsUser.permissions = 1)) 
				ORDER BY StreamsUser.permissions DESC, User.fullname DESC ";
		$userFilters = $this->Stream->query($q);//StreamsUser->find('all', $condition);
		//debug($userFilters);
		$usersList = $this->Stream->User->getUsersList_server($userFilters, $my);
		//debug($usersList);
		
		$selectFilterType = 'Filter_Tags';
		$selectFilterId = '';
		foreach($tagsList as $key=>$value)
		{
			if(strpos($key, $selectStreamId.'--') > -1)
			{
				$selectFilterId = $value['Tag']['id'];
				break;
			}
		}
		//debug($selectFilterId);
		
		$entityOn = $this->core['entityOn'];
		$entityOn['type'] = 'User';
		$entityOn['entityUser'] = '';
		$this->set(compact('streamsList', 'usersList', 'tagsList', 'entityOn', 'stats', 'selectStreamId', 'selectFilterType', 'selectFilterId')); //'streamsList', 
	}

	function view($id = null, $filterType = null, $filterId = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Stream.', true));
			$this->redirect(array('action'=>'index'));
		}
		
		$my = $this->Session->read('Auth.User');
		//create a new page_views entry
		/*$parameters = array(
			'stream_id' => $id,
			'user_id' => $my['id'],
			'ip' => $this->RequestHandler->getClientIP(),
		);
		$this->Stream->StreamView->create();
		$this->Stream->StreamView->save($parameters);*/
			
		$q = "SELECT StreamsUser.*, Stream.*, CreationUser.id, CreationUser.fullname, CreationUser.picture, CreationUser.about, AccessLevel.id, AccessLevel.access   
				FROM streams_users AS StreamsUser
				JOIN streams AS Stream ON StreamsUser.stream_id = Stream.id
				JOIN users AS CreationUser ON Stream.user_id = CreationUser.id 
				JOIN access_levels AS AccessLevel ON Stream.access = AccessLevel.id 
				WHERE Stream.id = ".$id." AND StreamsUser.tagged_user_id = ".$my['id']."
				ORDER BY StreamsUser.created";
		$streamsResult = $this->Stream->query($q);
		$streamsList = $this->Stream->getStreamsList_server($streamsResult, $my);
		//debug($streamsList);
		
		$q = "SELECT User.id, User.fullname, User.picture, User.about, StreamsUser.*, StreamsUser2.*
				FROM users AS User 
				JOIN streams_users AS StreamsUser ON (StreamsUser.stream_id = '$id' AND StreamsUser.tagged_user_id = User.id) 
				LEFT JOIN streams_users AS StreamsUser2 ON (StreamsUser2.stream_id = '$id' AND StreamsUser2.tagged_user_id = ".$my['id'].")
				WHERE StreamsUser.delete_user_id IS NULL AND StreamsUser2.delete_user_id IS NULL 
				ORDER BY User.fullname ASC";
		$userFilters = $this->Stream->query($q);
		//debug($userFilters);
		$usersList = $this->Stream->User->getUsersList_server($userFilters, /*$streamsResult[0]['StreamsUser'],*/ $my);
		//debug($usersList);
							
		$q = "SELECT Tag.id, Tag.tag, StreamsTag.*, StreamsUser.* 
				FROM tags AS Tag 
				JOIN streams_tags AS StreamsTag ON (StreamsTag.stream_id = '$id' AND StreamsTag.tag_id = Tag.id) 
				LEFT JOIN streams_users AS StreamsUser ON (StreamsUser.stream_id = '$id' AND StreamsUser.tagged_user_id = ".$my['id'].")
				WHERE StreamsTag.delete_user_id IS NULL AND StreamsUser.delete_user_id IS NULL
				ORDER BY Tag.tag ASC";
		$tagFilters = $this->Stream->query($q);
		$tagsList = $this->Stream->Tag->getTagsList_server($tagFilters, /*$streamsResult[0]['StreamsUser'], */$my);
		//debug($tagsList);
		
		$stream['Stream'] = $streamsResult[0]['Stream'];
		$stream['User'] = $streamsResult[0]['StreamsUser'];
		$stream['CreationUser'] = $streamsResult[0]['CreationUser'];
		unset($streamsResult[0]['CreationUser']);
				
		/*debug($streamsList);
		debug($usersList);
		debug($tagsList);*/
		
		$selectStreamId = $id;
		
		$selectFilterTypeAndId = $this->Stream->Tag->__getFilterTypeAndActiveFilterId($selectStreamId, $filterType, $filterId, $tagsList, $usersList);
		$selectFilterType = $selectFilterTypeAndId['selectFilterType'];
		$selectFilterId = $selectFilterTypeAndId['selectFilterId'];
		/*debug($selectStreamId);
		debug($selectFilterType);
		debug($selectFilterId);*/
				
		/*$_views = (int)$stream['Stream']['views'];
		$_save = $this->Stream->saveField('views', ($_views + 1));
		if (!$_save)
			$this->Session->setFlash(__('Stream view count increment failed.', true));
		
		$stream['Stream']['views'] = $_views + 1;*/
		
		$entityOn = $this->Session->read('entityOn');
		$entityOn['type'] = 'Stream';
		$entityOn['entityUser'] = '';
		
		$user = array('User' => $my);
		$this->set(compact('stream', 'streamsList', 'usersList', 'tagsList', 'entityOn', 'my', 'selectStreamId', 'selectFilterType', 'selectFilterId', 'user'));
	}

	
	
	function add() {
		$this->layout = 'ajax';
		Configure::write('debug',0);	
		if (!empty($this->data)) {	
			$my = $this->core['my'];	
			$clean = new Sanitize();
			
			// CREATE STREAM
			$_stream = $clean->html(trim($this->data['Stream']['stream']));			
			
			$data = array();
			$data['Stream']['stream'] = $_stream;
			$data['Stream']['user_id'] = $my['id'];
			$data['Stream']['parent_id'] = NULL;
			$data['Stream']['streamname'] = $this->Stream->getStreamname($_stream);
			/*if(isset($this->data['Stream']['id']))
				$data['Stream']['parent_id'] = $this->data['Stream']['id'];*/
			
			//debug($this->data['Stream']); debug($this->params);
			$this->Stream->create();
			$stream = $this->Stream->save($data);
			
			$stream['Stream']['id'] = $this->Stream->id;
			//debug($stream);
			if (!$stream || empty($stream['Stream']['id'] )) {
				$msg = "The Collection '$_stream' could not be saved.";
				$this->set(compact('msg'));
				$this->render('/elements/global/ajaxError');
			}else{
				// LINK STREAMS
				/* taken out by Matthew on Sunday Feb 7 2010 after discussing with Fahd
				if(isset($this->data['Stream']['id'])){
					$sourceStreamID = $this->data['Stream']['id'];
					$linkStreamID = $stream['Stream']['id'];
					$this->linkStreams($sourceStreamID, $linkStreamID); // Source Stream > Linked Stream
				}*/
				

				// CREATE STREAMS USER CONNECTION
				$stream['StreamsUser'] = $this->Stream->StreamsUser->add($this->Stream->id, $my['id'], $my['id']);
				// PREPARE VARIABLES FOR RENDERING
				if(!empty($stream['StreamsUser'])){
					// UPDATE ACCESS AND PERMISSIONS SESSION
					$this->__updateStreamPermissionAccessToSession('Streams.Access', $stream['Stream']['id'], 2);
					$this->__updateStreamPermissionAccessToSession('StreamsUser.Permission', $stream['Stream']['id'], 1);
				
					$selectStreamId = $stream['Stream']['id'];
					
					$msg = 'Collection "'.$stream['Stream']['stream'].'" created!';
					
					$stream['AccessLevel'] = array (
						'id' 		=> 2,
						'access'	=> 'public'
					);
					
					$streamsList = $this->Stream->getStreamsList_server(array($stream), $my);
					
					$streams = $streamsList['Active']; // RIVER3 (One "folder" to manage all Collections)
										
					$this->set(compact('streams', 'selectStreamId', 'msg'));
					$this->render('/elements/streams/renderMessageStreamTagUser'); 
				}else{
					$msg = "We had a problem creating the Collection - please try again.";
					$this->set(compact('msg'));
					$this->render('/elements/global/ajaxError');
				}
			}
		}else{
			$msg = "No data was passed.  Please try again.";
			$this->set(compact('msg'));
			$this->render('/elements/global/ajaxError');
		}	
	}
	
	function linkStreams($stream_id, $link_stream_id){
		$values = array($stream_id, $link_stream_id, '"'.date('Y-m-d H:i:s').'"', '"'.date('Y-m-d H:i:s').'"');
		$q = 'INSERT INTO streams_streams (stream_id, link_stream_id, created, modified) VALUES ('.implode(',', $values).')'
		.' ON DUPLICATE KEY UPDATE modified = "'.date('Y-m-d H:i:s').'"'; 	
		$this->Stream->query($q);	
	}

	function getStream(){
		if($this->RequestHandler->isAjax()){
			$this->layout = 'ajax';
			Configure::write('debug',0);
		}
		$options = $this->setOptions($this->params['url']);
		
		//$flag = $this->Stream->hasPermission($options['stream_id'], $this->core['my']['id'], $this->core['permissions'], $this->core['access']);		
		//if($flag['flag']){
			$streamsResults = $this->Stream->get($options['stream_id'], $this->core['my']['id']);	
			//debug($streamsResults);
			$streamsList = $this->Stream->getStreamsList_server($streamsResults, $this->core['my']['id']);
			$streamsAll = array();
			//if (array_key_exists('Active', $streamsList)) 
			foreach($streamsList AS $list){
				$streamsAll+= $list;
			}
							
			$streams	= $streamsAll;
			$filterStreams =  '\'#streamList_'.$options['stream_id'].'\'';
			$contextID = 'stream_'.$options['stream_id'];
			$selectStreamId = NULL;
			$this->set(compact('streams', 'selectStreamId', 'filterStreams', 'contextID'));
			$this->render('/elements/streams/renderStreams');
		//}
	}
	
	function getSubStreams($stream_id = NULL){
		if(isset($this->params['url']['stream_id']))
			$stream_id = $this->params['url']['stream_id'];

		$selectStreamId = NULL;
		$streamsResults = $this->Stream->getSubStreams($stream_id, $this->core['my']['id']);
		//debug($streamsResults);
		$streamsList = $this->Stream->getStreamsList_server($streamsResults, $this->core['my']['id']);		
		
		$streamsAll = array();

		//if (array_key_exists('Invited', $streamsList))
		//	$streamsAll += $streamsList['Invited'];
		if (array_key_exists('Active', $streamsList)) 
			$streamsAll += $streamsList['Active'];
		//if (array_key_exists('Requested', $streamsList)) 
		//	$streamsAll += $streamsList['Requested'];	
						
		$streams	= $streamsAll;		
		$filterStreams =  '\'#streamList_'.$stream_id.'\'';
		$contextID = 'stream_'.$stream_id;
		//$entityOn = $this->core['entityOn'];		
		//debug($this->core['entityOn']);
		$this->set(compact('streams', 'selectStreamId', 'filterStreams', 'contextID'));
		if($this->RequestHandler->isAjax()){
			$this->layout = 'ajax';
			Configure::write('debug',0);
		}
		$this->render('/elements/streams/renderStreams');
	}
		
	function getLinkedStreams($stream_id = NULL){
		if(isset($this->params['url']['stream_id']))
			$stream_id = $this->params['url']['stream_id'];

		$selectStreamId = NULL;
		$streamsResults = $this->Stream->getLinkedStreams($stream_id, $this->core['my']['id']);
		//debug($streamsResults);
		$streamsList = $this->Stream->getStreamsList_server($streamsResults, $this->core['my']['id']);		
		
		$streamsAll = array();

		//if (array_key_exists('Invited', $streamsList))
		//	$streamsAll += $streamsList['Invited'];
		if (array_key_exists('Active', $streamsList)) 
			$streamsAll += $streamsList['Active'];
		//if (array_key_exists('Requested', $streamsList)) 
		//	$streamsAll += $streamsList['Requested'];	
						
		$streams	= $streamsAll;		
		$filterStreams =  '\'#streamList_'.$stream_id.'\'';
		$contextID = 'stream_'.$stream_id;
		//$entityOn = $this->core['entityOn'];		
		//debug($this->core['entityOn']);
		$this->set(compact('streams', 'selectStreamId', 'filterStreams', 'contextID'));
		if($this->RequestHandler->isAjax()){
			$this->layout = 'ajax';
			Configure::write('debug',0);
		}
		$this->render('/elements/streams/renderStreams');
	}

	// TO DELETE: Jan 29th, 2010
	function get_filters($streams = null)
	{
		$json = array (
			'message'	=> 'Filters cannot be retrieved. Please try again.',
			'status' 	=> 'failed',
			'timestamp' => $this->params['url']['timestamp']
		);
		if($streams)
		{
			//debug($streams);
			$my = $this->Session->read('Auth.User');
			$entity = $this->Session->read('entityOn');
			$json['entity'] = $entity;

			$streamsArray = explode(',', $streams);
			
			//get tag filters
			if($entity['controller']=='pages' && $entity['action']== 'view'){
				$page_id = $entity['id'];
				$tags = $this->Stream->StreamsTag->getTagsForPages($streams, $page_id, $my);
			}else if($entity['controller']=='tags' && $entity['action']== 'view'){
				$tag_id = $entity['id'];
				/*$condition = array(
				'conditions' => array('StreamsTag.stream_id' => $streamsArray, 'StreamsTag.delete_user_id IS NULL', 'Tag.id' => $tag_id),
					 'fields' => array('StreamsTag.id', 'StreamsTag.stream_id', 'Tag.id', 'Tag.tag'),
					 'order' => array('Tag.tag DESC')
					);
				$tags = $this->Stream->StreamsTag->find('all', $condition);*/
				$tags = $this->Stream->StreamsTag->getTagsForTags($streams, $tag_id, $my);				
			}else if($entity['controller']=='comments' && $entity['action']== 'view'){
				$comment_id = $entity['id'];
				$tags = $this->Stream->StreamsTag->getTagsForComments($streams, $comment_id, $my);
			}else if($entity['controller']=='streams' && $entity['action']== 'index'){
				$tags = $this->Stream->StreamsTag->getTagsForDiscover($streams, $my);
			}else if($entity['controller']=='users' && $entity['action']== 'view'){
				$tags = $this->Stream->StreamsTag->getTagsForUsers($streams, $my);
				/*$condition = array(
					'conditions' => array('StreamsTag.stream_id' => $streamsArray, 'StreamsTag.delete_user_id IS NULL'),
					 'fields' => array('StreamsTag.id', 'StreamsTag.stream_id', 'Tag.id', 'Tag.tag'),
					 'order' => array('Tag.tag DESC')
					);
				$tags = $this->Stream->StreamsTag->find('all', $condition);*/
			}
			$tagsList = $this->Stream->StreamsTag->Tag->getTagsList($tags, $my, $entity);
			//end of get tag filters
			
			//get user filters
			$condition = array (
				'conditions' => array(
					'StreamsUser.stream_id' => $streamsArray, 
					'StreamsUser.delete_user_id IS NULL',
					//'StreamsUser.permissions <=' => 2,			
					'OR'=>array(
						'StreamsUser.stream_id !=' => 2,
						'StreamsUser.permissions =' => 1				
					)
				),
				'fields' => array('StreamsUser.id', 'StreamsUser.stream_id', 'StreamsUser.permissions', 'TaggedUser.id', 'TaggedUser.fullname'),
				'order' => array('StreamsUser.permissions DESC, TaggedUser.fullname DESC'),
			);
			$streamsUser = $this->Stream->StreamsUser->find('all', $condition);
			
			//debug($streamsUser);
			/*Array
			(
				[0] => Array
				(
					[StreamsUser] => Array
					(
						[id] => 210
						[stream_id] => 40
						[permissions] => 4
					)
					[TaggedUser] => Array
					(
						[id] => 24
						[fullname] => Blob
					)
				)
				[1] => Array (...)
			)
			*/
			
			if(!empty($streamsUser)){
				$usersList = $this->Stream->StreamsUser->User->getUsersList($streamsUser, $my);
			}
			//debug($usersList);
			//end of get user filters
			
			if(!empty($tagsList) && !empty($usersList)){
				$json['message'] = "";
				$json['status'] = 'success';				
				$json['relatedEntities'] = array (
					'Filter_Tags' => $tagsList,
					'Filter_Users' => $usersList
				);
			}
			else if (!empty($tagsList) && empty($usersList)){
				$json['message'] = ""; // We are displaying a message in the filter itself, so no need to have another message
				$json['status'] = 'success';				
				$json['relatedEntities'] = array ('Filter_Tags' => $tagsList);
				$json['error'] = 'usersList is empty.';
			}
			else if (empty($tagsList) && !empty($usersList)){
				$json['message'] = ""; // We are displaying a message in the filter itself, so no need to have another message
				$json['status'] = 'success';				
				$json['relatedEntities'] = array ('Filter_Users' => $usersList);
				$json['error'] = 'tagsList is empty.';
			}
			else{
				$json['message'] = "";
				$json['status'] = 'failed';				
				$json['relatedEntities'] = array ();
				$json['error'] = 'both usersList and tagsList are empty.';
			}
		}
		
		$this->layout = 'ajax';
		Configure::write('debug',0); //When output Json. 
		echo json_encode($json);
		exit();
	}
	
	function get_activity(){
		function activitySort($a, $b){
			//debug($a['entity']['modified'].' '.$b['entity']['modified']);
		    if ($a['entity']['modified'] == $b['entity']['modified']) {
		        return 0;
		    }
		    return ($a['entity']['modified']  < $b['entity']['modified']) ? -1 : 1;	
		}
		$my = $this->Session->read('Auth.User');
		$entityOn = $this->Session->read('entityOn');
		//$entityOn['id'] = $this->params['url']['entity_id']; //Contains information on the current view we are on
		//$entityOn['controller'] = $this->params['url']['entity_controller'];
		//$entityOn['action'] = $this->params['url']['entity_action'];

		$stream_id = $this->params['url']['stream_id']; // from GET
		$filter_type = $this->params['url']['type'];
		$filter_id = $this->params['url']['filter_id'];
		$paginatedPage = $this->params['url']['paginate'];
		$PAGESIZE = 20;
		
		if($filter_type == 'Filter_Tags'){$filter_type_id = 1;}
		else if($filter_type == 'Filter_Users'){$filter_type_id = 2;}

		//$message = 'No activities found in this tag.<br/>Be the first to add one!';
		$message = '';
		if ($filter_type == "Filter_Users") {
			if ((int)$filter_id == (int)$my['id']) {
				$json['readOnly'] = false;
				//$message = 'You have not posted anything yet.<br/>Try it now!';
				$message="";
			}
			else {
				$json['readOnly'] = true;
				//$message = 'No activities posted by this user.';
				$message="";
			}
		}
		
		$json = array (
			'message'	=> $message,
			'status' 	=> 'failed',
			'timestamp' => $this->params['url']['timestamp']
		);
		
		$flag = $this->Stream->hasPermission($stream_id, $my['id']);
		if($flag['flag']){			
			if($entityOn['controller'] == 'pages' && $entityOn['action'] == 'view' && isset($entityOn['Page']['comment_id'])){
				$comment_id = $entityOn['Page']['comment_id'];
			}else{
				$comment_id = NULL;
			}
			
			$this->Stream->StreamView->updateAll(
				array(	'StreamView.deleted' => '\''.date('Y-m-d H:i:s').'\''),
				array(	'StreamView.stream_id' => $stream_id, 
						'StreamView.entity_id' => $filter_id, 
						'StreamView.entity_type' => $filter_type_id,
						'StreamView.user_id' => $my['id'],
						'StreamView.deleted IS NULL'
						)
			);
			//create a new stream_views entry
			$parameters = array(
				'stream_id' => $stream_id,
				'entity_id' => $filter_id,
				'entity_type' => $filter_type_id,
				'user_id' => $my['id'],
				'ip' => $this->RequestHandler->getClientIP(),
			);
			$this->Stream->StreamView->create();
			$this->Stream->StreamView->save($parameters);
		
			$commentsList = array();
			$pagesList = array();
			$data = array('stream_id'=>$stream_id, 'filter_id' => $filter_id, 'filter_type' => $filter_type, 'entityOn' => $entityOn, 'paginatedPage' => $paginatedPage, 'PAGESIZE' => $PAGESIZE, 'comment_id'=>$comment_id);
			$pages = $this->Stream->PagesStream->getPagesforStream($data);
			$comments = $this->Stream->CommentsStream->getCommentsforStream($data);

			if($comments!=NULL){$commentsList = $this->Stream->CommentsStream->Comment->getCommentsList($comments, $my, false, $filter_id, $filter_type);}
			if($pages!=NULL){$pagesList = $this->Stream->PagesStream->Page->getPagesList($pages, $my, false, $filter_id, $filter_type);}
			
			//$merge = array_merge($pagesList, $commentsList); //alternative merge, overwrites duplicate index keys
			$activityArray = (array)$pagesList + (array)$commentsList;
			uasort($activityArray, 'activitySort');

			//debug($activityArray);
			
			if($activityArray!=NULL){
				//pagination logic
				$totalCount = count($activityArray);
				
				$start = $PAGESIZE * ($paginatedPage - 1);
				$end = min($start + $PAGESIZE, $totalCount);
				
				$i = 0;
				end($activityArray);
				while ($i < $start) {
					++$i;
					prev($activityArray);
				}
				
				$activity = array();
				for ($i = $start; $i < $end; ++$i) {
					$activity[] = current($activityArray);
					prev($activityArray);
				}
				$activity = array_reverse($activity, true); 
								
				$totalPages = (int)($totalCount / $PAGESIZE);
				if (($totalCount % $PAGESIZE) > 0)
					$totalPages++;
				//end pagination logic
						
				$json['status'] = 'success';
				$json['message'] = '';					
				$json['relatedEntities'] = array ('Activity' => $activity);
				$json['pagination'] = array (
					'totalPages' => $totalPages,
					'currentPage' => $paginatedPage,
					'total' => $totalCount,
					'start' => $start,
					'end' => $end,
					'pageSize' => $PAGESIZE,
					'streamId' => $stream_id,
					'filterType' => $filter_type,
					'filterId' => $filter_id,
				);
			}
		}else{
			$json['message'] = 'You do not have permission to view this Stream';							
		}		
		$this->layout = 'ajax';
		Configure::write('debug',0); //When output Json. 
		echo json_encode($json);
		exit();
	}
	
	// Moving this to comments/get
	function get_activity_date()
	{
		$my = $this->Session->read('Auth.User');
		$entityOn = $this->Session->read('entityOn');
		
		$clean = new Sanitize();
		
		$stream_id = $clean->html($this->params['url']['stream_id']); // from GET
		// TODO: Remove "filters", send tag_id, user_id etc (as a number or csv)
		if(isset($this->params['url']['type'])){
			$filter_type = $clean->html($this->params['url']['type']);
		}else{
			$filter_type = NULL;
		}	
		if(isset($this->params['url']['filter_id'])){
			$filter_id = $clean->html($this->params['url']['filter_id']);
		}else{
			$filter_id = NULL;
		}
		if(isset($this->params['url']['parent_id'])){
			$parent_id = $clean->html($this->params['url']['parent_id']);
		}else{
			$parent_id = NULL;
		}
		
		$tag_id = NULL;
		$user_id = NULL;
		if ($filter_type == 'Filter_Tags')
			$tag_id = $clean->html($this->params['url']['filter_id']);
		else if ($filter_type == 'Filter_Users')
			$user_id = $clean->html($this->params['url']['filter_id']);
		
		$paginatedPage = 1;// = $this->params['url']['date'];
		$PAGESIZE = 20;
		
		$isGetActivityByDate = array_key_exists('date', $this->params['url']);
		$activities = NULL;
		
		if($filter_type == 'Filter_Tags'){$filter_type_id = 1;}
		else if($filter_type == 'Filter_Users'){$filter_type_id = 2;}

		//$message = 'No activities found in this tag.<br/>Be the first to add one!';
		$message = '';
		$readOnly = false;
		if ($filter_type == "Filter_Users" && (int)$filter_id != (int)$my['id']) {
			$json['readOnly'] = true;
			//$message = 'You have not posted anything yet.<br/>Try it now!';
			$message="";
		}
		
		$json = array (
			'message'	=> $message,
			'status' 	=> 'failed',
			'timestamp' => $this->params['url']['timestamp']
		);
		
		$permissions = $this->Session->read('StreamsUser.Permission');
		$access = $this->Session->read('Streams.Access');
		
		$permissionResults = $this->Stream->hasPermission($stream_id, $my['id'], $permissions, $access);
		//if the permission and access info is not in the session variable, write them!
		// FIXME: Shouldn't this be taken care of in the function?!!
		if (array_key_exists('permissionToAdd', $permissionResults))
		{
			$this->__updateStreamPermissionAccessToSession('StreamsUser.Permission', $permissionResults['permissionToAdd']['stream_id'], $permissionResults['permissionToAdd']['permission'], $permissions);
			$permissions = $this->Session->read('StreamsUser.Permission');
		}
		if (array_key_exists('accessToAdd', $permissionResults))
		{
			$this->__updateStreamPermissionAccessToSession('Streams.Access', $permissionResults['accessToAdd']['stream_id'], $permissionResults['accessToAdd']['access'], $access);
			$access = $this->Session->read('Streams.Access');
		}
				
		$flag = $permissionResults['flag'];
		if($flag)
		{			
			if($entityOn['controller'] == 'pages' && $entityOn['action'] == 'view' && isset($entityOn['Page']['comment_id'])){
				$comment_id = $entityOn['Page']['comment_id'];
			}else{
				$comment_id = NULL;
			}
			
			if ($filter_id > -1) //only update stream_view if it's not favourite activity
			{
				$ip = $this->RequestHandler->getClientIP();
				$date = date('Y-m-d H:i:s');
				$this->Stream->query('INSERT INTO stream_views_history (stream_id, entity_id, entity_type, user_id, ip, created) VALUES("'.$stream_id.'", "'.$filter_id.'", "'.$filter_type_id.'", "'.$my['id'].'", "'.$ip.'", "'.$date.'" )');
						
				//create/update stream_views entry
				$parameters = array(
					'stream_id' => $stream_id,
					'entity_id' => $filter_id,
					'entity_type' => $filter_type_id,
					'user_id' => $my['id']
				);
				$this->Stream->StreamView->recursive = -1;
				$view_id= $this->Stream->StreamView->find('first', array('fields'=> array('id'), 'conditions'=> $parameters));
				if(isset($view_id['StreamView']['id']))
				{
					//$this->Stream->StreamView->id = $view_id;
					$this->Stream->query('UPDATE stream_views SET modified = "'.$date.'" WHERE id = '.$view_id['StreamView']['id'].'');		
				}
				else
				{
					//$this->Stream->StreamView->create();	
					$this->Stream->query('INSERT INTO stream_views (stream_id, entity_id, entity_type, user_id, ip, created, modified) VALUES("'.$stream_id.'", "'.$filter_id.'", "'.$filter_type_id.'", "'.$my['id'].'", "'.$ip.'", "'.$date.'", "'.$date.'")');				
				}
			}
			
			//debug($this->core);
			$options = array(
				'stream_id'		=> $stream_id, 
				'tag_id' 		=> $tag_id, // we need to send filters as themselves e.g. "tags" - remove the whole "filter" notion
				'type_id' 		=> '1,2,3,4,5,6,7,8,9,44, ', //$this->core['widget']['types'], // csv // We want certain types back 
				'user_id'		=> $user_id,
				'comment_id'	=> $comment_id,
				'parent_id'		=> $parent_id, // for Replies
				'widget_id'		=> NULL, //for Thought widget => Actually for thoughts you want ALL hence leave NULL
				'search_terms' 	=> NULL,
				'date'			=> NULL,
				'start_date' 	=> NULL,
				'end_date' 		=> NULL,
				'paginatedPage' => $paginatedPage,
				'limit'			=> $PAGESIZE
			);
			if (!$isGetActivityByDate) //this is the initial fetch of the activity feed
			{
				$options['pagination'] = true;		
				//$datePagination = $this->__getCountByDate($data); //need to get the dates where activity entries exist
				$datePagination = $this->Stream->Comment->getPagination($this->core, $options);
				//debug($datePagination);
			}
			else
				$options['date'] = $clean->html($this->params['url']['date']);
				//$data['date'] = '2009-07-24';
					
			//$activities = $this->Stream->CommentsStream->__getThoughtsforStream($data);]
			$thoughts = $this->Stream->Comment->get($this->core, $options);
			//debug($activities);
			$activityArray = $thoughts;//$this->Stream->__getActivitiesList($activities, $my, false, $filter_id, $filter_type, $permissions);
			
			if($activityArray != NULL)
			{
				$activities = $activityArray;
				//debug($activity);
				
				if(!$isGetActivityByDate) //initial fetch of activity feed
				{
					/*debug($activity);
					debug($filter_type);
					debug($datePagination);*/
					
					$this->set(compact('thoughts', 'datePagination', 'filter_type', 'readOnly'));
					$this->layout = 'ajax';
					$this->render('/activities/activity_pagination'); 
				}
				else
				{					
					$this->set(compact('message','thoughts', 'filter_type', 'readOnly'));
					$this->layout = 'ajax';
					$this->render('/activities/activity'); 
				}
			}
			else
			{
				$message = 'No activities found.';
				$this->set(compact('message', 'filter_type', 'thoughts'));
				$this->layout = 'ajax';
				$this->render('/activities/activity');
			}
		}
		else
		{
			$message = 'You do not have permission to view this Stream';
			$this->set(compact('message', 'filter_type', 'activities'));
			$this->layout = 'ajax';
			$this->render('/activities/activity'); 					
		}
	}
	
	function get_activity_date_old()
	{
		function activitySort($a, $b)
		{
			//debug($a['entity']['modified'].' '.$b['entity']['modified']);
		    if ($a['entity']['modified'] == $b['entity']['modified']) {
		        return 0;
		    }
		    return ($a['entity']['modified']  < $b['entity']['modified']) ? -1 : 1;	
		}
		
		$my = $this->Session->read('Auth.User');
		$entityOn = $this->Session->read('entityOn');
		
		$stream_id = $this->params['url']['stream_id']; // from GET
		$filter_type = $this->params['url']['type'];		
		$filter_id = $this->params['url']['filter_id'];
		$paginatedPage = 1;// = $this->params['url']['date'];
		$PAGESIZE = 20;
		
		$isGetActivityByDate = array_key_exists('date', $this->params['url']);
		$activities = NULL;
		
		if($filter_type == 'Filter_Tags'){$filter_type_id = 1;}
		else if($filter_type == 'Filter_Users'){$filter_type_id = 2;}

		//$message = 'No activities found in this tag.<br/>Be the first to add one!';
		$message = '';
		$readOnly = false;
		if ($filter_type == "Filter_Users" && (int)$filter_id != (int)$my['id']) {
			$json['readOnly'] = true;
			//$message = 'You have not posted anything yet.<br/>Try it now!';
			$message="";
		}
		
		$json = array (
			'message'	=> $message,
			'status' 	=> 'failed',
			'timestamp' => $this->params['url']['timestamp']
		);
		
		$permissions = $this->Session->read('StreamsUser.Permission');
		$access = $this->Session->read('Streams.Access');
		
		$permissionResults = $this->Stream->hasPermission($stream_id, $my['id'], $permissions, $access);
		//if the permission and access info is not in the session variable, write them!
		// FIXME: Shouldn't this be taken care of in the function?!!
		if (array_key_exists('permissionToAdd', $permissionResults))
		{
			$this->__updateStreamPermissionAccessToSession('StreamsUser.Permission', $permissionResults['permissionToAdd']['stream_id'], $permissionResults['permissionToAdd']['permission'], $permissions);
			$permissions = $this->Session->read('StreamsUser.Permission');
		}
		if (array_key_exists('accessToAdd', $permissionResults))
		{
			$this->__updateStreamPermissionAccessToSession('Streams.Access', $permissionResults['accessToAdd']['stream_id'], $permissionResults['accessToAdd']['access'], $access);
			$access = $this->Session->read('Streams.Access');
		}
				
		$flag = $permissionResults['flag'];
		if($flag)
		{			
			if($entityOn['controller'] == 'pages' && $entityOn['action'] == 'view' && isset($entityOn['Page']['comment_id'])){
				$comment_id = $entityOn['Page']['comment_id'];
			}else{
				$comment_id = NULL;
			}
			
			if ($filter_id > -1) //only update stream_view if it's not favourite activity
			{
				/*$this->Stream->StreamView->updateAll(
					array(	'StreamView.deleted' => '\''.date('Y-m-d H:i:s').'\''),
					array(	'StreamView.stream_id' => $stream_id, 
							'StreamView.entity_id' => $filter_id, 
							'StreamView.entity_type' => $filter_type_id,
							'StreamView.user_id' => $my['id'],
							'StreamView.deleted IS NULL'
							)
				);*/
				$ip = $this->RequestHandler->getClientIP();
				$date = date('Y-m-d H:i:s');
				$this->Stream->query('INSERT INTO stream_views_history (stream_id, entity_id, entity_type, user_id, ip, created) VALUES("'.$stream_id.'", "'.$filter_id.'", "'.$filter_type_id.'", "'.$my['id'].'", "'.$ip.'", "'.$date.'" )');
				
		
				//create/update stream_views entry
				
				$parameters = array(
					'stream_id' => $stream_id,
					'entity_id' => $filter_id,
					'entity_type' => $filter_type_id,
					'user_id' => $my['id']
				);
				//$view_id= $this->Stream->query('SELECT StreamView.id FROM stream_views AS StreamView WHERE stream_id = '.$stream_id.' AND entity_id = '.$filter_id.' AND entity_type = '.$filter_type_id.' AND user_id = '.$my['id'].'');
				$this->Stream->StreamView->recursive = -1;
				$view_id= $this->Stream->StreamView->find('first', array('fields'=> array('id'), 'conditions'=> $parameters));
				if(isset($view_id['StreamView']['id'])){
					//$this->Stream->StreamView->id = $view_id;
					$this->Stream->query('UPDATE stream_views SET modified = "'.$date.'" WHERE id = '.$view_id['StreamView']['id'].'');		
				}else{
					//$this->Stream->StreamView->create();	
					$this->Stream->query('INSERT INTO stream_views (stream_id, entity_id, entity_type, user_id, ip, created, modified) VALUES("'.$stream_id.'", "'.$filter_id.'", "'.$filter_type_id.'", "'.$my['id'].'", "'.$ip.'", "'.$date.'", "'.$date.'")');				
				}
				//$this->Stream->StreamView->recursive = -1;	
				//$this->Stream->StreamView->save($parameters);
			}
		
			$commentsList = array();
			$pagesList = array();
			$data = array(
				'stream_id'=>$stream_id, 
				'filter_id' => $filter_id, 
				'filter_type' => $filter_type, 
				'entityOn' => $entityOn, 
				'comment_id'=>$comment_id,
				'my_id' => $my['id'],
			);
						
			if (!$isGetActivityByDate) //this is the initial fetch of the activity feed
			{
				$data['limit'] = $PAGESIZE;
				$datePagination = $this->__getCountByDate($data); //need to get the dates where activity entries exist
			}
			else
				$data['date'] = $this->params['url']['date'];
				//$data['date'] = '2009-07-24';
		
			$activities = $this->Stream->CommentsStream->__getThoughtsforStream($data);
			//debug($activities);
			$activityArray = $this->Stream->__getActivitiesList($activities, $my, false, $filter_id, $filter_type, $permissions);
			/*
			//debug($data);
			//get the activities for the current date
			$pages = $this->Stream->PagesStream->getPagesforStream($data);
			//debug($pages);
			$comments = $this->Stream->CommentsStream->getCommentsforStream($data);
			//debug($comments);

			if($comments)
				$commentsList = $this->Stream->CommentsStream->Comment->getCommentsList($comments, $my, false, $filter_id, $filter_type, $permissions);
			if($pages)
				$pagesList = $this->Stream->PagesStream->Page->getPagesList($pages, $my, false, $filter_id, $filter_type, $permissions);
			
			//$merge = array_merge($pagesList, $commentsList); //alternative merge, overwrites duplicate index keys
			$activityArray = (array)$pagesList + (array)$commentsList;
			uasort($activityArray, 'activitySort');
			*/

			//debug($activityArray);
			
			if($activityArray != NULL)
			{
				$activities = $activityArray;
				//debug($activity);
				
				if(!$isGetActivityByDate) //initial fetch of activity feed
				{
					//pagination logic
					/*$totalCount = count($activityArray);
					
					$start = 0;//$PAGESIZE * ($paginatedPage - 1);
					$end = min($start + $PAGESIZE, $totalCount);
					
					$i = 0;
					end($activityArray);
					while ($i < $start) {
						++$i;
						prev($activityArray);
					}
					
					$activity = array();
					for ($i = $start; $i < $end; ++$i) {
						$activity[] = current($activityArray);
						prev($activityArray);
					}
					//$activities = array_reverse($activity, true); 
					$activities = $activity;
									
					/*$totalPages = (int)($totalCount / $PAGESIZE);
					if (($totalCount % $PAGESIZE) > 0)
						$totalPages++;*/
					//end pagination logic
					
					/*debug($activity);
					debug($filter_type);
					debug($datePagination);*/
					
					$this->set(compact('activities', 'datePagination', 'filter_type', 'readOnly'));
					$this->layout = 'ajax';
					$this->render('/activities/activity_pagination'); 
				}
				else
				{					
					$this->set(compact('message','activities', 'filter_type', 'readOnly'));
					$this->layout = 'ajax';
					$this->render('/activities/activity'); 
				}
										
				/*$json['status'] = 'success';
				$json['message'] = '';					
				$json['relatedEntities'] = array ('Activity' => $activity);*/
				/*$json['pagination'] = array (
					'totalPages' => $totalPages,
					'currentPage' => $paginatedPage,
					'total' => $totalCount,
					'start' => $start,
					'end' => $end,
					'pageSize' => $PAGESIZE,
					'streamId' => $stream_id,
					'filterType' => $filter_type,
					'filterId' => $filter_id,
				);*/
			}
			else
			{
				$message = 'No activities found.';
				$this->set(compact('message', 'filter_type', 'activities'));
				$this->layout = 'ajax';
				$this->render('/activities/activity');
			}
		}
		else
		{
			//$json['message'] = 'You do not have permission to view this Stream';	
			$message = 'You do not have permission to view this Stream';
			$this->set(compact('message', 'filter_type', 'activities'));
			$this->layout = 'ajax';
			$this->render('/activities/activity'); 					
		}		
		/*$this->layout = 'ajax';
		Configure::write('debug',0); //When output Json. 
		echo json_encode($json);
		exit();*/
	}
	
	// **********************
	// TODO: This is being moved to comment.php. 
	// DELETE THIS 
	//
	function __getCountByDate($data)
	{
		/*function countSort($a, $b)
		{
			//debug($a);
			//debug($b);
			$aKey = 'DATE(CommentsStream.modified)';
			$bKey = 'DATE(CommentsStream.modified)';
			if (array_key_exists('DATE(PagesStream.modified)', $a[0]) && array_key_exists('DATE(CommentsStream.modified)', $b[0]))
			{
				$aKey = 'DATE(PagesStream.modified)';
				$bKey = 'DATE(CommentsStream.modified)';
			}
			else if (array_key_exists('DATE(CommentsStream.modified)', $a[0]) && array_key_exists('DATE(PagesStream.modified)', $b[0]))
			{
				$aKey = 'DATE(CommentsStream.modified)';
				$bKey = 'DATE(PagesStream.modified)';
			}
			else if (array_key_exists('DATE(PagesStream.modified)', $a[0]) && array_key_exists('DATE(PagesStream.modified)', $b[0]))
			{
				$aKey = 'DATE(PagesStream.modified)';
				$bKey = 'DATE(PagesStream.modified)';
			}
		    if (($a[0][$aKey] == $b[0][$bKey]))
			{
				return 0;
		    }
		    return ($a[0][$aKey]  < $b[0][$bKey]) ? -1 : 1;	
		}*/
		
		/*$commentsCountArray = $this->Stream->CommentsStream->getCommentsforStreamCountByDate($data);
		//debug($commentsCountArray);
		$pagesCountArray = $this->Stream->PagesStream->getPagesforStreamCountByDate($data);
		//debug($pagesCountArray);
		
		$countArray = array();
		if ($commentsCountArray && $pagesCountArray)
		{
			$countArray = array_merge((array)$pagesCountArray, (array)$commentsCountArray);
			uasort($countArray, 'countSort');
		}
		else if ($commentsCountArray)
			$countArray = (array)$commentsCountArray;
		else if ($pagesCountArray)
			$countArray = (array)$pagesCountArray;*/
		
		$countArray = $this->Stream->CommentsStream->__getDatePaginationData($data);
		//debug($countArray);
					
		$datePagination = array();
		$j = 0;
		reset($countArray);
		for ($i = 0; $i < count($countArray); ++$i)
		{
			$currentArray = reset(current($countArray));
			//debug($currentArray);
			$date = reset($currentArray); //gets the date like '2009-07-28'
			$year = next($currentArray);
			$month = next($currentArray);
			$day = next($currentArray);
			$count = next($currentArray);
			if (!array_key_exists($year, $datePagination))
			{
				$datePagination[$year] = array
				(
					'count' => $count,
					'months' => array
					(
						$month => array
						(
							'count' => $count,
							'days' => array
							(
								$day => array (
									'count' => $count,
									'stream_id' => $data['stream_id'],
									'filter_id' => $data['filter_id'],
									'filter_type' => $data['filter_type'],
									'date'	=> $date								
								)
							)
						)
					)
				 );
			}
			else if (!array_key_exists($month, $datePagination[$year]['months']))
			{
				//$count = (int)end(current($countArray));
				$datePagination[$year]['count'] += $count;
				$datePagination[$year]['months'][$month] = array(
					'count' => $count,
					'days' => array (
						$day => array(
							'count' => $count,
							'stream_id' => $data['stream_id'],
							'filter_id' => $data['filter_id'],
							'filter_type' => $data['filter_type'],
							'date'	=> $date
						)
					)
				);
			}
			else if (!array_key_exists($day, $datePagination[$year]['months'][$month]['days']))
			{
				//$count = (int)end(current($countArray));
				$datePagination[$year]['count'] += $count;
				$datePagination[$year]['months'][$month]['count'] += $count;
				$datePagination[$year]['months'][$month]['days'][$day] = array(
					'count' => $count,
					'stream_id' => $data['stream_id'],
					'filter_id' => $data['filter_id'],
					'filter_type' => $data['filter_type'],
					'date'	=> $date				
				);
			}
			else
			{
				$datePagination[$year]['count'] += $count;
				$datePagination[$year]['months'][$month]['count'] += $count;
				$datePagination[$year]['months'][$month]['days'][$day]['count'] += $count;
			}
			//debug($datePagination);
			next($countArray);
		}
		//debug($dates);	
		return $datePagination;
	}


	function render_stream()
	{
		//debug($this->data);
		if (!empty($this->data)) 
		{
			$my = $this->Session->read('Auth.User');
			$entityOn = $this->Session->read('entityOn');
			
			$selectStreamId = $this->data['Stream']['id'];
			
			$streamsList = $this->Stream->getStreamsList_server(array($this->data), $my);
			$streams = $streamsList['Closed'];
			
			$this->set(compact('streams', 'entityOn', 'my', 'selectStreamId'));
			$this->layout = 'ajax';
			$this->render('/elements/streams/renderStreams'); 
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Stream', true));
			$this->redirect(array('action'=>'index'));
		}
		
		$my = $this->Session->read('Auth.User');
		

		//debug($isAllow);
		$isAdmin = $this->Stream->isAdmin($id, $my['id']);
		if (!$isAdmin['flag'])
		{
			$this->Session->setFlash(__('You need to be an administrator of the collection to modify its settings.', true));
			$this->redirect(array('controller' => 'users', 'action'=>'view', $my['id']));
		}
		
		if (!empty($this->data)) {
			$saveStream = $this->Stream->save($this->data);
			$saveStreamsUsers = true;
			$streamsUsers = $this->Stream->StreamsUser->find('list', array(
				'conditions' => array(
					'StreamsUser.stream_id' => $id,
					'StreamsUser.permissions <=' => 4,
					'StreamsUser.delete_user_id IS NULL',
				),
				'fields' => array('StreamsUser.id', 'StreamsUser.permissions')
			));
			foreach ($this->data['StreamsUser'] as $key => $newPermission)
			{
				$streamsUserId = substr($key, strpos($key, '_') + 1);
				$this->Stream->StreamsUser->id = $streamsUserId;	
				$saveStreamsUser = true;	
				if (strpos($key, 'revokeInvite_') > -1)
				{
					if ($newPermission == 1)
						$saveStreamsUser = $this->Stream->StreamsUser->saveField('delete_user_id', $my['id']);
				}
				else
				{
					if($streamsUsers[$streamsUserId] != $newPermission)
						$saveStreamsUser = $this->Stream->StreamsUser->saveField('permissions', $newPermission, false);
				}
				if (!$saveStreamsUser || !$saveStreamsUsers)
					$saveStreamsUsers = false;				
			}
			if ($saveStream && $saveStreamsUsers) {
				$this->Session->setFlash(__('The Collection setting has been saved', true));
				$this->redirect(array('controller' => 'users', 'action'=>'view', $my['id']));
			} else {
				$this->Session->setFlash(__('The Collection settings could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->Stream->recursive = 1;
			$this->data = $this->Stream->read(null, $id);
		}
		//debug($this->data);
		$accessLevels = $this->Stream->AccessLevel->find('list', array(
			'conditions'=>array('AccessLevel.id !='=> 4), // "default" streams that will be preloaded for ALL users (e.g. Thinkpanda stream)
			'fields'=>array('AccessLevel.access'),
		));
		
		//user permission management
		$permissions = $this->Stream->StreamsUser->Permission->find('list', array(
			'conditions' => array('Permission.id <=' => 4),
			'fields' => array('Permission.permission')
		));
		unset($permissions[3]);
				
		$memberPermissions = $permissions;
		unset($memberPermissions[4]);
		
		//debug($permissions);
		//debug($memberPermissions);
		
		// INEFFICIENT: This is really inefficient code, convert to SQL
		$streamsUsers = $this->Stream->StreamsUser->find('all', array(
			'conditions' => array(
				'StreamsUser.stream_id' => $id,
				'StreamsUser.permissions <=' => 4,
				'StreamsUser.delete_user_id IS NULL',
			),
			'fields' => array('StreamsUser.id', 'StreamsUser.tagged_user_id', 'StreamsUser.permissions', 'TaggedUser.fullname'),
			'order' => array('StreamsUser.permissions'),
		));
		//debug($streamsUsers);
		
		$this->set(compact('accessLevels', 'permissions', 'memberPermissions', 'streamsUsers'));
	}
	
	function rating()
	{
		if (!empty($this->params['form'])) 
		{
			//debug($this->params['form']);
			/*
				$this->params['form']:
				Array
				(
					[entity_id] => 5
					[user_id] => 16
					[rating] => 1 or [rating] => -1
					[entity_user_id] => 186
				)
			*/
			$stream = $this->Stream->findbyId($this->params['form']['entity_id']);
			//debug($stream);
			if (!empty($stream))
			{
				$goodRating = (int)$stream['Stream']['good_rating'];
				$badRating = (int)$stream['Stream']['bad_rating'];
				$this->Stream->id = $this->params['form']['entity_id'];
				if ($this->params['form']['rating'] == 1) //positive rating, +1 to good_ratng in tags
				{
					$goodRating++;
					//$json['message'] = 'tag good rating = '.$goodRating;
					$saveStreamSuccess = $this->Stream->saveField('good_rating', $goodRating);
				}
				else //negative rating, +1 to bad_ratng in tags
				{
					$badRating++;
					//$json['message'] = 'tag bad rating = '.$badRating;
					$saveStreamSuccess = $this->Stream->saveField('bad_rating', $badRating);
				}
				if ($saveStreamSuccess)
				{
					//todo: write to log file to indicate the current user made the rating
					//this replaces the streams_ratings table
					
					$msg = 'The collection rating has been saved.';
					
					if ($this->params['form']['entity_user_id'] == '') //StreamsUser relationship not established
					{
						//this is the case where the user is browsing an open stream
						$parameters = array (
							'user_id' => $this->params['form']['user_id'],
							'tagged_user_id' => $this->params['form']['user_id'],
							'stream_id' => $this->params['form']['entity_id'],
							'permissions' => 2, //make the user a member of the stream
						);
						
						$this->Stream->StreamsUser->create();
						if ($this->Stream->StreamsUser->save($parameters)) 
						{
							$msg .= '  You are now a member of the collection as well!';
							$this->__updateStreamPermissionAccessToSession('Streams.Permission', $this->params['form']['entity_id'], 2);
						}
					}
					
					$entity = "stream";
					
					Configure::write('debug',0);
					$this->set(compact('msg', 'goodRating', 'badRating', 'entity'));
					$this->layout = 'ajax';
					$this->render('/elements/global/rating');
				}
				else
				{
					$msg = "Stream rating cannot be saved at this time.  Please try again.";
					Configure::write('debug',0);
					$this->set(compact('msg'));
					$this->layout = 'ajax';
					$this->render('/elements/global/ajaxError');
				}
			}
			else
			{
				$msg = "Stream not found. Please try again later.";
				Configure::write('debug',0);
				$this->set(compact('msg'));
				$this->layout = 'ajax';
				$this->render('/elements/global/ajaxError');
			}
		}
		else
		{
			$msg = "No data...";
			Configure::write('debug',0);
			$this->set(compact('msg'));
			$this->layout = 'ajax';
			$this->render('/elements/global/ajaxError');
		}
	}
	
	function export()
	{
		if (!empty($this->data)) 
		{
			//debug($this->data);
			/*Array
			(
				[exportFormat] => html //or xml, rss
				[stream_id] => 38
				[filter_type] => Tag
				[filter_id] => 592
				[start_date] => '2009-10-18'
				[end_date] => '2009-10-18'
			)*/
			
			$my = $this->Session->read('Auth.User');
			$entityOn = $this->Session->read('entityOn');
			
			$stream_id = $this->data['stream_id']; // from GET
			$filter_type = 'Filter_'.$this->data['filter_type'].'s';
			$filter_id = $this->data['filter_id'];
			
			$isGetActivityByDate = false;
			$start_date = '';
			$end_date = '';
			if (!empty($this->data['start_date']))
			{
				$isGetActivityByDate = true;
				$start_date = $this->data['start_date'];
				
				$end_date = $start_date;
				if (!empty($this->data['end_date']))
					$end_date = $this->data['end_date'];
			}
			
			$permissions = $this->Session->read('StreamsUser.Permission');
			$access = $this->Session->read('Streams.Access');
			
			$permissionResults = $this->Stream->hasPermission($stream_id, $my['id'], $permissions, $access);
			//if the permission and access info is not in the session variable, write them!
			if (array_key_exists('permissionToAdd', $permissionResults))
			{
				$this->__updateStreamPermissionAccessToSession('StreamsUser.Permission', $permissionResults['permissionToAdd']['stream_id'], $permissionResults['permissionToAdd']['permission'], $permissions);
				$permissions = $this->Session->read('StreamsUser.Permission');
			}
			if (array_key_exists('accessToAdd', $permissionResults))
			{
				$this->__updateStreamPermissionAccessToSession('Streams.Access', $permissionResults['accessToAdd']['stream_id'], $permissionResults['accessToAdd']['access'], $access);
				$access = $this->Session->read('Streams.Access');
			}
					
			$flag = $permissionResults['flag'];
			if($flag)
			{			
				if($entityOn['controller'] == 'pages' && $entityOn['action'] == 'view' && isset($entityOn['Page']['comment_id'])){
					$comment_id = $entityOn['Page']['comment_id'];
				}else{
					$comment_id = NULL;
				}
				
				$commentsList = array();
				$pagesList = array();
				$data = array(
					'stream_id'=>$stream_id, 
					'filter_id' => $filter_id, 
					'filter_type' => $filter_type, 
					'entityOn' => $entityOn, 
					'comment_id'=>$comment_id,
					'my_id' => $my['id'],
					'start_date' => $start_date,
					'end_date' => $end_date,
				);
				
				//get favourites entries
				$activities = $this->Stream->CommentsStream->__getThoughtsforStream($data);
				//debug($activities);
				//$activityArray = $this->Stream->__getActivitiesList($activities, $my, false, $filter_id, $filter_type, $permissions);
				//debug($activityArray);
				
				$i = 0;
				foreach ($activities as $activity)
				{
					$activities[$i]['replies'] = array();
					if ($activity['CommentsTag']['comments_count'] > 0)
					{
						$data['comment_id'] = $activity['Comment']['id'];
						
						//get the replies
						$activities[$i]['replies'] = $this->Stream->CommentsStream->getCommentsforStream($data);
					}
					++$i;
				}
				//debug($activities);

				if(!is_null($activities))
				{
					$stream = $this->Stream->findById($stream_id);
					if ($filter_type = 'Filter_Tags')
					{
						$filter = $this->Stream->Tag->findById($filter_id);
						$filter['Filter']['filter'] = $filter['Tag']['tag'];
						unset($filter['Tag']);
					}
					else
					{
						$filter = $this->Stream->User->findById($filter_id);
						$filter['Filter']['filter'] = $filter['User']['fullname'];
						unset($filter['User']);
					}
					
					$fileName = "export_".$stream_id."_".$filter_type."_".$filter_id;
										
					if ($this->data['exportFormat'] == 'html')
						$content = $this->Stream->__renderActivitiesToHTML($stream, $filter, $activities);
					else if ($this->data['exportFormat'] == 'xml')
						$content = $this->Stream->__renderActivitiesToXML($stream, $this->data['filter_type'], $filter, $activities);
					else if ($this->data['exportFormat'] == 'csv' || $this->data['exportFormat'] == 'txt')
						$content = $this->Stream->__renderActivitiesToCsvOrTxt($stream, $this->data['filter_type'], $filter, $activities, $this->data['exportFormat']);
					
					$createFile = $this->Stream->Page->__createFile('files'.DS.'export'.DS.$this->data['exportFormat'].DS.$fileName.'.'.$this->data['exportFormat'], $content);
					$this->view = 'Media';
					$params = array(
						'id' => $fileName.".".$this->data['exportFormat'],
						'name' => "ThinkpandaExport",
						'download' => true,
						'extension' => $this->data['exportFormat'],
						'path' => 'webroot'.DS.'files'.DS.'export'.DS.$this->data['exportFormat'].DS
					);
					//debug($params);
					$this->set($params);	
				}
				else
				{
					$msg = 'There was no activity to export!';
					$this->set(compact('msg'));
					$this->layout = 'ajax';
					$this->render('/elements/global/ajaxError');
				}
			}
			else
			{
				//$json['message'] = 'You do not have permission to view this Stream';	
				$msg = 'You do not have permission to export from this Stream';
				$this->set(compact('msg'));
				$this->layout = 'ajax';
				$this->render('/elements/global/ajaxError');
			}
		}
		else
		{
			$msg = "No data...";
			Configure::write('debug',0);
			$this->set(compact('msg'));
			$this->layout = 'ajax';
			$this->render('/elements/global/ajaxError');
		}
	}
	
	function deleteExportFile()
	{
		if (!empty($this->data)) 
		{
			//debug($this->data);
			/*Array
			(
				[exportFormat] => html //or xml, rss
				[stream_id] => 38
				[filter_type] => Tag
				[filter_id] => 592
				[start_date] => '2009-10-18'
				[end_date] => '2009-10-18'
			)*/
			
			$stream_id = $this->data['stream_id']; // from GET
			$filter_type = 'Filter_'.$this->data['filter_type'].'s';
			$filter_id = $this->data['filter_id'];
			
			$fileName = "export_".$stream_id."_".$filter_type."_".$filter_id.".".$this->data['exportFormat'];
			$dir = 'files'.DS.'export'.DS.$this->data['exportFormat'].DS;
			
			$isDeleteSuccessful = unlink($dir.$fileName);
			
			if ($isDeleteSuccessful)
			{	
				$msg = 'File deleted successfully';
				$this->set(compact('msg'));
				$this->layout = 'ajax';
				$this->render('/elements/global/ajaxError');
			}
			else
			{
				$msg = 'File was not deleted';
				$this->set(compact('msg'));
				$this->layout = 'ajax';
				$this->render('/elements/global/ajaxError');
			}
		}
		else
		{
			$msg = "No data...";
			$this->set(compact('msg'));
			$this->layout = 'ajax';
			$this->render('/elements/global/ajaxError');
		}
	}
	
	function searchThoughts()
	{
		//debug($this->data);
		/*	Array
			(
				[Search] => Array
				(
					[terms] => sjdklfjsdklf
				)
				
				[Stream] => Array
				(
					[stream_id] => 38
				)
			
				[Filter] => Array
				(
					[type] => Filter_Tags
					[id] => 592
				)
			)*/
		
		$my = $this->Session->read('Auth.User');
		$entityOn = $this->Session->read('entityOn');
		
		$stream_id = $this->data['Stream']['id']; // from GET
		$filter_type = $this->data['Filter']['type'];
		$filter_id = $this->data['Filter']['id'];
		
		$clean = new Sanitize();
		$searchTerms = $clean->html(trim($this->data['Search']['terms']));
		
		$activities = NULL;
		$message = '';
		$readOnly = false;
		$isSearch = true;
		
		$permissions = $this->Session->read('StreamsUser.Permission');
		$access = $this->Session->read('Streams.Access');
		
		$permissionResults = $this->Stream->hasPermission($stream_id, $my['id'], $permissions, $access);
		//if the permission and access info is not in the session variable, write them!
		if (array_key_exists('permissionToAdd', $permissionResults))
		{
			$this->__updateStreamPermissionAccessToSession('StreamsUser.Permission', $permissionResults['permissionToAdd']['stream_id'], $permissionResults['permissionToAdd']['permission'], $permissions);
			$permissions = $this->Session->read('StreamsUser.Permission');
		}
		if (array_key_exists('accessToAdd', $permissionResults))
		{
			$this->__updateStreamPermissionAccessToSession('Streams.Access', $permissionResults['accessToAdd']['stream_id'], $permissionResults['accessToAdd']['access'], $access);
			$access = $this->Session->read('Streams.Access');
		}
				
		$flag = $permissionResults['flag'];
		
		if($flag)
		{			
			$comment_id = NULL;
		
			$commentsList = array();
			$pagesList = array();
			$data = array(
				'stream_id'		=> $stream_id, 
				'filter_id' 	=> $filter_id, 
				'filter_type' 	=> $filter_type, 
				'entityOn' 		=> $entityOn, 
				'comment_id'	=> $comment_id,
				'my_id' 		=> $my['id'],
				'search_terms' 	=> $searchTerms
			);
			
			//get favourites entries
			$activities = $this->Stream->CommentsStream->__getThoughtsforStream($data);
			//debug($activities);
			$activityArray = $this->Stream->__getActivitiesList($activities, $my, false, $filter_id, $filter_type, $permissions);
			
			if($activityArray != NULL)
			{
				$activities = $activityArray;
				//debug($activities);
				
				$this->set(compact('message','activities', 'filter_type', 'readOnly', 'isSearch'));
				$this->layout = 'ajax';
				$this->render('/activities/activity'); 
			}
			else
			{
				$message = 'No activities found.';
				$this->set(compact('message', 'filter_type', 'activities'));
				$this->layout = 'ajax';
				$this->render('/activities/activity');
			}
		}
		else
		{
			$message = 'You do not have permission to view this Stream';
			$this->set(compact('message', 'filter_type', 'activities'));
			$this->layout = 'ajax';
			$this->render('/activities/activity'); 					
		}
	}
	
	function add_project_on_signup()
	{
		//debug($this->data);
		/*Array
		(
			[Stream] => Array
			(
				[stream] => klsdjf
			)		
		)*/
		
		$clean = new Sanitize();
		
		// CREATE STREAM
		$_stream = $clean->html(trim($this->data['Stream']['stream']));	
		$_streamname = $this->Stream->getStreamname($_stream);
		
		$signupCode = "pub_".$_streamname;
		$userParams = array(
			"signupCode" => $signupCode
		);
		$this->Stream->StreamsUser->User->create();
		if ($this->Stream->StreamsUser->User->save($userParams, false))
		{
			$userId = $this->Stream->StreamsUser->User->id;
			$streamParams = array(
				'stream'		=> $_stream,
				'streamname'	=> $_streamname,
				'user_id'		=> $userId,
				'parent_id' 	=> NULL
			);
			$this->Stream->create();
			if ($this->Stream->save($streamParams))
			{
				$streamsUser = $this->Stream->StreamsUser->add($this->Stream->id, $userId, $userId);
				// PREPARE VARIABLES FOR RENDERING
				if(!empty($streamsUser))
				{
					$this->Session->setFlash(__('Your workspace is ready as soon as you register!', 'default', array(), 'success'));
					$this->redirect(array('controller'=>'users', 'action' => 'register', $signupCode));
				}
			}
		}
		$this->Session->setFlash(__('Sorry something went wrong as we try to create your workspace.  Perhaps try <a href="/users/register">signing up for an account</a>?', 'default', array(), 'success'));
		$this->redirect(array('controller'=>'hello', 'action' => 'about'));
	}
	
	/*function delete($id = null, $from = null) {
		$json = array (
			'message' => '', 
			'status' => 'failed'
		);
		
		if (!$id) 
			$json['message'] = 'Invalid id for Stream';
		else {
			$mainEntity = "user";
			$relatedEntity = "tag";
			if ($from == "Tags") {
				$mainEntity = "tag";
				$relatedEntity = "user";
			}
			$this->Stream->id = $id;
			if ($this->Stream->saveField('delete_user_id', $this->Session->read('Auth.User.id'))) {
				$json['message'] = 'The '.$relatedEntity.' is deleted from the '.$mainEntity;
				$json['status'] = 'success';
			}
			else
				$json['message'] = 'The '.$relatedEntity.' can not be deleted from the '.$mainEntity.' at this time.  Please try again later.';
		}
		$this->redirect(array('controller' => 'users', 'action' => 'view', $this->Session->read('Auth.User.id'))); 
		//echo $json['status'].'###'.$json['message'].'###';
		//$this->layout = 'ajax';
		//Configure::write('debug',0); //When output Json. 
		//echo json_encode($json);
		//exit();
	}*/

}
?>