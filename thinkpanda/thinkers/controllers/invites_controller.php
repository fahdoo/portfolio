<?php
class InvitesController extends ThinkersAppController {
	var $name = 'Invites';
	var $helpers = array('Html', 'Form', 'Javascript');
	var $components = array('Email', 'Facebook'); 
	
	var	$pageSize = 10;

			
	function beforeFilter() {
		parent::beforeFilter();

		$widget_id = 4; // Every widget must set their Widget.id	
		$widget = $this->getWidget($widget_id);
			
		$this->core['widget'] = $widget;
		$this->pageTitle = $widget['widget'];
    }
	
	
	function index($tab = NULL)
	{	
		if (is_null($tab))
			$tab = "email";
					
		$my=$this->Session->read('Auth.User');
		if(empty($my['id'])){
			$this->Session->setFlash(__('Invalid User.', true));
			$this->redirect(array('action'=>'index'));
		}
				
		// OINVITER
		App::import('Vendor', 'OpenInviter',array('file'=>'opinviter'.DS.'openinviter.php'));
		$inviter = new OpenInviter();
		
		$services = array();
		$plugins = $inviter->getPlugins();
		if (!empty($this->data['User']['source'])) {				
			$inviter->startPlugin($this->data['User']['source']); 
			
			if($inviter->login($this->data['User']['login'],$this->data['User']['password'])){
				
				$contacts = $inviter->getMyContacts();
				foreach($contacts AS $key => $contact){
					$info = '';
					if($key!=$contact)
						$info = ' - '.$key;
					$contact = strip_tags(stripslashes(html_entity_decode($contact)));
					if(empty($contact))
						$contact = $key;
					else
						$contact.=$info;
					$contacts[$key] = $contact;
				}
				asort($contacts); // alphasort
				$this->set("contacts",$contacts);
				$this->set("sessionID", $inviter->plugin->getSessionID());
				$this->set("source", $this->data['User']['source']);
				$this->Session->setFlash(__('Connection to '.$this->data['User']['source'].' successful - scroll below to invite and follow your imported contacts!', true));
				
				$inviter->logout();
			} else {
				$this->Session->setFlash(__('Login to '.$this->data['User']['source'].' failed, make sure you use a valid email/username and password for '.$this->data['User']['source'], true));
			}				
		} 

		/// build options array for drop-down selection of email/socials
		$allowed = array('gmail', 'hotmail', 'linkedin', 'msn', 'yahoo');
		$services = array();
		foreach ($plugins as $type=>$providers)	
		{
			if(in_array($type, array('email'))){
				//$services[$inviter->pluginTypes[$type]]= array();
				foreach ($providers as $provider=>$details){
					if(in_array($provider, $allowed))
						//$services[$inviter->pluginTypes[$type]][$provider] = $details['name'];
						$services[$provider] = $details['name'];
				}
			}
		}
		//debug($services);
		$this->set("services",$services);
		// END OINVITER
						
		if(!empty($this->data['User']['emails']) || !empty($this->data['User']['contacts']))
		{
			$clean = new Sanitize();
			$clean->clean($this->data);
			$eService = array(); $eManual = array();
			if(isset($this->data['User']['contacts']))
				$eService = $this->data['User']['contacts'];
			if(isset($this->data['User']['emails']))
				$eManual = explode(',', $this->data['User']['emails']);
			$emails = array_merge($eManual, $eService);
			
			if(isset($this->data['User']['allcontacts']))
				$this->set('contacts', $this->data['User']['allcontacts']);			
			//debug($emails);
			
			$validated = $this->Invite->User->__validateEmails($emails);
			$followUsers = $validated['exists'];
			foreach($followUsers AS $followUser)
				$this->Invite->User->UserRelation1->follow($this->core['my']['id'], $followUser['User']['id']);	
				
			$this->set('followUsers', $followUsers);
			
			if (count($validated['valid']) > 0)
			{
				$message = $this->data['User']['message'];

				$invite = $this->__sendInvite($validated['valid'], $message);
				if(count($invite['notsent']) == 0){
					$this->data['User']['emails'] = $validated['invalid'];
				}else{
					$this->data['User']['emails'] = array_merge(implode(',', $invite['notsent']), $validated['invalid']);
				}
				if (count($invite['sent']) == (count($emails) - count($validated['exists'])))
				{
					unset($this->data['User']['emails']);
					$this->Session->setFlash(__('All invite(s) have been sent.', true));
				}
				else
				{ 
					$this->Session->setFlash(__('Some of the invites could not be sent. Please check that they are valid email addresses and try again.', true));
				}
			}
		}
		$presetEmails = '';
		if(isset($this->data['User']['presetEmails']))
			$presetEmails = $this->data['User']['presetEmails'];
		$this->set(compact('presetEmails'));
		
		/*users_services*/
		$usersServices = $this->__getUserServices();
		
		$i = 0;
		foreach ($usersServices as $userService)
		{
			if ($userService["UsersService"]['service_id'] == 1)
			{
				$usersServices[$i]["UsersService"]["isLoggedIn"] = $this->Facebook->loggedin();
			}
			else if ($userService["UsersService"]['service_id'] == 2)
			{
				$usersServices[$i]["UsersService"]["isLoggedIn"] = false; //todo: LinkedIn
			}
			
			++$i;
		}
		
		$this->set(compact('usersServices', 'tab'));
	}
		

	
	function __createInvitedUser($emailAddress){
		$clean = new Sanitize();
		$clean->clean($this->data);	
		// Create User
		$emailname = $this->__deriveEmailname($emailAddress);
		$username = substr($clean->paranoid($emailname.strrev(time())), 0, 19);
		$signupCode = substr(md5($emailAddress.time().rand()), 0, 30); //this is because the column is set as varchar(30) 
		
		$parameters = array(
			'username' => $username,
			'email'		=> $emailAddress,
			'creation_user_id' => $this->core['my']['id'],
			'signupCode' => $signupCode
		);
							
		$this->Invite->User->create();
		$user = $this->Invite->User->save($parameters, false);		
		//  User
		if($user)
		{
			$follow1 = $this->Invite->User->UserRelation1->follow($this->core['my']['id'], $this->Invite->User->id);
			$follow2 = $this->Invite->User->UserRelation1->follow($this->Invite->User->id, $this->core['my']['id']);
		}
		return $user;
	}
	
