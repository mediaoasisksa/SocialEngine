<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: account.tpl 10143 2014-03-26 16:18:25Z andres $
 * @author     John
 */
?>

<style>
#signup_account_form #name-wrapper {
  display: none;
}
</style>

<script type="text/javascript">
//<![CDATA[
  scriptJquery(window).load(function() {
    if( scriptJquery('#username') && scriptJquery('#profile_address') ) {
      var profile_address = scriptJquery('#profile_address').html();
      profile_address = profile_address.replace('<?php echo /*$this->translate(*/'yourname'/*)*/?>',
          '<span id="profile_address_text"><?php echo $this->translate('yourname') ?></span>');
      scriptJquery('#profile_address').html(profile_address);

      scriptJquery(document).on('keyup','#username', function() {
        var text = '<?php echo $this->translate('yourname') ?>';
        if( this.value != '' ) {
          text = this.value;
        }
        scriptJquery('#profile_address_text').html(text.replace(/[^a-z0-9]/gi,''));
      });
      // trigger on page-load
      if( document.getElementById('username').value.length ) {
        document.getElementById('username').fireEvent('keyup');
      }
    }
  });
//]]>
</script>

<?php echo $this->form->render($this) ?>

<div class="c_signup_steps">
  <span></span>
  <span class="_active"></span>
  <span></span>
