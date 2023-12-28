<?php  

class Sitecourse_Form_Video extends Engine_Form
{

	public function init(){
		$hiddenOrderCount = 89760;
		$user = Engine_Api::_()->user()->getViewer();
		$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
		$url = "'" . $view->url(array('action' => 'create'), 'sitecourse_video_general', true) . "'";
		$viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

		// Init form
		$this
		->setAttrib('id', 'form-upload')
		->setAttrib('name', 'channels_create')
		->setAttrib('enctype', 'multipart/form-data')
		->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

		$this->setTitle(" Upload a New Intro Video");

		
		// Video Title
		$this->addElement('Text','title',array(
			'label' => 'Video Title',
			'required' => false,
			'maxlength' => 60,
			'filters' => array(
				new Engine_Filter_Censor(),
				'StripTags',
				new Engine_Filter_StringLength(array('max' => '63'))
			),
		));

    	// Init video
		$this->addElement('Radio', 'type', array(
			'label' => 'Choose Video Source',
			'escape' => false,
			'required' => true,
			'onclick' => 'updateUrlFields(this.value)',
		));
    	//YouTube, Vimeo ,Dailymotion
		$video_options = Array();
		$coreSettings = Engine_Api::_()->getApi('settings', 'core');
		$key = $coreSettings->getSetting('sitecourse.youtube.apikey',false);
		if($key) {
			$video_options['youtube'] = "Youtube";
		}
		// vimeo
		$video_options['vimeo'] = "Vimeo";
		// dailymotion
		$video_options['dailymotion'] = "Dailymotion";

    	//My Computer
		$ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->sitecourse_ffmpeg_path;
		if(!empty($ffmpeg_path)) {
			if( Engine_Api::_()->hasModuleBootstrap('mobi') && Engine_Api::_()->mobi()->isMobile() ) {
				$video_options['upload'] = "My Device";
			} else {
				$myComputerString  = Zend_Registry::get('Zend_Translate')->_("My Computer");
				$video_options['upload'] = "My Computer";
			}
		}
		// embedcode
		$video_options['embedcode'] = "Embed Code";

		$this->type->addMultiOptions($video_options);

	    //ADD AUTH STUFF HERE
	    // Init url
		$this->addElement('Text', 'url', array(
			'label' => 'Video Link (URL)',
			'filters' => array(
				new Engine_Filter_Censor(),
				'StripTags',
			),
			'description' => 'Paste the web address of the video here.',
			'maxlength' => '5000',
			'oninput' 	=> 'validateUrl(this.value)',

		));
		$this->url->getDecorator("Description")->setOption("placement", "append");
		$this->getElement('url')->setAttribs(array('style' => 'display: none'));
		$this->getElement('url')->getDecorator('Label')->setOption('style', 'display: none');
		$this->getElement('url')->getDecorator('Description')->setOption('style', 'display: none');

		$this->addElement('Hidden', 'code', array(
			'order' => $hiddenOrderCount++,
		));

		$this->addElement('Hidden', 'vtype', array(
			'order' => $hiddenOrderCount++,
		));

		$this->addElement('Hidden', 'id', array(
			'order' => $hiddenOrderCount++,
		));

		// Init file
		$uploadUrl = $view->url(array('action' => 'create', 'format' => 'json'), 'sitecourse_video_general', true) . '?ul=1';
	    $this->addElement('DropZoneDrogAndDrop', 'file', array(
	      'uploadMultiple' => false,
	      'url' =>  $uploadUrl,
	      'fileType' => 'video',
	      'formId' => 'form-upload',
	      'viewScript' => array('upload/dropzone-upload-video.tpl', 'seaocore', array()),
	      'dropzoneEleAccessVarableName' => 'dropzoneVideoFileEleAccessVar',
	      'data' => array(
	        'linkClass' => 'seaocore_icon_add',
	      )
	    ));
	    
		// Element: submit
		$this->addElement('Button', 'upload', array(
			'label' => 'Submit',
			'type' => 'submit',
			'style' => 'display:none',
		));

	}

}

?>
