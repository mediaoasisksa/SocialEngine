<?php 
class Sitebooking_Form_ServiceProvider_Edit extends Sitebooking_Form_ServiceProvider_Create
{
  public function init()
  {
    parent::init();
    $this->setTitle('Edit Service Provider\'s Profile ')
      ->setDescription('Edit your service provider here and then save the changes made.');
    $this->submit->setLabel('Save Changes');
  }
}
?>