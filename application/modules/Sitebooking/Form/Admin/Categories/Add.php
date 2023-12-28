<?php

class Sitebooking_Form_Admin_Categories_Add extends Engine_Form {

  public function init() {

    $category_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('category_id', 0);
    $perform = Zend_Controller_Front::getInstance()->getRequest()->getParam('perform', 'add');
    $first_level_category_id = 0;
    $second_level_category_id = 0;
    if ($category_id) {
      $category = Engine_Api::_()->getItem('sitebooking_category', $category_id);
      if ($category && empty($category->first_level_category_id)) {
        $first_level_category_id = $category->category_id;
      } elseif ($category && !empty($category->first_level_category_id)) {
        $first_level_category_id = $category->category_id;
        $second_level_category_id = $category->category_id;
      }
    }

    $isThirdLevelCat = false;
    if ($perform == 'add') {
      if (empty($category_id)) {
        $this
            ->setTitle('Add Category');
      } elseif (!empty($category_id) && empty($category->first_level_category_id)) {
        $this
            ->setTitle('Add Sub Category');
      } elseif (!empty($category_id) && !empty($category->first_level_category_id)) {
        $isThirdLevelCat = true;
        $this
            ->setTitle('Add 3rd Level Category');
      }
    } elseif ($perform == 'edit') {
      $category_name = $category->category_name;
      if (!empty($category->second_level_category_id))
        $isThirdLevelCat = true;
      $this
          ->setTitle("Edit $category_name");
    }

    $this->addElement('Text', 'category_name', array(
      'label' => 'Name',
      'required' => true,
      'empty' => false,
      'value' => '',
    ));

    $link = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
    $this->addElement('Text', 'category_slug', array(
      'label' => 'URL Component',
      'description' => "This will be the end of the URL of your service browse page, for example: $link/services/categoryid/URL-COMPONENT"
    ));
    $this->category_slug->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));

    $this->addElement('Text', 'meta_title', array(
      'label' => 'HTML Title',
      // 'required' => true,
      'value' => '',
    ));

    $this->addElement('Textarea', 'meta_description', array(
      'label' => 'Meta Description',
      // 'required' => true,
      'value' => '',
    ));

    $this->addElement('Textarea', 'meta_keywords', array(
      'label' => 'Meta Keywords',
      // 'required' => true,
      'value' => '',
    ));

    if (empty($isThirdLevelCat)) {
      $this->addElement('File', 'photo', array(
        'label' => 'Category Image',
        'Description' => 'Upload the image of the category, which will show in the categories carousel widget.',
        'allowEmpty' => true,
        'required' => false,
      ));
      $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg,JPG,PNG,GIF,JPEG');

      if (!empty($category_id) && $perform == 'edit') {
        $category = Engine_Api::_()->getItem('sitebooking_category', $category_id);
        $getCategoryPhoto = Engine_Api::_()->storage()->get($category->photo_id, '');
        if ($category->photo_id && !empty($getCategoryPhoto)) {
          $photoName = Engine_Api::_()->storage()->get($category->photo_id, '')->getPhotoUrl();
          $description = "<img src='$photoName' class='sr_sitebooking_categories_banner_img' />";

          //VALUE FOR LOGO PREVIEW.
          $this->addElement('Dummy', 'logo_photo_preview', array(
            'label' => 'Image Preview',
            'description' => $description,
          ));
          $this->logo_photo_preview
              ->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

          $this->addElement('Checkbox', 'removephoto', array('Description' => 'Delete Image', 'label' => 'Yes, delete this Image.'));
        }
      }
    }

    if (!empty($category_id) && $perform == 'edit') {
      $category = Engine_Api::_()->getItem('sitebooking_category', $category_id);
      if ($category->file_id) {
        $photoName = Engine_Api::_()->storage()->get($category->file_id, '')->getPhotoUrl();
        $description = "<img src='$photoName' class='sitebooking_categories_icon' />";

        //VALUE FOR LOGO PREVIEW.
        $this->addElement('Dummy', 'logo_icon_preview', array(
          'label' => 'Icon Preview',
          'description' => $description,
        ));
        $this->logo_icon_preview
            ->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

        $this->addElement('Checkbox', 'removeicon', array('Description' => 'Delete Icon', 'label' => 'Yes, delete this icon.'));
      }
    }
    
    $this->addElement('Button', 'submit', array(
      'label' => 'Add',
      'type' => 'submit',
      'ignore' => true
    ));
  }

}