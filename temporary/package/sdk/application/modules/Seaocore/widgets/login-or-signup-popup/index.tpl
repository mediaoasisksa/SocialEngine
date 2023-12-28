<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<?php
$this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'externals/mdetect/mdetect' . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.js');
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl
  . 'application/modules/Seaocore/externals/styles/style_login-signup-popup.css');
?>
<div id="seaocore_login_signup_popup" style="display: none;">
  <div class="_headline-poup">
    <?php if( $this->allowClose ): ?>
      <div class="_close_icon" onclick="SmoothboxSEAO.close();">
        <i class="fa fa-times" aria-hidden="true" ></i>
      </div>
    <?php endif; ?>
    <div class=''>
      <ul class="_navigation">
        <?php if( $this->pageIdentity !== 'user-auth-login' ): ?>
          <li class="user_login_form_tab" data-role="seao_user_auth_popup">
            <?php echo $this->translate('Sign In') ?>
          </li>
        <?php endif; ?>
        <?php if( $this->pageIdentity !== 'user-signup-index' ): ?>
          <li class="user_signup_form_tab"  data-role="seao_user_signup_popup">
            <?php echo $this->translate('Create Account') ?>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
  <div class="_form_wapper">
    <?php if( !in_array($this->pageIdentity, array('user-auth-login', 'sitelogin-auth-login')) ) : ?>
      <?php $loginModule = Engine_Api::_()->hasModuleBootstrap('sitelogin') ? 'sitelogin' : 'user'; ?>
      <div class="seao_user_auth_popup _form_cont">
        <?php
        echo $this->action('login', 'auth', $loginModule, array(
          'disableContent' => true,
          'return_url' => '64-' . base64_encode($this->url())
        ));
        ?>
        <?php if( $this->pageIdentity !== 'user-signup-index' ): ?>
          <ul class="_navigation _footer_bottom mtop10 mbot15">
            <li class="user_signup_form_tab"  data-role="seao_user_signup_popup">
              <?php echo $this->translate('Don\'t have an account? Sign Up') ?>
            </li>         
          </ul>
        <?php endif; ?>
      </div>
    <?php endif; ?>
    <?php if( !in_array($this->pageIdentity, array('user-signup-index', 'sitequicksignup-signup-index')) ) : ?>
      <?php
      if( Engine_Api::_()->hasModuleBootstrap('sitelogin') ) :
        Zend_Registry::set('siteloginSignupPopUp', 1);
      endif;
      ?>
      <div class="seao_user_signup_popup _form_cont">
        <?php
        $ifSiteLogin = Engine_Api::_()->hasModuleBootstrap('sitequicksignup');
        $signupModule = $ifSiteLogin ? 'sitequicksignup' : 'user';
        ?>  
        <?php echo $this->action('index', 'signup', $signupModule, array('disableContent' => true)); ?>
        <?php if( $this->pageIdentity !== 'user-auth-login' ): ?>
          <ul class="_navigation _footer_bottom mtop10 mbot15">
            <li class="user_login_form_tab" data-role="seao_user_auth_popup">
              <?php echo $this->translate('Already a member? Sign In') ?>
            </li>          
          </ul>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</div>
<script type='text/javascript'>
    en4.core.runonce.add(function () {
      en4.seaocore.popupLoginSignup.init({
        enableSignup: <?php echo!in_array($this->pageIdentity, array('user-signup-index', 'sitequicksignup-signup-index')) ?>,
        enableLogin: <?php echo!in_array($this->pageIdentity, array('user-auth-login', 'sitelogin-auth-login')) ?>,
        autoOpenLogin: <?php echo $this->autoOpenLogin ? 'true' : 'false'; ?>,
        autoOpenSignup: <?php echo $this->autoOpenSignup ? 'true' : 'false'; ?>,
        allowClose: <?php echo $this->allowClose ? 'true' : 'false'; ?>,
        openDelay: 200,
        popupVisibilty: <?php echo $this->popupVisibilty ?>
      });
    });
</script>

