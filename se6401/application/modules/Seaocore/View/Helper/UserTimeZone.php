<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 201-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Locale.php 6590 2016-01-21 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_View_Helper_UserTimeZone extends Engine_View_Helper_Locale
{
  public function userTimeZone($user = null)
  {
    if( !$user ) {
      $user = $this->view->viewer();
    }
    if( !$user || !$user->getIdentity() || !$user->timezone ) {
      return;
    }
    return $this->getUTCTimeZone($user->timezone);
  }

  protected function getUTCTimeZone($timezone)
  {
    $timezones = array(
      'US/Pacific' => '(UTC-8) Pacific Time (US & Canada)',
      'US/Mountain' => '(UTC-7) Mountain Time (US & Canada)',
      'US/Central' => '(UTC-6) Central Time (US & Canada)',
      'US/Eastern' => '(UTC-5) Eastern Time (US & Canada)',
      'America/Halifax' => '(UTC-4)  Atlantic Time (Canada)',
      'America/Anchorage' => '(UTC-9)  Alaska (US & Canada)',
      'Pacific/Honolulu' => '(UTC-10) Hawaii (US)',
      'Pacific/Samoa' => '(UTC-11) Midway Island, Samoa',
      'Etc/GMT-12' => '(UTC-12) Eniwetok, Kwajalein',
      'Canada/Newfoundland' => '(UTC-3:30) Canada/Newfoundland',
      'America/Buenos_Aires' => '(UTC-3) Brasilia, Buenos Aires, Georgetown',
      'Atlantic/South_Georgia' => '(UTC-2) Mid-Atlantic',
      'Atlantic/Azores' => '(UTC-1) Azores, Cape Verde Is.',
      'Europe/London' => 'Greenwich Mean Time (Lisbon, London)',
      'Europe/Berlin' => '(UTC+1) Amsterdam, Berlin, Paris, Rome, Madrid',
      'Europe/Athens' => '(UTC+2) Athens, Helsinki, Istanbul, Cairo, E. Europe',
      'Europe/Moscow' => '(UTC+3) Baghdad, Kuwait, Nairobi, Moscow',
      'Iran' => '(UTC+3:30) Tehran',
      'Asia/Dubai' => '(UTC+4) Abu Dhabi, Kazan, Muscat',
      'Asia/Kabul' => '(UTC+4:30) Kabul',
      'Asia/Yekaterinburg' => '(UTC+5) Islamabad, Karachi, Tashkent',
      'Asia/Calcutta' => '(UTC+5:30) Bombay, Calcutta, New Delhi',
      'Asia/Katmandu' => '(UTC+5:45) Nepal',
      'Asia/Omsk' => '(UTC+6) Almaty, Dhaka',
      'India/Cocos' => '(UTC+6:30) Cocos Islands, Yangon',
      'Asia/Krasnoyarsk' => '(UTC+7) Bangkok, Jakarta, Hanoi',
      'Asia/Hong_Kong' => '(UTC+8) Beijing, Hong Kong, Singapore, Taipei',
      'Asia/Tokyo' => '(UTC+9) Tokyo, Osaka, Sapporto, Seoul, Yakutsk',
      'Australia/Adelaide' => '(UTC+9:30) Adelaide, Darwin',
      'Australia/Sydney' => '(UTC+10) Brisbane, Melbourne, Sydney, Guam',
      'Asia/Magadan' => '(UTC+11) Magadan, Soloman Is., New Caledonia',
      'Pacific/Auckland' => '(UTC+12) Fiji, Kamchatka, Marshall Is., Wellington',
    );
    return isset($timezones[$timezone]) ? $timezones[$timezone] : '';
  }

}
