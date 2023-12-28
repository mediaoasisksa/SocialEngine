<?php

/**
 * DomElementsSeao
 *
 * For changing DomElements ids (e.g. global_header, global_footer, global_wrapper, global_content)
 *
 */
class DomElementsSeao
{

  protected static $cssKeys = array();
  protected static $swapReplaceKeys = array();

  /**
   * Replaces the ids
   *
   * @return void
   */
  public static function process()
  {
    $tempId = 'temp_' . time();
    if( strpos(Scaffold::$css->path, 'se-atlas') !== false ) {
      self::$swapReplaceKeys = array('#global_content' => '#global_' . $tempId . 'content', '#global_wrapper' => '#global_' . $tempId . 'wrapper', '#global_header' => '#global_' . $tempId . 'header', '#global_footer' => '#global_' . $tempId . 'footer');
      self::$cssKeys = array('#global_content' => '#se-content', '#global_wrapper' => '#se-main', '#global_header' => '#se-header', '#global_footer' => '#se-footer');
      Scaffold::$css->string = self::replace(Scaffold::$css->string);
    }
  }

  /**
   * Replaces Dom Elements ids in a CSS string
   *
   */
  public static function replace($css)
  {
    // replacing with temp ids
    $cssKeys = self::$cssKeys;
    $finderKeys = array_keys($cssKeys);
    $reg = '/(' . join('[0-9A-Za-z-_]+)|(', $finderKeys) . '[0-9A-Za-z-_]+)/';
    $css = preg_replace_callback($reg, array('DomElementsSeao', 'setTempKey'), $css);
    // replacing the desired ids
    $css = str_replace($finderKeys, $cssKeys, $css);
    // reverting temp ids to original one
    $css = str_replace(self::$swapReplaceKeys, array_keys(self::$swapReplaceKeys), $css);
    return $css;
  }

  public function setTempKey($data)
  {
    $string = $data[1];
    foreach( self::$swapReplaceKeys as $search => $replace ) {
      if( strpos($string, $search) == 0 ) {
        $string = str_replace($search, $replace, $string);
        break;
      }
    }
    return $string;
  }

}
