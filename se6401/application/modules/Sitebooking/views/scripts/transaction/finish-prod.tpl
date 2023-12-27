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
    $entityid = "8acda4c975da8cbe0175fabbdbcb1d65";
    if($card == "mada") {
        $entityid = "8acda4c975da8cbe0175fabe419a1df9";
    }
    
        $url = "https://eu-prod.oppwa.com/";
        $r = $url . $_GET['resourcePath'];
	$url = $r;
	$url .= "?entityId=$entityid";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                   'Authorization:Bearer OGFjZGE0Yzk3NWRhOGNiZTAxNzVmYWJhZWEzZTFkMDZ8a000c1lERGt5RA=='));
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);// this should be set to true in production
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$responseData = curl_exec($ch);
	if(curl_errno($ch)) {
		return curl_error($ch);
	}
	curl_close($ch);
	return $responseData;
}
$responseData = json_decode(request2(), true);
}
?>
<form method="get" action=action="<?php echo Engine_Api::_()->user()->getViewer()->getHref();?>"
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
