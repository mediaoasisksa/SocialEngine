<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl."externals/selectize/css/normalize.css");
$headScript = new Zend_View_Helper_HeadScript();
$headScript->appendFile($this->layout()->staticBaseUrl.'externals/selectize/js/selectize.js');
?>
<script type="text/javascript">
  en4.core.runonce.add(function()
  {
    var contentAutocomplete = scriptJquery('#title').selectize({
      maxItems: 1,
      valueField: 'label',
      labelField: 'label',
      searchField: 'label',
      create: true,
      render: {
        option: function(item, escape) {
         return `<div>
         <span class="option-image">${item.photo}</span>
         <span class="option-label">${item.label}</span>
         </div>`;
       }
     },
     onItemAdd: function(value, $item) {
      if(value in this.options) {
        document.getElementById('resource_id').value = this.options[value].id;
      }
    },
    load: function(query, callback) {
      if (!query.length) return callback();
      scriptJquery.ajax({
        url: '<?php echo $this->url(array('module' => 'sitebooking', 'controller' => 'service-of-the-day', 'action' => 'getitem'), 'admin_default', true) ?>',
        data: { text: query },
        success: function (token) {
          callback(token);
        },
        error: function () {
          callback([]);
        }
      });
    }
  });
  });
</script>
<script type="text/javascript">

  window.addEventListener('DOMContentLoaded', function() {
    const CALENDER_DATE_FORMAT = "dd/mm/yy";
    const startDateEl = scriptJquery('#starttime-date');
    const endDateEl = scriptJquery('#endtime-date');

    startDateEl.attr('type', 'text').datepicker({
      dateFormat: CALENDER_DATE_FORMAT,
      minDate: startDateEl.val()
    }).on('change', function(ev) {
      endDateEl.datepicker('option', 'minDate', startDateEl.val());
    });

    endDateEl.attr('type', 'text').datepicker({
      dateFormat: CALENDER_DATE_FORMAT,
      minDate: endDateEl.val()
    }).on('change', function(ev) {
      startDateEl.datepicker('option', 'maxnDate', endDateEl.val());
    });

  });



  window.addEventListener('DOMContentLoaded', function(){
    if(scriptJquery('#starttime-minute')) {
      scriptJquery('#starttime-minute').css('display','none');
    }
    if(scriptJquery('#starttime-ampm')) {
      scriptJquery('#starttime-ampm').css('display','none');
    }
    if(scriptJquery('#starttime-hour')) {
      scriptJquery('#starttime-hour').css('display','none');
    }

    //End date work
    if(scriptJquery('#endtime-minute')) {
      scriptJquery('#endtime-minute').css('display','none');
    }
    if(scriptJquery('#endtime-ampm')) {
      scriptJquery('#endtime-ampm').css('display','none');
    }
    if(scriptJquery('#endtime-hour')) {
      scriptJquery('#endtime-hour').css('display','none');
    }
    ///// End End date work

  });
</script>
<div class="settings global_form_popup seaocore_add_item">
  <?php echo $this->form->setAttrib('class', 'global_form')->render($this) ?>
</div>