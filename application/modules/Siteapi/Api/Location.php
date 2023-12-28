<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Cache.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_Api_Location extends Core_Api_Abstract {

    /**
     * Return $suggestGooglePalces 
     *
     * @param char $keyword
     * @param int $latitude
     * @param int $longitude
     */
    public function getSuggestGooglePalces($keyword, $latitude = 0, $longitude = 0) {
        //GET API KEY
        $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();

        //GET LATITUDE
        $latitude = str_replace(',', '.', "$latitude");

        //GET LONGITUDE
        $longitude = str_replace(',', '.', "$longitude");
        //SET PARAMS
        $params = array(
            'key' => $apiKey,
            'sensor' => 'false',
            'input' => $keyword,
            'language' => $this->getGoogleMapLocale(),
        );

        //SET LOCATION
        if ($latitude != '0' && $longitude != '0') {
            $params['location'] = $latitude . ',' . $longitude;
        }

        //BUILD QUERY STRING
        $query = http_build_query($params, null, '&');

        //SET URL
        $url = 'https://maps.googleapis.com/maps/api/place/autocomplete/json?' . $query;

        //SEND CURL REQUEST
        $ch = curl_init();
        $timeout = 0;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        ob_start();
        curl_exec($ch);
        curl_close($ch);

        //GET CURL RESPONSE
         $doGetContents = ob_get_contents();
         $response = !empty($doGetContents) ? Zend_Json::decode($doGetContents) : array();
        //IF EMPTY REESPONSE THEN GET RESPONSE FROM FILE GET CONTENTS
        if (empty($response)) {
            $fileGetContents = file_get_contents($url);
            $response = !empty($fileGetContents) ? Zend_Json::decode($fileGetContents) : array();
        }

        ob_end_clean();

        //IF STATUS IS NOT OK THE RETURN
        if (!isset($response['status']) || $response['status'] != 'OK') {
            return array();
        }

        //GET PREDICTIONS
        $results = isset($response['predictions']) ? $response['predictions'] : array();

        //MAKE SUGGEST ARRAY
        $suggestGooglePalces = array();
        foreach ($results as $place) {
            $suggestGooglePalces[] = array(
                'resource_guid' => 0,
                //'google_id' => $place['id'],
                'label' => $place['description'],
                'place_id' => $place['place_id'],
            );
        }
        return $suggestGooglePalces;
    }

    /**
     * Return $locale 
     *
     * @param char $locale
     */
    public function getGoogleMapLocale($locale = false) {

        if (!$locale) {
            // Set the translations for zend library.
            if (!Zend_Registry::isRegistered('Zend_Translate'))
                Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();

            $locale = Zend_Registry::get('Zend_Translate')->getLocale();
        }

        $british_english = array('en_AU', 'en_BE', 'en_BW', 'en_BZ', 'en_GB', 'en_GU', 'en_HK', 'en_IE', 'en_IN',
            'en_MT', 'en_NA', 'en_NZ', 'en_PH', 'en_PK', 'en_SG', 'en_ZA', 'en_ZW', 'kw', 'kw_GB');

        $friulian = array('fur', 'fur_IT');

        $swiss_german = array('gsw', 'gsw_CH');

        $norwegian_bokma = array('nb', 'nb_NO');

        $portuguese = array('pt', 'pt_PT');

        $brazilian_portuguese = array('pt_BR');

        $chinese = array('zh', 'zh_CN');

        $sar_china = array('zh_HK', 'zh_MO', 'zh_SG');

        $taiwan = array('zh_TW');

        if (in_array($locale, $british_english)) {
            $locale = 'en-GB';
        } elseif (in_array($locale, $friulian)) {
            $locale = 'it';
        } elseif (in_array($locale, $swiss_german)) {
            $locale = 'de';
        } elseif (in_array($locale, $norwegian_bokma)) {
            $locale = 'no';
        } elseif (in_array($locale, $portuguese)) {
            $locale = 'pt-PT';
        } elseif (in_array($locale, $brazilian_portuguese)) {
            $locale = 'pt-BR';
        } elseif (in_array($locale, $chinese)) {
            $locale = 'zh-CN';
        } elseif (in_array($locale, $sar_china)) {
            $locale = 'zh-HK';
        } elseif (in_array($locale, $taiwan)) {
            $locale = 'zh-TW';
        } elseif ($locale) {
            $locale_arr = explode('_', $locale);
            $locale = ($locale_arr[0]) ? $locale_arr[0] : 'en';
        } else {
            $locale = 'en';
        }

        return $locale;
    }

}
