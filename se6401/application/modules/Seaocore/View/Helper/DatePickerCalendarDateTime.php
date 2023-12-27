<?php

class Seaocore_View_Helper_DatePickerCalendarDateTime extends Engine_View_Helper_FormCalendarDateTime {

    // Which format date is passed to this function
    // If there is default date which is passed here should be in below format
    protected $_defaultFormatOfValueDate = 'Y-m-d H:i:s';

    public function datePickerCalendarDateTime($name, $value = null, $attribs = null, $options = null, $listsep = "<br />\n") {

        $info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
        extract($info); // name, value, attribs, options, listsep, disable
        // Get use military time
        $useMilitaryTime = isset($attribs['useMilitaryTime']) ? $attribs['useMilitaryTime'] : true;

        // if value is send in other format then attribute valueDateFormat must be send.
        $valueDateFormat = empty($attribs['valueDateFormat']) ? $this->_defaultFormatOfValueDate : $attribs['valueDateFormat'];

        $dateToSet = null;
        if ( is_string($value) ) {
            $value = $this->getDateFromFormat( $value, $valueDateFormat, $useMilitaryTime );
            if ( is_array($value) && count($value) > 0 ) {
                $dateToSet = 'scriptJquery("#'.$name . '-date").datepicker("setDate", ' . "new Date( {$value['year']}, {$value['month']}, {$value['day']} )" . ');';
            } else {
                $value = array();
            }
        }

        $minDate = null;
        if ( is_string($attribs['minDate']) ) {
            $date = $this->getDateFromFormat( $attribs['minDate'], $valueDateFormat, $useMilitaryTime );
            if ( is_array($date) ) {
                $minDate = 'min: ' . "new Date( {$date['year']}, {$date['month']}, {$date['day']} ),";
            }
        }

        $onSelectionData = null;
        if ( !empty($attribs['onSelectCustonCodeToExecute']) || !empty($attribs['onSelectCustonFunction']) ) {
            $onSelectionData = "onSelect: function(date){";
            if ( !empty($attribs['onSelectCustonCodeToExecute']) ) {
                $onSelectionData .= "\n" . $attribs['onSelectCustonCodeToExecute'];
            }
            if ( !empty($attribs['onSelectCustonFunction']) ) {
                $onSelectionData .= "\n " . $attribs['onSelectCustonFunction'] . "( this, date );";
            }
            $onSelectionData .= "\n },";
        }
        /*
            altField : '#hidden_date_picker-{$name}-date',
            altFormat : 'yy-mm-dd',
            datepicker will assign date value to element which has id altField in format altFormat because display date format can be different based on user's browser. Languages etc.
            but it should always be same for server so a hidden element is being used to assign value in same format always for all users and browsers.
        */
        $windowdatePickerCode = "
            window.cal_{$name} = null;
            en4.core.runonce.add(function() {
                window.cal_{$name} =  scriptJquery('#{$name}-date')
                                        .datepicker(
                                            {
                                              altField : '#hidden_date_picker-{$name}-date',
                                              altFormat : 'yy-mm-dd',
                                              autoclose: true,
                                              yearRange : '-50:+50',
                                              showOn: 'both',
                                              buttonImage: '{$this->view->layout()->staticBaseUrl}' + 'application/modules/Seaocore/externals/images/calendar.gif',
                                              changeMonth: true,
                                              changeYear: true,
                                              //$minDate
                                              $onSelectionData
                                            }
                                        );
                $dateToSet;

            });  ";

        // Append files and script
        $this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/application/modules/Seaocore/externals/styles/calendar/styles.css');
        if (isset($attribs['loadedbyAjax']) && !empty($attribs['loadedbyAjax'])) {
            echo "<script type='text/javascript'>
                    {$windowdatePickerCode}
                 </script>";
        } else {
            $this->view->headScript()->appendScript( " {$windowdatePickerCode} " );
        }
        return
            '<div class="event_calendar_container" style="display:inline">' .
                '<input  type="hidden" name="' . $name .'[date]" id="hidden_date_picker-' .$name . '-date" >' .
                '<input  type="text" class="form-control" id="' .$name . '-date" placeholder=" '. $this->view->translate('Date') . '" readonly="readonly">' .
            '</div>' .
            $this->view->formTime($name, $value, $attribs, $options);
    }


    protected function getDateFromFormat( $valueDate, $valueDateFormat, $useMilitaryTime ) {

        $valueDate = trim($valueDate);
        $valueDateFormat = trim($valueDateFormat);
        $valueDate = preg_split( '(\/| |:|-)', $valueDate );
        $valueDateFormat = preg_split( '(\/| |:|-)', $valueDateFormat );
        // now check if date is in correct or not if not correct then do not set default date
        $finalDate = array();
        foreach ($valueDateFormat as $key => $value ) {
            if ( isset($valueDate[$key]) ) {
                $finalDate[$value] = $valueDate[$key];
            }
        }
        $value = array();
        if ( checkdate( $finalDate['m'], $finalDate['d'], $finalDate['Y'] ) ) {
            $value['year'] = $finalDate['Y'];
            $value['month'] = $finalDate['m'] - 1; // javascript month start from zero
            $value['day'] = $finalDate['d'];
            if ( isset($finalDate['H']) ) {
                $value['hour'] = $finalDate['H'];
                $value['minute'] = $finalDate['i'];
                if (!$useMilitaryTime) {
                    $value['ampm'] = ( $value['hour'] >= 12 ? 'PM' : 'AM' );
                    if (0 == (int) $value['hour']) {
                        $value['hour'] = 12;
                    } else if ($value['hour'] > 12) {
                        $value['hour'] -= 12;
                    }
                }
            }
        }
        return $value;
    }

}