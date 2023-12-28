<?php
/**
 * SocialApps.tech
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
    if(scriptJquery('#seao_message')) {
      scriptJquery('#seao_message').html(message);
    } else {
      scriptJquery('.form-elements')[0].prepend(scriptJquery.crtEle('span', {'id': 'seao_message', 'text': message, 'style': 'color: red'}));
    }
    scriptJquery('#submit').html("Login SAT Account");
  } 
  window.callbackOnGetResponseOfSEAOLicenses = function (data) {
    if (data.status == 1) {
      console.log('asdadas');
      scriptJquery.ajax({
        url: en4.core.baseUrl + "admin/sitecore/plugin-manage/save-seao-auth-token",
        format:'json',
        method: 'post',
        data: data,
        success: function (responseJSON) {
          if (responseJSON.status == 1) {
             window.location.reload();
          } else {
            addMessage("Something went wrong!! Please try again.");
          }
        },
        failure: function (resp) {
          addMessage("Something went wrong!! Please try again.");
        }
      })
    } else {
      addMessage("Sorry, unrecognized email or password.");
    }
  };
  scriptJquery('#seao-featch').on('submit', function (e) {
     e.stopPropagation();
    e.preventDefault();
    var data = {}; 
    scriptJquery('#seao-featch').serializeArray().forEach((item)=>{
        data[item.name] = item.value;
    });
    if ( data.email.length <= 0 || data.password.length <= 0) {
      return;
    }
     scriptJquery.ajax({
      url: 'https://socialapps.tech/licenses/auth/token',
      method: 'post',
      data: data,
      request: function () {
        scriptJquery('#submit').html("Authorising......"); 
      },
      success: function (responseJSON) {
        window.callbackOnGetResponseOfSEAOLicenses(JSON.parse(responseJSON));
      }
    });
  }); 
</script>
<?php else: ?> 
  <div class="logout_seao_wrapper">
    <span class="seaocore_logged_in">
      You are logged-in as 
      <a href="https://socialapps.tech/user/<?php echo $this->session->userId; ?>" target = '_blank' ><?php echo $this->session->userName; ?></a>
    </span>
    <span id="logout_seao" align="right">
      <button class="seaocore_logout_btn" onclick = "window.location = '<?php echo $this->url(array('module' => 'sitecore', 'controller' => 'plugin-manage', 'action' => 'logout', 'redirectUrl' => urlencode($this->redirectUrl) ), 'admin_default', true);?>'" >Logout from SAT</button> 
    </span>
  </div>
<?php endif;?>
