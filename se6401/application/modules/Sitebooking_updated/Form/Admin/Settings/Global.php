<?php
class Sitebooking_Form_Admin_Settings_Global extends Engine_Form
{

  protected $_priorityCurrencies = array(
    'USD' => 1,
    'EUR' => 2,
  );

  protected $_currencies;

  public function init()
  {
    
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');
    $this->addElement('Text', 'sitebooking_page', array(
      'label' => 'Entries Per Page',
      'description' => 'How many entries of Providers / Services will be shown per page in case of Browse Services, Browse Providers, Manage Providers and Manage Services pages? (Enter a number between 1 and 999).',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.page', 10),
    ));

    $this->addElement('Text', 'sitebooking_bookingsingular', array(
      'label' => 'Plugin URL Alternate Text for "booking"',
      'description' => 'Please enter the text below which you want to display in place of "booking" in the URLs of this plugin.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.bookingsingular', 'booking'),
    ));

    $this->addElement('Text', 'sitebooking_bookingplural', array(
      'label' => 'Plugin URL Alternate Text for "bookings"',
      'description' => 'Please enter the text below which you want to display in place of "bookings" in the URLs of this plugin.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.bookingplural', 'bookings'),
    ));
    $this->addElement('Text', 'sitebooking_providersingular', array(
      'label' => 'Plugin URL Alternate Text for "provider"',
      'description' => 'Please enter the text below which you want to display in place of "provider" in the URLs of this plugin.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.providersingular', 'provider'),
    ));

    $this->addElement('Text', 'sitebooking_providerplural', array(
      'label' => 'Plugin URL Alternate Text for "providers"',
      'description' => 'Please enter the text below which you want to display in place of "providers" in the URLs of this plugin.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.providerplural', 'providers'),
    ));

    $this->addElement('Text', 'sitebooking_servicesingular', array(
      'label' => 'Plugin URL Alternate Text for "service"',
      'description' => 'Please enter the text below which you want to display in place of "service" in the URLs of this plugin.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.servicesingular', 'service'),
    ));

    $this->addElement('Text', 'sitebooking_serviceplural', array(
      'label' => 'Plugin URL Alternate Text for "services"',
      'description' => 'Please enter the text below which you want to display in place of "services" in the URLs of this plugin.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.serviceplural', 'services'),
    ));

    $ProviderShare = array();
    $ProviderShare["facebook"] = "Facebook";
    $ProviderShare["twitter"] = "Twitter";
    $ProviderShare["linkedin"] = "Linkedin";
    $ProviderShare["pinterest"] = "Pinterest";
    $ProviderShare["share"] = "Share on the website itself";

    $coreSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.share");
    $share = array();
    $share = explode(",",$coreSettings);

    $this->addElement('MultiCheckbox', 'sitebooking_share', array(
      'label' => 'Share Icons for Service Providers',
      'description' => 'Do you want social sharing to be enabled for Service Provider? (If enabled, social sharing icons will be shown on service providers entries on the Service Provider View Page, Browse Service Providers Page and Providers Wishlist Page. Please check those social sharing options which you want to enable.)',
      'multiOptions' => $ProviderShare,
      'value' => $share,
    ));

    $ProviderShareLink = array();
    $ProviderShareLink["yes"] = "Yes";
    $ProviderShareLink["no"] = "No";

    $sharelinkcoreSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.provider.sharelink",'yes');

    $this->addElement('radio', 'sitebooking_provider_sharelink', array(
      'label' => 'Share Link on Service Provider View Page',
      'description' => 'Do you want to show the link for sharing service provider on the website itself on Service Provider View page?',
      'multiOptions' => $ProviderShareLink,
      'value' => $sharelinkcoreSettings,
    ));

    $ProviderReport = array();
    $ProviderReport["yes"] = "Yes";
    $ProviderReport["no"] = "No";

    $reportcoreSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.provider.report",'yes');

    $this->addElement('radio', 'sitebooking_provider_report', array(
      'label' => 'Report Link on Service Provider View Page',
      'description' => 'Do you want to show the link for reporting something to the Admin about a service provider on Service Provider View Page?',
      'multiOptions' => $ProviderReport,
      'value' => $reportcoreSettings,
    ));

    $ServiceShareLink = array();
    $ServiceShareLink["yes"] = "Yes";
    $ServiceShareLink["no"] = "No";

    $servicesharelinkcoreSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.service.sharelink",'yes');

    $this->addElement('radio', 'sitebooking_service_sharelink', array(
      'label' => 'Share Link on Service View Page',
      'description' => 'Do you want to show the link for sharing service on the website itself on Service View Page?',
      'multiOptions' => $ServiceShareLink,
      'value' => $servicesharelinkcoreSettings,
    ));

    $ServiceReport = array();
    $ServiceReport["yes"] = "Yes";
    $ServiceReport["no"] = "No";

    $servicereportcoreSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.service.report",'yes');

    $this->addElement('radio', 'sitebooking_service_report', array(
      'label' => 'Report Link on Service View Page',
      'description' => 'Do you want to show the link for reporting something to the Admin about a service on Service View Page?',
      'multiOptions' => $ServiceReport,
      'value' => $servicereportcoreSettings,
    ));

    $locationField = array();
    $locationField["yes"] = "Yes";
    $locationField["no"] = "No";

    $locationFieldcoreSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.locationfield",'yes');

    $this->addElement('radio', 'sitebooking_locationfield', array(
      'label' => 'Location Field',
      'description' => 'Do you want the Location field to be enabled for Services and Providers?',
      'multiOptions' => $locationField,
      'value' => $locationFieldcoreSettings,
    ));

    $search = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.search",'miles');
    $this->addElement('Select', 'sitebooking_search', array(
      'label' => 'Proximity Search ',
      'description' => 'Proximity search will enable users to search for services and providers within a certain distance from a location. Please select whether you want this search to be made using miles or kilometers.',
      'multiOptions' => array(
        'miles' => 'Miles',
        'kilometers' => 'Kilometers',  
      ),
      'value' => $search,
    ));


    //currency type
    $currency_type = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.unit",'USD');
    $locale = Zend_Registry::get('Zend_Translate')->getLocale();
    $this->_currencies = $currencies = Zend_Locale::getTranslationList('NameToCurrency', $locale);
    uksort($currencies, array($this, '_orderCurrencies'));
    $this->addElement('Select', 'sitebooking_unit', array(
      'label' => 'Currency Type',
      'description' => 'Select the currency type that you want to enable for showing services costs. Please note, currency value conversion will not be done, just the currency symbol will be changed.',
      'multiOptions' => $currencies,
      'value' => $currency_type,
    ));


    $timeFrame = array();
    $timeFrame["1"] = "1 Week";
    $timeFrame["2"] = "2 Weeks";
    $timeFrame["3"] = "3 Weeks";
    $timeFrame["4"] = "4 Weeks";
    $timeFrame["5"] = "5 Weeks";
    $timeFrame["6"] = "6 Weeks";
    $timeFrame["7"] = "7 Weeks";
    $timeFrame["8"] = "8 Weeks";

    $timeFrameValue = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.bookingtimeframe",'3');

    $this->addElement('Select', 'sitebooking_bookingtimeframe', array(
      'label' => 'Booking Time Frame',
      'description' => 'Please select a time period here, users will then be able to book their appointments within that time frame.',
      'multiOptions' => $timeFrame,
      'value' => $timeFrameValue,
    ));

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }

    protected function _orderCurrencies($a, $b)
  {
    $ai = @$this->_priorityCurrencies[$a];
    $bi = @$this->_priorityCurrencies[$b];
    if( null !== $ai && null !== $bi ) {
      return ($ai < $bi) ? -1 : 1;
    } else if( null !== $ai ) {
      return -1;
    } else if( null !== $bi ) {
      return 1;
    } else {
      return strcmp($this->_currencies[$a], $this->_currencies[$b]);
    }
  }
}
?>