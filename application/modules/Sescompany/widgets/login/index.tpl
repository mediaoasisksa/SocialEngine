<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: login.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>
<link href="application/modules/Customtheme/externals/styles/login.css" rel="stylesheet" type="text/css" />
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Customtheme/externals/styles/styles.css'); ?>


<?php $pType = $_GET['profile_type'] ? $_GET['profile_type'] : 92; ?>
<div class="container ls_form_wrapper">
  <div class="login">
    <section class="sec-one">
        <h2 class="text-center py-3 head-h-2">Login and explore Coworker</h2>
     <?php echo $this->form->render($this) ?>
    </section>

    <section class="text-center foot-er" style="display:none;">
      <div class="container">
        <p class="py-3">Dont have an account?</p>
        <div class="row py-2">
          <div class="col-md-10 m-auto">
            <a href="/signup">
              <button type="button" class="bt-login-5 btn-block btn btn-lg">
                Sign Up
              </button>
            </a>
          </div>
        </div>
      </div>
    </section>
  </div>
</div>
