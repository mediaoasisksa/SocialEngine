<?php

class Sitecourse_PaymentController extends Core_Controller_Action_Standard {

    protected $_user;
    protected $_session;
    protected $_order;
    protected $_gateway;
    protected $_subscription;
    protected $_user_order;
    protected $_success;
    protected $_course_gateway_id;

    public function init() {

        // Get user and session
        $this->_session = new Zend_Session_Namespace('Payment_Sitecourse');
        $this->_success = new Zend_Session_Namespace('Payment_Success');

        // Check viewer and user
        if (!$this->_user_order) {
            if (!empty($this->_session->user_order_id)) {
                $this->_user_order = Engine_Api::_()->getItem('sitecourse_order', $this->_session->user_order_id);
            }
        }
    }

    public function processAction() {
        if (!$this->_user_order) {
            $this->_session->unsetAll();

            return $this->_helper->redirector->gotoRoute(array(), 'sitecourse_general', true);
        }
        if (!empty($this->_session->checkout_course_id)) {
            $course_id = $this->_session->checkout_course_id;
            // Payment Gateway
            $plugin = 'Payment_Plugin_Gateway_PayPal';
            $course_gateway_id = Engine_Api::_()->getDbtable('gateways', 'sitecourse')->getPayPalGatewayId($course_id, $plugin);
            if (empty($course_gateway_id)) {
                return $this->_helper->redirector->gotoRoute(array(), 'sitecourse_general', true);
            } else {
                $this->_course_gateway_id = $course_gateway_id;
            }
        }
        // order id
        $parent_order_id = $this->_session->user_order_id;
        
        // Get order
        if (!$parent_order_id ||
            !($user_order = Engine_Api::_()->getItem('sitecourse_order', $parent_order_id))) {
            return $this->_helper->redirector->gotoRoute(array(), 'sitecourse_general', true);
        }

        // Process
        $ordersTable = Engine_Api::_()->getDbtable('orders', 'payment');
        if (!empty($this->_session->order_id)) {
            $previousOrder = $ordersTable->find($this->_session->order_id)->current();
            if ($previousOrder && $previousOrder->state == 'pending') {
                $previousOrder->state = 'incomplete';
                $previousOrder->save();
            }
        }

        $sourceType = 'sitecourse_order';
        $sourceId = $parent_order_id;
        $gateway_id = $user_order->gateway_id;

        // Create order
        $ordersTable->insert(array(
            'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
            'gateway_id' => $gateway_id,
            'state' => 'pending',
            'creation_date' => new Zend_Db_Expr('NOW()'),
            'source_type' => $sourceType,
            'source_id' => $sourceId,
        ));
        $this->_session->order_id = $order_id = $ordersTable->getAdapter()->lastInsertId();

        // correct this unneceesary setup letter
        $gateway = Engine_Api::_()->getItem('sitecourse_gateway',$course_gateway_id);

        // Get gateway plugin
        $this->view->gatewayPlugin = $gatewayPlugin = $gateway->getGateway();
        $plugin = $gateway->getPlugin();

        // Prepare host info
        $schema = 'http://';
        if (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) {
            $schema = 'https://';
        }
        $host = $_SERVER['HTTP_HOST'];

        // Prepare transaction
        $params = array();
        // $params['language'] = $this->_user->language;
        $params['vendor_order_id'] = $order_id;
        $params['return_url'] = $schema . $host
        . $this->view->url(array('action' => 'return', 'controller' => 'payment', 'module' => 'sitecourse'), 'default')
        . '?order_id=' . $order_id
        . '&state=' . 'return';
        $params['cancel_url'] = $schema . $host
        . $this->view->url(array('action' => 'return', 'controller' => 'payment', 'module' => 'sitecourse'), 'default')
        . '?order_id=' . $order_id
        . '&state=' . 'cancel';
        $params['ipn_url'] = $schema . $host
        . $this->view->url(array('action' => 'index', 'controller' => 'ipn', 'module' => 'payment'), 'default')
        . '?order_id=' . $order_id;

        if (!empty($course_id)) {

            $params['course_id'] = $course_id;

            $params['return_url'] .= '&course_id=' . $course_id . '&course_gateway_id=' . $course_gateway_id;
            $params['cancel_url'] .= '&course_id=' . $course_id . '&course_gateway_id=' . $course_gateway_id;
            $params['ipn_url'] .= '&course_id=' . $course_id . '&course_gateway_id=' . $course_gateway_id;
        }

        $params['source_type'] = $sourceType;
        $currentCurrency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $supportedCurrency = $gatewayPlugin->getSupportedCurrencies();
        $isSupported = true;
        if (!in_array($currentCurrency, $supportedCurrency)) {
            $isSupported = false;
        }

        $this->view->isSupported = $isSupported;
        $isAllowedAmount = true;
        $this->view->isAllowedAmount = $isAllowedAmount;
       
        if ($isSupported && $isAllowedAmount) {
            // Process transaction
            $transaction = $plugin->createUserOrderTransaction($parent_order_id, $params, $this->_user);
            $this->view->transactionUrl = $transactionUrl = $gatewayPlugin->getGatewayUrl();
            $this->view->transactionMethod = $transactionMethod = $gatewayPlugin->getGatewayMethod();
            $this->view->transactionData = $transactionData = $transaction->getData();

            unset($this->_session->user_order_id);
            $this->view->transactionMethod = $transactionMethod;
            // Handle redirection
            if ($transactionMethod == 'GET' && !Engine_Api::_()->seaocore()->isSiteMobileModeEnabled()) {
                if (isset($transaction->mangoPayRedirectUrl) && strlen($transaction->mangoPayRedirectUrl) > 0) {
                    $transactionUrl = $transaction->mangoPayRedirectUrl;
                } elseif (isset($transaction->payKey)) {
                    $transactionUrl .= $transaction->payKey;
                    $this->_session->payKey = $transaction->payKey;
                } else {
                    $transactionUrl .= '?' . http_build_query($transactionData);
                }
                return $this->_helper->redirector->gotoUrl($transactionUrl, array('prependBase' => false));
            }
        }
    }

