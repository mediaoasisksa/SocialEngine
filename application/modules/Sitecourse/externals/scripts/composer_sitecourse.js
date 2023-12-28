
(function() { // START NAMESPACE
  var $ = 'id' in document ? document.id : window.$;


  Composer.Plugin.Sitecourse = function(options = {}) {
    this.__proto__ = new Composer.Plugin.Interface(options),
    
    this.name = 'sitecourse';
    this.options = {
      title: 'Create course',
      lang: {},
      loadJSFiles: [],
      // Options for the link preview request
      requestOptions: {},
    };
    this.initialize = function(options) {
      this.elements = new Hash(this.elements);
      this.params = new Hash(this.params);
      this.__proto__.initialize.call(this, scriptJquery.extend(options, this.__proto__.options));
    };
    this.attach = function() {
      this.__proto__.attach.call(this);
      this.__proto.makeActivator.call(this);
      
      var jsfile;
      while ((jsfile = this.options.loadJSFiles.shift())) {
        scriptJquery.ajax(jsfile, {
          onLoad: function() {}})
        }
      this.options.loadJSFiles = [];
      return this;
    };
    this.detach = function() {
      this.__proto__.detach.call(this);
      return this;
    };
    this.activate = function() {
      if (this.active)
        return;
    
        if(this.options.packageEnable) {
            window.location.href = this.options.requestOptions.url;
            return;
        } else {
            SmoothboxSEAO.open(this.options.requestOptions.url);
        }
      //  this.makeBody();
    };
    this.deactivate = function() {
      // clean video out if not attached
      if (!this.active)
        return;
      this.__proto__.deactivate.call(this);
    };

    this.initialize(options);
  };



})(); // END NAMESPACE
