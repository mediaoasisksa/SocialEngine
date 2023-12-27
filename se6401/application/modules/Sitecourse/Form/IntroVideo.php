<?php  

class Sitecourse_Form_IntroVideo extends Engine_Form{

	protected $_parent_id;
	protected $_parent_type;

	public function setParent_id($value){
		$this->_parent_id = $value;
	}

	public function setParent_type($value){
		$this->_parent_type = $value;
	}
	public function init(){
		$this->setTitle('Choose a Intro Video')
		->setMethod('post')
		->setDescription('Please select a existing video from lessons ')
		->setAttrib('name', 'sitcourse_create');


		$topics = Engine_Api::_()->getItemTable('sitecourse_topic')->fetchAllTopics($this->_parent_id);


		$topicsOptions = array(0=>'');
		foreach($topics as $key => $value){
			$topicsOptions[$value['topic_id']] = $value['title'];
		}
		$flag = false;
		// if no topic is there
		if(count($topicsOptions) <= 1){
			$topicsOptions[0] = 'Please add a topic or upload a new video';
			$flag = true;
		}

		// topic element
		$this->addElement('Select','topic_id',array(
			'label' => 'Choose a topic',
			'multiOptions' => $topicsOptions,
			'onchange' => 'changeTopic(this)',
		));

		// if no topic disable select box
		if($flag){
			$this->topic_id->setAttrib('disabled',true);
		}

		//lesson element
		$this->addElement('Select','lesson_id',array(
			'label' => 'Choose a lesson',
			'RegisterInArrayValidator' => false,
			'multiOptions' => array(0=>'Please select a topic'),
			'disabled' => true,
			'onchange' => 'changeLesson(this)',
		));
		// Element: submit
		$this->addElement('Button', 'submit', array(
			'label' => 'Add',
			'type' => 'submit',
			'ignore' => true,
			'decorators' => array('ViewHelper')
		));

		$this->addElement('Cancel', 'cancel', array(
			'label' => 'cancel',
			'link' => true,
			'prependText' => ' or ',
			'href' => '',
			'onClick'=> 'javascript:parent.Smoothbox.close();',
			'decorators' => array(
				'ViewHelper'
			)
		));
		$this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
		$button_group = $this->getDisplayGroup('buttons');

	}


}


?>