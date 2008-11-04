
var $ListenerModel = {

	addEventListenerFirefox: function(type, listener, useCapture) {		
		if (!useCapture) useCapture = false;
		this.__addEventListener(type, listener, useCapture);
		this._listenerManager.listenerAdded(type, listener);
	},  
	
	addEventListenerIE: function(type, listener, useCapture) {		
		if (!useCapture) useCapture = false;
		var ename = 'on' + type;
		this.attachEvent(ename, listener);
		this._listenerManager.listenerAdded(type, listener);
	},
	
	removeEventListenerFirefox: function(type, listener, useCapture) {		
		if (!useCapture) useCapture = false;
		this.__removeEventListener(type, listener, useCapture);
		this._listenerManager.listenerRemoved(type, listener);
	},  
	
	removeEventListenerIE: function(type, listener, useCapture) {		
		if (!useCapture) useCapture = false;
		var ename = 'on' + type;
		this.detachEvent(ename, listener);
		this._listenerManager.listenerRemoved(type, listener);
	},
	
	listEventListeners: function(ename) {
		return this._listenerManager.getAllListeners(ename);
	},
	
	purgeEventListeners: function(ename) {
		var elist;
		do {
			elist = this._listenerManager.getAllListeners(ename);
			this.removeEventListener(ename, elist[elist.length-1]);
		} while (elist.length > 0);
	},
	
	decorateObject: function(node)
	{
		node._listenerManager = new $ListenerManager();
		node.listEventListeners = $ListenerModel.listEventListeners;
		node.purgeEventListeners = $ListenerModel.purgeEventListeners;
		if (!document.all) {
			if (node.addEventListener) {
				node.__addEventListener = node.addEventListener;
				node.addEventListener = $ListenerModel.addEventListenerFirefox;
				node.__removeEventListener = node.removeEventListener;
				node.removeEventListener = $ListenerModel.removeEventListenerFirefox;
			}
		} else {
			if (node.attachEvent) {
				node.addEventListener = $ListenerModel.addEventListenerIE;
				node.removeEventListener = $ListenerModel.removeEventListenerIE;
			}
		}
	}
	
};

function $ListenerManager() {
	
	this.eventListeners = new Array();
	
	this.listenerAdded = function(ename, ehandler) {
		if (!this.eventListeners[ename])  this.eventListeners[ename] = new Array();
		var alen = this.eventListeners[ename].length;
		this.eventListeners[ename][alen] = ehandler;
	},
	
	this.getAllListeners = function(ename) {
		if (this.eventListeners[ename]) return this.eventListeners[ename];
		return new Array();
	},

	this.listenerRemoved = function(ename, listener) {
		var alist = this.getAllListeners(ename);
		for (var k = 0; k < alist.length; k++) {
			if (alist[k] == listener) {
				this.eventListeners[ename].splice(k, 1);	
				break;
			}
		}
	}
		
};

