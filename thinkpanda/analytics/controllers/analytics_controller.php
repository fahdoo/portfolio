<?php
class AnalyticsController extends AnalyticsAppController {
	var $name = 'Analytics';
	var $helpers = array('Html', 'Form', 'Javascript');
	//var $components = array('Email'); 
	
	var	$pageSize = 10;

	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('contextLogger');
		
		$widget_id = 11; // Every widget must set their Widget.id	
		$widget = $this->getWidget($widget_id);
			
		$this->core['widget'] = $widget;
		$this->pageTitle = $widget['widget'];
    }
	
	function view(){
		$this->layout = 'ajax';

		$my = $this->core['my'];
		$options = $this->setOptions($this->params['url']);
		if(isset($options['user_id']) && true){
			$id = $options['user_id'];
			$analytics['user'] = $this->user($id);
			if($this->isTPTeam($this->core['my']['id'])){
				$analytics['admin'] = $this->Analytic->admin();
				$this->set(compact('analytics'));
				$this->render('/analytics/admin');
			}else{
				return false;
			}
		}else if(isset($options['stream_id'])){
			return false;
			$id = $options['stream_id'];
			$analytics['stream'] = $this->stream($id);
			$this->set(compact('analytics'));
		}		
		
	}

	function admin(){
		Configure::write('debug',2); 
		$my = $this->core['my'];

		$analytics['admin'] = $this->Analytic->admin();
		//debug($admin);
		//$this->set(compact());	
		$this->set(compact('analytics'));
		$this->render('/analytics/admin');
	}
		
	function user($id){
		$my = $this->core['my'];
		return false;
	}
	
	function stream($id){
		$my = $this->core['my'];
		$this->Analytic->User->Stream->recursive = -1;
		$stream = $this->Analytic->User->Stream->read(null, $id);
		
		$userFilters = $this->Analytic->getParticipants($id, $my);
		//debug($userFilters);
		$usersList = $this->Analytic->User->getUsersList_server($userFilters, $my);
		//debug($usersList);
		$streamsUser = $usersList['Normal'];
		$streamsUserToApprove =  $usersList['Requested'];
		return false;
	}

	function contextLogger($context_id = NULL, $context_type = NULL, $widget_class = NULL){
		if(empty($this->core['my'])){
			$this->core['my'] = $this->initGuest();
		}	
		switch($context_type){
			case 'user':
				$context_type_id = 1;
				break;
			case 'stream':
				$context_type_id = 2;
				break;
		}
		if(isset($widget_class) && isset($this->core['my'])){
			$this->Analytic->updateContextLog($this->core['my'], $context_id, $context_type_id, $widget_class);		
			$json = true;		
		}else{
			$json = false;
		}
		$this->layout = 'ajax';
		Configure::write('debug',0); //When output Json. 
		echo json_encode($json);
		exit();
	}
}
?>