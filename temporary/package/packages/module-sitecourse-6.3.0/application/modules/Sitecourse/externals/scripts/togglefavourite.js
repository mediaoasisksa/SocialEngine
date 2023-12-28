/**
 * @param {string} url where to send the ajax request
 * @param {html-element} element whose text to toggle
 */
function toggleFavourite(url = '' , callbackFun) {
  // no url is passed
  if(!url) return;
  let request = en4.core.request.send(scriptJquery.ajax({
    url : url,
    data : {
      format : 'json'
    },
    success : function(responseJSON) {
      callbackFun(responseJSON.favourite);
    }
  }));
}
