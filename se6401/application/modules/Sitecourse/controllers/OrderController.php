<?php
class Sitecourse_OrderController extends Seaocore_Controller_Action_Standard {

	/**
	 * 
	 * @param {int} course id
	 * @return {boolean} PAYPAL PAYMENT GATWAY IS ENABLED
	 * 
	 */
	private function isPayPalGatwayEnabled($course_id) {
    	// Payment_Plugin_Gateway_PayPal Admin Panel
		$gateway_table = Engine_Api::_()->getDbtable('gateways', 'payment');
		$enable_gateway = $gateway_table->select()
		->from($gateway_table->info('name'), array('gateway_id', 'title', 'plugin', 'config'))
		->where('enabled = 1')
		->where('plugin in (?)', array('Payment_Plugin_Gateway_PayPal'))
		->query()
		->fetch();
        // Payment_Plugin_Gateway_PayPal Not Enabled
		if(empty($enable_gateway)) {
			return false;
		}

		// sitecourse_gatway table 
		$sitecourse_gateway_table = Engine_Api::_()->getDbtable('gateways','sitecourse');
		$isPayPalEnable = $sitecourse_gateway_table->isPayPalGatewayEnable($course_id);
        // Payment_Plugin_Gateway_PayPal Not Enabled By Seller
		if(!$isPayPalEnable) {
			return false;
		}

		return true;
	}

	public function canBuyCourse() {
		$course_id = $this->_getParam('course_id',null);
		$course = Engine_Api::_()->getItem('sitecourse_course', $course_id);
		$checks = new Sitecourse_Api_Checks();
		if(!$checks->_canBuyCourse($course_id)) {
			return $this->_helper->redirector->gotoRoute(array('action' => 'profile','url'=>$course['url']), 'sitecourse_entry_profile', true);
		}
	}

	public function init() {
        //LOGGED IN USER VALIDATON
		if (!$this->_helper->requireUser()->isValid()) {
			return;
		}
	}

	public function buyerDetailsAction() {
		$this->canBuyCourse();
        // GET VIEWER
		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
		$course_id = $this->_getParam('course_id',0);
		if (!$this->getRequest()->isPost() || empty($_POST)) {
			return $this->_helper->redirector->gotoRoute(array('action' => 'profile','course_id'=>$course_id), 'sitecourse_specific', true);
		}
		$this->view->course_id = $course_id;
        // set session
		$session = new Zend_Session_Namespace('sitecourse_cart_formvalues');
		$session->formValues = $this->view->formValues = $_POST;    
	}

	public function checkoutAction() {
		$this->canBuyCourse();
		$session = new Zend_Session_Namespace('sitecourse_cart_formvalues');

        // no session data i.e. invalid request
		if(empty($session) || empty($session->formValues['course'])) {
			return $this->_helper->redirector->gotoRoute(array('action' => 'index'), 'sitecourse_general', true);
		}

        // get course id from session data
		$formValues = $session->formValues;
		$this->view->course_id = $course_id = $formValues['course'];

        // session available but not the course id
		if(empty($course_id)) {
			return $this->_helper->redirector->gotoRoute(array('action' => 'index'), 'sitecourse_general', true);
		}

        // check post
		if (!$this->getRequest()->isPost()) {
			return $this->_helper->redirector->gotoRoute(array('action' => 'index'), 'sitecourse_general', true);
		}

        // payment gatway status
		$this->view->no_payment_gateway_enabled = !($this->isPayPalGatwayEnabled($course_id));

        // MANAGE COMPLETE CHECKOUT PROCESS
		$checkout_process = array();
		$viewer = Engine_Api::_()->user()->getViewer();
		$buyerDetails = array('buyer_id'=>$viewer->getIdentity(),'buyer_name'=>$viewer->getTitle());
		if (isset($session->formValues)) {
            /* BUYER DETAIL CASE - FORMVALUES
                             * Array having cart details in **$session->formValues** & Buyer Details in **viewer object**
                             * merged in formValues
            */

            $this->view->formValues = $values = array_merge($session->formValues, $buyerDetails);
            $session->sitecourse_buyer_details = $buyerDetails;
        }
        else{
        	return;
        }
        $course = Engine_Api::_()->getItem('sitecourse_course',$course_id);
        $this->view->price = $totalPrice = $course['price'];
        $this->view->grandTotal = $grandTotal = $course['price'];
        $totalOrderPriceFree = false;        
        if($totalPrice == 0 ) {
        	$totalOrderPriceFree = true;
        }
        $this->view->totalOrderPriceFree = $totalOrderPriceFree;      

    }


