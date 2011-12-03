<?php
class FeedsController extends FeedsAppController {

	var $name = 'Feeds';
	var $components = array('Simplepie');
	var $helpers = array('Html', 'Form', 'Javascript');
	//var $components = array('Email'); 
	
	var $uses = array(); // Doesn't have a model
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('__getArticles', 'articles');
		$widget_id = 5; // Every widget must set their Widget.id	
		$widget = $this->getWidget($widget_id);
		
		$widget['types'] = 9; // RSS
			
		$this->core['widget'] = $widget;
		//debug("hello");
		$this->pageTitle = $widget['widget'];
    }
	
	function view(){
		$this->loadModel('Comment');

		$options = $this->setOptions($this->params['url']);

		if(empty($options['type_id'])){
			$options['type_id'] = $this->core['widget']['types']; // csv
		}
		
		$feeds = $this->Comment->get($this->core, $options);


		if (!empty($feeds)){
			$articles = $this->__getArticles($feeds);
		}else{
			$articles = NULL;
		}
	
		$this->set(compact('feeds', 'articles'));
	}
	
	function add(){
		$this->loadModel('Comment');
		$this->loadModel('Page');
		if(isset($this->data)){
			define("FIELD_NAME_URL", 'url');
			define("FIELD_NAME_WEBSITE_TITLE", 'websiteTitle');
			define("FIELD_NAME_FAVICON", 'favicon');
			
			 // Because we know there is only one result as there is only one type
			$types = $this->__getTypes();
			$this->data['Type'] = $types['Type'];
			$this->data['Field'][FIELD_NAME_URL][0] = $this->Page->urlResolve($this->data['Field'][FIELD_NAME_URL][0]);
			$this->data['Field'][FIELD_NAME_FAVICON][0] = $this->Page->getFavicon($this->data['Field'][FIELD_NAME_URL][0]);
			 
			//debug($this->data);
			// ADD
			$options = $this->setOptions($this->params['form']);
			$data = array();
			$data['feeds'] = $this->Comment->add($this->data, $this->core, $options);
			$data['articles']  = $this->__getArticles($data['feeds']);
			$this->set(compact('data'));	
		}else{
			$message = "You forgot to enter the form!";
			$this->set(compact('message'));
		}		
		$this->layout = 'ajax';
		$this->render('/elements/thoughts'); 
	}
	
		
	function edit(){
	
	}
	
	function articles(){
		//debug( $this->params);
		$feedsList = $this->params['feeds'];
		if (!empty($feedsList)){
			$articles = $this->__getArticles($feedsList);
			//debug($articles);
			//$this->set(compact('articles'));
			return $articles;
		}else{
			$message = "Could not retrieve articles.";
			//$this->set(compact('message'));
			return $message;
		}
		//$this->layout = 'ajax';
		//$this->render('/elements/feedArticles'); 
	}
	
	function __getArticles($feedsList){
		$this->loadModel('Page');
		$rssURLs = array();
		foreach($feedsList as $feed)
		{
			$thought_id = $feed['Comment']['id'];
			$rssURLs[$thought_id] = array('source' => $feed['Content']['url'][0], 'id' => $thought_id);
		}
		//$articlesList = $this->Simplepie->feed($rssURLs);
		//debug($articlesList);
		//return $articlesList;
		$articlesList = array();
		foreach ($rssURLs as $feedId => $rssURL)
		{
			$itemList = array();
			$items = array();
			$items = $this->Simplepie->feed($rssURL['source']);
			
			//$this->Simplepie->set_feed_url($rss);
			//$this->Simplepie->get_items(0,15)
			if($items!= NULL){
				foreach($items as $item)
				{
					$itemInfo = array(
						'feed_id' => $rssURL['id'],
						'title' => strip_tags($item->get_title()),
						'permalink' => $item->get_permalink(),
						'description' => strip_tags($item->get_description()),
						'copyright' => $item->get_copyright(),
						'date' => $item->get_date('d M h:i'),
						'link' => $item->get_link(0),
						'author' => $item->get_author(),
						'category' => $item->get_category(),
						//'content' => $item->get_content(),
						'contributor' => $item->get_contributor(),
						'id' => $item->get_id(),
						'local_date' => $item->get_local_date(),
						'source' => $item->get_source(),
						'favicon' => $this->Page->getFavicon($item->get_permalink())
					);
					$itemList[] = $itemInfo;
				}
			}

			$articlesList[$feedId] = $itemList;
		}
		//debug($this->Simplepie);
		//debug($articlesList);
		return $articlesList;
	}
	
	function __mergeRssFeedArticles($feed, $articles){
		//debug($feed);
		//debug($articles);
		
		$mergedList = array(
			'feed' => $feed,
			'articles' => $articles
		);
		
		
		return $mergedList;
	}	
	
	function __getTypes(){
			// type_id = 9 = RSS
		if(empty($this->core['widget']['types-data'])){		
			$this->loadModel('Type');
			$this->Type->recursive = -1;
			
			$widget['types-data'] = $this->Type->find('first', array(
				'fields' => array('Type.id, Type.type, Type.class'), 
				'conditions' => ('Type.id IN ('.$this->core['widget']['types'].')')
			));	
			
			$this->core['widget']['types-data'] = $widget['types-data'];	
		}
		return $this->core['widget']['types-data'];
	}		
}
?>