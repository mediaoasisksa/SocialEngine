<?php  

class Sitecourse_Form_VideoLesson extends Sitecourse_Form_Video {
	public function init(){
		parent::init();
		$this->setTitle("Add a Video Lesson");
		$this->title->setRequired(true);
		$this->title->setLabel('Lesson Title');
		
	}
}

?>
