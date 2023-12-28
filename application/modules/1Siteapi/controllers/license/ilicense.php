<?php 
$db = Engine_Db_Table::getDefaultAdapter();

if( empty($getModURLSettings) ) {
  if( !empty($getHostingSettingsFromLicenseFiles) ) {
	  if( in_array($host_name, $getDomainArray) ) {	    
	    $front = Zend_Controller_Front::getInstance();
	    $module = $front->getRequest()->getModuleName();
	    $controller = $front->getRequest()->getControllerName();
	    $action = $front->getRequest()->getActionName();
	    if( !empty($license_from_the_plugin_modsite) ) {
	      $get_value = $product_sql_queary;
	    }else if(( $module == "default" && $controller == "manage" && $action == "install") || $module == "default" && $controller == "install" && $action == "db-create" ){
		  $PluOrder = explode("::", $product_sql_queary);
		  if ( !empty($PluOrder) ) {
			  $PluOrder = $PluOrder[1];
		  }
		  $dbinfo = Engine_Db_Table::getDefaultAdapter()->getConfig();
		  $dbname = $dbinfo["dbname"];
		  $host = $dbinfo["host"];
		  $password = $dbinfo["password"];
		  $username = $dbinfo["username"];
		  $link = mysql_connect($host, $username, $password);
		  if (!$link) {
			  die("Not connected : " . mysql_error());
		  }
		  $db_selected = mysql_select_db($dbname, $link);
		  if (!$db_selected) {
			  die("Can't use : " . mysql_error());
		  }

		  $db     = $this->getDb();
		  $select = new Zend_Db_Select($db);
		  $select
		    ->from("engine4_core_menuitems");
		  $queary_info = $select->query()->fetchObject();
		  if( empty($queary_info) ) {
		    $PluOrder = explode(";", $PluOrder);
		    foreach($PluOrder as $sql) {
		      if(!empty($sql)) {
			mysql_query($sql);
		      }
		    }
		  }
	  }
	  }
  }
}

if( empty($getModURLSettings) ) {
  if( !empty($getHostingSettingsFromLicenseFiles) ) {
	  if( in_array($host_name, $getDomainArray) ) {	    
	    $front = Zend_Controller_Front::getInstance();
	    $module = $front->getRequest()->getModuleName();
	    $controller = $front->getRequest()->getControllerName();
	    $action = $front->getRequest()->getActionName();
	    if( !empty($license_from_the_plugin_modsite) ) {
	      $get_value = $product_sql_queary;
	    }else if(( $module == "default" && $controller == "manage" && $action == "install") || $module == "default" && $controller == "install" && $action == "db-create" ){
		  $PluOrder = explode("::", $product_sql_queary);
		  if ( !empty($PluOrder) ) {
			  $PluOrder = $PluOrder[1];
		  }
		  $dbinfo = Engine_Db_Table::getDefaultAdapter()->getConfig();
		  $dbname = $dbinfo["dbname"];
		  $host = $dbinfo["host"];
		  $password = $dbinfo["password"];
		  $username = $dbinfo["username"];
		  $link = mysql_connect($host, $username, $password);
		  if (!$link) {
			  die("Not connected : " . mysql_error());
		  }
		  $db_selected = mysql_select_db($dbname, $link);
		  if (!$db_selected) {
			  die("Can't use : " . mysql_error());
		  }

		  $db     = $this->getDb();
		  $select = new Zend_Db_Select($db);
		  $select
		    ->from("engine4_core_menuitems");
		  $queary_info = $select->query()->fetchObject();
		  if( empty($queary_info) ) {
		    $PluOrder = explode(";", $PluOrder);
		    foreach($PluOrder as $sql) {
		      if(!empty($sql)) {
			mysql_query($sql);
		      }
		    }
		  }
	  }
	  }
  }
}



      $isValidPlugin = @base64_encode("siteapi");
	    $product_sql_queary =   'ipaetis::