	function __sendInvite($validEmails, $message){
		$emailsSent = array();
		$emailsNotSent = array();
		foreach($validEmails as $emailAddress)
		{
			$user = $this->__createInvitedUser($emailAddress);

			//debug($parameters);				
			//debug($user);				
			//debug($saveUserRelationsSuccess);				

			//reference at http://bakery.cakephp.org/articles/view/brief-overview-of-the-new-emailcomponent
			$this->Email->to = $emailAddress;
			$this->Email->subject = $this->core['my']['fullname'].' invited you to Thinkpanda';
			$this->Email->replyTo = $this->core['my']['email'];
			$this->Email->from = $this->core['my']['fullname'].'<'.$this->core['my']['email'].'>';
			$this->Email->sendAs = 'html'; //Send as 'html', 'text' or 'both' (default is 'text') 
			
			//Set the body of the mail as we send it.
			//Note: the text can be an array, each element will appear as a seperate line in the message body.
			//also we can use templates
			$this->Email->template = 'invite_contacts'; //using the template in views/elements/email/html/.ctp
			$this->set('message', $message); 
			$this->set('name', $this->core['my']['fullname']);
			$this->set('myId', $this->core['my']['id']);
			$this->set('signupCode', $user['User']['signupCode']);
			
			if($this->Email->send())
				$emailsSent[] = $emailAddress;
			else
				$emailsNotSent[] = $emailAddress;
		}	
		$invite['sent'] = $emailsSent;
		$invite['notsent'] = $emailsNotSent;
		return $invite;
	}	

	function topInviter(){
		$q = 'SELECT COUNT(users.creation_user_id), Inviter.* FROM `users`
JOIN users AS Inviter ON(Inviter.id = users.creation_user_id)
 WHERE users.created > DATE_SUB(CURDATE(),INTERVAL 8 DAY) GROUP BY users.creation_user_id  ORDER BY COUNT(users.creation_user_id) DESC';
	}

	function __getUserServices()
	{
		return $this->Invite->User->UsersService->__getServices($this->core['my']['id']);
	}
	
}
?>