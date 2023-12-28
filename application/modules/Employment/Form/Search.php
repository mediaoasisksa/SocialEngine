<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Employment
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: WidgetController.php
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Employment
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Employment_Form_Search extends Fields_Form_Search
{
  protected $_fieldType = 'employment';
  
  public function init()
  {
    parent::init();

    $this->loadDefaultDecorators();

    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box employments_browse_filters field_search_criteria',
      ))
      ->setAction($_SERVER['REQUEST_URI'])
      ->setMethod('GET')
      ->getDecorator('HtmlTag')
        ->setOption('class', 'browseemployments_criteria employments_browse_filters');

    // Generate
    //$this->generate();

    // Add custom elements
    $this->getAdditionalOptionsElement();
  }

  public function getAdditionalOptionsElement()
  {
    $i = -5000;

    $this->addElement('Hidden', 'page', array(
      'order' => $i--,
    ));

    $this->addElement('Hidden', 'tag', array(
      'order' => $i--,
    ));

    $this->addElement('Hidden', 'start_date', array(
      'order' => $i--,
    ));

    $this->addElement('Hidden', 'end_date', array(
      'order' => $i--,
    ));

    $this->addElement('Text', 'search', array(
      'label' => 'Search Employment Listings',
      'order' => $i--,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));

    $orderby = array(
      'creation_date' => 'Most Recent',
      'view_count' => 'Most Viewed',
    );
    if(Engine_Api::_()->getApi('settings', 'core')->getSetting('employment.enable.rating', 1)) {
      $orderby['rating'] = 'Highest Rated';
    }
    $this->addElement('Select', 'orderby', array(
      'label' => 'Browse By',
      'multiOptions' => $orderby,
      'onchange' => 'searchEmployments();',
      'order' => $i--,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));

    $this->addElement('Select', 'show', array(
      'label' => 'Show',
      'multiOptions' => array(
        '1' => 'Everyone\'s Posts',
        '2' => 'Only My Friends\' Posts',
      ),
      'onchange' => 'searchEmployments();',
      'order' => $i--,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));

    $this->addElement('Select', 'closed', array(
      'label' => 'Status',
      'multiOptions' => array(
        '' => 'All Listings',
        '0' => 'Only Open Listings',
        '1' => 'Only Closed Listings',
      ),
      'onchange' => 'searchEmployments();',
      'order' => $i--,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));

    $categories = Engine_Api::_()->getDbtable('categories', 'employment')->getCategoriesAssoc();
    if (engine_count($categories) > 0) {
      $categories = array('0' => 'All Categories') + $categories;
      $this->addElement('Select', 'category_id', array(
        'label' => 'Category',
        'multiOptions' => $categories,
        'onchange' => "showSubCategory(this.value);",
      ));
      $this->addElement('Select', 'subcat_id', array(
        'label' => "2nd-level Category",
        'allowEmpty' => true,
        'required' => false,
        'multiOptions' => array('0' => ''),
        'registerInArrayValidator' => false,
        'onchange' => "showSubSubCategory(this.value);"
      ));
      $this->addElement('Select', 'subsubcat_id', array(
        'label' => "3rd-level Category",
        'allowEmpty' => true,
        'registerInArrayValidator' => false,
        'required' => false,
        'multiOptions' => array('0' => '')
      ));
    }



    
   /** $this->addElement('Checkbox', 'has_photo', array(
      'label' => 'Only Employment Listings With Photos',
      'order' => 10000000,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('placement' => 'APPEND', 'tag' => 'label')),
        array('HtmlTag', array('tag' => 'li'))
      ),
    )); */
    
    $this->addElement('Button', 'done', array(
      'label' => 'Search',
      'onclick' => 'searchEmployments();',
      'ignore' => true,
      'order' => 10000001,
      'decorators' => array(
        'ViewHelper',
        //array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));
  }
}
