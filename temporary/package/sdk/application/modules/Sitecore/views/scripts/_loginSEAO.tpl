<?php
/**
 * SocialEngineAddons
 * @package    Seaocore
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: _loginSEAO.tpl 9915 2013-02-15 01:30:19Z alex $
 * @author     John
 */
?>  
<?php if( empty($this->seaoDetailsSession) ): ?>
<div class="seaocore_settings_form">
  <div class='settings'>
    <?php echo $this->seaoDetailsForm->render($this); ?>
  </div>
</div>

<script type="text/javascript"> 
  function addMessage(message) {
    if($('seao_message')) {
      $('seao_message').innerHTML = message;
    } else {
      $$('.form-elements')[0].prepend(new Element('span', {'id': 'seao_message', 'text': message, 'style': 'color: red'}));
    }
    $('submit').innerHTML = "Login SEAO Account";
  } 
  window.callbackOnGetResponseOfSEAOLicenses = function (data) {
    if (data.status == 1) {
      (new Request.JSON({
        url: en4.core.baseUrl + "admin/sitecore/plugin-manage/save-seao-auth-token",
        method: 'post',
        data: data,
        onSuccess: function (responseJSON) {
          if (responseJSON.status == 1) {
             window.location.reload();
          } else {
            addMessage("Something went wrong!! Please try again.");
          }
        },
        onFailure: function (resp) {
          addMessage("Something went wrong!! Please try again.");
        }
      })).send();
    } else {
      addMessage("Sorry, unrecognized email or password.");
    }
  };
  $('seao-featch').addEvent('submit', function (e) {
    e.stop();
    var data = $('seao-featch').toQueryString().parseQueryString();
    if ( data.email.length <= 0 || data.password.length <= 0) {
      return;
    }
    var request = new Request.JSON({
      url: 'https://www.socialengineaddons.com/licenses/auth/token',
      method: 'post',
      data: data,
      onRequest: function () {
        $('submit').innerHTML = "Authorising......"; 
      },
      onSuccess: function (responseJSON) {
        window.callbackOnGetResponseOfSEAOLicenses(responseJSON);
      }
    });
    request.send();
  }); 
</script>
<?php else: ?> 
  <div class="logout_seao_wrapper">
    <span class="seaocore_logged_in">
      You are logged-in as 
      <a href="https://www.socialengineaddons.com/user/<?php echo $this->session->userId; ?>" target = '_blank' ><?php echo $this->session->userName; ?></a>
    </span>
    <span id="logout_seao" align="right">
      <button class="seaocore_logout_btn" onclick = "window.location = '<?php echo $this->url(array('module' => 'sitecore', 'controller' => 'plugin-manage', 'action' => 'logout', 'redirectUrl' => urlencode($this->redirectUrl) ), 'admin_default', true);?>'" >Logout from SEAO</button> 
    </span>
  </div>
<?php endif;?>
