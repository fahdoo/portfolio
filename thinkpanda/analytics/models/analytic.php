<?php 

class Analytic extends AnalyticAppModel {
	var $name = "Analytic";
	var $useTable = false;
    
	function __construct() {
    	App::import('Model', 'User');
    	$this->User = new User();
    }

	function clean($input)
	{
		return parent::clean($input);
	} 


	function updateContextLog($my, $context_id, $context_type_id, $widget_class){
		$logOnDisk = false;
		if($logOnDisk){
			$params = array(
				'id' => $context_id,
				'context' => $context_type_id,
				'user_id'	=> $my['id'],
				'widget'	=> $widget_class,
			);
			$this->log(json_encode($params), 'contextLog');
		}else{
			$date = date('Y-m-d H:i:s');
			$this->query('INSERT INTO context_log (context_id, context_type, widget_class, user_id, ip, created) VALUES("'.$context_id.'", "'.$context_type_id.'", "'.$widget_class.'", "'.$my['id'].'", "'.$my['ip'].'", "'.$date.'" )');
		}
	}
		    
    function admin(){
		//$team = array(1,2,5,10);
		$team = '1,2,5,10';
		//WHERE user_id NOT IN ('.$team.')
		
		$analytics['total'] = $this->getTotals($team);
		$analytics['users'] = $this->getUserAnalytics($team);
		$analytics['thoughts'] = $this->getThoughtAnalytics($team);
		$analytics['trend'] = $this->getTrends($team);
		// Simplify the SQL resultset
    	return $analytics;
    }

