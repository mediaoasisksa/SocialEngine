(function(){
  var Hash = function(object = {}){
  	var proto = {
  		has: Object.prototype.hasOwnProperty,
  		getClean: function(){
  			var clean = {};
  			for (var key in this){
  				if (this.hasOwnProperty(key)) clean[key] = this[key];
  			}
  			return clean;
  		},
  		extend: function(properties){
  			Object.entries((properties || {})).forEach(([key,value])=>{
  				this.set(this, key, value);
  			});
  			return this;
  		},
  		getLength: function(){
  			var length = 0;
  			for (var key in this){
  				if (this.hasOwnProperty(key)) length++;
  			}
  			return length;
  		},
  		erase: function(key){
  			if (this.hasOwnProperty(key)) delete this[key];
  			return this;
  		},
  		get: function(key){
  			return (this.hasOwnProperty(key)) ? this[key] : null;
  		},
  		set: function(key, value){
  			if (!this[key] || this.hasOwnProperty(key)) this[key] = value;
  			return this;
  		},
  		empty: function(){
  			Object.entries(this).forEach(function([key,value]){
  				delete this[key];
  			}, this);
  			return this;
  		},
  	};
  	for (var key in proto) Hash.prototype[key] = proto[key];
    for (var key in object) this[key] = object[key];
    return this;
  }
  this.Hash = Hash;
})();
(function(){
  var serverOffset = 0;
  Date.setServerOffset = function(ts){
    var server = new Date(ts);
    var client = new Date();
    serverOffset = server - client;
  };

  Date.getServerOffset = function() {
    return serverOffset;
  };
})();
$type = function(object){
  var type = typeof (object);
  return (object == null || type == 'null' || type == 'undefined') ? false : type;
}

