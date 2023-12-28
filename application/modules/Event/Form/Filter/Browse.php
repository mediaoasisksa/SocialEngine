<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Browse.php 9829 2012-11-27 01:13:07Z richard $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Event_Form_Filter_Browse extends Engine_Form
{
  public function init()
  {
    $this->clearDecorators()
      ->addDecorators(array(
        'FormElements',
        array('HtmlTag', array('tag' => 'dl')),
        'Form',
      ))
      ->setMethod('get')
      ->setAttrib('class', 'filters')
      ;
    
    $this->addElement('Text', 'search_text', array(	
      'label' => 'Search Events:',	
    ));
    
    $categories = Engine_Api::_()->getDbtable('categories', 'event')->getCategoriesAssoc();
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

    $this->addElement('Select', 'view', array(
      'label' => 'View:',
      'multiOptions' => array(
        '' => 'Everyone\'s Events',
        '1' => 'Only My Friends\' Events',
      ),
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'dd')),
        array('Label', array('tag' => 'dt', 'placement' => 'PREPEND'))
      ),
      'onchange' => 'this.form.submit();',
    ));

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $param = $request->getParams();
    $filter = !empty($param['filter']) ? $param['filter'] : 'future';

    $orderOptions = array(
      'starttime ASC' => 'Start Time',
      'creation_date DESC' => 'Recently Created',
      'member_count DESC' => 'Most Popular',
    );

    $orderValue = 'starttime ASC';

    //Adding Recently Ended option for past events
    if( $filter == 'past') {
      $orderBy = 'endtime DESC';
      $orderOptions[$orderBy] = 'Recently Ended';
      $orderValue = $orderBy;
    }
    if(Engine_Api::_()->getApi('settings', 'core')->getSetting('event.enable.rating', 1)) {
      $orderOptions['rating'] = 'Highest Rated';
    }
    $this->addElement('Select', 'order', array(
      'label' => 'List By:',
      'multiOptions' => $orderOptions,
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'dd')),
        array('Label', array('tag' => 'dt', 'placement' => 'PREPEND'))
      ),
      'value' => $orderValue,
      'onchange' => 'this.form.submit();',
    ));

    $this->addElement('Button', 'find', array(
      'type' => 'submit',
      'label' => 'Search',
      'ignore' => true,
      'order' => 10000001,
    ));
  }
}
