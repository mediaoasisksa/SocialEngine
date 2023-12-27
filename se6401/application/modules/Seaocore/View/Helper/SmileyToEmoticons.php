<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: UserFriendship.php 8835 2011-04-10 05:11:55Z jung $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Seaocore_View_Helper_SmileyToEmoticons extends Zend_View_Helper_Abstract {

    public function smileyToEmoticons($string) {
        $SEA_EMOTIONS_TAG = unserialize(SEA_EMOTIONS_TAG);
        if (!empty($SEA_EMOTIONS_TAG)) {
// $string = htmlspecialchars_decode($string);
            $string = str_replace("&lt;:o)", "<:o)", $string);
            $string = str_replace("(&amp;)", "(&)", $string);
            $SEA_EMOTIONS_TAG = @array_merge($SEA_EMOTIONS_TAG[0], $SEA_EMOTIONS_TAG[1]);

            $string = strtr($string, $SEA_EMOTIONS_TAG);
            $translate = Zend_Registry::get('Zend_Translate');
            $string = preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/", "<img class=\"emotions_use\"  src=\"" . $this->view->layout()->staticBaseUrl . "application/modules/Seaocore/externals/emoticons/$1\" border=\"0\" />", $string);
        }
        $content = $this->smileyToEmoji($string);
        return ($this->view->BBCode($content, array('link_no_preparse' => true)));
    }

  private function smileyToEmoji($content)
  {
//    $dir = APPLICATION_PATH . '/application/modules/Seaocore/settings/config/emojiInfo.php';
//    $data = file_get_contents($dir);
//    $icons = json_decode($data, true);
//    $urls = array();
//    foreach( $icons as $tags ) {
//      foreach( $tags['icons'] as $key => $tag ) {
//        $utf8 = html_entity_decode('&#x'.str_replace('_', ';&#x', $tag).';', ENT_COMPAT, 'UTF-8');
//        $code = str_replace('"', '', json_encode($utf8));
//        $urls[$code] = $tag;
//      }
//    }
//    $file_contents = "<?php return ";
//    $file_contents .= var_export($urls, true);
//    $file_contents .= "; ";
//    file_put_contents(APPLICATION_PATH . '/application/modules/Seaocore/settings/config/emojiUTFCodeMap.php', $file_contents);
//    die;
    $urls = include APPLICATION_PATH . '/application/modules/Seaocore/settings/config/emojiUTFCodeMap.php';
    $jcontent = json_encode($content);
    $format = "/(\\\\u[0-9a-f]{4}\\\\u[0-9a-f]{4}\\\\u[0-9a-f]{4}\\\\u[0-9a-f]{4})|(\\\\u[0-9a-f]{4}\\\\u[0-9a-f]{4})|(\\\\u[0-9a-f]{4})/";
    preg_match_all($format, $jcontent, $matches);
    if( !isset($matches[0][0]) ) {
      return $content;
    }
    $replace1 = array();
    $replace2 = array();
    foreach( $matches[0] as $emj ) {
      if( isset($urls[$emj]) ) {
        $md5Code = md5($emj);
        $replace1[$emj] = $md5Code;
        $replace2[$md5Code] = "<img class='emotions_use' src='" . $this->view->layout()->staticBaseUrl . "application/modules/Seaocore/externals/emoji/emoji_" . $urls[$emj] . ".png' />";
      }
    }

    $jcontent = str_replace(array_keys($replace1), array_values($replace1), $jcontent);
    $content = json_decode($jcontent);
    return str_replace(array_keys($replace2), array_values($replace2), $content);
  }

}