$time = function() {
  return (new Date()).getTime();
}
String.prototype.vsprintf = function(args) {
  str = this;
  // Check for no params
  if( !args || !args.length )
  {
    return str;
  }
  // Replace params
  var out = '';
  var m;
  var masterIndex = 0;
  var currentIndex;
  var arg;
  var instr;
  var meth;
  var sign;
  while( str.length > 0 )
  {
    // Check for no more expressions
    if( !str.match(/[%]/) )
    {
      out += str;
      break;
    }
    // Remove any preceeding non-expressions
    m = str.match(/^([^%]+?)([%].+)?$/)
    if( m )
    {
      out += m[1];
      str = typeof(m[2]) ? m[2] : '';
      if( str == '' )
      {
        break;
      }
    }
    // Check for escaped %
    if( str.substring(0, 2) == '%%' )
    {
      str = str.substring(2);
      out += '%';
      continue;
    }
    // Proc next params
    m = str.match(/^[%](?:([0-9]+)\x24)?(\x2B)?(\x30|\x27[^$])?(\x2D)?([0-9]+)?(?:\x2E([0-9]+))?([bcdeEfosuxX])/)
    if( m )
    {
      instr = m[7];
      meth = m[6] || false;
      sign = m[2] || false;
      currentIndex = ( m[1] ? m[1] - 1 : masterIndex++ );
      if($type(args[currentIndex]) )
      {
        arg = args[currentIndex];
      }
      else
      {
        throw('Undefined argument for index ' + currentIndex);
      }
      // Make sure passed sane argument type
      switch( typeof(arg) )
      {
        case 'number':
        case 'string':
        case 'boolean':
          // Okay
          break;
        case 'undefined':
          if( arg == null )
          {
            arg = '';
            break;
          }
        default:
          throw('Unknown argument type: ' + typeof(arg));
          break;
      }
      // Now proc instr
      switch( instr )
      {
        // Binary
        case 'b':
          if( typeof(arg) != 'number' ) arg = parseInt(arg);
          arg = arg.toString(2);
          break;
        // Char
        case 'c':
          arg = String.fromCharCode(arg);
          break;
        // Integer
        case 'd':
          arg = parseInt(arg);
          break;
        // Scientific notation
        case 'E':
        case 'e':
          if( typeof(arg) != 'number' ) arg = parseFloat(arg);
          if( meth )
          {
            arg = arg.toExponential(meth);
          }
          else
          {
            arg = arg.toExponential();
          }
          if( instr == 'E' ) arg = arg.toUpperCase();
          break;

        // Unsigned integer
        case 'u':
          arg = Math.abs(parseInt(arg));
          break;

        // Float
        case 'f':
          if( meth )
          {
            arg = parseFloat(arg).toFixed(meth)
          }
          else
          {
            arg = parseFloat(arg);
          }
          break;
        // Octal
        case 'o':
          if( typeof(arg) != 'number' ) arg = parseInt(arg);
          arg = arg.toString(8);
          break;

        // String
        case 's':
          if( typeof(arg) != 'string' ) arg = String(arg);
          if( meth )
          {
            arg = arg.substring(0, meth);
          }
          break;

        // Hex
        case 'x':
        case 'X':
          if( typeof(arg) != 'number' ) arg = parseInt(arg);
          arg = arg.toString(8);
          if( instr == 'X' ) arg = arg.toUpperCase();
          break;
      }

      // Add a sign if requested
      if( (instr == 'd' || instr == 'e' || instr == 'f') && sign && arg > 0 )
      {
        arg = '+' + arg;
      }
      // Do repeating if necessary
      var repeatChar, repeatCount;
      if( m[3] )
      {
        repeatChar = m[3];
      }
      else
      {
        repeatChar = ' ';
      }
      if( m[5] )
      {
        repeatCount = m[5];
      }
      else
      {
        repeatCount = 0;
      }
      repeatCount -= arg.length;

      // Do the repeating
      if( repeatCount > 0 )
      {
        var paddedness = function(str, count)
        {
          var ret = '';
          while( count > 0 )
          {
            ret += str;
            count--;
          }
          return ret;
        }(repeatChar, repeatCount);

        if( m[4] )
        {
          out += arg + paddedness;
        }
        else
        {
          out += paddedness + arg;
        }
      }
      // Just add the string
      else
      {
        out += arg;
      }
      // Remove from str
      str = str.substring(m[0].length);
    }
    else
    {
      throw('Malformed expression in string: ' + str);
    }
  }
  return out;
}

var Cookie = function(key, options){
  defaultOptions = {
    path: '/',
    domain: false,
    duration: false,
    secure: false,
    document: document,
    encode: true
  }
  this.write = function(value){
    if (this.options.encode) value = encodeURIComponent(value);
    if (this.options.domain) value += '; domain=' + this.options.domain;
    if (this.options.path) value += '; path=' + this.options.path;
    if (this.options.duration){
      var date = new Date();
      date.setTime(date.getTime() + this.options.duration * 24 * 60 * 60 * 1000);
      value += '; expires=' + date.toGMTString();
    }
    if (this.options.secure) value += '; secure';
    this.options.document.cookie = this.key + '=' + value;
    return this;
  }

  this.read = function(){
    var value = this.options.document.cookie.match('(?:^|;)\\s*' + this.key.replace(/([-.*+?^${}()|[\]\/\\])/g, '\\$1') + '=([^;]*)');
    return (value) ? decodeURIComponent(value[1]) : null;
  }

  this.dispose = function(){
    new Cookie(this.key, scriptJquery.extend(true,{}, this.options, {duration: -1})).write('');
    return this;
  }
  this.key = key;
  this.options = scriptJquery.extend(true,{},defaultOptions,options);
};

Cookie.write = function(key, value, options){
  return new Cookie(key, options).write(value);
};

Cookie.read = function(key){
  return new Cookie(key).read();
};

Cookie.dispose = function(key, options){
  return new Cookie(key, options).dispose();
};

