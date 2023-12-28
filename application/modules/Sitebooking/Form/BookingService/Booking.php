<?php
Class Sitebooking_Form_BookingService_Booking extends Engine_Form
{
  public function init()
  {

    // $this->setTitle('Book Service');
    $this->setAttrib('name', 'Service_Booking_Form');
      

  	$bookingDate = new Engine_Form_Element_CalendarDateTime('servicing_date');
    $bookingDate->setLabel("Servicing Date");
    $bookingDate->setAllowEmpty(false);
    $bookingDate->setRequired(true);  
    $this->addElement($bookingDate);


    $this->addElement('Button', 'button', array(
      'label' => 'Search',
      'type' => 'button',
      'onclick' => 'showAvailability()',
    ));
    

    $this->addElement('Textarea', 'problem_desc', array(
      'label' => 'Description',
      'allowEmpty' => false,
      'description' => 'Tell me your problem. Description length cannot exceed 300 characters.',
      'required' => false,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '300'))
      )
    ));

    $this->addElement('Text', 'telephone_no', array(
      'label' => 'Telephone No.',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '20'))
      ),
      'autofocus' => 'autofocus',
    ));
    
     $this->addElement('Checkbox', 'commission', array(
            'label' => '5% commission will be deducted from your total booking amount, remaining payment will be transfer. Default payment gateway will be paypal. once you will click submit button you will automatic redirected to paypal website for payment.',
            'value' => True
        ));

    $this->addElement('Hidden', 'total_charges', array(
      'id' => 'total_charges',
      'order' => 998,
    ));

    $this->addElement('Hidden', 'duration', array(
      'id' => 'duration',
      'order' => 999,
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Continue',
      'type' => 'submit',
    ));


  }
}
?>