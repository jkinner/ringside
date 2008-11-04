
var $HTMLDocument = {

	setTitle: function(t) {
		this.title = t;
	},
	
	getTitle: function() {
		return this.title;
	},
	
	getRootElement: function() {
		return this.getElementById('canvasContainer');
	},
	
	setLocation: function(l) {
		document.location.href = l;
	}
	
};

var $Location = {

	setHref: function(url) {
		this.href = url;
	},
	
	getHref: function() {
		return this.href;
	}
}

// Source: http://www.geekdaily.net/2007/06/18/javascript-htmlelement-in-ie/
// Modified by Jason Kinner <jkinner@ringsidenetworks.com>, Mike Schachter <mschachter@ringsidenetworks.com>
var $HTMLElement = {

	getClientWidth: function() {
		return this.clientWidth;
	},
	
	getClientHeight: function() {
		return this.clientHeight;
	},
	
	getOffsetWidth: function() {
		return this.offsetWidth;
	},
	
	getOffsetHeight: function() {
		return this.offsetHeight;
	},
	
	getScrollWidth: function() {
		return this.scrollWidth;
	},
	
	setScrollWidth: function(s) {
		this.scrollWidth = s;
	},
	
	getScrollHeight: function() {
		return this.scrollHeight;
	},
	
	setScrollHeight: function(s) {
		this.scrollHeight = s;
	},
		
	getScrollTop: function() {
		return this.scrollTop;
	},
	
	setScrollTop: function(s) {
		this.scrollTop = s;
	},
	
	getScrollLeft: function() {
		return this.scrollLeft;
	},
	
	setScrollLeft: function(s) {
		this.scrollLeft = s;
	},

	getId: function() {
		return this.id;
	},
	
	setId: function(id) {
		this.id = id;
	},
	
	getName: function() {
		return this.name;
	},
	
	setName: function(n) {
		this.name = n;
	},
	
	getTabIndex: function() {
		return this.tabIndex;
	},
	
	setTabIndex: function(t) {
		this.tabIndex = t;
	},
	
	getDir: function() {
		return this.dir;
	},
	
	setDir: function(d) {
		this.dir = d;
	},
	
	getTagName: function() {
		return this.tagName;
	},
	
	getParentNode: function() {
		return this.parentNode;
	},

	getClassName: function() {
		return this.className;
	},

	setClassName: function(className) {
		return this.className = className;
	},

	setStyle: function() {
		if ( arguments.length == 1 && typeof arguments[0] == 'object' ) {
			// Bulk set
			for ( var key in arguments[0] ) {
				// No recursion, since we're using the explicit set command
				this.setStyle(key, arguments[0][key]);
			}
		} else {
			this['style'][arguments[0]] = arguments[1];
		}
	},

	getStyle: function(style) {
		return this.style[style];
	},

	getValue: function() {
		return this.value;
	},

	getChildNodes: function() {
		DOMElement._initArray(this.childNodes);
		return this.childNodes;
	},

	getNextSibling: function() {
		return DOMElement._initElement(this.nextSibling);
	},

	getPreviousSibling: function() {
		return DOMElement._initElement(this.previousSibling);
	},

	getFirstChild: function() {
		return DOMElement._initElement(this.firstChild);
	},

	getLastChild: function() {
		return DOMElement._initElement(this.lastChild);
	},

	setTextValue: function(text) {
		return this.innerText = text;
	},

	setInnerFBML: function (innerFBML) {
		var _self = this;
		var _innerFBML = innerFBML;
		var _renderURL = Ajax.RENDER_URL;
		// Invoke the render endpoint, then replace the contents with the results
		_self._req = new Ajax();
		// Applications should NEVER, EVER use this
		_self._req.dontUseProxy = true;
		_self._req.ondone = function(data) {
	//			alert("Setting inner HTML to " + data);
				child = _self.firstChild;
				while ( child ) {
					next = child.nextSibling;
					_self.removeChild(child);
					child = next;
				}
				_self.innerHTML = data;
			};
		_self._req.onerror = function(transport) {
				alert(transport.responseText);
			}
		_self._req.post(_renderURL, 'method=fbml&api_key='+Ajax.API_KEY+'&fbml='+escape(_innerFBML));
	},

	setInnerXHTML: function(innerXHTML) {
		this.innerHTML = innerXHTML;
	},
	
	getAbsoluteTop: function() {
		var sum = this.offsetTop;
		var npar = this.offsetParent
		while (npar) {
			
			sum += npar.offsetTop;
			npar = npar.offsetParent;
		}
		return sum;
	},
	
	getAbsoluteLeft: function() {
		var sum = this.offsetLeft;
		var npar = this.offsetParent
		while (npar) {
			sum += npar.offsetLeft;
			npar = npar.offsetParent;
		}
		return sum;
	}
};

