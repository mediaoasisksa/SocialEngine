<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    index.tpl 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<form method="get" action="<?php echo $this->escape($this->url(array(), 'default', true)) ?>"
      class="global_form" enctype="application/x-www-form-urlencoded">
    <div>
        <div>

            <?php if ($this->status == 'pending'): ?>

                <h3>
                    <?php echo $this->translate('Payment Pending') ?>
                </h3>
                <p class="form-description">
                    <?php
                    echo $this->translate('Thank you for submitting your ' .
                            'payment. Your payment is currently pending - your account ' .
                            'will be activated when we are notified that the payment has ' .
                            'completed successfully. Please return to our login page ' .
                            'when you receive an email notifying you that the payment ' .
                            'has completed.')
                    ?>
                </p>


                <?php elseif ($this->status == 'active'): ?>

                <h3>
                    <?php echo $this->translate('Payment Complete') ?>
                </h3>
                <p class="form-description">
    <?php echo $this->translate('Thank you! Your payment has ' .
            'completed successfully.')
    ?>
                </p>

                <?php elseif ($this->status == 'unauthorized'): ?>

                <h3>
                    <?php echo $this->translate('User is unauthorized') ?>
                </h3>
                <p class="form-description">
    <?php echo $this->translate('Please try again') ?>
                </p>
                <?php elseif ($this->status == 'free'): ?>

                <h3>
                    <?php echo $this->translate('Free Subsciption Enabled') ?>
                </h3>
                <p class="form-description">
    <?php echo $this->translate('Your have successfully enabled your free subscription.') ?>
                </p>
                <?php elseif ($this->status == 'verified'): ?>

                <h3>
                    <?php echo $this->translate('Account Verification') ?>
                </h3>
                <p class="form-description">
                <?php echo $this->translate('Please make sure your account has been verified.') ?>
                </p>

                <?php else: //if( $this->status == 'failed' ): ?>

                <h3>
                    <?php echo $this->translate('Payment Failed') ?>
                </h3>
                <p class="form-description">
                    <?php if (empty($this->error)): ?>
                        <?php
                        echo $this->translate('Our payment processor has notified ' .
                                'us that your payment could not be completed successfully. ' .
                                'We suggest that you try again with another credit card ' .
                                'or funding source.')
                        ?>
    <?php else: ?>
                                <?php echo $this->translate($this->error) ?>
                            <?php endif; ?>

<?php endif; ?>

        </div>
    </div>
</form>