	function getTotals($team){

		$analytics['total']['users'] = $this->query('
			SELECT COUNT(*) AS total FROM users 
			WHERE hasConfirmed = 1 and delete_user_id IS NULL
		');
		

		$analytics['total']['publicProjects'] = $this->query('
			SELECT COUNT(id) AS total FROM streams WHERE access = 2 AND delete_user_id IS NULL AND user_id NOT IN('.$team.')
		');

		$analytics['total']['privateProjects'] = $this->query('
			SELECT COUNT(id) AS total FROM streams WHERE access = 3 AND delete_user_id IS NULL AND user_id NOT IN('.$team.')
		');		

		$analytics['total']['thoughts'] = $this->query('
			SELECT COUNT(id) AS total FROM comments WHERE parent_id IS NULL AND delete_user_id IS NULL AND user_id NOT IN('.$team.')
		');

		$analytics['total']['replies'] = $this->query('
			SELECT COUNT(id) AS total FROM comments WHERE parent_id IS NOT NULL AND delete_user_id IS NULL AND user_id NOT IN('.$team.')
		');	
					
		$analytics['total'] = $this->__total($analytics['total']);
		return $analytics['total'];	
	}
		
	function getTrends($team){
    	$analytics['trend']['dashboardInteractions'] = $this->query('
    		SELECT DATE(created) AS x, COUNT(*) AS y 
    		FROM context_log
			WHERE created IS NOT NULL AND user_id NOT IN('.$team.')
    		GROUP BY x
    		ORDER BY x ASC 
    		LIMIT 365
    	');

		$analytics['trend']['widgetInstalls'] = $this->query('
			SELECT DATE(created) AS x, COUNT(*) AS y
			FROM users_widgets
			WHERE created IS NOT NULL AND user_id NOT IN('.$team.')
			GROUP BY x
			ORDER BY created ASC 
			LIMIT 365		
		');	
			
		/*
		$analytics['trend']['types'] = $this->query('
			SELECT DATE(created) AS x, COUNT(DISTINCT type_id) AS y 
			FROM comments
			WHERE parent_id IS NULL AND created IS NOT NULL
			GROUP BY x
			ORDER BY created ASC 
			LIMIT 365		
		');	
		*/	
		
		$analytics['trend']['publicProjects'] = $this->query('
			SELECT DATE(created) AS x, COUNT(id) AS y 
			FROM streams
			WHERE created IS NOT NULL AND access = 2 AND user_id NOT IN('.$team.')
			GROUP BY x
			ORDER BY created ASC 
			LIMIT 365		
		');
		
		$analytics['trend']['privateProjects'] = $this->query('
			SELECT DATE(created) AS x, COUNT(id) AS y 
			FROM streams
			WHERE created IS NOT NULL AND access = 3 AND user_id NOT IN('.$team.')
			GROUP BY x
			ORDER BY created ASC 
			LIMIT 365		
		');
				
		$analytics['trend'] = $this->__trendXY($analytics['trend']);
		return $analytics['trend'];					
	}

	
	function getUserAnalytics($team){
				
		$analytics['users']['signups'] = $this->query('
			SELECT DATE(created) AS x, COUNT(*) AS y FROM users 
			WHERE hasConfirmed = 1 and delete_user_id IS NULL AND created IS NOT NULL
    		GROUP BY x
    		ORDER BY x ASC 	
    		LIMIT 365				
		');
		/*$analytics['users']['retention']['month'] = $this->query('
			SELECT MONTH(created) AS x, COUNT(DISTINCT user_id) AS y
			FROM context_log
			GROUP BY x
			ORDER BY created ASC 
			LIMIT 12		
		');*/			
		$analytics['users']['retention'] = $this->query('
			SELECT DATE(created) AS x, COUNT(DISTINCT user_id) AS y
			FROM context_log
			WHERE created IS NOT NULL AND user_id NOT IN('.$team.')
			GROUP BY x
			ORDER BY created ASC 
			LIMIT 365		
		');		
		
		/*$analytics['users']['active']['month'] = $this->query('
			SELECT MONTH(created) AS x, COUNT(DISTINCT user_id) AS y
			FROM comments
			GROUP BY x
			ORDER BY created ASC 
			LIMIT 12	
		');*/	
		$analytics['users']['active'] = $this->query('
			SELECT DATE(created) AS x, COUNT(DISTINCT user_id) AS y
			FROM comments
			WHERE created IS NOT NULL AND user_id NOT IN('.$team.')
			GROUP BY x
			ORDER BY created ASC 
			LIMIT 365		
		');

				
		$analytics['users']['follows'] = $this->query('
			SELECT DATE(created) AS x, COUNT(*) AS y
			FROM user_relations
			WHERE created IS NOT NULL AND user1 NOT IN('.$team.')
			GROUP BY x
			ORDER BY created ASC 
			LIMIT 365			
		');

		$analytics['users']['participants'] = $this->query('
			SELECT DATE(streams_users.created) AS x, COUNT(streams_users.id) AS y 
			FROM streams_users
			JOIN streams ON (streams.id = streams_users.stream_id AND streams.access IN(2,3))
			WHERE streams_users.created IS NOT NULL AND streams_users.user_id NOT IN ('.$team.')
			GROUP BY x
			ORDER BY streams_users.created ASC 
			LIMIT 365		
		');	
		
		$analytics['users'] = $this->__trendXY($analytics['users']);
		return $analytics['users'];					
	}
	
	function getThoughtAnalytics($team){
		$analytics['thoughts']['posted'] = $this->query('
			SELECT DATE(created) AS x, COUNT(id) AS y 
			FROM comments
			WHERE parent_id IS NULL AND created IS NOT NULL AND user_id NOT IN('.$team.')
			GROUP BY x
			ORDER BY created ASC 
			LIMIT 365		
		');
		$analytics['thoughts']['replies'] = $this->query('
			SELECT DATE(created) AS x, COUNT(id) AS y 
			FROM comments
			WHERE parent_id IS NOT NULL AND created IS NOT NULL AND user_id NOT IN('.$team.')
			GROUP BY x
			ORDER BY created ASC 
			LIMIT 365		
		');
		
		$analytics['thoughts'] = $this->__trendXY($analytics['thoughts']);
		return $analytics['thoughts'];	
	}

	function __total($arrays){
		foreach($arrays AS $key=>$array):
			$rs[$key]['total'] = $array[0][0]['total'];
		endforeach;
		return $rs;		
	}
		
	function __totalProjects($array){
		$rs = array();
		foreach($array AS $row){
			$rs[] = array('total' => $row[0]['total'], 'access' => $row['streams']['access']);
		}
		return $rs;		
	}
		
	function __trendXY($arrays){
		$rs = array();
		foreach($arrays AS $type => $array){
			foreach($array AS $row){
				foreach($row AS $data){
					$rs[$type][]= array(
						'x' => date('Y,n,j', strtotime('-1 month', strtotime($data['x']))),
						'y' => $data['y']
					);
				}
			}		
		}
		return $rs;
	}
}

?>