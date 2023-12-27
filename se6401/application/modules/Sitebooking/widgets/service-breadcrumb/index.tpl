<?php
  echo $this->htmlLink(array('action' => 'home','route' => 'sitebooking_service_browse'), "Service Home ");

  if(!empty($this->category_name)) 
  echo $this->htmlLink(array('action' => 'index','route' => 'sitebooking_service_browse','category' => $this->item->category_id,'reset' => true), $this->category_name);
      
  if(!empty($this->first_level_category_name))
  echo " ".$this->htmlLink(array('action' => 'index','route' => 'sitebooking_service_browse','first_level_category_id' => $this->item->first_level_category_id,'reset' => true), $this->first_level_category_name);
  
  if(!empty($this->second_level_category_name))
  echo "  ".$this->htmlLink(array('action' => 'index','route' => 'sitebooking_service_browse','second_level_category_id' => $this->item->second_level_category_id,'reset' => true), $this->second_level_category_name);

  echo " ".$this->htmlLink($this->item->getHref(), $this->item->getTitle())  
?>