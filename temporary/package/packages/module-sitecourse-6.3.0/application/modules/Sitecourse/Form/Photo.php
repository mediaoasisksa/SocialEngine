<?php
class Sitecourse_Form_Photo extends Engine_Form {

	public function init() {

		$course_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('course_id', null);
		$sitecourse = Engine_Api::_()->getItem('sitecourse_course', $course_id);
		$this
		->setTitle('Edit Profile Picture and Signature')
		->setAttrib('enctype', 'multipart/form-data')
		->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
		->setAttrib('name', 'EditPhoto');
		$this->addElement('Image', 'current', array(
			'label' => 'Current Photo',
			'ignore' => true,
			'decorators' => array(array('ViewScript', array(
				'viewScript' => '_formEditImage.tpl',
				'class' => 'form element',
				'testing' => 'testing'
			)))
		));
		Engine_Form::addDefaultDecorators($this->current);
		$this->addElement('File', 'Filedata', array(
			'label' => 'Choose New Photo',
			'order'=> 901,
			'destination' => APPLICATION_PATH . '/public/temporary/',
			'validators' => array(
				array('Extension', false, 'jpg,jpeg,png,gif'),
			),
			'onchange' => 'javascript:uploadPhoto();'
		));
		

		$this->addElement('Hidden', 'coordinates', array(
			'order' => 900,
			'filters' => array(
				'HtmlEntities',
			)
		));
		$this->addElement('File', 'Signature', array(
			'label' => 'Choose New Signature',
			'destination' => APPLICATION_PATH . '/public/temporary/',
			'validators' => array(
				array('Extension', false, 'jpg,jpeg,png,gif'),
			),
			'onchange' => 'javascript:uploadPhoto();'
		));

		$this->addElement('Hidden', 'coordinates2', array(
			'order' => 902,
			'filters' => array(
				'HtmlEntities',
			)
		));
    // if ($sitecourse->photo_id != 0) {
    //     $this->addElement('Cancel', 'remove', array(
    //         'label' => 'Remove Photo',
    //         'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
    //             'action' => 'remove-photo',
    //         )),
    //         'onclick' => null,
    //         'decorators' => array(
    //             'ViewHelper'
    //         ),
    //     ));
    //     $this->addDisplayGroup(array('done', 'remove'), 'buttons');
    // }

	}

}

?>
