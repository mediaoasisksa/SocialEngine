<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    https://socialapps.tech/license/
 * @version    $Id: Controller.php 2010-11-18 9:40:21Z SocialApps.tech $
 * @author     SocialApps.tech
 */

class Sitecore_Widget_SeaocoresNewsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Zend_Feed required DOMDocument
    // @todo add to sanity check
    if( !class_exists('DOMDocument', false) ) {
      $this->view->badPhpVersion = true;
      return;
      //return $this->setNoRender();
    }

    $rss = Zend_Feed::import('https://socialapps.tech/blog/feed');
    $channel = array(
      'title'       => $rss->title(),
      'link'        => $rss->link(),
      'description' => $rss->description(),
      'items'       => array()
    );

    $max = $this->_getParam('max', 10);
    $count = 0;

    // Loop over each channel item and store relevant data
    foreach( $rss as $item )
    {
      if( $count++ >= $max ) break;
      $channel['items'][] = array(
        'title'       => $item->title(),
        'link'        => $item->link(),
        'description' => $item->description(),
        'pubDate'     => $item->pubDate(),
        'guid'        => $item->guid(),
      );
    }

    $this->view->channel = $channel;
  }
}