    public function placeOrderAction() {
    	/**
    	 * @todo check -> session is available
    	 * @todo check -> user is logged in
    	 * @todo check -> permission to buy and owner
    	 */
    	if(empty($_POST)) {
    		return;
    	}
    	$this->canBuyCourse();

        // GET VIEWER
    	$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    	$checkout_process = @unserialize($_POST['checkout_process']);

    	if(!empty($_POST['param'])) {
    		$checkout_process['payment_method'] = $_POST['param'];
    	}

    	// empty form values
    	if(!isset($_POST['formValues'])) {
    		$this->view->error = 'No Data Found';
    		return;
    	}

    	// form values
    	$formValues = $_POST['formValues'];
    	$course_id = $formValues['course'];
    	$course = Engine_Api::_()->getItem('sitecourse_course',$course_id);

    	// not a valid course
    	if(!$course || !$course->getIdentity()) {
    		$this->view->error = 'No Course Found';
    		return;
    	}

    	$totalPrice = $course['price'];
    	$freeCourse = false;
    	// if free set payment method to 5
    	if($totalPrice == 0) {
    		$freeCourse = true;
    		$checkout_process['payment_method'] = 5;
    	}

    	// check paypal is enabled
    	if(!$freeCourse && !$this->isPayPalGatwayEnabled($course_id)) {
    		$this->view->error = 'PAYPAL PAYMENT GATWAY IS DISABLED';
    		return;
    	}
        // PROCESS
    	$db = Engine_Db_Table::getDefaultAdapter();
    	$db->beginTransaction();
        // GET IP ADDRESS
    	$ipObj = new Engine_IP();
    	$ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));

    	try {
    		try {
    			$order_status = 1;
                //SAVE ORDER TICKETS DETAILS IN TICKET TABLE.
    			$table = Engine_Api::_()->getDbtable('orders', 'sitecourse');
    			$order = $table->createRow();

    			$order->user_id = $viewer_id;    			
    			$order->course_id = $course_id;
    			$order->order_status = $order_status;

    			if ($checkout_process['payment_method'] == 5) {
					$order_status = 2; // PROCESSING
					$payment_status = 'active';
				} else {
					$order_status = 1; // PAYMENT PENDING
					$payment_status = 'initial';
				}

                //status
				$order->order_status = $order_status;
				$order->payment_status = $payment_status;
				$order->creation_date = date('Y-m-d H:i:s');

				$order->gateway_id = $checkout_process['payment_method'];
				$order->ip_address = $ipExpr;
				$order->direct_payment = 1;
				$order->save();
			} catch (Exception $e) {
				throw $e;
			}
			try {
				$orderCourseTable = Engine_Api::_()->getDbtable('ordercourses', 'sitecourse');
				$subtotal = 0;

                //SAVE DETAILS IN ORDER Courses TABLE
				$orderRow = $orderCourseTable->createRow();
				$orderRow->order_id = $order->order_id;
				$orderRow->course_id = $course_id;
				$orderRow->title = $course['title'];
				$orderRow->price = @round($course['price'],2);
				$orderRow->save();
				$subtotal += $course['price'];

                //VALUE OF GRAND TOTAL & ITEM COUNT
				$order->tax_amount = 0;
				$grandtotal = $subtotal + $total_tax;
				$order->sub_total = @round($subtotal, 2);
				$order->grand_total = @round($grandtotal, 2);
				$order->save();
			} catch (Exception $e) {
				throw $e;
			}
            // COMMIT
			$db->commit();
		} catch (Exception $e) {
			$db->rollBack();
			throw $e;
		}

		$this->view->order_id = Engine_Api::_()->sitecourse()->getDecodeToEncode($order->order_id);
		$this->view->gateway_id = $checkout_process['payment_method'];

	}

	public function paymentAction() {
		$this->canBuyCourse();

		$gateway_id = $this->_getParam('gateway_id',null);

		//PAYMENT FLOW CHECK
		$course_id = $this->_getParam('course_id', null);

		if (empty($course_id) || ($gateway_id != 1)) {
			return $this->_forward('notfound', 'error', 'core');
		}

		$order_id = $order_id = (int) Engine_Api::_()->sitecourse()->getEncodeToDecode($this->_getParam('order_id'));
		if (empty($gateway_id) || empty($order_id)) {
			return $this->_forward('notfound', 'error', 'core');
		}

		$this->_session = new Zend_Session_Namespace('Payment_Sitecourse');
		$this->_session->unsetAll();
		$this->_session->user_order_id = $order_id;
		$this->_session->checkout_course_id = $course_id;

		return $this->_forwardCustom('process', 'payment', 'sitecourse', array());
	}

	public function successAction() {
		$this->canBuyCourse();
		$viewer = Engine_Api::_()->user()->getViewer();
		$this->view->viewer_id = $viewer_id = $viewer->getIdentity();

		if ($this->_getParam('success_id')) {
			$order_id = (int) Engine_Api::_()->sitecourse()->getEncodeToDecode($this->_getParam('success_id'));
			$state = $error = '';
		} else {
			$session = new Zend_Session_Namespace('Sitecourse_Order_Payment_Detail');

			if (empty($session->sitecourseOrderPaymentDetail['success_id'])) {
				return $this->_forward('notfound', 'error', 'core');
			}

			$order_id = $session->sitecourseOrderPaymentDetail['success_id'];
			$this->view->state = $state = $session->sitecourseOrderPaymentDetail['state'];

			$this->view->error = $error = $session->sitecourseOrderPaymentDetail['errorMessage'];
		}
		$this->view->order_id = $order_id;
		$this->view->course_id = $this->_getParam('course_id','0');
		// if state is failure
		if($state == 'failure') {
			// if have active session
			if($session) {
				$session->unsetAll();

			}
			return;
		}
		$order_obj = Engine_Api::_()->getItem('sitecourse_order', $order_id);
		$this->view->sitecourse = $sitecourse = Engine_Api::_()->getItem('sitecourse_course', $order_obj->course_id);
		if (empty($order_id) || empty($order_obj) || empty($sitecourse)) {
			return $this->_forward('notfound', 'error', 'core');
		}

		// someone navigated through url-> show error
		if($order_obj->gateway_id == 1 && $order_obj->payment_status == 'initial') {
			return $this->_forward('notfound', 'error', 'core');
		}

		$this->view->course_id = $course_id = $order_obj->course_id;
		// sitecourse order table
		$order_table = Engine_Api::_()->getDbtable('orders', 'sitecourse');

		$success_message = '<b>' . $this->view->translate("Thanks for your purchase!") . '</b><br/><br/>';
		$success_message .= $this->view->translate("Your Order ID is") . ' #'.$order_id.'<br><br>';

		// IF PAYMENT IS SUCCESSFULLY DONE FOR THE ORDER
		if (	$order_obj->payment_status == 'active' ) {
			$order_table->update(array('order_status' => 2), array('order_id = ?' => $order_id));
		}

		$order_table->update(array('payment_status' => $order_obj->payment_status), array('order_id = ?' => $order_id));

        //BUYER DETAIL FORM SAVE
		$session = new Zend_Session_Namespace('sitecourse_cart_formvalues');

		$values = $session->formValues;
        // PROCESS
		$db = Engine_Db_Table::getDefaultAdapter();
		$db->beginTransaction();
		$buyerDetails = Engine_Api::_()->getItem('user',$viewer_id);
		$names = explode(' ',$buyerDetails['displayname']);
		$firstName = $names[0];
		$lastName = $names[1];

		try {
            //SAVE ORDER TICKETS DETAILS IN TICKET TABLE.
			$buyerDetail_table = Engine_Api::_()->getDbtable('buyerdetails', 'sitecourse');
			$this->view->course_id = $course_id = $order_obj->course_id;
			$buyerDetail = $buyerDetail_table->createRow();
			$buyerDetail->course_id = $course_id;
			$buyerDetail->order_id = $order_id;
			$buyerDetail->first_name = $firstName;
			$buyerDetail->last_name = $lastName;
			$buyerDetail->buyer_id = $viewer_id;
			$buyerDetail->email = $buyerDetails['email'];
			$buyerDetail->save();

			// get buyers table
			$buyerdetailTable = Engine_Api::_()->getDbtable('buyerdetails','sitecourse');
			$buyersCount = $buyerdetailTable->courseEnrollementCount($course_id);

			// fetch buyers threshold value
			$bestSellerThreshold = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitecourse.bestseller.threshold", "0");
			// mark course as bestseller course
			if($buyersCount >= $bestSellerThreshold) {
				if(!empty($sitecourse) && !$sitecourse->bestseller) {
					$sitecourse->bestseller = 1;
					$sitecourse->save();
					Engine_Api::_()->sitecourse()->sendMail('BESTSELLER', $course_id);
				}
			}

            // Commit
			$db->commit();
		} catch (Exception $e) {
			$db->rollBack();
			throw $e;
		}

		$owner = Engine_Api::_()->getItem("user" , $sitecourse->owner_id);
		Engine_Api::_()->getDbtable('notifications', 'activity')
		->addNotification($owner,$viewer , $sitecourse, 'sitecourse_purchase',array(
			'object_link' => Engine_Api::_()->getItem("sitecourse_course",$sitecourse->course_id)->getHref(),
			'sender_email'=>Engine_Api::_()->getApi('settings', 'core')->core_mail_from));

        // SUCCESS MESSAGE
		$tempViewUrl = $this->view->url(array('action' => 'index', 'course_id' => $this->view->course_id), 'sitecourse_learning', true);

		$viewer_order = '<a href="' . $tempViewUrl . '">Go to My Course</a>';
		$success_message .= $viewer_order . '. <br><br> ';
		$this->view->success_message = $success_message;
		$session->unsetAll();

		// send email
		Engine_Api::_()->sitecourse()->sendMail('PURCHASE', $course_id);
	}


	public function paymentInfoAction() {

		if( !$this->_helper->requireUser()->isValid() ) return;
		$course_id = $this->_getParam('course_id', 0);
		$viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();
		$course= Engine_Api::_()->getItem('sitecourse_course', $course_id);
        // validate course and course owner
		if( !$course || !$course->getIdentity() || (!$course->isOwner($viewer)) ) {
			return $this->_helper->requireSubject->forward();
		}
		$this->view->images = $getContentImages = Engine_Api::_()->getApi('Core', 'sitecourse')->getContentImage($course);
		
        // paypal details form
		$this->view->form = $form = new Sitecourse_Form_Order_Paypal();
		$this->view->course_id = $course_id;
        // hardcode payment gatway
		$enabledgateway = array(); 
		$enabledgateway['paypal'] = true;
		$this->view->enabledgateway = $enabledgateway;

        // required this code
		if (!empty($enabledgateway['paypal'])) {
			$courseGatewayObj = Engine_Api::_()->getDbtable('gateways', 'sitecourse')->fetchRow(array('course_id = ?' => $course_id, 'plugin LIKE \'Payment_Plugin_Gateway_PayPal\''));
			if (!empty($courseGatewayObj)) {
				$gateway_id = $courseGatewayObj->gateway_id;

				if (!empty($gateway_id)) {
					$this->view->paypalEnable = true;
                    // Get gateway
					$gateway = Engine_Api::_()->getItem("sitecourse_gateway",
						$gateway_id);

                    // Populate form
					$form->populate($gateway->toArray());
					if (is_array($gateway->config)) {
						$form->populate($gateway->config);
					}
				}
			}
		}


		if(!$this->getRequest()->isPost()) {
			return;
		}

		if( !$form->isValid($this->getRequest()->getPost()) ) {
			return;
		}
		$paypalDetails = $form->getValues();
        // IF PAYPAL GATEWAY IS ENABLE, THEN INSERT PAYPAL ENTRY IN ENGINE4_SITECOURSE_GATEWAY TABLE

		$sitecourse_gateway_table = Engine_Api::_()->getDbtable('gateways', 'sitecourse');
		$sitecourse_gateway_table_obj = $sitecourse_gateway_table->fetchRow(array('course_id = ?' => $course_id, 'plugin = \'Payment_Plugin_Gateway_PayPal\''));
		if (!empty($sitecourse_gateway_table_obj)) {
			$gateway_id = $sitecourse_gateway_table_obj->gateway_id;
		} else {
			$gateway_id = 0;
		}

		$success_message = $error_message = false;

		$db = Engine_Db_Table::getDefaultAdapter();
		$db->beginTransaction();

		$paypalEnabled = true;
		$paypalEmail = $paypalDetails['email'];
		$test_mode = $paypalDetails['test_mode'];
		unset($paypalDetails['email']);
		unset($paypalDetails['test_mode']);
        // Process
		try {
			if (empty($gateway_id)) {
				$row = $sitecourse_gateway_table->createRow();

				$row->course_id = $course_id;
				$row->user_id = $viewer_id;
				$row->email = $paypalEmail;
				$row->title = 'Paypal';
				$row->description = '';
				$row->test_mode = $test_mode;
				$row->plugin = 'Payment_Plugin_Gateway_PayPal';
				$row->save();

				$gateway = $row;
			} else {
				$gateway = Engine_Api::_()->getItem("sitecourse_gateway", $gateway_id);
				$gateway->email = $paypalEmail;
				$gateway->test_mode = $test_mode;
				$gateway->save();
			}
			$db->commit();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

        // Validate gateway config
		$gatewayObject = $gateway->getGateway();

		try {

			$gatewayObject->setConfig($paypalDetails);
			$response = $gatewayObject->test();
		} catch (Exception $e) {
			$paypalEnabled = false;
                // $form->populate(array('enabled' => false));
			$error_message = $this->view->translate(sprintf('Gateway login failed. Please double check your connection information. The gateway has been disabled. The message was: [%2$d] %1$s', $e->getMessage(), $e->getCode()));
		}

        // Process
		$message = null;
		try {
			$values = $gateway->getPlugin()->processAdminGatewayForm($paypalDetails);
		} catch (Exception $e) {
			$message = $e->getMessage();
			$values = null;
		}

		if (empty($paypalDetails['username']) || empty($paypalDetails['password']) || empty($paypalDetails['signature'])) {
			$paypalDetails = null;
		}

		if (null !== $paypalDetails) {
			$gateway->setFromArray(array(
				'enabled' => $paypalEnabled,
				'config' => $paypalDetails,
			));
			$gateway->save();
			$eventPaypalId = $gateway->gateway_id;
		} else {
			if (!$error_message) {
				$error_message = $message;
			}
		}

		if(!empty($error_message)) {
			$form->addError($error_message);
		} else {
			$form->addNotice('Changes Saved Successfully');
		}

	}

}

?>
