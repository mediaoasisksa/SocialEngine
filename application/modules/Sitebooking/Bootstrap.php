<?php

class Sitebooking_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
	protected function _initFrontController() {
    	$this->initViewHelperPath();
   		$this->initActionHelperPath();
	}
}