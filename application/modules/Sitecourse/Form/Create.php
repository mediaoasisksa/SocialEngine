<?php  

class Sitecourse_Form_Create extends Engine_Form{

	public function init($param = null){
		$this->setTitle('Create New Course')
		->setDescription('Fill in the basic details and click on the create button.')
		->setAttrib('name', 'sitcourse_create');
		$user = Engine_Api::_()->user()->getViewer();
		$userLevel = Engine_Api::_()->user()->getViewer()->level_id;

		// Element: course title
		$this->addElement('Text', 'title', array(
			'label' => 'Title',
			'allowEmpty' => false,
			'required' => true,
			'maxlength' => '63',
			'filters' => array(
				new Engine_Filter_Censor(),
				'StripTags',
				new Engine_Filter_StringLength(array('max' => '63'))
			),
			'autofocus' => 'autofocus',
		));

		$front = Zend_Controller_Front::getInstance();
		$baseUrl = $front->getBaseUrl();
		$COURSE_NAME =  $routeStartS = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.UrlS', "course");
		$link2 = $_SERVER['HTTP_HOST'] . $baseUrl . '/' . $COURSE_NAME;



		$des=sprintf(Zend_Registry::get('Zend_Translate')->_("This will be the end of your Course URL. It should be indicative of the title and can contain alphabets, numbers, underscores, and dashes only. Its length should be in the range of 3-255 characters. The complete URL of your course will be: %s%s%s"), "<span>http://$link2/</span>","<span id='course_url_address'></span>","<p id= 'urlMessage'></p>");

		// Element: course url
		$this->addElement('Text', 'url', array(
			'label' => 'URL',
			'description' => $des,
			'allowEmpty' => false,
			'required' => true,
			'maxlength' => '63',
			'filters' => array(
				new Engine_Filter_Censor(),
				'StripTags',
				new Engine_Filter_StringLength(array('max' => '63'))
			),
			'autofocus' => 'autofocus',
			'oninput'=> 'replacetext(this)',
			'onblur' => 'checkUrl(this)',
		));
		$this->url->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));


		// prepare categories
		$categories = Engine_Api::_()->getDbtable('categories', 'sitecourse')->getCategoriesAssoc();
		$categoryOptions = array();
		$categoryOptions[0] = "";
		if(count($categories)>0){
			foreach($categories as $category) {
				$categoryOptions[$category['category_id']] = $category['category_name'];
			}
			$this->addElement('Select', 'category_id', array(
				'label' => 'Course Category',
				'required' => true,
				'multiOptions' => $categoryOptions,
				'onchange' => 'changeSubCategory(this.value)',
			));
		}

		if (count($categories)>0) {
			$this->addElement('Select', 'subcategory_id', array(
				'label' => 'Course Sub-Category',
				'RegisterInArrayValidator' => false,
				'allowEmpty' => true,
				'required' => false
			));
		}

		$tagsAllowed = Engine_Api::_()->getApi('settings', 'core')->sitecourse_allow_tags;

		if($tagsAllowed){
        	// Element: tags
			$this->addElement('Text', 'tags', array(
				'label' => 'Tags (Keywords)',
				'autocomplete' => 'off',
				'description' => 'Separate tags with commas.',
				'filters' => array(
					'StripTags',
					new Engine_Filter_Censor(),
				),
			));
			$this->tags->getDecorator("Description")->setOption("placement", "append");
		}


		// Element: Course Difficulty
		$difficulty_levels = array('' => '',0 => "Beginner", 1 => "Intermediate", 2 => "Expert");
		$this->addElement('Select', 'difficulty_level', array(
			'label' => 'Difficulty Level',
			'required' => true,
			'multiOptions' => $difficulty_levels,
		));



		// Element: course price
		$this->addElement('Text', 'price', array(
			'label' => 'Price',
			'allowEmpty' => false,
			'required' => true,
			'maxlength' => '10',
			'filters' => array(
				new Engine_Filter_Censor(),
				'StripTags',
				new Engine_Filter_StringLength(array('max' => '10'))
			),
			'autofocus' => 'autofocus',
		));


		// Element: course duration
		$this->addElement('Text', 'duration', array(
			'label' => 'Duration(hrs)',
			'description' => ' Enter the number of hours required to complete the course.',
			'allowEmpty' => false,
			'required' => true,
			'filters' => array(
				new Engine_Filter_Censor(),
				'StripTags',
			),
			'autofocus' => 'autofocus',
			'validators' => array(
				array('Float', true),
				new Engine_Validate_AtLeast(1),
			),
		));

		//Element: course photo
		$this->addElement('File', 'photo', array(
			'label' => 'Choose Main Photo',
			'required' => true
		));
		$this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');


        // Element: auth_view
		$viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitecourse_course', $user, 'auth_view');

		$availableLabels = array(
			'everyone'            => 'Everyone',
			'registered'          => 'All Registered Members',
			'owner_network'       => 'Friends and Networks',
			'owner_member_member' => 'Friends of Friends',
			'owner_member'        => 'Friends Only',
			'owner'               => 'Just Me'
		);
		$viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
		
		if (!empty($viewOptions) && count($viewOptions) >= 1) {
            // Make a hidden field
			if (count($viewOptions) == 1) {
				$this->addElement('hidden', 'auth_view', array('order' => 101, 'value' => key($viewOptions),
			));
                // Make select box
			} else {
				$this->addElement('Select', 'auth_view', array(
					'label' => 'View Privacy',
					'description' => 'Who may see this course?',
					'multiOptions' => $viewOptions,
					'value' => key($viewOptions),
				));
				$this->auth_view->getDecorator('Description')->setOption('placement', 'append');
			}
		}


		  // Element: auth_comment
		$commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitecourse_course', $user, 'auth_comment');

		$availableLabels = array(
			'everyone'            => 'Everyone',
			'registered'          => 'All Registered Members',
			'owner_network'       => 'Friends and Networks',
			'owner_member_member' => 'Friends of Friends',
			'owner_member'        => 'Friends Only',
			'owner'               => 'Just Me'
		);
		$commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));
		
		if (!empty($commentOptions) && count($commentOptions) >= 1) {
            // Make a hidden field
			if (count($commentOptions) == 1) {
				$this->addElement('hidden', 'auth_comment', array('order' => 102, 'value' => key($commentOptions),
			));
                // Make select box
			} else {
				$this->addElement('Select', 'auth_comment', array(
					'label' => 'Comment Privacy',
					'description' => 'Who may comment on this course?',
					'multiOptions' => $commentOptions,
					'value' => key($commentOptions),
				));
				$this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
			}
		}



		//Element: publish or draft drop down
		if($param){
			$this->addElement('Select','course_publish',array(
				'label' => 'Status',
				'description' => 'If this Course is published, it cannot be switched back to draft mode.',
				'multiOptions' => array('draft'=>'Draft','publish'=>'Publish'),
			));
		}

        // Element: submit
		$this->addElement('Button', 'submit', array(
			'label' => 'Create',
			'type' => 'submit',
		));
	}

	/**
	 * @param course url
	 * @return url is alphanumeric or not
	 */
	public function validUrl($url){
		$alphaNumericRegex = "/^[A-Za-z0-9_-]*$/";
		return preg_match($alphaNumericRegex,$url);
	}

	/**
	 * @param course price
	 * @return price is numeric or not
	 */

	public function validPrice($price){
		// $numericeRegex = "/^(0|[1-9][0-9]*)$/";
		$numericeRegex = "/^([0-9])\d*(\.\d+)?$/";	
		return preg_match($numericeRegex,$price);
	}


}
