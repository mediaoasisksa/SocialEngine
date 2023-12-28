<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: finish.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John Boehr <j@webligo.com>
 */
?>
<?php
    
if(isset($_GET['resourcePath'])) {
    function request2() {
    
    $card = Zend_Controller_Front::getInstance()->getRequest()->getParam('card', null);
    $entityid = "8ac7a4ca87ba6ed60187bc9d8a41023c";
    if($card == "mada") {
        $entityid = "8ac7a4ca87ba6ed60187bc9e08290240";
    }
        
        	$url = "https://eu-test.oppwa.com/";
        $r = $url . $_GET['resourcePath'];
	$url = $r;
	$url .= "?entityId=$entityid";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	   "Content-Type: application/json",

                   'Authorization:Bearer OGFjN2E0Y2E4N2JhNmVkNjAxODdiYzljZGMwNjAyMzh8clJOcUtBUXdjOQ=='));
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$responseData2 = curl_exec($ch); 

	if(curl_errno($ch)) {
		return curl_error($ch);
	}
	curl_close($ch);
	$a =  json_decode($responseData2, true);
	return $a;
}
$responseData  = request2();
}
?>
<form method="get" action="<?php echo Engine_Api::_()->user()->getViewer()->getHref();?>"
      class="global_form" enctype="application/x-www-form-urlencoded">
  <div>
    <div>

      <?php if( $this->status == 'pending' ): ?>

        <h3>
          <?php echo $this->translate('Payment Pending') ?>
        </h3>
        <p class="form-description">
            
         <?php if($this->order->params):?>
         <?php $data = json_decode($this->order->params, true);
         $data['sadadNumber'] = $data['Status']['description']; 
         ?>
          <?php echo $this->translate('Thank you for submitting your ' .
              'payment. Your payment is currently pending - your subscription ' .
              'will be activated when we are notified that the payment has ' .
              'completed successfully. Please transfer the 80 SAR into the SADAD Account with the SADAD NUMBER: <b>' . $data['sadadNumber'] . '</b>' ) ?>
         <?php else:?>
          <?php echo $this->translate('Thank you for submitting your ' .
              'payment. Your payment is currently pending - your account ' .
              'will be activated when we are notified that the payment has ' .
              'completed successfully. Please return to our login page ' .
              'when you receive an email notifying you that the payment ' .
              'has completed.') ?>
         <?php endif;?>
         
        </p>
        <div class="form-elements">
          <div id="buttons-wrapper" class="form-wrapper">
            <button type="submit">
              <?php echo $this->translate('Back to Home') ?>
            </button>
          </div>
        </div>

     <?php elseif( 1 ): ?>
    
    <?php
    if( $this->order ){
       Engine_Api::_()->getDbtable('servicebookings','sitebooking')->approve($this->order);
    }
    ?>

        <h3>
          <?php echo $this->translate('Payment Complete') ?>
        </h3>
        <p class="form-description">
          <?php echo $this->translate($responseData['result']['description']) ?>
        </p>
        <div class="form-elements">
          <div id="buttons-wrapper" class="form-wrapper">
            <button type="submit">
              <?php echo $this->translate('Continue') ?>
            </button>
          </div>
        </div>

      <?php else: //if( $this->status == 'failed' ): ?>

        <h3>
          <?php echo $this->translate('Payment Failed') ?>
        </h3>
        <p class="form-description">
          <?php if( empty($this->error) ): ?>
            <?php echo $this->translate('Our payment processor has notified ' .
                'us that your payment could not be completed successfully. ' .
                'We suggest that you try again with another credit card ' .
                'or funding source.') ?>
            <?php else: ?>
              <?php echo $this->translate($this->error) ?>
            <?php endif; ?>
        </p>
        <div class="form-elements">
          <div id="buttons-wrapper" class="form-wrapper">
            <button type="submit">
              <?php echo $this->translate('Back to Home') ?>
            </button>
          </div>
        </div>

      <?php endif; ?>

    </div>
  </div>
</form>
