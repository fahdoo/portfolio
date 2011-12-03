<?php 

class Thinker extends ThinkerAppModel {
	var $name = "Thinker";
	var $useTable = false;
    
	function __construct() {
    	App::import('Model', 'User');
    	$this->User = new User();
    }
    
    function getThinker($uid){
    	$q = 'SELECT *
    			FROM widgets AS Widget
    			LEFT JOIN users_widgets AS UsersWidget ON(UsersWidget.widget_id = Widget.id AND UsersWidget.user_id = '.$uid.' AND UsersWidget.deleted IS NULL)';
    	$where = array();
    	$where[] = 'Widget.deleted IS NULL';
    	$where[] = 'Widget.core = 2';
    	$where[] = 'Widget.status = 2';
    	
    	$q.= ' WHERE '.implode(' AND ', $where);
    	$q.= ' ORDER BY Widget.install_count DESC';
    	return $this->query($q);
    }
    
    	
	function getParticipants($stream_id, $my)
	{
		$q = "SELECT User.id, User.fullname, User.picture, User.about, StreamsUser.*, StreamsUser2.permissions, StreamsUser2.tagged_user_id 
			FROM users AS User 
			JOIN streams_users AS StreamsUser ON 
				(StreamsUser.stream_id IN (".$stream_id.") AND StreamsUser.tagged_user_id = User.id) 
			LEFT JOIN streams_users AS StreamsUser2 ON 
				(StreamsUser2.stream_id = StreamsUser.stream_id AND StreamsUser2.tagged_user_id = ".$my['id'].")
			WHERE StreamsUser.delete_user_id IS NULL AND 
				StreamsUser2.delete_user_id IS NULL AND 
				(
					(StreamsUser.stream_id = 2 AND StreamsUser.permissions = 1) 
					OR 
					(StreamsUser.stream_id <> 2 AND StreamsUser.permissions IN(1, 2, 5))
				)
			ORDER BY StreamsUser.created ASC
			LIMIT 100
			";
		return $this->query($q);
	}
    
	function clean($input)
	{
		return parent::clean($input);
	} 
}

?>