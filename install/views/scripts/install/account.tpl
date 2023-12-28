<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: account.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<h1>
  <?php echo $this->translate('Step 3: Create Admin Account') ?>
</h1>

<p>
  <?php echo $this->translate('Now that you\'ve setup SocialEngine, let\'s get started by naming your community and creating an administrator account. Please provide your email address and choose a password. You will use this information to sign in to your control panel and manage your social network.') ?>
</p>

<br />

<?php if( !empty($this->form) ): ?>
  <div class="create-admin-form">
    <?php echo $this->form->render($this) ?>
  </div>
  <script>
    var togglePassword = document.querySelector('#togglePassword');
    var password = document.querySelector('#password');
    togglePassword.addEventListener('click', function (e) {
        // toggle the type attribute
        var type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        // toggle the eye / eye slash icon
        this.classList.toggle('fa-eye-slash');
    });

    var confirmtogglePassword = document.querySelector('#confirmtogglePassword');
    var password_conf = document.querySelector('#password_conf');
    
    confirmtogglePassword.addEventListener('click', function (e) {
        // toggle the type attribute
        var type = password_conf.getAttribute('type') === 'password' ? 'text' : 'password';
        password_conf.setAttribute('type', type);
        // toggle the eye / eye slash icon
        this.classList.toggle('fa-eye-slash');
    });
  </script>
<?php endif; ?>
