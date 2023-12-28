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


<ul>
<?php if($this->subject()->jobtitle):?>
 <li>
     <b> <?php echo $this->translate("JobTitle: ");?></b> 
     <?php echo $this->subject()->jobtitle;?>
  </li>   
 <br />
 <?php endif;?> 
 
 
<?php if($this->subject()->qualifications):?>
<li>
    <b>
        <?php echo $this->translate("Company Name: ");?>
    </b> 
    <?php echo $this->subject()->qualifications;?> 
</li>  <br />

<?php endif;?>

<?php if($this->subject()->history):?>

<li><b><?php echo $this->translate("History: ");?> </b>

<?php echo $this->subject()->history;?>

</li>  
<br />
<?php endif;?>



<?php if($this->subject()->file_id):?>
<?php $storage = Engine_Api::_()->getItem('storage_file', $this->subject()->file_id);?>
<li><b><?php echo $this->translate("CV: ");?></b> <a target="_blank" href="/<?php echo Engine_Api::_()->storage()->get($this->subject()->file_id, '')->storage_path;?>">Preview</a></li> 
<?php endif;?>
 

</ul>
