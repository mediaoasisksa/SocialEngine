<?php  

class Sitecourse_Form_Edit extends Sitecourse_Form_Create
{

  protected $_category_id;
  protected $_draft;
  protected $_canPublish;

  public function setCategory_id($id){
    $this->_category_id = $id;
  }

  public function setDraft($value = true){
    $this->_draft = $value;
  }

  public function setCanPublish($value = true){
    $this->_canPublish = $value;
  }

  public function init($params = true)
  {
    parent::init(true);
    $this->setTitle('Edit Course Information')
    ->setDescription('Edit your entry below, then click "Save Changes" to publish the entry on your course.');
    $this->submit->setLabel('Save Changes');
    $this->photo->setRequired(false);
    $canEditCategory= Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.allow.editcategory',true);
    if(!$canEditCategory) {
      $options = $this->category_id->options;
      $newOptions = array();
      foreach($options as $key=>$value){
        if($key == $this->_category_id){
          $newOptions[$key] = $value;
          break;
        }
      }
      $this->category_id->options = $newOptions;
      $this->subcategory_id->setAttrib('disabled',true);
    }
    $this->removeElement('url');
    $this->removeElement('photo');

    // check course is in draft or not
    if(!$this->_draft){
      $this->removeElement('course_publish');
    }

    // if cannot publish remove the publis options
    if(!$this->_canPublish){
      $publishOptions = array('draft'=>'Draft');
      $this->course_publish->options = $publishOptions;
      $this->course_publish->setAttrib('disabled',true);
    }


    if($this->_category_id){
      $subCategories = Engine_Api::_()->getDbtable('categories', 'sitecourse')->getSubCategoresAssoc($this->_category_id);

      $subCategoryOptions = array();
      foreach($subCategories as $category) {
        $subCategoryOptions[$category['category_id']] = $category['category_name'];
      }
      $this->subcategory_id->options = array();
      $this->subcategory_id->addMultiOptions($subCategoryOptions);
    }

    $options = $this->category_id->options;

    if($options && count($options) > 0){
      unset($options[0]);
      $this->category_id->options = $options;
    }
    
  }
}
?>