var $HTMLAnchorElement = {
	
	getTarget: function() {
		return this.target;
	},
	
	setTarget: function(t) {
		this.target = t;
	}
};

var $HTMLImageElement = {
	
	getSrc: function() {
		return this.src;
	},
	
	setSrc: function(s) {
		this.src = s;
	}
};

var $HTMLFormElement = {
	serialize: function()
	{
		function getElementValue(formElement)
		{
			if(formElement.length != null) var type = formElement[0].type;
			if((typeof(type) == 'undefined') || (type == 0)) var type = formElement.type;

		   switch(type)
		   {
			 case 'undefined': return;

			 case 'radio':
				for(var x=0; x < formElement.length; x++)
				  if(formElement[x].checked == true)
				return formElement[x].value;

			 case 'select-multiple':
				var myArray = new Array();
				for(var x=0; x < formElement.length; x++)
				  if(formElement[x].selected == true)
					 myArray[myArray.length] = formElement[x].value;
				return myArray;

			 case 'checkbox':
				return formElement.checked;

			 default:
				return formElement.value;
			}
		}


		var form=this;
		var query = "";
		for(var i=0; i<form.elements.length; i++)
		{
			var key = form.elements[i].name;
			var value = getElementValue(form.elements[i]);
			if(key && value)
			{
				query += key +"="+ value +"&";
			}
		}
		return query;
	},
	
	getAction: function() {
		return this.action;
	},
	
	setAction: function(fAction) {
		this.action = fAction;
	}
};

var $HTMLFormChildElement = {
	setDisabled: function(isDisabled) {
		this.disabled = isDisabled;
	},

	setValue: function(value) {
		this.value = value;
	},
	
	setChecked: function(c) {
		this.checked = c;
	},
	
	getChecked: function() {
		return this.checked;
	},
	
	setReadOnly: function(r) {
		this.readOnly = r;
	},
	
	getReadOnly: function() {
		return this.readOnly;
	},
	
	setType: function(t) {
		this.type = t;
	},
	
	getType: function() {
		return this.type;
	},
	
	setAccessKey: function(t) {
		this.accessKey = t;
	},
	
	getAccessKey: function() {
		return this.accessKey;
	}
	
};

var $HTMLSelectElement = {
	getSelectedIndex: function() {
		return this.selectedIndex;
	},
	
	setSelectedIndex: function(t) {
		this.selectedIndex = t;
	},

	getOptions: function() {
		return this.options;
	}
};

var $HTMLOptionElement = {
	
	getSelected: function() {
		return this.selected;
	},
	
	setSelected: function(s) {
		this.selected = s;
	}
};

var $HTMLTableElement = {

	getCols: function() {
		return this.cols;
	},
	
	setCols: function(c) {
		this.cols = c;
	},
	
	setRows: function(r) {
		this.rows = r;
	},
	
	getRows: function() {
		return this.rows;
	}
};

