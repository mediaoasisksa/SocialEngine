<?php
require_once realpath(dirname(__FILE__)) . '/sitemodule_install.php';

class Sitecourse_Installer extends SiteModule_Installer
{
    protected $_deependencyVersion = array(
        'seaocore' => '6.0.0',
    );

    protected $_installConfig = array(
        'sku' => 'sitecourse'
  );

    function onPreInstall()
    {
        $db = $this->getDb();
        $coreModuleSelect = new Zend_Db_Select($db);
        $coreModuleSelect->from('engine4_core_modules')->where( "name = 'core'" )->where('enabled = ?', 1);
        $coreModule = $coreModuleSelect->query()->fetchObject();
        if ( !empty($coreModule) && isset($coreModule->version) ) {
            $result = $this->checkVersion( $coreModule->version, '6.2.0');
            if ( $result == 0 ) {
                return $this->_error('<div class="global_form"><div><div>THe version is not compatiable with versions greater than 6.2.0 of Social Engine please contact support.</div></div></div>');
            }
        }

        parent::onPreInstall();
    }

    private function checkVersion($databaseVersion, $checkDependancyVersion) {
        $f = $databaseVersion;
        $s = $checkDependancyVersion;
        if (strcasecmp($f, $s) == 0)
            return -1;

        $fArr = explode(".", $f);
        $sArr = explode('.', $s);
        if (count($fArr) <= count($sArr))
            $count = count($fArr);
        else
            $count = count($sArr);

        for ($i = 0; $i < $count; $i++) {
            $fValue = $fArr[$i];
            $sValue = $sArr[$i];
            if (is_numeric($fValue) && is_numeric($sValue)) {
                if ($fValue > $sValue)
                    return 1;
                elseif ($fValue < $sValue)
                    return 0;
                else {
                    if (($i + 1) == $count) {
                        return -1;
                    } else
                        continue;
                }
            }
            elseif (is_string($fValue) && is_numeric($sValue)) {
                $fsArr = explode("p", $fValue);

                if ($fsArr[0] > $sValue)
                    return 1;
                elseif ($fsArr[0] < $sValue)
                    return 0;
                else {
                    return 1;
                }
            } elseif (is_numeric($fValue) && is_string($sValue)) {
                $ssArr = explode("p", $sValue);

                if ($fValue > $ssArr[0])
                    return 1;
                elseif ($fValue < $ssArr[0])
                    return 0;
                else {
                    return 0;
                }
            } elseif (is_string($fValue) && is_string($sValue)) {
                $fsArr = explode("p", $fValue);
                $ssArr = explode("p", $sValue);
                if ($fsArr[0] > $ssArr[0])
                    return 1;
                elseif ($fsArr[0] < $ssArr[0])
                    return 0;
                else {
                    if ($fsArr[1] > $ssArr[1])
                        return 1;
                    elseif ($fsArr[1] < $ssArr[1])
                        return 0;
                    else {
                        return -1;
                    }
                }
            }
        }
    }

    public function onInstall()
    {
        $db = $this->getDb();
        if($this->_databaseOperationType != 'upgrade'){
            $select = new Zend_Db_Select($db);
            $this->_addSitecourseBrowsePage();
            $this->_addSitecourseManagePage();
            $this->_addSitecourseProfilePage();
            $this->_addSitecourseLearningPage();
            $this->_addSitecourseTagsPage();
        }

        parent::onInstall();
    }

    protected function _addSitecourseManagePage()
    {
        $db = $this->getDb();

        // profile page
        $pageId = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sitecourse_index_manage')
        ->limit(1)
        ->query()
        ->fetchColumn();

        // insert if it doesn't exist yet
        if( !$pageId ) {
            // Insert page
            $db->insert('engine4_core_pages', array(
                'name' => 'sitecourse_index_manage',
                'displayname' => 'Course Manage Page',
                'title' => 'My Entries',
                'description' => 'This page lists a user\'s course entries.',
                'custom' => 0,
            ));
            $pageId = $db->lastInsertId();

            // Insert top
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $pageId,
                'order' => 1,
            ));
            $topId = $db->lastInsertId();

            // Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $pageId,
                'order' => 2,
            ));
            $mainId = $db->lastInsertId();

            // Insert top-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $pageId,
                'parent_content_id' => $topId,
            ));
            $topMiddleId = $db->lastInsertId();

            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $pageId,
                'parent_content_id' => $mainId,
                'order' => 2,
            ));
            $mainMiddleId = $db->lastInsertId();

            // Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $pageId,
                'parent_content_id' => $mainId,
                'order' => 1,
            ));
            $mainRightId = $db->lastInsertId();


             // Insert Tab container
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.container-tabs',
                'page_id' => $pageId,
                'parent_content_id' => $mainMiddleId,
                'order' => 1,
                'params' => '{"max":"2","title":"","name":"core.container-tabs","nomobile":"0"}'
            ));
            $tabContainerId = $db->lastInsertId();
            
            // Insert My courses
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.my-courses-sitecourse',
                'page_id' => $pageId,
                'parent_content_id' => $tabContainerId,
                'order' => 1,
                'params' => '{"title":"My Courses","titleCount":true}'
            ));

            // Insert My Purchased courses
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.my-purchased-courses-sitecourse',
                'page_id' => $pageId,
                'parent_content_id' => $tabContainerId,
                'order' => 2,
                'params' => '{"title":"My Purchased Courses","titleCount":true}'
            ));

             // Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.browse-menu',
                'page_id' => $pageId,
                'parent_content_id' => $topMiddleId,
                'order' => 2,
            ));

             // Insert Create new course
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.newcourse-sitecourse',
                'page_id' => $pageId,
                'parent_content_id' => $mainRightId,
                'order' => 1,
            ));

             // Insert search
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.search-sitecourse',
                'page_id' => $pageId,
                'parent_content_id' => $mainRightId,
                'order' => 2,
                'params' => '{"viewType":"vertical"}'
            ));

            // Insert tags
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.tags-sitecourse',
                'page_id' => $pageId,
                'parent_content_id' => $mainRightId,
                'order' => 3,
                'params' => '{"title":"Popular Courses Tags","titleCount":true,"itemCount":"12","alphabetical":"1","nomobile":"0","name":"sitecourse.tags-sitecourse"}'
            ));

        }

    }

    protected function _addSitecourseBrowsePage()
    {
        $db = $this->getDb();

        // profile page
        $pageId = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sitecourse_index_index')
        ->limit(1)
        ->query()
        ->fetchColumn();

        // insert if it doesn't exist yet
        if( !$pageId ) {
            // Insert page
            $db->insert('engine4_core_pages', array(
                'name' => 'sitecourse_index_index',
                'displayname' => 'Course Browse Page',
                'title' => 'Course Browse',
                'description' => 'This page lists course entries.',
                'custom' => 0,
            ));
            $pageId = $db->lastInsertId();

            // Insert top
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $pageId,
                'order' => 1,
            ));
            $topId = $db->lastInsertId();

            // Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $pageId,
                'order' => 2,
            ));
            $mainId = $db->lastInsertId();

            // Insert top-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $pageId,
                'parent_content_id' => $topId,
            ));
            $topMiddleId = $db->lastInsertId();

            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $pageId,
                'parent_content_id' => $mainId,
                'order' => 2,
            ));
            $mainMiddleId = $db->lastInsertId();

            // Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $pageId,
                'parent_content_id' => $mainId,
                'order' => 1,
            ));
            $mainRightId = $db->lastInsertId();

            // Insert menu -> Top Middle
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.browse-menu',
                'page_id' => $pageId,
                'parent_content_id' => $topMiddleId,
                'order' => 1,
            ));

            // Insert Full Width Slider -> Top Middle
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.fullwidth-slider-courses',
                'page_id' => $pageId,
                'parent_content_id' => $topMiddleId,
                'order' => 2,
                'params' => '{"titleCount":true,"itemCount":"3","sliderHieght":"380","truncationLimit":"20","coursesCriteria":"0","courseInfo":["postedBy","enrolledCount","category","difficultyLevel"],"nomobile":"0","name":"sitecourse.fullwidth-slider-courses"}'
            ));

            // Insert Course Carousel -> Top Middle
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.course-carousel',
                'page_id' => $pageId,
                'parent_content_id' => $topMiddleId,
                'order' => 3,
                'params' => '{"titleCount":true,"itemCount":"6","courseType":"1","sortingCriteria":"1","courseInfo":["postedBy","creationDate","category","difficultyLevel"],"tabletItemCount":"1","desktopItemCount":"3","carouselSpeed":"3000","nomobile":"0","name":"sitecourse.course-carousel"}'
            ));    

            // Insert Search Courses Form -> Top Middle
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.search-sitecourse',
                'page_id' => $pageId,
                'parent_content_id' => $topMiddleId,
                'order' => 4,
                'params' => '{"viewType":"horizontal"}'
            ));

            // Insert Browse Courses-> Main Middle         
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.browse-courses-sitecourse',
                'page_id' => $pageId,
                'parent_content_id' => $mainMiddleId,
                'order' => 1,
                'params' => '{"title":"","itemCount":"3","truncationLimit":"25","courseInfo":["postedBy","enrolledCount","category","difficultyLevel"],"nomobile":"0","name":"sitecourse.browse-courses-sitecourse"}'
            ));

            // Insert Newest Courses-> Main Middle         
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.newest-courses',
                'page_id' => $pageId,
                'parent_content_id' => $mainMiddleId,
                'order' => 2,
                'params' => '{"title":"Newest Courses","titleCount":true,"itemCount":"3","sortingCriteria":"0","courseInfo":["postedBy","creationDate","category","difficultyLevel"],"nomobile":"0","name":"sitecourse.newest-courses"}'
            ));

            // Insert Best Seller Courses-> Main Middle         
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.bestseller-courses',
                'page_id' => $pageId,
                'parent_content_id' => $mainMiddleId,
                'order' => 3,
                'params' => '{"title":"Best Seller Courses","titleCount":true,"itemCount":"3","sortingCriteria":"1","courseInfo":["postedBy","creationDate","category","difficultyLevel"],"nomobile":"0","name":"sitecourse.bestseller-courses"}'
            ));

            // Insert Top Rated Courses -> Main Middle         
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.toprated-courses',
                'page_id' => $pageId,
                'parent_content_id' => $mainMiddleId,
                'order' => 4,
                'params' => '{"title":"Top-Rated Courses","titleCount":true,"itemCount":"3","sortingCriteria":"1","courseInfo":["postedBy","creationDate","category","difficultyLevel"],"nomobile":"0","name":"sitecourse.toprated-courses"}'
            ));

            // Insert Categories -> Main Right
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.categories',
                'page_id' => $pageId,
                'parent_content_id' => $mainRightId,
                'order' => 3,
                'params' => '{"title":"Categories","titleCount":true,"showAll":"1","nomobile":"0","name":"sitecourse.categories"}'
            ));

             // Insert Create new course -> Main Right 
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.newcourse-sitecourse',
                'page_id' => $pageId,
                'parent_content_id' => $mainRightId,
                'order' => 2,
            ));

            // Insert tags -> Main Right
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.tags-sitecourse',
                'page_id' => $pageId,
                'parent_content_id' => $mainRightId,
                'order' => 3,
                'params' => '{"title":"Popular Courses Tags","titleCount":true,"itemCount":"12","alphabetical":"1","nomobile":"0","name":"sitecourse.tags-sitecourse"}'
            ));
        }
    }

    protected function _addSitecourseLearningPage()
    {
        $db = $this->getDb();

        // profile page
        $pageId = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sitecourse_learning_index')
        ->limit(1)
        ->query()
        ->fetchColumn();

        // insert if it doesn't exist yet
        if( !$pageId ) {
            // Insert page
            $db->insert('engine4_core_pages', array(
                'name' => 'sitecourse_learning_index',
                'displayname' => 'Course Learning Page',
                'title' => 'Course Learning page',
                'description' => 'This page displays lessons and topics.',
                'provides' => 'subject=sitecourse',
                'custom' => 0,
            ));
            $pageId = $db->lastInsertId();

            // Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $pageId,
                'order' => 2,
            ));
            $mainId = $db->lastInsertId();

            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $pageId,
                'parent_content_id' => $mainId,
                'order' => 2,
            ));
            $mainMiddleId = $db->lastInsertId();

            // Insert main-left
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'left',
                'page_id' => $pageId,
                'parent_content_id' => $mainId,
                'order' => 1,
            ));
            $mainLeftId = $db->lastInsertId();

            // Insert Course Learning Curriculum -> Main Left
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.course-learning-curriculum',
                'page_id' => $pageId,
                'parent_content_id' => $mainLeftId,
                'order' => 1
            ));

            // Insert Course Learning Content -> Main Middle
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.course-learning-content',
                'page_id' => $pageId,
                'parent_content_id' => $mainMiddleId,
                'order' => 1,
            ));

             // Insert Tab container
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.container-tabs',
                'page_id' => $pageId,
                'parent_content_id' => $mainMiddleId,
                'order' => 2,
                'params' => '{"max":"2","title":"","name":"core.container-tabs","nomobile":"0"}'
            ));
            $tabMainMiddleContainerId = $db->lastInsertId();

            // Insert Course Profile Benefits -> Main Middle Tab Container
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.course-benefits',
                'page_id' => $pageId,
                'parent_content_id' => $tabMainMiddleContainerId,
                'order' => 1,
                'params' => '{"title":"Course Benefits","titleCount":true}'
            ));

            // Insert Course Profile Benefits -> Main Middle Tab Container
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.course-overview',
                'page_id' => $pageId,
                'parent_content_id' => $tabMainMiddleContainerId,
                'order' => 1,
                'params' => '{"title":"Overview","titleCount":true}'

            ));

        }
    }

    protected function _addSitecourseProfilePage()
    {
        $db = $this->getDb();

        // profile page
        $pageId = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sitecourse_index_profile')
        ->limit(1)
        ->query()
        ->fetchColumn();

        // insert if it doesn't exist yet
        if( !$pageId ) {
            // Insert page
            $db->insert('engine4_core_pages', array(
                'name' => 'sitecourse_index_profile',
                'displayname' => 'Course Profile Page',
                'title' => 'Course View',
                'description' => 'This page displays a course entry.',
                'provides' => 'subject=sitecourse',
                'custom' => 0,
            ));
            $pageId = $db->lastInsertId();

             // Insert top
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $pageId,
                'order' => 1,
            ));
            $topId = $db->lastInsertId();

            // Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $pageId,
                'order' => 2,
            ));
            $mainId = $db->lastInsertId();

            // Insert top-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $pageId,
                'parent_content_id' => $topId,
            ));
            $topMiddleId = $db->lastInsertId();

            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $pageId,
                'parent_content_id' => $mainId,
                'order' => 2,
            ));
            $mainMiddleId = $db->lastInsertId();

            // Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $pageId,
                'parent_content_id' => $mainId,
                'order' => 1,
            ));
            $mainRightId = $db->lastInsertId();

            // Insert main-left
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'left',
                'page_id' => $pageId,
                'parent_content_id' => $mainId,
                'order' => 1,
            ));
            $mainLeftId = $db->lastInsertId();


            // Insert Course Profile Bredcrumb -> Top Middle
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.course-profile-breadcrumb',
                'page_id' => $pageId,
                'parent_content_id' => $topMiddleId,
                'order' => 1,
            ));

            // Insert Course Annoncement -> Top Middle
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.course-announcement',
                'page_id' => $pageId,
                'parent_content_id' => $topMiddleId,
                'order' => 2,
                'params' => '{"title":"Announcements","titleCount":true}'
            ));

            // Insert Course Promotional Video -> Top Middle
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.course-promotional-video',
                'page_id' => $pageId,
                'parent_content_id' => $topMiddleId,
                'order' => 3,
                'params' => '{"titleCount":true}'

            ));                        

            // Insert Course Onwer Info -> Main Left
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.course-owner-info',
                'page_id' => $pageId,
                'parent_content_id' => $mainLeftId,
                'order' => 1,
                'params' => '{"title":"Owner Info","titleCount":true}'
            ));        

            // Insert Course About Instructor -> Main Left
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.about-instructor',
                'page_id' => $pageId,
                'parent_content_id' => $mainLeftId,
                'order' => 2,
                'params' => '{"title":"Instructor","titleCount":true}'

            ));

            // Insert Course Overview -> Main Middle
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.course-overview',
                'page_id' => $pageId,
                'parent_content_id' => $mainMiddleId,
                'order' => 1,
                'params' => '{"title":"Overview","titleCount":true}'
            ));

            // Insert Course Benefits -> Main Middle
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.course-benefits',
                'page_id' => $pageId,
                'parent_content_id' => $mainMiddleId,
                'order' => 2,
                'params' => '{"title":"Course Benefits","titleCount":true}'
            ));

            // Insert Course Curriculum -> Main Middle
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.course-curriculum',
                'page_id' => $pageId,
                'parent_content_id' => $mainMiddleId,
                'order' => 3,
                'params' => '{"title":"Curriculums","titleCount":true}'
            ));

            // Insert Course Requirments -> Main Middle
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.course-requirements',
                'page_id' => $pageId,
                'parent_content_id' => $mainMiddleId,
                'order' => 4,
                'params' => '{"title":"Requirements","titleCount":true}'
            ));

            // Insert Course Review -> Main Middle
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.course-review',
                'page_id' => $pageId,
                'parent_content_id' => $mainMiddleId,
                'order' => 5,
                'params' => '{"title":"Course Reviews","titleCount":true}'
            ));

            // Insert Course Create -> Main Right
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.newcourse-sitecourse',
                'page_id' => $pageId,
                'parent_content_id' => $mainRightId,
                'order' => 1,
            ));

            // Insert Course Options -> Main Right
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.course-options',
                'page_id' => $pageId,
                'parent_content_id' => $mainRightId,
                'order' => 2,
                'params' => '{"title":"Options","titleCount":true}'
            ));
            
        }
    }

    protected function _addSitecourseTagsPage() {
        $db = $this->getDb();

        // profile page
        $pageId = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sitecourse_index_tagscloud')
        ->limit(1)
        ->query()
        ->fetchColumn();

        // insert if it doesn't exist yet
        if( !$pageId ) {
            // Insert page
            $db->insert('engine4_core_pages', array(
                'name' => 'sitecourse_index_tagscloud',
                'displayname' => 'Course Tags Page',
                'title' => 'Course Tags page',
                'description' => 'This page displays tags.',
                'provides' => 'subject=sitecourse',
                'custom' => 0,
            ));
            $pageId = $db->lastInsertId();
            // Insert top
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $pageId,
                'order' => 2,
            ));
            $topId = $db->lastInsertId();

            // Insert top-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $pageId,
                'parent_content_id' => $topId,
            ));
            $topMiddleId = $db->lastInsertId();

            // Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $pageId,
                'order' => 2,
            ));
            $mainId = $db->lastInsertId();

            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $pageId,
                'parent_content_id' => $mainId,
                'order' => 2,
            ));
            $mainMiddleId = $db->lastInsertId();

            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.browse-menu',
                'page_id' => $pageId,
                'parent_content_id' => $topMiddleId,
                'order' => 1,
            ));

            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitecourse.tags-sitecourse',
                'page_id' => $pageId,
                'parent_content_id' => $mainMiddleId,
                'order' => 1,
                'params' => '{"title":"Popular Courses Tags","titleCount":true,"itemCount":"12","alphabetical":"1","nomobile":"0","name":"sitecourse.tags-sitecourse"}'
            ));


        }
    }
}
?>
