
$provider = Engine_Api::_()->getItemTable('sitebooking_pro');
    $location = Engine_Api::_()->getItemTable('sitebooking_providerlocation');
    $db = $provider->getAdapter();
    $db->beginTransaction();

    try {
    
            $valuess = array();
            $valuess['title'] = $data['fname'] . ' ' . $data['lname'];
            $valuess['slug'] = "provider-" . $user->getIdentity();
            $valuess['designation'] = $data['specialist'];
            $valuess['description'] = "Consulting Service";
            $valuess['status'] =1;
            $valuess['timezone'] =$data['timezone'];
            $valuess['location'] =$data['country'];
            $valuess['city'] =$data['city'];
            $valuess['view'] ="everyone";
            $valuess['comment'] ="registered";
            $valuess['owner_id'] =$user->getIdentity();
            $valuess['approved'] =1;
            $provider = $provider->createRow();
            $provider->setFromArray($valuess);
            $provider->save();
    


          // Auth
          $auth = Engine_Api::_()->authorization()->context;
          $roles = array('owner_network', 'registered', 'everyone');
    
          $viewMax = array_search($valuess['view'], $roles);
    
          foreach( $roles as $i => $role ) {
              $auth->setAllowed($provider, $role, 'view', ($i <= $viewMax));
          }
    
          $roles = array('owner_network', 'registered', 'everyone');
    
          $viewMax = array_search($valuess['comment'], $roles);
    
          foreach( $roles as $i => $role ) {
              $auth->setAllowed($provider, $role, 'comment', ($i <= $viewMax));
          }
    $table = Engine_Api::_()->getItemTable('sitebooking_ser');

        $valuesss = array();
        $valuesss['title'] =  "Consulting Service";
        $valuesss['price'] = $data['price'];
        $valuesss['description'] = $data['description'];
        $valuesss['slug'] = "service-" . $provider->getIdentity();
        $valuesss['category_id'] = $data['consulatant_category_id'];
        $valuesss['duration'] = $data['duration'] * 60;
        $valuesss['view'] = "everyone";
        $valuesss['comment'] ="registered";
        $valuesss['owner_id'] = $user->getIdentity();
        $valuesss['approved'] = 1;
        $valuesss['parent_type'] = 'sitebooking_pro';
        $valuesss['parent_id'] = $provider->getIdentity();
        $valuesss['status'] = 1;
        $valuesss['type'] = 1;
        $providerS = $table->createRow();
        $providerS->setFromArray($valuesss);
        $providerS->save();
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner_network', 'registered', 'everyone');
        
        $viewMax = array_search($valuesss['view'], $roles);
        
        foreach( $roles as $i => $role ) {
            $auth->setAllowed($providerS, $role, 'view', ($i <= $viewMax));
        }
        
        $roles = array('owner_network', 'registered', 'everyone');
        
        $viewMax = array_search($valuesss['comment'], $roles);
            
        foreach( $roles as $i => $role ) {
            $auth->setAllowed($providerS, $role, 'comment', ($i <= $viewMax));
        }
          
          
      $db->commit();
        }
        catch (Execption $e) {
          $db->rollBack();
          }