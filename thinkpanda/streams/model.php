<?php
class Stream extends AppModel {

	var $name = 'Stream';
	var $validate = array(
		/*'table_id' => array('numeric'),*/
		'stream' => array('notempty'),
		'good_rating' => array('numeric'),
		'bad_rating' => array('numeric'),
		'views' => array('numeric')
	);
	var $actsAs = array('Containable'); // Add this to any model you want to use the Containable functionality

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'CreationUser' => array('className' => 'User',
								'foreignKey' => 'user_id',
								'conditions' => '',
								'fields' => '',
								'order' => ''
			),
			'DeleteUser' => array('className' => 'User',
								'foreignKey' => 'delete_user_id',
								'conditions' => '',
								'fields' => '',
								'order' => ''
			),
			'AccessLevel' => array('className' => 'AccessLevel',
								'foreignKey' => 'access',
								'conditions' => '',
								'fields' => '',
								'order' => ''
			),
	);
	
	var $hasMany = array(
			'StreamView' => array('className' => 'StreamView',
							'foreignKey' => 'stream_id',
							'dependent' => false,
							'conditions' => '',
							'fields' => '',
							'order' => '',
							'limit' => '',
							'offset' => '',
							'exclusive' => '',
							'finderQuery' => '',
							'counterQuery' => ''
			),
			'StreamsStream' => array('className' => 'StreamsStream',
								'foreignKey' => 'stream_id',
								'dependent' => false,
								'conditions' => '',
								'fields' => '',
								'order' => '',
								'limit' => '',
								'offset' => '',
								'exclusive' => '',
								'finderQuery' => '',
								'counterQuery' => ''
			)
	);

	var $hasAndBelongsToMany = array(
			'Comment' => array('className' => 'Comment',
						'joinTable' => 'comments_streams',
						'foreignKey' => 'stream_id',
						'associationForeignKey' => 'comment_id',
						'unique' => true,
						'conditions' => array(
							'CommentsStream.delete_user_id IS NULL',
						),
						'fields' => '',
						'order' => '',
						'limit' => '',
						'offset' => '',
						'finderQuery' => '',
						'deleteQuery' => '',
						'insertQuery' => '',
						'with' => 'CommentsStream'
			),
			'Page' => array('className' => 'Page',
						'joinTable' => 'pages_streams',
						'foreignKey' => 'stream_id',
						'associationForeignKey' => 'page_id',
						'unique' => true,
						'conditions' => array(
							'PagesStream.delete_user_id IS NULL',
						),
						'fields' => '',
						'order' => '',
						'limit' => '',
						'offset' => '',
						'finderQuery' => '',
						'deleteQuery' => '',
						'insertQuery' => '',
						'with' => 'PagesStream'
			),
			'User' => array('className' => 'User',
						'joinTable' => 'streams_users',
						'foreignKey' => 'stream_id',
						'associationForeignKey' => 'tagged_user_id',
						'unique' => true,
						'conditions' => array(
							'StreamsUser.delete_user_id IS NULL',
						),
						'fields' => '',
						'order' => '',
						'limit' => '',
						'offset' => '',
						'finderQuery' => '',
						'deleteQuery' => '',
						'insertQuery' => '',
						'with' => 'StreamsUser'
			),
			'Tag' => array('className' => 'Tag',
						'joinTable' => 'streams_tags',
						'foreignKey' => 'stream_id',
						'associationForeignKey' => 'tag_id',
						'unique' => true,
						'conditions' => array(
							'StreamsTag.delete_user_id IS NULL',
						),
						'fields' => '',
						'order' => '',
						'limit' => '',
						'offset' => '',
						'finderQuery' => '',
						'deleteQuery' => '',
						'insertQuery' => '',
						'with' => 'StreamsTag'
			),					
	);

	// TRENDING
	    
	function __getMostFollowedProjects($uid = 0, $limit = 10)
	{
		$q = '
			SELECT Stream.*, StreamsUser.permissions, COUNT(StreamsUser2.stream_id) AS users_count
			FROM streams AS Stream 
			LEFT JOIN streams_users AS StreamsUser ON (Stream.id = StreamsUser.stream_id AND StreamsUser.tagged_user_id = '.$uid.')
			JOIN streams_users AS StreamsUser2 ON Stream.id = StreamsUser2.stream_id
			WHERE Stream.access = 2 
				AND StreamsUser2.created > DATE_SUB(CURDATE(),INTERVAL 30 DAY) 
				AND StreamsUser2.permissions IN (1, 2) 
			GROUP BY Stream.id
			ORDER BY users_count DESC, Stream.comments_count DESC
			LIMIT '.$limit
		;
		return $this->query($q);
	}
	
	function __getMostActiveProjects($uid = 0, $limit = 10)
	{
		$q = '
			SELECT Stream.*, StreamsUser.permissions
			FROM streams AS Stream 
			LEFT JOIN streams_users AS StreamsUser ON (Stream.id = StreamsUser.stream_id AND StreamsUser.tagged_user_id = '.$uid.')
			WHERE Stream.access = 2 
				AND Stream.modified > DATE_SUB(CURDATE(),INTERVAL 30 DAY) 
			GROUP BY Stream.id
			ORDER BY Stream.comments_count DESC
			LIMIT '.$limit;
		return $this->query($q);	
	}    
	// -TRENDING
	
	function getStreamname($_stream, $id = NULL){
		$slug = substr($this->createSlug($_stream), 0, 110);
		$this->recursive = -1;
		$params = array ();	$params ['conditions']= array();
		$params ['conditions']['streamname']= $slug;
		if (!is_null($id)) {
			$params ['conditions']['not'] = array('id'=>$id);
		}
		$count = $this->find ('count',$params);		
		//$stream = $this->findByStream($_stream);
		if($count > 0){
			$slug =  $slug.'-'.rand(0,100).time();
		}
		// debug($slug);
		return $slug;
	}

	function getStreamsForTagViewer($user_id, $my_id, $permissions){
		if($user_id == $my_id){
			$streams = array_keys($permissions, 1)+array_keys($permissions, 2)+array_keys($permissions, 5);
		}else{
			$access = $this->__getStreamPermissionsAndAccess($user_id);
			$streams = array_keys($access['access'], 2) + array_keys($access['access'], 4);		
		}
		return implode(',', $streams);
	}
				
	function __getStreamPermissionsAndAccess($user_id)
	{
		$streamsPermissions = $this->StreamsUser->find('list', 
			array(
				'fields' 		=> array('StreamsUser.stream_id', 'StreamsUser.permissions'),
				'conditions'	=> array(
					'StreamsUser.tagged_user_id' => $user_id,
					'StreamsUser.delete_user_id IS NULL'
				),
				'order'			=> array('StreamsUser.stream_id')
			)
		);
		
		$stream_ids = implode(',', array_keys($streamsPermissions));
		
		$streamsAccess = $this->find('list',
			array(
				'fields' 		=> array('Stream.id', 'Stream.access'),
				'conditions'	=> array(
					'Stream.id IN ('.$stream_ids.')',
					'Stream.delete_user_id IS NULL'
				),
				'order'			=> array('Stream.id')
			)
		);
		
		return array('permissions' => $streamsPermissions, 'access' => $streamsAccess);
	}
	
	function hasPermission($stream_id = NULL, $uid = NULL, $permissions = NULL, $access = NULL)
	{
		$results = array (
			'flag' => false
		);
		if(!is_null($stream_id) && !is_null($uid))
		{
			// Check if user has permission to view this Stream
			if ((is_null($permissions) || !array_key_exists($stream_id, $permissions)) || (is_null($access) || !array_key_exists($stream_id, $access)))
			{
				$permissions = $this->query('SELECT permissions FROM streams_users AS StreamsUser WHERE stream_id = '.$stream_id.' AND tagged_user_id = '.$uid. ' AND delete_user_id IS NULL');
				$access = $this->query('SELECT access FROM streams AS Streams WHERE id = '.$stream_id.' AND delete_user_id IS NULL');
				/*debug($access);
				debug($permissions);*/
				if(isset($access[0]))
				{
					if($access[0]['Streams']['access'] == 2 || $access[0]['Streams']['access'] == 4)
					{ // Open and Default
						$results['flag'] = true;
					}
					else if(isset($permissions[0]))
					{
						if($permissions[0]['StreamsUser']['permissions'] != 4)
							$results['flag']=true;
						$results['permissionToAdd'] = array
						(
							'stream_id' => $stream_id,
							'permission' => $permissions[0]['StreamsUser']['permissions']
						);
					}
					$results['accessToAdd'] = array(
						'stream_id' => $stream_id,
						'access' => $access[0]['Streams']['access']
					);
				}
			}
			else
			{
				$results['flag'] = ($access[$stream_id] == 2 || $permissions[$stream_id] != 4);
			}

			/*
			FIXME: 20-12-2009: Moved from stream_controller.php > Can't find the update function though but it belongs here
			if (array_key_exists('permissionToAdd', $results))
			{
				$this->__updateStreamPermissionAccessToSession('StreamsUser.Permission', $results['permissionToAdd']['stream_id'], $results['permissionToAdd']['permission'], $permissions);
			}
			if (array_key_exists('accessToAdd', $permissionResults))
			{
				$this->__updateStreamPermissionAccessToSession('Streams.Access', $results['accessToAdd']['stream_id'], $results['accessToAdd']['access'], $access);
			}
			*/
		}
		return $results;
	}
	
	function isAdmin($stream_id=NULL, $uid=NULL, $permissions = NULL)
	{
		$results = array (
			'flag' => false
		);
		
		if (!is_null($stream_id) && !is_null($uid))
		{
			if (is_null($permissions) || !array_key_exists($stream_id, $permissions))
			{
				$isAllow = $this->StreamsUser->find('first', array(
					'conditions' => array(
						'StreamsUser.stream_id' => $stream_id,
						'StreamsUser.tagged_user_id' => $uid,
						'StreamsUser.delete_user_id IS NULL'
					),
					'fields' => array('StreamsUser.permissions')
				));	
				if($isAllow['StreamsUser']['permissions'] == 1)
					$results['flag'] = true;
					
				$results['permissionToAdd'] = array
				(
					'stream_id' => $stream_id,
					'permission' => $isAllow['StreamsUser']['permissions']
				);
			}
			else
			{
				$results['flag'] = ($permissions[$stream_id] == 1);
			}
		}
		return $results;
	}
	
	// From the permissions or access session variable, create a comma separate value of all the stream id's
	function listStreamsForQuery($streamPermissions){
		$streams = array_keys($streamPermissions, 1) + array_keys($streamPermissions, 2) + array_keys($streamPermissions, 5);
		return  implode(',', $streams);
	}
	
	function get($stream_id, $uid){
		$q = 'SELECT StreamsUser.*, Stream.*, AccessLevel.id, AccessLevel.access 
				FROM streams AS Stream
				JOIN access_levels AS AccessLevel
				LEFT JOIN streams_users AS StreamsUser ON (StreamsUser.tagged_user_id = '.$uid.' AND StreamsUser.stream_id = Stream.id AND StreamsUser.delete_user_id IS NULL)';
		
		$where = array();
		$where[] = 'Stream.id IN ( '.$stream_id.')';
		$where[] = 'Stream.access = AccessLevel.id';
		$where[] = 'Stream.delete_user_id IS NULL';
		
		$where[] = '(Stream.access IN (2, 4) OR StreamsUser.permissions IN(1,2, 3, 5) OR Stream.user_id = '.$uid.')';	// Only shows a linked open or closed streams	
		
		$q.= ' WHERE '.implode(' AND ', $where);
		
		$q.=' ORDER BY Stream.stream ASC';
		return $this->query($q);	
	}

	function getByStreamname($streamname, $uid){
		$q = 'SELECT StreamsUser.*, Stream.*, AccessLevel.id, AccessLevel.access 
				FROM streams AS Stream
				JOIN access_levels AS AccessLevel
				LEFT JOIN streams_users AS StreamsUser ON (StreamsUser.tagged_user_id = '.$uid.' AND StreamsUser.stream_id = Stream.id AND StreamsUser.delete_user_id IS NULL)';
		
		$where = array();
		$where[] = 'Stream.streamname = "'.$streamname.'"';
		$where[] = 'Stream.access = AccessLevel.id';
		$where[] = 'Stream.delete_user_id IS NULL';
		
		$where[] = '(Stream.access IN (2, 4) OR StreamsUser.permissions IN(1,2, 3, 5) OR Stream.user_id = '.$uid.')';	// Only shows a linked open or closed streams	
		
		$q.= ' WHERE '.implode(' AND ', $where);
		
		$q.=' ORDER BY Stream.stream ASC';
		return $this->query($q);	
	}
	
	function getSubStreams($stream_id, $uid){
		$q = 'SELECT StreamsUser.*, Stream.*, AccessLevel.id, AccessLevel.access 
				FROM streams AS Stream
				JOIN access_levels AS AccessLevel
				LEFT JOIN streams_users AS StreamsUser ON (StreamsUser.tagged_user_id = '.$uid.' AND StreamsUser.stream_id = Stream.id)';
		
		$where = array();
		$where[] = 'Stream.parent_id = '.$stream_id;
		$where[] = 'Stream.access = AccessLevel.id';
		$where[] = 'StreamsUser.delete_user_id IS NULL';
		$where[] = 'Stream.delete_user_id IS NULL';
		
		$where[] = '(Stream.access <= "2" OR StreamsUser.permissions IN( 1,2,5))';	// Only shows a linked open or closed streams	
		
		$q.= ' WHERE '.implode(' AND ', $where);
		
		$q.=' ORDER BY Stream.stream ASC';
		return $this->query($q);
	}
	
	function getLinkedStreams($stream_id, $uid){
		$q = 'SELECT StreamsUser.*, Stream.*, AccessLevel.id, AccessLevel.access, StreamsStream.* 
				FROM streams_streams AS StreamsStream
				JOIN streams AS Stream
				JOIN access_levels AS AccessLevel
				LEFT JOIN streams_users AS StreamsUser ON (StreamsUser.tagged_user_id = '.$uid.' AND StreamsUser.stream_id = Stream.id)';
		
		$where = array();
		$where[] = 'StreamsStream.link_stream_id = Stream.id';
		$where[] = 'StreamsStream.stream_id = '.$stream_id;
		//$where[] = 'StreamsStream.link_stream_id <> '.$stream_id;
		$where[] = 'StreamsUser.delete_user_id IS NULL';
		$where[] = 'Stream.access = AccessLevel.id';
		$where[] = 'StreamsStream.delete_user_id IS NULL';
		$where[] = 'Stream.delete_user_id IS NULL';
		
		$where[] = '(Stream.access <= "2" OR StreamsUser.permissions IN( 1,2,5))';	// Only shows a linked open or closed streams	
		
		$q.= ' WHERE '.implode(' AND ', $where);
		
		$q.=' ORDER BY Stream.stream ASC';
		return $this->query($q);
	}
	
	
	function getStreamsForTag($id, $uid = NULL){
		if($uid != NULL)
		{  
			$q = "SELECT StreamsUser.id, StreamsUser.tagged_user_id, StreamsUser.permissions,  StreamsUser.archive, Stream.id, Stream.stream, Stream.description, Stream.good_rating, Stream.bad_rating, Stream.views, Stream.created, Stream.modified, Stream.user_id, Stream.access, User.id, User.fullname, StreamsTag.tag_id, StreamsTag.created, StreamsTag.modified
				FROM streams AS Stream
				RIGHT JOIN streams_tags AS StreamsTag ON (StreamsTag.tag_id = ".$id." AND StreamsTag.stream_id = Stream.id)
				JOIN streams_users AS StreamsUser ON ( StreamsUser.stream_id = Stream.id)
				JOIN users AS User ON (User.id = Stream.user_id)
				WHERE StreamsUser.delete_user_id IS NULL AND StreamsTag.delete_user_id IS NULL
				AND (StreamsUser.tagged_user_id = ".$uid." OR Stream.access = 2)
				GROUP BY Stream.id ORDER BY StreamsTag.modified";
			return $this->query($q);
		}
		return false;
	}
	
	function getStreamsForPage($id, $uid = NULL)
	{
		if($uid != NULL)
		{  
			/*$q = "SELECT StreamsUser.id, StreamsUser.tagged_user_id, StreamsUser.permissions,  StreamsUser.archive, Stream.id, Stream.stream,  Stream.description, Stream.good_rating, Stream.bad_rating, Stream.views, Stream.created, Stream.modified, Stream.user_id, Stream.access, User.id, User.fullname, PagesStream.page_id, PagesStream.comment_id, PagesStream.title, PagesStream.created, PagesStream.modified
				FROM streams AS Stream
				RIGHT JOIN pages_streams AS PagesStream ON (PagesStream.stream_id = Stream.id)
				JOIN streams_users AS StreamsUser ON ( StreamsUser.stream_id = Stream.id)
				JOIN users AS User ON (User.id = Stream.user_id)
				WHERE StreamsUser.delete_user_id IS NULL AND PagesStream.delete_user_id IS NULL
				AND (StreamsUser.tagged_user_id = ".$uid." OR Stream.access = 2)
				AND PagesStream.page_id = ".$id." 
				GROUP BY Stream.id ORDER BY PagesStream.modified";*/
			$q = "SELECT StreamsUser.*, Stream.*, PagesStream.*, CreationUser.id, CreationUser.fullname, CreationUser.picture, CreationUser.about, AccessLevel.id, AccessLevel.access   
				FROM streams_users AS StreamsUser
				JOIN streams AS Stream ON StreamsUser.stream_id = Stream.id
				JOIN users AS CreationUser ON Stream.user_id = CreationUser.id 
				JOIN access_levels AS AccessLevel ON Stream.access = AccessLevel.id 
				RIGHT JOIN pages_streams AS PagesStream ON PagesStream.stream_id = Stream.id
				WHERE PagesStream.page_id = ".$id." 
					AND (StreamsUser.tagged_user_id = ".$uid." OR Stream.access = 2)
					AND StreamsUser.delete_user_id IS NULL 
					AND PagesStream.delete_user_id IS NULL
				GROUP BY Stream.id 
				ORDER BY PagesStream.modified";
			return $this->query($q);	
		}
		return false;
	}
	
	function getStreamsForDiscover($uid = NULL){
		if($uid != NULL){  
			$q = "SELECT StreamsUser.id, StreamsUser.tagged_user_id, StreamsUser.permissions,  StreamsUser.archive, Stream.id, Stream.stream,  Stream.description, Stream.good_rating, Stream.bad_rating, Stream.views, Stream.created, Stream.modified, Stream.user_id, Stream.access, User.id, User.fullname, COUNT(StreamsUser.id) as counter
				FROM streams AS Stream
				JOIN streams_users AS StreamsUser ON (StreamsUser.stream_id = Stream.id)
				JOIN users AS User ON (User.id = Stream.user_id)
				WHERE StreamsUser.delete_user_id IS NULL
				 AND Stream.access = 2 AND Stream.id NOT IN (SELECT s.stream_id FROM streams_users AS s WHERE s.tagged_user_id = ".$uid." AND s.delete_user_id IS NULL)
				GROUP BY Stream.id ORDER BY counter DESC LIMIT 50";
			return $this->query($q);	
		}
		return false;
	}
		
	function getStreamsForUser($id, $uid = NULL){
	
	}
	
	function getStreamsForComment($id, $uid = NULL){
		if($uid != NULL){  
			$q = "SELECT StreamsUser.id, StreamsUser.tagged_user_id, StreamsUser.permissions, StreamsUser.archive, Stream.id, Stream.stream,  Stream.description, Stream.good_rating, Stream.bad_rating, Stream.views, Stream.created, Stream.modified, Stream.user_id, Stream.access, CommentsStream.comment_id, CommentsStream.created, CommentsStream.modified
FROM streams AS Stream
RIGHT JOIN comments_streams AS CommentsStream ON (CommentsStream.stream_id = Stream.id AND CommentsStream.comment_id = ".$id.")
JOIN streams_users AS StreamsUser ON ( StreamsUser.stream_id = Stream.id)
WHERE StreamsUser.delete_user_id IS NULL AND CommentsStream.delete_user_id IS NULL AND (StreamsUser.tagged_user_id = ".$uid." OR Stream.access = 2)
GROUP BY Stream.id ORDER BY CommentsStream.modified";
			return $this->query($q);	
		}
		return false;	
	}
	
	function saveCommentsStream($comment_id = NULL, $stream_id = NULL, $user_id = NULL){
		if($comment_id != NULL && $stream_id != NULL && $user_id != NULL){
			$q = "INSERT INTO comments_streams"
				." (comment_id, stream_id, user_id, created, modified)"
				." VALUES ('".$comment_id."','".$stream_id."', '".$user_id."', '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."')";
			return $this->query($q);
		}
		return false;
	}

								
	function savePagesStream($page_id = NULL, $stream_id = NULL, $user_id = NULL, $tag_id = NULL, $comment = NULL, $title = NULL){
		if($page_id != NULL && $stream_id != NULL && $user_id != NULL){
			$q = "SELECT id, comment_id"
				." FROM pages_streams AS PagesStream"
				." WHERE PagesStream.delete_user_id IS NULL"
				." AND PagesStream.page_id = ".$page_id
				." AND PagesStream.stream_id = ".$stream_id;
			$result = $this->query($q);
			//echo $result;
			$entityStream['message']= "The link has been saved";			
			if (empty($result)) //pages_streams relation doesn't exist
			{
				$parent_id = NULL;	$is_child = 1;
				$q = "INSERT INTO comments"
					." (comment, is_child, user_id, created, modified)"
					." VALUES ('".$comment."','".$is_child."', '".$user_id."', '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."')";
				$blurb = $this->query($q);	
				$comment_id = mysql_insert_id();
				// SAVE COMMENT STREAM
				$parameters_stream = array (
					'user_id' => $user_id,
					'stream_id' => $stream_id,
					'comment_id' => $comment_id
				);
				$this->CommentsStream->create();
				$save_stream = $this->CommentsStream->save($parameters_stream);				

				// SAVE COMMENT TAG
				if($tag_id != NULL){
					$parameters_tag = array (
						'user_id' => $user_id,
						'tag_id' => $tag_id,
						'comment_id' => $comment_id
					);
					$this->Comment->CommentsTag->create();
					$save_tag = $this->Comment->CommentsTag->save($parameters_tag);				
				}
							
				$q = "INSERT INTO pages_streams"
					." (page_id, stream_id, comment_id, user_id,  modified_user_id, title, created, modified)"
					." VALUES ('".$page_id."','".$stream_id."', '".$comment_id."', '".$user_id."', '".$user_id."', '".$title."', '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."')";
				$pagesStreamExisted = $this->query($q);
				$id = mysql_insert_id();
				//echo $id.' '.$stream_id.' '.$tag_id.' '.$comment.' '.$blurb_id;
			}
			else
			{
				//$q = "UPDATE pages_streams SET modified = NOW(), modified_user_id = ".$user_id;
				$pagesStreamExisted = $this->query($q);
				$id = $result[0]['PagesStream']['id'];
				$comment_id =  $result[0]['PagesStream']['comment_id'];
				$this->PagesStream->updateAll(
						array('PagesStream.modified_user_id' => $user_id, 'PagesStream.modified' => '\''.date('Y-m-d H:i:s').'\''),
						array('PagesStream.id' => $id)
					);
					
				$condition = array (
					'conditions' => array(
						'CommentsTag.comment_id' => $comment_id,
						'CommentsTag.tag_id' => $tag_id,
						'CommentsTag.delete_user_id IS NULL',
					),
					'fields' => array('CommentsTag.id')
				);
				$ct = $this->Comment->CommentsTag->find('first', $condition);					
				// SAVE COMMENT TAG
				if(!$ct){
					$parameters_tag = array (
						'user_id' => $user_id,
						'tag_id' => $tag_id,
						'comment_id' =>$comment_id,
					);
					$this->Comment->CommentsTag->create();
					$save_tag = $this->Comment->CommentsTag->save($parameters_tag);					
				}

				$entityStream['message']="This link already exists in this Stream";			
			}

			$entityStream['PagesStream']['id'] = $id;
			$entityStream['PagesStream']['created'] = "5 seconds ago";
			$entityStream['PagesStream']['modified'] = "5 seconds ago";
			$entityStream['Comment']['id'] = $comment_id;
			return $entityStream;	
		}
		return false;
	}
	
	function saveEntityStream($entity_id = NULL, $stream_id = NULL, $user_id = NULL, $type)
	{
		if ($type == 'Pages'){
			$table = 'pages_streams';
			$entityColumn = 'page_id';
			$model = 'PagesStream';
		}
		
		if($entity_id != NULL && $stream_id != NULL && $user_id != NULL){
			$q_select = "SELECT ".$table.".id"
				." FROM ".$table
				." WHERE delete_user_id IS NULL"
				." AND ".$entityColumn." = ".$entity_id
				." AND stream_id = ".$stream_id;
			$result = $this->query($q_select);				
			/*$entityStreamExisted = $this->find('first', array('conditions' => 
											array(
												'delete_user_id IS NULL', 
												$entityColumn => $entity_id, 
												'stream_id' => $stream_id 
											)
										));*/
			$date = date('Y-m-d H:i:s');
			if (empty($result)) //entity_streams relation doesn't exist
			{
				$q = "INSERT INTO ".$table
					." (".$entityColumn.", stream_id, user_id, created, modified)"
					." VALUES ('".$entity_id."','".$stream_id."', '".$user_id."', '".$date."', '".$date."')";
				$entityStreamExisted = mysql_query($q);
				$id = mysql_insert_id();
				//debug($id);
			}
			else
			{
				$q = "UPDATE ".$table
					." SET modified = '".$date."', modified_user_id = '".$user_id."'"
					." WHERE ".$entityColumn." = ".$entity_id
					." AND stream_id = ".$stream_id;
				$entityStreamExisted = $this->query($q);
				$id = $result[0][$table]['id'];
				//debug($entityStreamExisted);
			}
				$entityStream[$model]['id'] = $id;
				$entityStream[$model]['created'] = "Now";
				$entityStream[$model]['modified'] = "Now";
				return $entityStream;			
			
		}
		return false;
	}
	
	function getStreamsList_server($streams, $my)
	{
		$streamsList = array();
		
		$i = 0;
		foreach($streams as $stream)
		{
			//debug($stream);
			
			$class = 'contextItem stream ';
			
			$class.= ' streamParent_'.$stream['Stream']['parent_id'];		
			
			$isOpen = false;
			$isHidden = false;
			switch($stream['Stream']['access']){
				case 1:
					$class.=' closedStream';
					$accessType = 'Closed';
					break;
				case 2:
					$class.=' openStream';
					$isOpen = true;
					$accessType = 'Open';
					break;
				case 3:
					$class.=' hiddenStream';
					$accessType = 'Hidden';
					$isHidden = true;
					break;
				default:
					$class.=' default';
					$accessType = 'Default';
					break;				
			}
			
			$stream['permission'] = $stream['StreamsUser']['tagged_user_id'] == $my['id'] && $stream['Stream']['access'] != 4;
					
			$archive = false;
			$originalAccessType = $accessType;
			if(array_key_exists('archive', $stream['StreamsUser']) && !is_null($stream['StreamsUser']['archive']) && !empty($stream['StreamsUser']['archive']) && array_key_exists('archive', $stream['StreamsUser'])&& $stream['StreamsUser']['tagged_user_id'] == $my['id']){
				$accessType = 'Archive';	
				$archive = true;		
			}
			$stream['archive'] = $archive;
						
			$isAdmin = false;
			$isMember = false;
			$isInvited = false;
			$isRequested = false;
			$isModerator = false;
			
			if($stream['StreamsUser']['permissions'] == 1 && $stream['StreamsUser']['tagged_user_id'] == $my['id']){
				$class.=' admin';
				$isAdmin = true;
			}else if($stream['StreamsUser']['permissions']==2 && $stream['StreamsUser']['tagged_user_id'] == $my['id']){
				$class.=' member';	
				$isMember = true;		
			}else if($stream['StreamsUser']['permissions']==3 && $stream['StreamsUser']['tagged_user_id'] == $my['id']){
				$class.=' invited';	
				$isInvited = true;			
				$accessType = 'Invited';
			}else if($stream['StreamsUser']['permissions']==4 && $stream['StreamsUser']['tagged_user_id'] == $my['id']){
				$class.=' requested';		
				$isRequested = true;
				$accessType = 'Requested';
			}else if($stream['StreamsUser']['permissions']==5 && $stream['StreamsUser']['tagged_user_id'] == $my['id']){
				$class.=' moderator';		
				$isModerator = true;
			}
			

			$stream['actionRequired'] = $isInvited || $isRequested;
			
			$isCreator = false;
			if($stream['Stream']['user_id']==$my['id']){
				$class.=' creator';
				$isCreator = true;
			}
			

			$stream['cssClass'] = $class;
			
			if($accessType != 'Default' AND $accessType != 'Requested' AND $accessType != 'Invited' AND $accessType != 'Archive')
				$streamType = 'Active'; // Grouping Hidden, Closed, Open, Archive into Active
			else
				$streamType = $accessType;			
			$stream['accessType'] = $accessType;
			
			
			$stream['edit'] = false;
			$stream['accept'] = false;
			if ($isAdmin)
			{	
				$stream['edit'] = true;
			}	
			else if ($isInvited)
			{	
				$stream['accept'] = true;
			}

			$stream['join'] = false;
			if (!$isHidden && !$isAdmin && !$isMember && !$isInvited && !$isRequested && !$isModerator) 
			{ 	
				$stream['join'] = true;
			}
			
			$streamsList[$streamType][$stream['Stream']['id']] = $stream;
		}
		return $streamsList;
	}
	
		//Get Search Streams
	function searchStreams($q = NULL, $my, $paginate = NULL, $pageSize = NULL, $count = false)
	{
		if ($q!=NULL)
		{	
			str_replace(" ", "%", $q);	
				
			$condition = 'WHERE (Stream.access = 2 OR Stream.access = 1) AND (Stream.stream LIKE "%'.$q.'%" OR Stream.description LIKE "%'.$q.'%" OR Tag.tag LIKE "%'.$q.'%")';
			$limit = '';
			if ($paginate != NULL && $pageSize != NULL){
				$start = $pageSize * ($paginate - 1);
				$limit = ' LIMIT '.$start.', '.$pageSize;
			}
			// Switch between doing the paginated query or finding the total count
			if($count){
				// Streams Count is acting weird: returns an array of total results size with # of matching tags, but not the actual Count
				$q = 'SELECT Stream.id
					FROM streams AS Stream 
					JOIN streams_tags AS StreamsTag ON (StreamsTag.stream_id = Stream.id AND StreamsTag.delete_user_id IS NULL)
					JOIN tags AS Tag ON (StreamsTag.tag_id = Tag.id)'
					.$condition
					.'GROUP BY Stream.id';
				$rs =  mysql_query($q);	
				return mysql_num_rows($rs);
			}else{
				$q = 'SELECT Stream.id, Stream.stream, Stream.description, Stream.access, Stream.created, 
						StreamsUser.permissions, 
						User.id, User.fullname, User.about, User.picture, 
						(SELECT COUNT(*) FROM streams_users WHERE streams_users.stream_id = Stream.id AND delete_user_id IS NULL) AS user_count, 
						Count(*) AS tag_count 
					FROM streams AS Stream 
					JOIN streams_tags AS StreamsTag ON (StreamsTag.stream_id = Stream.id AND StreamsTag.delete_user_id IS NULL)
					JOIN tags AS Tag ON (StreamsTag.tag_id = Tag.id)
					JOIN users AS User ON (User.id = Stream.user_id)
					LEFT JOIN streams_users AS StreamsUser ON (StreamsUser.stream_id = Stream.id AND StreamsUser.tagged_user_id = '.$my['id'].' AND StreamsUser.delete_user_id IS NULL)'
					.$condition
					.'GROUP BY Stream.id ORDER BY tag_count DESC, user_count DESC,  Stream.modified DESC, Stream.stream ASC'
					.$limit;
				return $this->query($q);
			}
		}
		return false;
	}
	
	function __searchThoughts($q = NULL, $my, $paginate = NULL, $pageSize = NULL, $count = NULL)
	{
		if ($q!=NULL)
		{		
			$q = str_replace(" ", "%", $q);
			// Don't want to search OR Stream.access = 1!!	
			$condition = 'AND (Stream.access = 2) AND (Page.title LIKE "%'.$q.'%" OR Comment.comment LIKE "%'.$q.'%" OR (Page.page LIKE "%'.$q.'%"';
			if (strpos($q, "pages/document_view/") < 0)
				$condition .= 'AND NOT(Page.page LIKE "%pages/document_view/%")'; 
			$condition .= '))';
			
			$limit = '';
			if ($paginate != NULL && $pageSize != NULL){
				$start = $pageSize * ($paginate - 1);
				$limit = ' LIMIT '.$start.', '.$pageSize;
			}

			if($count)
			{
				$q = "SELECT Comment.id
					FROM comments AS Comment 
					JOIN comments_streams AS CommentsStream ON Comment.id = CommentsStream.comment_id 
					JOIN comments_tags AS CommentsTag ON Comment.id = CommentsTag.comment_id  
					LEFT JOIN pages_streams AS PagesStream ON 
					(
						PagesStream.stream_id = CommentsStream.stream_id 
						AND PagesStream.comment_id = Comment.id
					)
					LEFT JOIN pages AS Page ON Page.id = PagesStream.page_id 
					JOIN users AS User ON User.id = Comment.user_id 
					LEFT JOIN comments_users AS CommentsUser ON 
					(
						CommentsUser.comment_id = CommentsStream.comment_id 
						AND CommentsUser.user_id = ".$my['id']."
					)
					LEFT JOIN pages_users AS PagesUser ON 
					(
						PagesUser.page_id = PagesStream.page_id 
						AND PagesUser.tagged_user_id = ".$my['id']."
					)
					JOIN streams AS Stream ON Stream.id = CommentsStream.stream_id 
					JOIN tags AS Tag ON Tag.id = CommentsTag.tag_id 
					LEFT JOIN streams_users AS StreamsUser ON 
					(
						StreamsUser.stream_id = Stream.id 
						AND StreamsUser.tagged_user_id = ".$my['id']."
					)
					WHERE CommentsTag.delete_user_id IS NULL 
						AND Comment.delete_user_id IS NULL 
						AND (Comment.parent_id IS NULL OR Comment.parent_id = 0)
						AND (CommentsUser.delete_user_id IS NULL OR PagesUser.delete_user_id IS NULL)
						AND (CommentsStream.delete_user_id IS NULL OR PagesStream.delete_user_id IS NULL) 
						AND StreamsUser.delete_user_id IS NULL
						$condition
					GROUP BY Comment.id, CommentsTag.tag_id";
				$rs = mysql_query($q);	
				//debug($rs);
				return mysql_num_rows($rs);
			}
			else
			{
				$q = "SELECT CommentsStream.stream_id, CommentsStream.created, 
						CommentsTag.comments_count, CommentsTag.tag_id, 
						Comment.id, Comment.parent_id, Comment.comment, Comment.is_child, Comment.good_rating, Comment.bad_rating, 
						Page.id, Page.page, Page.title, Page.type_id, Page.good_rating, Page.bad_rating, 
						PagesStream.title, 
						User.id, User.fullname, User.picture, User.about, 
						Stream.stream, Stream.description,
						Tag.tag,
						StreamsUser.permissions
					FROM comments AS Comment 
					JOIN comments_streams AS CommentsStream ON Comment.id = CommentsStream.comment_id 
					JOIN comments_tags AS CommentsTag ON Comment.id = CommentsTag.comment_id  
					LEFT JOIN pages_streams AS PagesStream ON 
					(
						PagesStream.stream_id = CommentsStream.stream_id 
						AND PagesStream.comment_id = Comment.id
					)
					LEFT JOIN pages AS Page ON Page.id = PagesStream.page_id 
					JOIN users AS User ON User.id = Comment.user_id 
					LEFT JOIN comments_users AS CommentsUser ON 
					(
						CommentsUser.comment_id = CommentsStream.comment_id 
						AND CommentsUser.user_id = ".$my['id']."
					)
					LEFT JOIN pages_users AS PagesUser ON 
					(
						PagesUser.page_id = PagesStream.page_id 
						AND PagesUser.tagged_user_id = ".$my['id']."
					)
					JOIN streams AS Stream ON Stream.id = CommentsStream.stream_id 
					JOIN tags AS Tag ON Tag.id = CommentsTag.tag_id 
					LEFT JOIN streams_users AS StreamsUser ON 
					(
						StreamsUser.stream_id = Stream.id 
						AND StreamsUser.tagged_user_id = ".$my['id']."
					)
					WHERE CommentsTag.delete_user_id IS NULL 
						AND Comment.delete_user_id IS NULL 
						AND (Comment.parent_id IS NULL OR Comment.parent_id = 0)
						AND (CommentsUser.delete_user_id IS NULL OR PagesUser.delete_user_id IS NULL)
						AND (CommentsStream.delete_user_id IS NULL OR PagesStream.delete_user_id IS NULL) 
						AND StreamsUser.delete_user_id IS NULL
						$condition
					GROUP BY Comment.id, CommentsTag.tag_id 
					ORDER BY CommentsStream.modified DESC
					$limit ";
				//debug($q);
				return $this->query($q);
			}
		}
		return false;
	}
	
	function __searchReplies($q, $my, $stream_id, $tag_id, $parent_comment_id)
	{
		$q = str_replace(" ", "%", $q);
				
		$condition = 'AND Comment.comment LIKE "%'.$q.'%"';
		
		$q = "SELECT 		CommentsStream.id, CommentsStream.stream_id, CommentsStream.created, 
							CommentsTag.tag_id, CommentsTag.modified, CommentsTag.comments_count, 
							Comment.id, Comment.parent_id, Comment.comment, Comment.is_child, Comment.good_rating, Comment.bad_rating, 
							User.id, User.fullname, User.picture, User.username
				FROM 		comments AS Comment 
				JOIN 		comments_streams AS CommentsStream ON (Comment.id = CommentsStream.comment_id 
							AND CommentsStream.stream_id = ".$stream_id." AND Comment.parent_id= ".$parent_comment_id.")
				JOIN 		comments_tags AS CommentsTag ON (Comment.id = CommentsTag.comment_id 
							AND CommentsTag.tag_id = ".$tag_id.")  
				JOIN 		users AS User ON User.id = Comment.user_id 
				WHERE 		CommentsTag.delete_user_id IS NULL 
							AND Comment.delete_user_id IS NULL 
							AND CommentsStream.delete_user_id IS NULL
							$condition
				GROUP BY 	Comment.id, CommentsTag.tag_id 
				ORDER BY 	CommentsStream.modified DESC";
		//debug($q);
		return $this->query($q);
	}
	
	function __getStreamIdsAndActiveStreamId ($streamId, $streamsList, $streamListTypes, $streamListTypesToAvoid)
	{
		$streamIdsArray = array();
		$selectStreamId = $streamId;
		
		/*foreach($streamListTypes as $streamListType)
		{
			if (array_key_exists($streamListType, $streamsList))
			{
				$tempStreamsIdArray = array_keys($streamsList[$streamListType]);
				$streamIdsArray[] = implode(",", $tempStreamsIdArray);
				if (array_search($streamListType, $streamListTypesToAvoid) === FALSE && empty($selectStreamId)){
					$selectStreamId = $tempStreamsIdArray[0];
				}
			}
		}*/
		$results = array (
			'selectStreamId' => $selectStreamId,
			'streamsIds' => implode(", ", $streamIdsArray)
		);
		return $results;
	}
	
	function __getActivitiesList($activities, $my, $new, $filter_id, $filter_type, $permissions)
	{
		$activitiesList = array();
		foreach($activities as $activity)
		{
			if (empty($activity['Page']['id'])) //this is a comment
				$activitiesList['comment_'.$activity['Comment']['id']] = $this->Comment->__getCommentListItem($activity, $my, $new, $filter_id, $filter_type, $permissions);
			else
				$activitiesList['page_'.$activity['Page']['id']] = $this->Page->__getPageListItem($activity, $my, $new, $filter_id, $filter_type, $permissions);
		}
		return $activitiesList;
	}
	
	function __renderActivitiesToHTML($stream, $filter, $activities)
	{
		$html = '	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
					<html xmlns="http://www.w3.org/1999/xhtml">
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
					<title>Thoughts from &quot;'.$stream['Stream']['stream'].'&quot; -&gt; &quot;'.$filter['Filter']['filter'].'&quot;</title>
					<style type="text/css">
						
					</style>
					</head>
					
					<body>
						<h1>Thoughts from &quot;'.$stream['Stream']['stream'].'&quot; -&gt; &quot;'.$filter['Filter']['filter'].'&quot;</h1>';
		foreach ($activities as $activity)
		{
			$html .= '	<div class="activity">
							<span class="created">Added On '.$activity['CommentsStream']['created'].'</span>
							<span class="by">By '.$activity['User']['fullname'].'</span>';
			
			if (!empty($activity['Page']['page'])) //we have a page
			{
				if (strncmp($activity['Page']['page'], "/pages/document_view/", strlen("/pages/document_view/")) == 0)
					$activity['Page']['page'] = "http://www.thinkpanda.com".$activity['Page']['page'];
				
				$html .= '	<span class="favicon">
								<img src="/img/globe.png" height="16px" width="16px" />
							</span>
							<span class="page">
								<a href="'.$activity['Page']['page'].'">'.$activity['Page']['title'].'</a>
								(URL: '.$activity['Page']['page'].')
							</span>';
			}
			if (!empty($activity['Comment']['comment'])) //we have a comment or a page with a blurb
			{
				$html .= '	<span class="comment">
								'.$activity['Comment']['comment'].'
							</span>';
			}
			
			foreach ($activity['replies'] as $reply)
			{
				$html .= '	<div class="reply">
								<span class="created">Added On '.$reply['CommentsStream']['created'].'</span>
								<span class="by">By '.$reply['User']['fullname'].'</span>
					
								<span class="comment">
									'.$reply['Comment']['comment'].'
								</span>
							</div>';
			}
			$html .= '	</div>';
		}
		$html .= '		<div class="thanks">
							<p>Thank you for using Thinkpanda!</p>
						</div>
					</body>
					</html>';
		return $html;
	}
	
	function __renderActivitiesToXML($stream, $filterType, $filter, $activities)
	{
		$xml = '	<?xml version="1.0" encoding="UTF-8" ?>
					<thinkpanda>
						<streams>
							<stream>
								<streamName>'.$stream['Stream']['stream'].'</streamName>
								<filters>
									<filter>
										<filterType>'.$filterType.'</filterType>
										<filterName>'.$filter.'</filterName>
										<thoughts>
				';
		
		foreach ($activities as $activity)
		{
			$thoughtType = "Comment";
			if (!empty($activity['Page']['page'])) //we have a page
			{	
				$thoughtType = "Page";
				if (strncmp($activity['Page']['page'], "/pages/document_view/", strlen("/pages/document_view/")) == 0)
					$activity['Page']['page'] = "http://www.thinkpanda.com".$activity['Page']['page'];
			}
			
			$xml .= '						<thought>
												<thoughtType>'.$thoughtType.'</thoughtType>
												<page>
													<url>'.$activity['Page']['page'].'</url>
													<title>'.$activity['Page']['title'].'</title>
													<good_rating>'.$activity['Page']['good_rating'].'</good_rating>
													<bad_rating>'.$activity['Page']['bad_rating'].'</bad_rating>
												</page>
												<comment>
													<comment>'.$activity['Comment']['comment'].'</comment>
													<good_rating>'.$activity['Comment']['good_rating'].'</good_rating>
													<bad_rating>'.$activity['Comment']['bad_rating'].'</bad_rating>
												</comment>
												<created>'.$activity['CommentsStream']['created'].'</created>
												<by>'.$activity['User']['fullname'].'</by>
												<replies>';
				foreach ($activity['replies'] as $reply)
				{
					$xml .= '						<reply>
														<comment>
															<comment>'.$reply['Comment']['comment'].'</comment>
															<good_rating>'.$reply['Comment']['good_rating'].'</good_rating>
															<bad_rating>'.$reply['Comment']['bad_rating'].'</bad_rating>
														</comment>
														<created>'.$reply['CommentsStream']['created'].'</created>
														<by>'.$reply['User']['full name'].'</by>
													</reply>
							';
				}
			$xml .= '							</replies>
											</thought>
					';
		}
		$xml .= '						</thoughts>
									</filter>
								</filters>
							</stream>
						</streams>
					</thinkpanda>';
		
		return $xml;
	}
	
	function __renderActivitiesToCsvOrTxt($stream, $filterType, $filter, $activities, $docType)
	{
		$delimiter = ",";
		$lineFeed = '
';
		if ($docType == 'txt')
			$delimiter = "	";
		
		$content = 'Thoughts from "'.$stream['Stream']['stream'].'" -> "'.$filter['Filter']['filter'].'"'.$lineFeed.$lineFeed;
		$content .= 'Stream: '.$delimiter.$stream['Stream']['stream'].$lineFeed;
		$content .= $delimiter.'Filter Type: '.$delimiter.$filterType.$lineFeed;
		$content .= $delimiter.'Filter: '.$delimiter.$filter['Filter']['filter'].$lineFeed;
		
		if (count($activities) > 0)
		{
			$content .= $delimiter.$delimiter.'Thoughts: '.$lineFeed;
		}
		
		foreach ($activities as $activity)
		{
			$content .= $delimiter.$delimiter.$delimiter.'Thought: '.$lineFeed;
			
			$content .= $delimiter.$delimiter.$delimiter.$delimiter.'Created: '.$delimiter.$activity['CommentsStream']['created'].$lineFeed;
			$content .= $delimiter.$delimiter.$delimiter.$delimiter.'By: '.$delimiter.$activity['User']['fullname'].$lineFeed;
			
			$thoughtType = "Comment";
			$pageContent = '';
			if (!empty($activity['Page']['page'])) //we have a page
			{	
				$thoughtType = "Page";
				if (strncmp($activity['Page']['page'], "/pages/document_view/", strlen("/pages/document_view/")) == 0)
					$activity['Page']['page'] = "http://www.thinkpanda.com".$activity['Page']['page'];
				
				$pageContent = $delimiter.$delimiter.$delimiter.$delimiter.'Page: '.$lineFeed;
				$pageContent .= $delimiter.$delimiter.$delimiter.$delimiter.$delimiter.'Url: '.$delimiter.$activity['Page']['page'].$lineFeed;
				$pageContent .= $delimiter.$delimiter.$delimiter.$delimiter.$delimiter.'Title: '.$delimiter.$activity['Page']['title'].$lineFeed;
				$pageContent .= $delimiter.$delimiter.$delimiter.$delimiter.$delimiter.'Good Rating: '.$delimiter.$activity['Page']['good_rating'].$lineFeed;
				$pageContent .= $delimiter.$delimiter.$delimiter.$delimiter.$delimiter.'Bad Rating: '.$delimiter.$activity['Page']['bad_rating'].$lineFeed;
			}
			
			$content .= $delimiter.$delimiter.$delimiter.$delimiter.'Type: '.$thoughtType.$lineFeed;
			$content .= $pageContent;
			
			if (!empty($activity['Comment']['comment'])) //we have a comment or a page with a blurb
			{
				$content .= $delimiter.$delimiter.$delimiter.$delimiter.'Comment: '.$lineFeed;
				$content .= $delimiter.$delimiter.$delimiter.$delimiter.$delimiter.'Comment: '.$delimiter.$activity['Comment']['comment'].$lineFeed;
				$content .= $delimiter.$delimiter.$delimiter.$delimiter.$delimiter.'Good Rating: '.$delimiter.$activity['Comment']['good_rating'].$lineFeed;
				$content .= $delimiter.$delimiter.$delimiter.$delimiter.$delimiter.'Bad Rating: '.$delimiter.$activity['Comment']['bad_rating'].$lineFeed;
			}
			
			if (count($activity['replies']) > 0)
			{
				$content .= $delimiter.$delimiter.$delimiter.$delimiter.'Replies: '.$lineFeed;
			}
			
			foreach ($activity['replies'] as $reply)
			{
				$content .= $delimiter.$delimiter.$delimiter.$delimiter.$delimiter.'Reply: '.$lineFeed;
				$content .= $delimiter.$delimiter.$delimiter.$delimiter.$delimiter.$delimiter.'Created On: '.$delimiter.$reply['CommentsStream']['created'].$lineFeed;
				$content .= $delimiter.$delimiter.$delimiter.$delimiter.$delimiter.$delimiter.'By: '.$delimiter.$reply['User']['fullname'].$lineFeed;
				$content .= $delimiter.$delimiter.$delimiter.$delimiter.$delimiter.$delimiter.'Comment: '.$delimiter.$reply['Comment']['comment'].$lineFeed;
				$content .= $delimiter.$delimiter.$delimiter.$delimiter.$delimiter.$delimiter.'Good Rating: '.$delimiter.$reply['Comment']['good_rating'].$lineFeed;
				$content .= $delimiter.$delimiter.$delimiter.$delimiter.$delimiter.$delimiter.'Bad Rating: '.$delimiter.$reply['Comment']['bad_rating'].$lineFeed;
				
				$content .= $delimiter.$delimiter.$delimiter.$delimiter.$delimiter.'----------'.$lineFeed;
			}	
			
			$content .= $delimiter.$delimiter.$delimiter.$delimiter.'----------'.$lineFeed;
		}
		
		return $content;
	}
}
?>