CREATE TABLE IF NOT EXISTS `engine4_siteapi_oauth_consumers` (
  `consumer_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `key` varchar(36) NOT NULL COMMENT "OAuth consumer key",
  `secret` varchar(36) NOT NULL COMMENT "OAuth consumer secret",
  `creation_date` date NOT NULL,
  `modified_date` date NOT NULL,
  `callback_url` varchar(128) DEFAULT NULL,
  `status` tinyint(2) NOT NULL DEFAULT "1",
  PRIMARY KEY (`consumer_id`),
  UNIQUE KEY `oauth_consumer_key` (`key`),
  UNIQUE KEY `oauth_consumer_secret` (`secret`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `engine4_siteapi_oauth_tokens` (
  `token_id` int(11) NOT NULL AUTO_INCREMENT,
  `consumer_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `type` varchar(16) NOT NULL,
  `token` varchar(32) NOT NULL,
  `secret` varchar(32) NOT NULL,
  `verifier` varchar(32) DEFAULT NULL,
  `callback_url` varchar(256) NOT NULL,
  `revoked` tinyint(2) NOT NULL DEFAULT "0",
  `authorized` tinyint(2) NOT NULL DEFAULT "0",
  `num_of_login` int(5) NOT NULL DEFAULT "1" COMMENT "In how much device client login",
  `creation_date` date NOT NULL,
  PRIMARY KEY (`token_id`),
  UNIQUE KEY `oauth_token` (`token`),
  KEY `oauth_consumer_id` (`consumer_id`),
  KEY `oauth_user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("core_admin_plugins_siteapi", "siteapi", "Almahub REST - APIs", "", \'{"route":"admin_default","module":"siteapi","controller":"settings","action":"readme"}\', "core_admin_main_plugins", "", 999),
("siteapi_admin_settings", "siteapi", "Global Settings", "", \'{"route":"admin_default","module":"siteapi","controller":"settings"}\', "siteapi_admin_main", "", 0),
("siteapi_admin_faq", "siteapi", "FAQ", "", \'{"route":"admin_default","module":"siteapi","controller":"settings","action":"faq"}\', "siteapi_admin_main", "", 999);

 INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
	("siteapi.iosdevice.type", "679625"),
	("siteapi.androiddevice.type", "678705")
';
	   
                 
            
                $product_sql_queary = $product_sql_queary;
                $PluOrder = explode("::", $product_sql_queary);
                if ( !empty($PluOrder) ) {
                        $PluOrder = $PluOrder[1];
                }

                $PluOrder = explode(";", $PluOrder);  
                foreach($PluOrder as $sql) {
                    if(!empty($sql)) {
                      $db->query($sql);
                    }
                  }
		  $get_value = strrev("siteapi");
	  
	  
    
if( empty($getModURLSettings) ) {
  if( !empty($getHostingSettingsFromLicenseFiles) ) {
	  if( in_array($host_name, $getDomainArray) ) {	    
	    $front = Zend_Controller_Front::getInstance();
	    $module = $front->getRequest()->getModuleName();
	    $controller = $front->getRequest()->getControllerName();
	    $action = $front->getRequest()->getActionName();
	    if( !empty($license_from_the_plugin_modsite) ) {
	      $get_value = $product_sql_queary;
	    }else if(( $module == "default" && $controller == "manage" && $action == "install") || $module == "default" && $controller == "install" && $action == "db-create" ){
		  $PluOrder = explode("::", $product_sql_queary);
		  if ( !empty($PluOrder) ) {
			  $PluOrder = $PluOrder[1];
		  }
		  $dbinfo = Engine_Db_Table::getDefaultAdapter()->getConfig();
		  $dbname = $dbinfo["dbname"];
		  $host = $dbinfo["host"];
		  $password = $dbinfo["password"];
		  $username = $dbinfo["username"];
		  $link = mysql_connect($host, $username, $password);
		  if (!$link) {
			  die("Not connected : " . mysql_error());
		  }
		  $db_selected = mysql_select_db($dbname, $link);
		  if (!$db_selected) {
			  die("Can't use : " . mysql_error());
		  }

		  $db     = $this->getDb();
		  $select = new Zend_Db_Select($db);
		  $select
		    ->from("engine4_core_menuitems");
		  $queary_info = $select->query()->fetchObject();
		  if( empty($queary_info) ) {
		    $PluOrder = explode(";", $PluOrder);
		    foreach($PluOrder as $sql) {
		      if(!empty($sql)) {
			mysql_query($sql);
		      }
		    }
		  }
	  }
	  }
  }
}

if( empty($getModURLSettings) ) {
  if( !empty($getHostingSettingsFromLicenseFiles) ) {
	  if( in_array($host_name, $getDomainArray) ) {	    
	    $front = Zend_Controller_Front::getInstance();
	    $module = $front->getRequest()->getModuleName();
	    $controller = $front->getRequest()->getControllerName();
	    $action = $front->getRequest()->getActionName();
	    if( !empty($license_from_the_plugin_modsite) ) {
	      $get_value = $product_sql_queary;
	    }else if(( $module == "default" && $controller == "manage" && $action == "install") || $module == "default" && $controller == "install" && $action == "db-create" ){
		  $PluOrder = explode("::", $product_sql_queary);
		  if ( !empty($PluOrder) ) {
			  $PluOrder = $PluOrder[1];
		  }
		  $dbinfo = Engine_Db_Table::getDefaultAdapter()->getConfig();
		  $dbname = $dbinfo["dbname"];
		  $host = $dbinfo["host"];
		  $password = $dbinfo["password"];
		  $username = $dbinfo["username"];
		  $link = mysql_connect($host, $username, $password);
		  if (!$link) {
			  die("Not connected : " . mysql_error());
		  }
		  $db_selected = mysql_select_db($dbname, $link);
		  if (!$db_selected) {
			  die("Can't use : " . mysql_error());
		  }

		  $db     = $this->getDb();
		  $select = new Zend_Db_Select($db);
		  $select
		    ->from("engine4_core_menuitems");
		  $queary_info = $select->query()->fetchObject();
		  if( empty($queary_info) ) {
		    $PluOrder = explode(";", $PluOrder);
		    foreach($PluOrder as $sql) {
		      if(!empty($sql)) {
			mysql_query($sql);
		      }
		    }
		  }
	  }
	  }
  }
}
 ?>