var DOMElement =
{
	_initialized: false,
	
	_copy: function(el, fns) {
		for ( var key in fns ) {
			el[key] = fns[key];
		}
	},
	
	_initArray: function(arr) {
		if(!document.all||arr==null) { return; }
	
		for(var el=0;el<arr.length;el++) {
			DOMElement._initElement(arr[el]);
		}
	},	

	_initElement: function (el) {
		if(!document.all) { return; }
		if ( el == null || 'object' != typeof el ) { return; }
		
		if ( el['_DOMElement_initialized'] !== true ) {
			
			DOMElement._copy(el, $HTMLElement);			
			if ( el.tagName.toLowerCase() == 'form' ) {				
				DOMElement._copy(el, $HTMLFormElement);				
			} else if ( el.tagName.toLowerCase() == 'input' ) {				
				DOMElement._copy(el, $HTMLFormChildElement);
				if ( el['type'].toLowerCase() == 'select' ) {
					DOMElement._copy(el, $HTMLSelectElement);
				}				
			}
			el['_DOMElement_initialized'] = true;
		}
	},
	
	//decorates nodes with necessary functions on post-page-load
	decorate: function(node)
	{
		if (node.nodeName != '#text') {
			
			$ListenerModel.decorateObject(node);
			
			if (document.all) {
				
				DOMElement._copy(node, $HTMLElement);
				var tname = node.tagName.toLowerCase(); 
				if (tname == 'a')    DOMElement._copy(node, $HTMLAnchorElement);
				if (tname == 'img')  DOMElement._copy(node, $HTMLImageElement);
				if (tname == 'form') DOMElement._copy(node, $HTMLFormElement);
				if ((tname == 'input') || (tname == 'select') || (tname == 'textarea')) {
					DOMElement._copy(node, $HTMLFormChildElement);
					DOMElement._copy(node, $HTMLOptionElement);
					DOMElement._copy(node, $HTMLFormChildElement);
					DOMElement._copy(node, $HTMLSelectElement);
				}
				if (tname == 'table')  DOMElement._copy(node, $HTMLTableElement);
			}
			
			for (var k = 0; k < node.childNodes.length; k++) {
				this.decorate(node.childNodes[k]);
			}	
		}
	},
	
	//called when the page loads to do post-load decoration
	onPageLoad: function()
	{
		for (var k = 0; k < document.childNodes.length; k++) {
			DOMElement.decorate(document.childNodes[k]);
		}
	},
	
	init: function()
	{
		if ( DOMElement._initialized ) { return; }
		DOMElement._initialized = true;
	
		document.location.setHref = $Location.setHref;
		document.location.getHref = $Location.getHref;
		
		document.setTitle = $HTMLDocument.setTitle;
		document.getTitle = $HTMLDocument.getTitle;
		
		document.getRootElement = $HTMLDocument.getRootElement;
		
		document.setLocation = $HTMLDocument.setLocation;
		
		window.location.setHref = $Location.setHref;
		window.location.getHref = $Location.getHref;

		$ListenerModel.decorateObject(document);

		if(!document.all)
		{	
			
			$ListenerModel.decorateObject(HTMLDocument.prototype);
			$ListenerModel.decorateObject(HTMLElement.prototype);
			
			DOMElement._copy(HTMLDocument.prototype, $HTMLDocument);
			DOMElement._copy(HTMLElement.prototype, $HTMLElement);
			DOMElement._copy(HTMLAnchorElement.prototype, $HTMLAnchorElement);
			DOMElement._copy(HTMLImageElement.prototype, $HTMLImageElement);
			DOMElement._copy(HTMLFormElement.prototype, $HTMLFormElement);
			DOMElement._copy(HTMLSelectElement.prototype, $HTMLSelectElement);
			DOMElement._copy(HTMLSelectElement.prototype, $HTMLFormChildElement);
			DOMElement._copy(HTMLOptionElement.prototype, $HTMLOptionElement);
			DOMElement._copy(HTMLInputElement.prototype, $HTMLFormChildElement);
			DOMElement._copy(HTMLButtonElement.prototype, $HTMLFormChildElement);
			DOMElement._copy(HTMLTextAreaElement.prototype, $HTMLFormChildElement);
			DOMElement._copy(HTMLTableElement.prototype, $HTMLTableElement);
		}
		else
		{
			//
			//	IE doesn''t allow access to HTMLElement
			//	so we need to override
			//	*document.createElement
			//	*document.getElementById
			//	*document.getElementsByTagName
			// *document.addEventListener

			//take a copy of
			//document.createElement
			var _createElement = document.createElement;

			//override document.createElement
			document.createElement = function(tag)
			{
				var _elem = _createElement(tag);
				DOMElement._initElement(_elem);
				return _elem;
			}

			//take copy of
			//document.getElementById
			var _getElementById = document.getElementById;

			//override document.getElementById
			document.getElementById = function(id)
			{
				var _elem = _getElementById(id);

				DOMElement._initElement(_elem);

				return _elem;
			}

			//take copy of
			//document.getElementsByTagName
			var _getElementsByTagName = document.getElementsByTagName;

			//override document.getElementsByTagName
			document.getElementsByTagName = function(tag)
			{
				var _arr = _getElementsByTagName(tag);

				DOMElement._initArray(_arr);
				
				return _arr;
			}						
		}
	}
};


DOMElement.init();

window.__onload = window.onload;
window.onload = function() {
	DOMElement.onPageLoad();
	if (window.__onload)   window.__onload();
}