scriptJquery.extend(scriptJquery,{
	crtEle:function(tagName,options){
		function makeInline(options,sub= false){
			let eleContent = '';
			if(typeof options === "object"){
				Object.entries(options).forEach(([key, value])=>{
					if(typeof value === "object"){
						eleContent += `${key}="${makeInline(value,1)}"`;
					} else {
						eleContent += `${key}${sub ? ":" : "="}'${value}'${sub ? ";" : ""}`;
					}
				});
			} else if(typeof options === "string"){
				eleContent = options;
			}
			return eleContent;
		}
		eleContent = makeInline(options);
		return scriptJquery(`<${tagName} ${eleContent}>`);
	}
});

scriptJquery.fn.enableLinks = function(){
  this.each(function(){
    scriptJquery(this).html(scriptJquery.parseHTML(scriptJquery(this).html()));
  });
}


class Occlude{
  constructor(){
  }
  occlude(property, element){
    element = (element || this.element);
    var instance = element.data(property || this.property);
    if (instance && !this.occluded)
      return (this.occluded = instance);

    this.occluded = false;
    element.data(property || this.property, this);
    return this.occluded;
  }

};

class OverText extends Occlude{
  Binds = ['reposition', 'assert', 'focus', 'hide'];
  options = {
    element: 'label',
    labelClass: 'overTxtLabel',
    positionOptions: {
      position: 'upperLeft',
      edge: 'upperLeft',
      offset: {
        x: 4,
        y: 2
      }
    },
    poll: false,
    pollInterval: 250,
    wrap: false
  }
  property = 'OverText';
  constructor(element, options){
    super();
    element = this.element = element;

    if (this.occlude()) return this.occluded;
      this.options = scriptJquery.extend(this.options,options);

    this.attach(element);
    OverText.instances.push(this);
    if (this.options.poll) this.poll();
  }
  toElement(){
    return this.element;
  }

  attach(){
    var element = this.element,
      options = this.options,
      value = options.textOverride || element.attr('alt') || element.attr('title');

    if (!value) return this;

    var text = this.text = scriptJquery.crtEle(options.element, {
      'class': options.labelClass,
    }).css({
        lineHeight: 'normal',
        position: 'absolute',
        cursor: 'text',
        top : 2,
        left : 4
    }).click(this.hide.bind(this,options.element == 'label')).html(value)
    .insertAfter(element);

    if (options.element == 'label'){
      if (!element.attr('id')) element.attr('id', 'input_' + $time().toString(36));
      text.attr('for', element.attr('id'));
    }

    if (options.wrap){
      this.textHolder = scriptJquery.crtEle('div', {
        class: 'overTxtWrapper',
      }).css({
          lineHeight: 'normal',
          position: 'relative'
      }).append(text).insertBefore(element);
    }
    this.element.parent().css("position","relative");
    return this.enable();
  }

  destroy(){
   // this.element.eliminate(this.property); // Class.Occlude storage
    this.disable();
    if (this.text) this.text.remove();
    if (this.textHolder) this.textHolder.remove();
    return this;
  }

  disable(){
    this.element.off({
      focus: this.focus.bind(this),
      blur: this.assert.bind(this),
      input: this.assert.bind(this)
    });
    //scriptJquery(window).off('resize', this.reposition.bind(this));
    this.hide(true, true);
    return this;
  }

  enable(){
    this.element.on({
      focus: this.focus.bind(this),
      blur: this.assert.bind(this),
      input: this.assert.bind(this)
    });
    //scriptJquery(window).off().on('resize', this.reposition.bind(this));
    this.reposition();
    return this;
  }

  wrap(){
    if (this.options.element == 'label'){
      if (!this.element.attr('id')) this.element.attr('id', 'input_' + $time().toString(36));
      this.text.attr('for', this.element.attr('id'));
    }
  }

  startPolling(){
    this.pollingPaused = false;
    return this.poll();
  }

  poll(stop){
    //start immediately
    //pause on focus
    //resumeon blur
    if (this.poller && !stop) return this;
    if (stop){
      clearInterval(this.poller);
    } else {
      this.poller = setInterval(function(){
        if (!this.pollingPaused) this.assert(true);
      }.bind(this),this.options.pollInterval);
    }

    return this;
  }

