<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    https://socialapps.tech/license/
 * @version    $Id: _formButtonCancelPhoto.tpl 2010-08-17 9:40:21Z SocialApps.tech $
 * @author     SocialApps.tech
 */
echo '<button type="submit" id="done" name="done">'.$this->translate('Save').'</button>
      '.$this->translate('or').' <a href="javascript:void(0);" onclick="parent.Smoothbox.close();">'.$this->translate('Cancel').'</a>'
?>
