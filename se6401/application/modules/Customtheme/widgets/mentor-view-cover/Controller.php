<?php

class Customtheme_Widget_MentorViewCoverController extends Engine_Content_Widget_Abstract{
  public function indexAction(){
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    if (!Engine_Api::_()->core()->hasSubject('user')) {
      $this->view->user = $user = $viewer;
    } else {
      $this->view->user = $user = Engine_Api::_()->core()->getSubject('user');
    }

    if (!$user->getIdentity()) {
      return $this->setNoRender();
    }
    $params = array();
    $params['status'] = "1";
    $params['approved'] = "1";
    $params['type'] = "2";
    $params['user_id'] = $user->getIdentity();
    $params['limit'] = 1;
    $sql = Engine_Api::_()->getItemTable('sitebooking_ser')->getServicesSelect($params);
    $dataMentor = Engine_Api::_()->getItemTable('sitebooking_ser')->fetchRow($sql);
    $this->view->dataMentor = '';
    if($dataMentor) {
        $this->view->dataMentor = $dataMentor = $dataMentor->toArray();
    }
    
    $params = array();
    $params['status'] = "1";
    $params['approved'] = "1";
    $params['type'] = "1";
    $params['user_id'] = $user->getIdentity();
    $params['limit'] = 1;
    $sql = Engine_Api::_()->getItemTable('sitebooking_ser')->getServicesSelect($params);
    $dataConsultant = Engine_Api::_()->getItemTable('sitebooking_ser')->fetchRow($sql);
    if($dataConsultant) {
        $this->view->dataConsultant = $dataConsultant = $dataConsultant->toArray();
    }
    // Load fields view helpers
    $view = $this->view;
    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

    // Values
    $this->view->fieldStructure = $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($user);
    //echo $this->view->fieldValueLoop($user, $this->view->fieldStructure);die;
    $profileType ='';$educationlevel='';$educationinstitute='';$linkedinLink='';$twitterLink='';$country='';$city='';
    $values = Engine_Api::_()->fields()->getTable('user', 'values')->getValues($user);
    //print_r($values->toArray());die;
    // Array ( [0] => Array ( [item_id] => 1729 [field_id] => 1 [index] => 0 [value] => 4 [privacy] => everyone ) [1] => Array ( [item_id] => 1729 [field_id] => 7 [index] => 0 [value] => Marwan [privacy] => everyone ) [2] => Array ( [item_id] => 1729 [field_id] => 8 [index] => 0 [value] => Asmawi [privacy] => everyone ) [3] => Array ( [item_id] => 1729 [field_id] => 11 [index] => 0 [value] => Saudi Arabia [privacy] => everyone ) [4] => Array ( [item_id] => 1729 [field_id] => 12 [index] => 0 [value] => Riyadh [privacy] => everyone ) [5] => Array ( [item_id] => 1729 [field_id] => 41 [index] => 0 [value] => 111 [privacy] => everyone ) )
        foreach($values->toArray() as $value) {
            if($value['field_id'] == 36) {
                $fname = $value['value'];
            } else if($value['field_id'] == 47) {
                $fname = $value['value'];
            } elseif($value['field_id'] ==37) {
                $lname = $value['value'];
            } else if($value['field_id'] == 48) {
                $lname = $value['value'];
            } else if($value['field_id'] == 67) {
                if($value['value'] == 22) {
                    $profileType = "Student";
                } else if($value['value'] == 21) {
                    $profileType = "Professional";
                }
            }  else if($value['field_id'] == 1) {
                if($value['value'] == 13) {
                    $profileType = "Consultant / Mentor";
                } 
            } else if($value['field_id'] == 72) {
                $linkedinLink = $value['value'];
            } else if($value['field_id'] == 74) {
                $linkedinLink = $value['value'];
            } elseif($value['field_id'] ==73) {
                $twitterLink = $value['value'];
            } else if($value['field_id'] == 75) {
                $twitterLink = $value['value'];
            } elseif($value['field_id'] == 71) {
                $educationinstitute = $value['value'];
            }  elseif($value['field_id'] == 70) {
                if($value['value'] == 23) {
                    $educationlevel = 'High School';
                } 
                if($value['value'] == 24) {
                    $educationlevel = 'Bachelor Degree';
                } 
                 if($value['value'] == 25) {
                    $educationlevel = 'Master Degree';
                } 
                 if($value['value'] == 26) {
                    $educationlevel = 'Doctorate';
                } 
                 if($value['value'] == 27) {
                    $educationlevel = 'Others';
                } 
            } elseif($value['field_id'] == 65) {
                $country = $value['value'];
            } else if($value['field_id'] == 60) {
                $country = $value['value'];
            } else if($value['field_id'] == 66) {
                $city = $value['value'];
            } else if($value['field_id'] == 62) {
                $city = $value['value'];
            }
        }
  
        
    $this->view->fname = $fname;
    $this->view->lname = $lname;
    $this->view->profileType =$profileType;
    $this->view->linkedinLink =$linkedinLink;
    $this->view->twitterLink =$twitterLink;
    $this->view->educationlevel =$educationlevel;
    $this->view->educationinstitute =$educationinstitute;
    $this->view->country =$country;
    $this->view->city =$city;
  }
}
?>