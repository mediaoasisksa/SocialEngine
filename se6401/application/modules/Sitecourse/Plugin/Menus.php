<?php

class Sitecourse_Plugin_Menus {

	public function canCreateSitecourse() {
        // Must be logged in
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!$viewer || !$viewer->getIdentity()) {
			return false;
		}

        // Must be able to view Sitecourse
		if (!Engine_Api::_()->authorization()->isAllowed('sitecourse_course', $viewer, 'view')) {
			return false;
		}

        // Must be able to create Sitecourse
		if (!Engine_Api::_()->authorization()->isAllowed('sitecourse_course', $viewer, 'create')) {
			return false;
		}

		return true;
	}

	public function canViewSitecourse() {
        // Must be logged in
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!$viewer || !$viewer->getIdentity()) {
			return false;
		}
    	// Must be able to view Sitecourse
		if (!Engine_Api::_()->authorization()->isAllowed('sitecourse_course', $viewer, 'view')) {
			return false;
		}

		return true;
	}

	public function canManageSitecourse() {
		// Must be logged in
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!$viewer || !$viewer->getIdentity()) {
			return false;
		}

		return true;
	}
}
?>
