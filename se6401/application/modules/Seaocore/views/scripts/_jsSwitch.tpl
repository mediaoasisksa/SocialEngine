<script type="text/javascript">

scriptJquery().ready(function(){
  
  var topLevelId = '<?php echo sprintf('%d', (int) @$this->topLevelId) ?>';
  var topLevelValue = '<?php echo sprintf('%d', (int) @$this->topLevelValue) ?>';
  var elementCache = {};
  var fieldAjaxUrl = scriptJquery("#field_ajax_url").val();
  var fieldEnabledAjaxLoad = scriptJquery("#enable_ajax_load").val();
  function getFieldsElements(selector) {
    if( selector in elementCache || $type(elementCache[selector]) ) {
      return elementCache[selector];
    } else {
      return elementCache[selector] = scriptJquery(selector);
    }
  }
  
  function updateFieldValue(element, value) {
    if( element.prop('tagName').toLowerCase() == 'option' ) {
      element = element.parents('select:first');
    } else if(element.attr('type') == 'checkbox' || element.attr('type') == 'radio' ) {
      element.prop('checked', Boolean(value));
      return;
    }
    if (element.prop("tagName") == 'SELECT') {
      if (element.attr('multiple')) {
        element.find('option').each(function(subEl){
          subEl.prop('selected', false);
        });
      }
    }
    if( element ) {
      element.val(value);
    }
  }

  var changeFields = window.changeFields = function(element, force, isLoad,topLevelId) {
    element = scriptJquery(element);
    // We can call this without an argument to start with the top level fields
    if( !element.length) {
      scriptJquery('.parent_' + topLevelId ).each(function(element) {
        element = scriptJquery(this);
        let parent_field_id = element.attr('class').match(/option_([\d]+)/i)[1];
        changeFields(element, force, isLoad,parent_field_id);
      });
      return;
    }
    // If this cannot have dependents, skip
    if( !$type(element) || !$type(element.attr("onchange")) ) {
      return;
    }
    // Get the input and params
    var field_id = element.attr('class').match(/field_([\d]+)/i)[1];
    var parent_field_id = element.attr('class').match(/parent_([\d]+)/i)[1];
    var parent_option_id = element.attr('class').match(/option_([\d]+)/i)[1];

    if( !field_id || !parent_option_id || !parent_field_id ) {
      return;
    }

    force = ( $type(force) ? force : false );
    var eleWrapper = element.closest(".form-wrapper");
    var childrens = eleWrapper.attr("data-childrens");

    if(childrens && fieldEnabledAjaxLoad){
      var guid = eleWrapper.attr("data-guid");
      childrens = JSON.parse(childrens);
      
      if(childrens.includes(parseInt(element.val()))){
        loadChild({
          parent_field_id : parseInt(element.attr("data-field-id")),
          parent_option_id : parseInt(element.val()),
          guid : guid
        }).then(data=>{

          const index = childrens.indexOf(parseInt(element.val()));
          if (index > -1) {
            childrens.splice(index, 1);
          }
          eleWrapper.attr("data-childrens",JSON.stringify(childrens));
          let elementBody;
          if(scriptJquery('body').find('.ajaxfields').length == 0){
            scriptJquery('body').append('<div class="ajaxfields">'+data+'</div>');
            scriptJquery('.ajaxfields').find('form').find('.form-elements').find('#enable_ajax_load').remove();
            scriptJquery('.ajaxfields').find('form').find('.form-elements').find('#field_ajax_url').remove();
            scriptJquery('.ajaxfields').find('form').find('.form-elements').find('.form-wrapper').show();
            elementBody = scriptJquery('.ajaxfields').find('form').find('.form-elements').html();
          }
          scriptJquery('body').find('.ajaxfields').remove();
          //element.addClass("children_loaded");
          scriptJquery(elementBody).insertAfter(eleWrapper);
          
        });
      }
    }
    
    // Now look and see
    // Check for multi values
    var option_id = [];
    var isRadio = true;
    if( $type(element.attr("name")) && element.attr("name").indexOf('[]') > -1 ) {
      if(element.attr("type") == 'checkbox' ) { // MultiCheckbox
        scriptJquery('.field_' + field_id).each(function(multiEl) {
          multiEl = scriptJquery(this);
          if( multiEl.prop('checked')) {
            option_id.push(multiEl.val());
          }
        });
      } else if( element.prop("tagName") == 'SELECT' && element.attr("multiple") ) { // Multiselect
        element.children().each(function(multiEl) {
          if(scriptJquery(this).prop('selected')) {
            option_id.push(this.value);
          }
        });
      }
    } else if( element.attr("type") == 'radio' ) {
      if(element.prop('checked')) {
        option_id = [element[0].value];
      } else {
        isRadio = false;
      }
    } else {
      option_id = [element[0].value];
    }
    // Iterate over children
    scriptJquery('.parent_' + field_id).each(function(childElement) {
      childElement = scriptJquery(this);
      var childContainer = null;
      if(childElement.closest('form').hasClass('field_search_criteria')) {
        childContainer = (childElement.closest('li:first').closest('li:first').length || childElement.closest('li:first')) || (childElement.closest('li:first').closest('li:first'));
      }
      if( childContainer == null || childContainer.length == 0 ) {
         childContainer = childElement.closest('div.form-wrapper');
      }
      if( childContainer == null || childContainer.length == 0 ) {
        childContainer = childElement.closest('div.form-wrapper-heading');
      }
      if( childContainer == null || childContainer.length == 0 ) {
        childContainer = childElement.closest('li');
      }
      
      //var childLabel = childContainer.getElement('label');
      var childOptions = childElement.attr('class').match(/option_([\d]+)/gi);

      for(var i = 0; i < childOptions.length; i++) {
        for(var j = 0; j < option_id.length; j++) {
          if(childOptions[i] == "option_" + option_id[j]) {
            var childOptionId = option_id[j];
            break;
          }
        }
      }

      //var childOptionId = childElement.attr('class').match(/option_([\d]+)/i)[1];
      var childIsVisible = ( 'none' != childContainer.css('display') );
      var skipPropagation = false;
      //var childFieldId = childElement.attr('class').match(/field_([\d]+)/i)[1];

      // Forcing hide

      var nextForce;
      if( force == 'hide' && !option_id.includes(childOptionId)) {
        if( !childElement.hasClass('field_toggle_nohide') ) {
          childContainer.hide();
          if( !isLoad ) {
            updateFieldValue(childElement, null);
          }
        }
        nextForce = force;
      } else if( force == 'show' ) {
        childContainer.show();
        nextForce = force;
      } else if( !$type(option_id) == 'array' || !option_id.includes(childOptionId) ) {
        // Hide fields not tied to the current option (but propogate hiding)
        
        if( !childElement.hasClass('field_toggle_nohide') && isRadio) {
          childContainer.css('display', 'none');
          if( !isLoad ) {
            updateFieldValue(childElement, null);
          }
        }
        nextForce = 'hide';
        if( !childIsVisible ) {
          skipPropagation = true;
        }
      } else {
        // Otherwise show field and propogate (nothing, show?)
        childContainer.show();
        nextForce = undefined;
        //if( childIsVisible ) {
        //  skipPropagation = true;
        //}
      }

      if( !skipPropagation ) {
        changeFields(childElement, nextForce, isLoad);
      }
    });
    scriptJquery(window).trigger('onChangeFields');
  }

  changeFields(null, null, true,topLevelId);
  
  function loadChild(params) {
    return scriptJquery.ajax({
            url: fieldAjaxUrl,
            async: false,
            type: "GET",
            data : params,
          });
  } 
});
</script>
