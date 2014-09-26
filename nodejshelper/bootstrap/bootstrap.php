<?php 

class erLhcoreClassExtensionNodejshelper {
	public function __construct() {
		
	}
	
	private $settings = array();
	
	public function run() {				
		include 'extension/nodejshelper/design/nodejshelpertheme/tpl/pagelayouts/parts/nodejshelper_config.tpl.php';
		$this->settings = $nodeJsHelperSettings;
		
		if ($this->settings['use_publish_notifications'] == true) {	
			include_once 'extension/nodejshelper/vendor/predis-1.0.0/autoload.php';
			$dispatcher = erLhcoreClassChatEventDispatcher::getInstance();	
			
			// On what events should NodeJS listening operators be notified
			$dispatcher->listen('chat.chat_started',array($this,'notifyBackOfficeOperators'));
			$dispatcher->listen('chat.data_changed',array($this,'notifyBackOfficeOperators'));		
			$dispatcher->listen('chat.unread_chat',array($this,'notifyBackOfficeOperatorsDelay'));		
			$dispatcher->listen('chat.data_changed_auto_assign',array($this,'notifyBackOfficeOperators'));		
			$dispatcher->listen('chat.data_changed_assigned_department',array($this,'notifyBackOfficeOperators'));		
			$dispatcher->listen('chat.close',array($this,'notifyBackOfficeOperators'));		
			$dispatcher->listen('chat.delete',array($this,'notifyBackOfficeOperators'));	
			$dispatcher->listen('chat.user_reopened',array($this,'notifyBackOfficeOperators'));	
			$dispatcher->listen('chat.chat_transfer_accepted',array($this,'notifyBackOfficeOperators'));	
			$dispatcher->listen('chat.chat.chat_transfered',array($this,'notifyBackOfficeOperators'));	
			
			// Listed for desktop client events
			$dispatcher->listen('chat.desktop_client_admin_msg',array($this,'notifyUserNewMessage'));		
		}
	}	
	
	public function notifyBackOfficeOperators($params)
	{		
		try {			
			$client = new Predis\Client($this->settings['redis']);			
			$client->publish('admin_room_'.$this->settings['instance_id'],'snow');
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
	
	public function notifyBackOfficeOperatorsDelay($params)
	{		
		try {			
			$client = new Predis\Client($this->settings['redis']);			
			$client->publish('admin_room_'.$this->settings['instance_id'],'sdelay');
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
	
	public function notifyUserNewMessage($params)
	{		
		try {			
			$client = new Predis\Client($this->settings['redis']);			
			$client->publish('chat_room_'.$this->settings['instance_id'].'_'.$params['chat']->id,$params['chat']->id);						
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
}


