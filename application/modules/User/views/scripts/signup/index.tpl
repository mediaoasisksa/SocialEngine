<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Customtheme/externals/styles/styles.css'); ?>
<h2>
  <?php echo ( $this->title ? $this->translate($this->title) : '' ) ?>
</h2>

<script type="text/javascript">
  function skipForm() {
    document.getElementById("skip").value = "skipForm";
    $('#SignupForm').submit();
  }
  function finishForm() {
    document.getElementById("nextStep").value = "finish";
  }
</script>
<div class="c_signup_main">
  <div class="c_signup_left">
    <!--First Step Start-->
    <div class="c_signup_cont" id="signup_first_step_form">
      <div class="_logo">
        <a href="/"><img src="./images/logo-consulto-update.png" alt=""></a>
      </div>
      <div class="_header">
        <h2><?php echo $this->translate("We make mentoring effective");?></h2>
        <p>Are you mentor or trainee ?</p>
      </div>
      <form>
        <div class="_field" >
          <ul class="_type">
            <li>
              <input type="radio" id="mtype1" name="mtype" checked="checked" value="13">
              <label for="mtype1">
                <i class="_icon"><img src="application/modules/Customtheme/externals/images/mentor.png"></i>
                <div class="_cont">
                  <p class="_title"><?php echo $this->translate("I'am a Mentor / Consultant")?></p>
                  <p class="_txt"><?php echo $this->translate("providing membership and consultation service")?></p>
                </div>
                <span class="_checkmark">
                  <i></i>    
                </span>
              </label>
            </li>
            <li>
              <input type="radio" id="mtype2" name="mtype" value="17">
              <label for="mtype2">
                <i class="_icon"><img src="application/modules/Customtheme/externals/images/student.png"></i>
                <div class="_cont">
                  <p class="_title"><?php echo $this->translate("I'am a Mentee / Trainee")?></p>
                  <p class="_txt"><?php echo $this->translate("seeking for mentorship or consultant")?></p>
                </div>
                <span class="_checkmark">
                  <i></i>    
                </span>
              </label>
            </li>
          </ul>
        </div>
        <div class="_field">
          <button onclick="showNextStep();return false;"><?php echo $this->translate("Continue");?></button>
        </div>
        <div class="_fieldtxt">
          <?php echo $this->translate("Already have an account?");?> <a href="login"><?php echo $this->translate("Log in");?></a>
        </div>
      </form>
      <div class="c_signup_steps">
        <span class="_active"></span>
        <span></span>
        <span></span>
      </div>
    </div>

    <div class="c_signup_form" style="display:none;" id="c_signup_form">
      <?php echo $this->partial($this->script[0], $this->script[1], array('form' => $this->form)) ?>
    </div>
  </div>
  <div class="c_signup_right">
    <div class="c_signup_right_img">
      <img src="application/modules/Customtheme/externals/images/signup-right-img.png" alt="" />
    </div>
  </div>
</div>


<script>
function showNextStep() {
  document.getElementById('c_signup_form').style.display = 'block';
  document.getElementById('signup_first_step_form').style.display = 'none';
  enterField(document.querySelector('input[name="mtype"]:checked').value);
  document.querySelector('input[name="profile_type"]').value = document.querySelector('input[name="mtype"]:checked').value;
  $('#signup_account_form').attr('action', '/signup?step=1&profile_type='+document.querySelector('input[name="mtype"]:checked').value);
  enterField(document.querySelector('input[name="mtype"]:checked').value);
}
function showBackStep() {
  document.getElementById('c_signup_form').style.display = 'none';
  document.getElementById('signup_first_step_form').style.display = 'block';
}
if('<?php echo $_GET["step"];?>') {
    showNextStep();
}
if('<?php echo $_GET["profile_type"];?>') {
showNextStep();
enterField(<?php echo $_GET["profile_type"];?>);
}
</script>