  stopPolling(){
    this.pollingPaused = true;
    return this.poll(true);
  }

  focus(){
    if (this.text && (!this.text.is(":visible") || this.element.prop('disabled'))) return this;
    return this.hide();
  }

  hide(suppressFocus, force){
    if (this.text && (this.text.is(":visible") && (!this.element.prop('disabled') || force))){
      this.text.hide();
      this.pollingPaused = true;
    }
    return this;
  }

  show(){
    if (this.text && !this.text.is(":visible")){
      this.text.show();
      this.reposition();
      //scriptJquery(this).trigger('textShow',this.text,this.element);
      this.pollingPaused = false;
    }
    return this;
  }

  test(){
    return !this.element.val();
  }

  assert(suppressFocus){
    return this[this.test() ? 'show' : 'hide'](suppressFocus);
  }

  reposition(){
    if (!this.element.is(":visible")) return this.stopPolling().hide();
    if (this.text && this.test()){
      let obj = this.options.positionOptions;
      this.text.css({top:obj.offset.y,left:obj.offset.x});
    }
    return this;
  }
};


function htmlspecialchars_decode (string, quote_style) {
  // Convert special HTML entities back to characters
  //
  // version: 1004.2314
  // discuss at: http://phpjs.org/functions/htmlspecialchars_decode
  // +   original by: Mirek Slugen
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   bugfixed by: Mateusz "loonquawl" Zalega
  // +      input by: ReverseSyntax
  // +      input by: Slawomir Kaniecki
  // +      input by: Scott Cariss
  // +      input by: Francois
  // +   bugfixed by: Onno Marsman
  // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
  // +      input by: Ratheous
  // +      input by: Mailfaker (http://www.weedem.fr/)
  // +      reimplemented by: Brett Zamir (http://brett-zamir.me)
  // +    bugfixed by: Brett Zamir (http://brett-zamir.me)
  // *     example 1: htmlspecialchars_decode("<p>this -&gt; &quot;</p>", 'ENT_NOQUOTES');
  // *     returns 1: '<p>this -> &quot;</p>'
  // *     example 2: htmlspecialchars_decode("&amp;quot;");
  // *     returns 2: '&quot;'
  var optTemp = 0, i = 0, noquotes= false;
  if (typeof quote_style === 'undefined') {
    quote_style = 2;
  }
  string = string.toString().replace(/&lt;/g, '<').replace(/&gt;/g, '>');
  var OPTS = {
    'ENT_NOQUOTES': 0,
    'ENT_HTML_QUOTE_SINGLE' : 1,
    'ENT_HTML_QUOTE_DOUBLE' : 2,
    'ENT_COMPAT': 2,
    'ENT_QUOTES': 3,
    'ENT_IGNORE' : 4
  };
  if (quote_style === 0) {
    noquotes = true;
  }
  if (typeof quote_style !== 'number') { // Allow for a single string or an array of string flags
    quote_style = [].concat(quote_style);
    for (i=0; i < quote_style.length; i++) {
      // Resolve string input to bitwise e.g. 'PATHINFO_EXTENSION' becomes 4
      if (OPTS[quote_style[i]] === 0) {
        noquotes = true;
      }
      else if (OPTS[quote_style[i]]) {
        optTemp = optTemp | OPTS[quote_style[i]];
      }
    }
    quote_style = optTemp;
  }
  if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
    string = string.replace(/&#0*39;/g, "'"); // PHP doesn't currently escape if more than one 0, but it should
    // string = string.replace(/&apos;|&#x0*27;/g, "'"); // This would also be useful here, but not a part of PHP
  }
  if (!noquotes) {
    string = string.replace(/&quot;/g, '"');
  }
  // Put this in last place to avoid escape being double-decoded
  string = string.replace(/&amp;/g, '&');
  
  return string;
}

OverText.instances = [];
