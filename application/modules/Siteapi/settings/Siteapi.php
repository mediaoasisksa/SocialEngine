<?php

class Engine_Boot_Siteapi extends Engine_Boot_Abstract {
    public function beforeBoot() {
        if (empty($_SERVER['REQUEST_URI'])) {
            return;
        }
        
        $getRequestUri = trim(htmlspecialchars($_SERVER['REQUEST_URI']), '/');
        if (strstr($getRequestUri, "api/rest")) {
            $this->_boot->setRootBootFileName('siteapi.php');
        }
    }

}
