function showSubCategory(category_id,selectedId) {
  var selected;
  if(selectedId != '')
    selected = selectedId;
  
  if(modulename == 'music') {
    var URL = en4.core.baseUrl + modulename + '/subcategory/category_id/' + category_id;
  } else {
    var URL = en4.core.baseUrl + modulename + '/index/subcategory/category_id/' + category_id;
  }
  scriptJquery.ajax({
    url: URL,
    dataType: 'html',
    data: {
      'selected' : selected,
      'category_id': category_id
    },
    success: function(responseHTML) {
      if (document.getElementById('subcat_id') && responseHTML) {
        if (document.getElementById('subcat_id-wrapper')) {
          document.getElementById('subcat_id-wrapper').style.display = "block";
        }
        document.getElementById('subcat_id').innerHTML = responseHTML;
      } else {
        if (document.getElementById('subcat_id-wrapper')) {
          document.getElementById('subcat_id-wrapper').style.display = "none";
          document.getElementById('subcat_id').innerHTML = '<option value="0"></option>';
        }
      }
      if (document.getElementById('subsubcat_id-wrapper')) {
        document.getElementById('subsubcat_id-wrapper').style.display = "none";
        document.getElementById('subsubcat_id').innerHTML = '<option value="0"></option>';
      }
    }
  });
}

function showSubSubCategory(category_id, selectedId) {
  if(category_id == 0) {
    if (document.getElementById('subsubcat_id-wrapper')) {
      document.getElementById('subsubcat_id-wrapper').style.display = "none";
      document.getElementById('subsubcat_id').innerHTML = '';
    }
    return false;
  }
  
  var selected;
  if(selectedId != '')
    selected = selectedId;
  if(modulename == 'music') {
    var URL = en4.core.baseUrl + modulename + '/subsubcategory/subcategory_id/' + category_id;
  } else {
    var URL = en4.core.baseUrl + modulename + '/index/subsubcategory/subcategory_id/' + category_id;
  }
  scriptJquery.ajax({
    url: URL,
    dataType: 'html',
    data: {'selected':selected,'subcategory_id':category_id},
    success: function(responseHTML) {
      if (document.getElementById('subsubcat_id') && responseHTML) {
        if (document.getElementById('subsubcat_id-wrapper')) {
          document.getElementById('subsubcat_id-wrapper').style.display = "block";
        }
        document.getElementById('subsubcat_id').innerHTML = responseHTML;
      } else {
        if (document.getElementById('subsubcat_id-wrapper')) {
          document.getElementById('subsubcat_id-wrapper').style.display = "none";
          document.getElementById('subsubcat_id').innerHTML = '<option value="0"></option>';
        }
      }
    }
  });
}
