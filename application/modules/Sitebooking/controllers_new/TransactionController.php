<?php

require_once("application/modules/Classroom/controllers/create.php"); 
require_once("application/modules/Classroom/controllers/AddAttendee.php"); 
class Sitebooking_TransactionController extends Core_Controller_Action_Standard
{ 
	protected $_user;
	protected $_session;
	protected $_order;

	public function init()
	{
		// Get user and session
		$this -> _user = Engine_Api::_() -> user() -> getViewer();
		$this -> _session = new Zend_Session_Namespace('Sitebooking_PayPackage');

		// Check viewer and user
		if (!$this -> _user || !$this -> _user -> getIdentity())
		{
			if ($this -> _session -> __isset('user_id'))
			{
				$this -> _user = Engine_Api::_() -> getItem('user', $this -> _session -> user_id);
			}
			// If no user, redirect to home?
			if (!$this -> _user || !$this -> _user -> getIdentity())
			{
				return $this -> _redirector();
			}
		}
		$this -> _session -> user_id = $this -> _user -> getIdentity();
		// Get Credit order
		$order_id = $this -> _getParam('order_id', $this -> _session -> order_id);
		$params = $this -> _getAllParams();
		if($params['action'] != 'finish')
		{
			if ($order_id)
			{
				$this -> _order = Engine_Api::_() -> getDbTable('orders', 'sitebooking') -> findRow($order_id);
			}
			else
			{
				return $this -> _redirector();
			}
	
			// If no product or product is empty, redirect to home?
			if (!$this -> _order || !$this -> _order -> getIdentity())
			{
				return $this -> _redirector();
			}
			$this -> _session -> __set('order_id', $this -> _order -> getIdentity());
		}
	}
	public function indexAction()
	{
		return $this -> _helper -> redirector -> gotoRoute(array('action' => 'process'), 'sitebooking_transaction', true);
	}
	public function processAction()
	{
		$api = Engine_Api::_() -> sitebooking();
		$gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'sitebooking');
		$gatewaySelect = $gatewayTable -> select() -> where('enabled = ?', 1) -> where('gateway_id = ?', $this -> _order -> gateway_id);
		if (null == ($gateway = $gatewayTable -> fetchRow($gatewaySelect)))
		{
			$this -> _redirector();
		}
		
