<?php

class Seaocore_View_Helper_FormCalendarDateTime extends Engine_View_Helper_FormCalendarDateTime {

    // Which format date is passed to this function
    protected $defaultFormatOfValueDate = 'Y-m-d';

    public function formCalendarDateTime($name, $value = null, $attribs = null, $options = null, $listsep = "<br />\n") {

        $showAdvanceCalendar = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.advancedcalendar', 1);
        $showAdvanceCalendar = 0; // advanced form should not work.
        $seaocoreCalenderDayStart = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.calendar.daystart', 1);

        if ($seaocoreCalenderDayStart == 1) {
            $seaocoreCalenderDayStart = 0;
        } else if ($seaocoreCalenderDayStart == 2) {
            $seaocoreCalenderDayStart = 1;
        } else if ($seaocoreCalenderDayStart == 3) {
            $seaocoreCalenderDayStart = 6;
        }

        if (empty($showAdvanceCalendar))
            return parent::formCalendarDateTime($name, $value, $attribs, $options, $listsep);

        $info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
        extract($info); // name, value, attribs, options, listsep, disable
        // Get date format
        if (isset($attribs['dateFormat'])) {
            $dateFormat = $attribs['dateFormat'];
            //unset($attribs['dateFormat']);
        } else {
            $dateFormat = 'ymd';
        }
        // Get use military time
        if (isset($attribs['useMilitaryTime'])) {
            $useMilitaryTime = $attribs['useMilitaryTime'];
            //unset($attribs['useMilitaryTime']);
        } else {
            $useMilitaryTime = true;
        }

        // Check value type
        if (is_string($value) && preg_match('/^(\d{4})-(\d{2})-(\d{2})( (\d{2}):(\d{2})(:(\d{2}))?)?$/', $value, $m)) {
            $tmpDateFormat = trim(str_replace(array('d', 'y', 'm'), array('/%3$s', '/%1$s', '/%2$s'), $dateFormat), '/');
            $value = array();

            // Get date
            $value['date'] = sprintf($tmpDateFormat, $m[1], $m[2], $m[3]);
            $tmpDateFormatForCheck = trim(str_replace(array('d', 'y', 'm'), array('/%3$d', '/%1$d', '/%2$d'), $dateFormat), '/');
            $tempDate = sprintf($tmpDateFormatForCheck, $m[1], $m[2], $m[3]);
            if ($tempDate == '0/0/0') {
                unset($value['date']);
            }

            // Get time
            if (isset($m[6])) {
                $value['hour'] = $m[5];
                $value['minute'] = $m[6];
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

        if (!is_array($value)) {
            $value = array();
        }

        // Prepare javascript
        // Prepare month and day names
        $localeObject = Zend_Registry::get('Locale');

        $months = Zend_Locale::getTranslationList('months', $localeObject);
        if ($months['default'] == NULL) {
            $months['default'] = "wide";
        }
        $months = $months['format'][$months['default']];

        $days = Zend_Locale::getTranslationList('days', $localeObject);
        if ($days['default'] == NULL) {
            $days['default'] = "wide";
        }
        $days = $days['format'][$days['default']];

        /*$calendarFormatString = trim(preg_replace('/\w/', '$0/', $dateFormat), '/');
        $calendarFormatString = str_replace('y', 'Y', $calendarFormatString);*/

        $calendarFormatString = trim(preg_replace('/\w/', '$0/', $dateFormat), '/');
        $calendarFormatString = str_replace('Y', 'y', $calendarFormatString);
        if ( substr_count( $calendarFormatString, 'y' ) == 1 ) {
            $calendarFormatString = str_replace("y", "yyyy", $calendarFormatString);
        }
        $dateToSet = isset( $value['date']) ? 'scriptJquery("#'.$name . '-date").datepicker("setDate", "' . $value['date'] . '");' : null;
        $minDate = ($name == 'scheduled_time') ? 'minDate: 0' : '';

        if ( empty($value['date']) ) {
            $dateToSet = null;
        } else {
            // This will help to convert upcoming date so browser can understand the date
            // $attribs['valueDateFormat'] will tell which format date, $value['date'] is passed to this function.
            $valueDateFormat = empty($attribs['valueDateFormat']) ? $this->defaultFormatOfValueDate : $attribs['valueDateFormat'];
            $dateToSet = DateTime::createFromFormat( $valueDateFormat, $value['date'] )->format( $this->defaultFormatOfValueDate );
            $dateToSet = strtotime($dateToSet);
            $temp['year'] = date( "Y", $dateToSet );
            $temp['month'] = date( "m", $dateToSet ) -1; // Javascript months start with zero not one
            $temp['day'] = date( "d", $dateToSet );
            $dateToSet = 'scriptJquery("#'.$name . '-date").datepicker("setDate", ' . "new Date( {$temp['year']}, {$temp['month']}, {$temp['day']} )" . ');';
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
                                              buttonImage: '{$this->view->layout()->staticBaseUrl}',
                                              changeMonth: true,
                                              changeYear: true,
                                              $minDate
                                            }
                                        );
                $dateToSet;

            });  ";

        // Append files and script
        //$this->view->headScript()->appendFile($this->view->baseUrl() . '/externals/calendar/calendar.compat.js');
        // $this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/externals/calendar/styles.css');
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

        return
                '<div class="event_calendar_container" style="display:inline">' .
                $this->view->formHidden($name . '[date]', @$value['date'], array_merge(array('class' => 'calendar', 'id' => $name . '-date'), (array) @$attribs['dateAttribs'])) .
                '<span class="calendar_output_span" id="calendar_output_span_' . $name . '-date">' .
                ( @$value['date'] ? @$value['date'] : $this->view->translate('Select a date') ) .
                '</span>' .
                '</div>' .
                $this->view->formTime($name, $value, $attribs, $options)
        ;
    }

}
