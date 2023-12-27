<?php
class Sitebooking_Form_ServiceProvider_BookingSearch extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttribs(array(
        'id' => 'booking_filter_form',
        'class' => 'global_form_box',
      ))
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      // ->setMethod('GET')
      ;
    
    $this->addElement('Text', 'service_title', array(
      'label' => 'Service Title',
    ));

    $this->addElement('Select', 'status', array(
      'label' => 'Browse By',
      'multiOptions' => array(
        '' => 'All ',
        'booked' => 'Requested Bookings',
        'pending' => 'Accepted Bookings',
        'Rejected' => 'Declined Bookings',
        'completed' => 'Completed Bookings',
        'canceled' => 'Canceled Bookings',
      ),
    ));

    $table = Engine_Api::_()->getItemTable('sitebooking_category')->fetchAll();
    $category_value["0"] = "All Categories";

    foreach ($table as $key => $value) {
        $category_value[$value['category_id']]  = $value['category_name'];
    }

    $this->addElement('Select', 'category', array(
        'label' => 'Category',
        'multiOptions' => $category_value,
        'onchange' => "this.form.submit();",
    ));

    $bookingDate = new Seaocore_Form_Element_DatepickerCalendarDateTime('booking_date');
    $bookingDate->setLabel("Booking Date");
    $bookingDate->setOptions(array('dateFormat' => 'ymd'));
    $bookingDate->setAllowEmpty(true);
    $bookingDate->setRequired(false);  
    $this->addElement($bookingDate);

    $servicingDate = new Seaocore_Form_Element_DatepickerCalendarDateTime('servicing_date');
    $servicingDate->setLabel("Servicing Date");
    $servicingDate->setOptions(array('dateFormat' => 'ymd'));
    $servicingDate->setAllowEmpty(true);
    $servicingDate->setRequired(false);  
    $this->addElement($servicingDate);

    $this->addElement('Button', 'find', array(
      'type' => 'submit',
      'label' => 'Search',
      'value' => 'find',
      'ignore' => true,
      'order' => 10000001,
    ));
  }
}
?>