		// Prepare host info
		$schema = 'http://';
		if (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]))
		{
			$schema = 'https://';
		}
		$host = $_SERVER['HTTP_HOST'];

		// Prepare transaction
		$params = array();
		$params['language'] = $this -> _user -> language;
		$localeParts = explode('_', $this -> _user -> language);
		if (count($localeParts) > 1)
		{
			$params['region'] = $localeParts[1];
		}
		$params['vendor_order_id'] = $this -> _order -> getIdentity();
		$params['return_url'] = $schema . $host . $this -> view -> url(array('action' => 'return')) . '?order_id=' . $params['vendor_order_id'] . '&state=' . 'return';
		$params['cancel_url'] = $schema . $host . $this -> view -> url(array('action' => 'return')) . '?order_id=' . $params['vendor_order_id'] . '&state=' . 'cancel';
		$params['ipn_url'] = $schema . $host . $this -> view -> url(array(
			'action' => 'index',
			'controller' => 'ipn'
		)) . '?order_id=' . $params['vendor_order_id'] . '&state=' . 'ipn';
		$gatewayPlugin = $api -> getGateway($this -> _order -> gateway_id);
		$plugin = $api -> getPlugin($this -> _order -> gateway_id);
		$transaction = $plugin -> createPackageTransaction($this -> _order, $params);
		
		// Pull transaction params
		$this -> view -> transactionUrl = $transactionUrl = $gatewayPlugin -> getGatewayUrl();
		$this -> view -> transactionMethod = $transactionMethod = 'GET';
		$this -> view -> transactionData = $transactionData = $transaction -> getData();
		
		$this -> _session -> lock();
		// Handle redirection
		if ($transactionMethod == 'GET')
		{
			$transactionUrl .= '?' . http_build_query($transactionData);
			return $this -> _helper -> redirector -> gotoUrl($transactionUrl, array('prependBase' => false));
		}
	}
	public function returnAction()
	{
		$params = $this -> _getAllParams();
		// Get order
		if (!$this -> _user 
			|| !($orderId = $this -> _getParam('order_id', 1)) 
			|| !($order = Engine_Api::_() -> getDbTable('orders', 'sitebooking') -> findRow($orderId)) 
			|| !($gateway = Engine_Api::_() -> getItem('payment_gateway', $order -> gateway_id)))
		{
			return $this -> _finishPayment('failed');
		}
		
		try
		{
			if(in_array($gateway -> title, array('2Checkout', 'PayPal')))
			{
				$api = Engine_Api::_() -> sitebooking();
				$plugin = $api -> getPlugin($gateway -> getIdentity());
				$status = $plugin -> onPackageTransactionReturn($order, $this -> _getAllParams());
			}
			else
			{
				$status = $order -> onPackageTransactionReturn($this -> _getAllParams());
			}
		}
		catch( Payment_Model_Exception $e )
		{
			$status = 'failed';
			$this -> _session -> __set('errorMessage', $e -> getMessage());
		}
		
		return $this -> _helper -> redirector -> gotoRoute(array(
			'action' => 'finish',
			'state' => $status,
			'order_id' => $order->getIdentity(),
		), 'sitebooking_transaction', true);
	}

	public function finishAction()
	{
		$this -> view -> status = $status = $this -> _getParam('state');
		$this -> view -> order = $order = Engine_Api::_() -> getItem('sitebooking_order', $this->_getParam('order_id'));
		
		$product = Engine_Api::_() -> getItem('sitebooking_servicebooking', $order -> source_id);
		if ($status == 'completed')
		{
		    
		  //  $viewer = $this -> _user;
		  //  $pro_id = $product->pro_id;
		    
		  //  $duration = json_decode($product->duration, true);
		    
		    
		  //  try {
		        
            
    //             foreach( $duration as $key => $kd) {
    //             	$val = $key;
                	
    //             	$datetime = explode(",",$kd);
    //             	foreach($datetime as $d) {
    //             		$b = explode("-", $d);
                		
    //             		$start = strtotime($val .' '. $b[0]);
    //             		$end = strtotime($val .' '. $b[1]);
                		
                		
    //             		$starttime = $key . ' ' . $b[0];
                		
    //             		$pros = Engine_Api::_() -> getItem('sitebooking_pro', $pro_id);
    //         		    $values = array();
    //         		    $values['parent_type'] = 'classroom';
    //                     $values['parent_id'] = $pros->parent_id;
    //                     $classroom = Engine_Api::_() -> getItem('classroom', $pro_id);
    //                     $secretAcessKey = '1ZJxaO3FB95iq18o9tfJiQ==';
    //                     $access_key = 'vNBHxdarp4o=';
    //                     $webServiceUrl="http://classapi.wiziqxt.com/apimanager.ashx";
    //                     $date = new DateTime($starttime, new DateTimeZone('GMT'));
    //                     $date->setTimezone(new DateTimeZone(Engine_Api::_() ->getItem('user', $order->user_id)->timezone));
    //                     $time= $date->format('Y-m-d H:i:s');
    //                     $values['starttime'] = $time;
    //                     $values['user_id'] = $order->user_id;
    //                     $values['name'] = Engine_Api::_() ->getItem('user', $pros->owner_id)->getTitle();
    //                     $values['duration'] = $order->duration;
    //                     $values['timezone'] = $order->timezone;
    //                     $values['title'] = Engine_Api::_() ->getItem('user', $order->user_id)->getTitle(); // Need to send service title
    //                     $obj=new ScheduleClass($secretAcessKey,$access_key,$webServiceUrl, $values, $classroom);
    //                     $values['class_id'] = $obj->object['class_id'];
    //                     $values['class_master_id'] = $obj->object['class_master_id'];
    //                     $values['set_guest_privacy'] = 2;
    //                     $values['attendee_limit'] = 1;
    //                     $values['auth_view'] = 'everyone';
    //                     $values['auth_comment'] = 'everyone';
    //                     $values['view_privacy'] =  $values['auth_view'];
    //                     $table = Engine_Api::_()->getDbtable('classrooms', 'classroom');
    //                     $classroom = $table->createRow();
    //                     $classroom->setFromArray($values);
    //                     $classroom->save();
    //                     $values['id'] = $viewer->getIdentity();
    //                     $obj=new AddAttendee($secretAcessKey,$access_key,$webServiceUrl, $values);	
                         
    //                     $db = Engine_Api::_()->getDbtable('attendee', 'classroom')->getAdapter();
    //                     $db->beginTransaction();
    //                         try { 
                              
    //                           $values['attendee_master_id'] = $obj->object['attendee_id'];
    //                           $values['attendee_url'] = $obj->object['attendee_url'];
    //                           $values['class_id'] = $classroom->class_id;
    //                           $values['add_attendeesStatus'] = $obj->object['add_attendeesStatus'];
    //                           $values['user_id'] = $order->user_id;
    //                           $table = Engine_Api::_()->getDbtable('attendee', 'classroom');
    //                           $attendee = $table->createRow();
    //                           $attendee->setFromArray($values);
    //                           $attendee->save();
                    
    //                          $db->commit();
                    
    //                       // Redirect
    //                     } catch( Exception $e ) {
    //                 }
    //         	}
            
    //             }
		  //  } catch( Exception $e ) {
    //             print_r($e->getMessage());die;
    //         }
		    
		    
	        $url = $this -> view -> escape($this -> view -> url(( array()), 'sitebooking_service_browse', true));		}
		else
		{
			//redirect to homepage base on type
			$url = $this -> view -> escape($this -> view -> url(( array()), 'sitebooking_service_browse', true));
			$this -> view -> error = $this -> _session -> errorMessage;
		}
		
		$pros = Engine_Api::_() -> getItem('sitebooking_pro', $product -> pro_id);
	//	$classroom = Engine_Api::_() -> getItem('classroom', $pros -> parent_id);
		
		$this -> view -> continue_url = $url;
		//$this -> view -> classroom_url = $classroom->getHref();
		$this -> _session -> unsetAll();
	}
	protected function _redirector()
	{
		$this -> _session -> unsetAll();
		 $this->_forward('success' ,'utility', 'core', array(
	      'parentRedirect' => Zend_Controller_Front::getInstance()
	        ->getRouter()
	        ->assemble(
	          array(),
	          'sitebooking_service_browse', true
	        ),
	      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Error!'))
	    ));
	}

}