</div>
<script type="text/javascript">
 var pType = '<?php echo $_GET["profile_type"] ? $_GET["profile_type"] : 13 ?>';
  function passwordRoutine(value){
      var pswd = value;
      // valid length
      if ( pswd.length < 6) {
        scriptJquery('#passwordroutine_length').removeClass('valid').addClass('invalid');
      } else {
        scriptJquery('#passwordroutine_length').removeClass('invalid').addClass('valid');
      }

      //validate special character
      if ( pswd.match(/[#?!@$%^&*-]/) ) {
          if ( pswd.match(/[\\\\:\/]/) ) {
              scriptJquery('#passwordroutine_specialcharacters').removeClass('valid').addClass('invalid');
          } else {
              scriptJquery('#passwordroutine_specialcharacters').removeClass('invalid').addClass('valid');
          }
      } else {
          scriptJquery('#passwordroutine_specialcharacters').removeClass('valid').addClass('invalid');
      }

      //validate capital letter
      if ( pswd.match(/[A-Z]/) ) {
          scriptJquery('#passwordroutine_capital').removeClass('invalid').addClass('valid');
      } else {
          scriptJquery('#passwordroutine_capital').removeClass('valid').addClass('invalid');
      }

      //validate small letter
      if ( pswd.match(/[a-z]/) ) {
          scriptJquery('#passwordroutine_lowerLetter').removeClass('invalid').addClass('valid');
      } else {
          scriptJquery('#passwordroutine_lowerLetter').removeClass('valid').addClass('invalid');
      }

      //validate number
      if ( pswd.match(/\d{1}/) ) {
          scriptJquery('#passwordroutine_number').removeClass('invalid').addClass('valid');
      } else {
          scriptJquery('#passwordroutine_number').removeClass('valid').addClass('invalid');
      }
  }
  
    if(pType != '') {
      enterField(pType);
  }
 
  function enterField(value) {
      if(value == 13) {   
          scriptJquery('#specialist-wrapper').css('display', 'block');
          scriptJquery('#contactno-wrapper').css('display', 'block');
          scriptJquery('#gender-wrapper').css('display', 'block');
          
          scriptJquery('#country-wrapper').css('display', 'block');
          scriptJquery('#city-wrapper').css('display', 'block');
          scriptJquery('#price-wrapper').css('display', 'block');
          scriptJquery('#duration-wrapper').css('display', 'block');
          scriptJquery('#specialist').css('display', 'block');
          scriptJquery('#contactno').css('display', 'block');
          scriptJquery('#gender').css('display', 'block');
            scriptJquery('#country').css('display', 'block');
          scriptJquery('#city').css('display', 'block');
          scriptJquery('#price').css('display', 'block');
          scriptJquery('#duration').css('display', 'block');
           scriptJquery('#country').val("");
            scriptJquery('#city').val("");
            scriptJquery('#price').val("");
            scriptJquery('#duration').val("30");          
          scriptJquery('#jobtitle-wrapper').css('display', 'block');
          scriptJquery('#qualifications-wrapper').css('display', 'block');
          scriptJquery('#history-wrapper').css('display', 'block');
          scriptJquery('#cvupload-wrapper').css('display', 'block');
          scriptJquery('#studentorprofessional-wrapper').css('display', 'none');
          scriptJquery('#educationlevel-wrapper').css('display', 'none');
          scriptJquery('#educationinstitute-wrapper').css('display', 'none');
          
      } else if(value == 114) {
          scriptJquery('#specialist-wrapper').css('display', 'block');
          scriptJquery('#contactno-wrapper').css('display', 'block');
          scriptJquery('#gender-wrapper').css('display', 'block');
          
          scriptJquery('#country-wrapper').css('display', 'block');
          scriptJquery('#city-wrapper').css('display', 'block');
          scriptJquery('#price-wrapper').css('display', 'block');
          scriptJquery('#duration-wrapper').css('display', 'none');
          scriptJquery('#specialist').css('display', 'block');
          scriptJquery('#contactno').css('display', 'block');
          scriptJquery('#gender').css('display', 'block');
            scriptJquery('#country').css('display', 'block');
          scriptJquery('#city').css('display', 'block');
          scriptJquery('#price').css('display', 'none');
          scriptJquery('#price-wrapper').css('display', 'none');
          scriptJquery('#duration').val(30);
          scriptJquery('#price').val(1500);
          //$('input').attr('readonly', true);
          //scriptJquery('#price').setAttribute('display', 'none');
          //scriptJquery('#price').setAttribute('display', '');
          scriptJquery('#duration').css('display', 'none');
           scriptJquery('#country').val("");
            scriptJquery('#city').val("");
           
                      
          scriptJquery('#jobtitle-wrapper').css('display', 'block');
          scriptJquery('#qualifications-wrapper').css('display', 'block');
          scriptJquery('#history-wrapper').css('display', 'block');
          scriptJquery('#cvupload-wrapper').css('display', 'block');
          
          }else {
          scriptJquery('#specialist-wrapper').css('display', 'block');
          scriptJquery('#contactno-wrapper').css('display', 'block');
          scriptJquery('#gender-wrapper').css('display', 'block');
          
          scriptJquery('#country-wrapper').css('display', 'block');
          scriptJquery('#city-wrapper').css('display', 'block');
          scriptJquery('#price-wrapper').css('display', 'block');
          scriptJquery('#duration-wrapper').css('display', 'none');
          scriptJquery('#specialist').css('display', 'block');
          scriptJquery('#contactno').css('display', 'block');
          scriptJquery('#gender').css('display', 'block');
            scriptJquery('#country').css('display', 'block');
          scriptJquery('#city').css('display', 'block');
          scriptJquery('#price').css('display', 'none');
          scriptJquery('#price-wrapper').css('display', 'none');
          scriptJquery('#duration').val(30);
          scriptJquery('#price').val(1500);
          //$('input').attr('readonly', true);
          //scriptJquery('#price').setAttribute('display', 'none');
          //scriptJquery('#price').setAttribute('display', '');
          scriptJquery('#duration').css('display', 'none');
          scriptJquery('#country').val("");
        scriptJquery('#city').val("");
           
         scriptJquery('#consulatant_category_id-wrapper').css('display', 'none');
        // scriptJquery('#mentor_category_id-wrapper').css('display', 'none');
          scriptJquery('#jobtitle-wrapper').css('display', 'none');
          scriptJquery('#qualifications-wrapper').css('display', 'none');
          scriptJquery('#history-wrapper').css('display', 'none');
          scriptJquery('#file-wrapper').css('display', 'none');
          scriptJquery('#description-wrapper').css('display', 'none');
          scriptJquery('#consulatant_category_id').val("1");
          //scriptJquery('#mentor_category_id').val("16");
          scriptJquery('#studentorprofessional-wrapper').css('display', 'block');
          scriptJquery('#educationlevel-wrapper').css('display', 'block');
          scriptJquery('#educationinstitute-wrapper').css('display', 'block');
      }
  }
  
  function showOptions(value) {
      if(value == "Student") {
           scriptJquery('#educationlevel-wrapper').css('display', 'block');
          scriptJquery('#educationinstitute-wrapper').css('display', 'block');
                    scriptJquery('#jobtitle-wrapper').css('display', 'none');
          scriptJquery('#qualifications-wrapper').css('display', 'none');
      } else {
           scriptJquery('#educationlevel-wrapper').css('display', 'none');
          scriptJquery('#educationinstitute-wrapper').css('display', 'none');
        scriptJquery('#jobtitle-wrapper').css('display', 'block');
          scriptJquery('#qualifications-wrapper').css('display', 'block');
      }
  }
      
  

</script>
