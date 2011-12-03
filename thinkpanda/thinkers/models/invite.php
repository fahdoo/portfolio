<?php 

class Invite extends ThinkerAppModel {
	var $name = "Invite";
	var $useTable = false;
    
	function __construct() {
    	App::import('Model', 'User');
    	$this->User = new User();
    }
    
	function clean($input)
	{
		return parent::clean($input);
	} 
}

?>