    public function returnAction() {

        // Get order
        if (!($orderId = $this->_getParam('order_id', $this->_session->order_id)) ||
            !($order = Engine_Api::_()->getItem('payment_order', $orderId)) ||
            ($order->source_type != 'sitecourse_order') ||
            !($user_order = $order->getSource())) {
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        }

        $course_gateway_id = $this->_getParam('course_gateway_id');
        $gateway = Engine_Api::_()->getItem('sitecourse_gateway',$course_gateway_id);

        if (!$gateway) {
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        }

        // Get gateway plugin
        $plugin = $gateway->getPlugin();

        unset($this->_session->errorMessage);

        try {
            $params = $this->_getAllParams();
            if (isset($this->_session->payKey) && strlen($this->_session->payKey) > 0) {
                $params['payKey'] = $this->_session->payKey;
            }
            $status = $plugin->onUserOrderTransactionReturn($order, $params);
        } catch (Payment_Model_Exception $e) {
            $status = 'failure';
            $this->_session->errorMessage = $e->getMessage();
        }

        $this->_success->succes_id = $user_order->order_id;

        return $this->_finishPayment($status);
    }

    protected function _finishPayment($state = 'active') {

        // Clear session
        $errorMessage = $this->_session->errorMessage;
        $this->_session->errorMessage = $errorMessage;
        $course_id = $this->_session->checkout_course_id;
        // Redirect
        if ($state == 'free') {
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        } else {
            return $this->_helper->redirector->gotoRoute(array('action' => 'finish', 'state' => $state, 'course_id' => $course_id));
        }
    }

    public function finishAction() {
        $session = new Zend_Session_Namespace('Sitecourse_Order_Payment_Detail');

        if (!empty($session->sitecourseOrderPaymentDetail)) {
            $session->sitecourseOrderPaymentDetail = '';
        }

        $paymentDetail = array('success_id' => $this->_success->succes_id, 'state' => $this->_getParam('state'), 'errorMessage' => $this->_session->errorMessage);
        $course_id = $this->_session->checkout_course_id;
        $session->sitecourseOrderPaymentDetail = $paymentDetail;

        if(!$course_id) {
        	return $this->_helper->redirector->gotoRoute(array(), 'sitecourse_general', true);            
        }

        // $this->_session->unsetAll();
        return $this->_helper->redirector->gotoRoute(array('action' => 'success', 'course_id' => $course_id), 'sitecourse_order', false);
    }

}
