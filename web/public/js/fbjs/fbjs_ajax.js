
var Ajax = function(){
	// Constructor
	// Properties
	this.RAW=0;
	this.JSON=1;
	this.FBML=2;
	this.ondone=function(){}
	this.onerror=function(){}
	this.onlogin=function(){}
	this.requireLogin = false;
	this.responseType = this.RAW;
	this.useLocalProxy= false;
	this.dontUseProxy = false;

	// Declare Private Methods
	function createXMLHttpRequest() {
		try { return new XMLHttpRequest(); } catch(e) {}
		try { return new ActiveXObject("Msxml2.XMLHTTP"); } catch (e) {}
		try { return new ActiveXObject("Microsoft.XMLHTTP"); } catch (e) {}
		return null;
	}

	this.xmlHttpRequest=createXMLHttpRequest();

	// Declare Public Methods
	this.post=function(url, query){
		try {
			//check whether user is in session (see fbjs_all.php for variable definition)
			if(this.requireLogin && !ringside_fbjs_in_session) {
				this.onlogin();
				window.location = ringside_fbjs_login_url;
			}

			// Send the request
			if ( this.dontUseProxy ) {
				this.xmlHttpRequest.open("POST", url, true);
			} else {
				this.xmlHttpRequest.open("POST", Ajax.PROXY_URL + '/' + Ajax.API_KEY + '?' + url, true);
			}

			this.xmlHttpRequest.setRequestHeader("Content-length", query.length);
			this.xmlHttpRequest.setRequestHeader("Connection", "close");
			this.xmlHttpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			var _parent=this;
 			this.xmlHttpRequest.onreadystatechange = function() {
   				if (_parent.xmlHttpRequest.readyState != 4)  {
   					return;
   				}
   				if(_parent.xmlHttpRequest.status == 200){
	   				var serverResponse = _parent.xmlHttpRequest.responseText;
	   				try {
		   				if(_parent.responseType==_parent.JSON){
		   					var obj=eval(serverResponse);
		   					_parent.ondone(obj);
		   				} else if(_parent.responseType==_parent.FBML){
		   					var dom=_parent.parseText(serverResponse);
								_parent.ondone(dom);
		   				} else {
		   					_parent.ondone(serverResponse);
		   				}
		   			} catch ( e ) {
//		   				document.write("<!-- " + e.message + " -->");
		   				_parent.onerror(e.message);
		   			}
   				} else {
						_parent.onerror();
   				}
 			};

 			// Build query
 			if ( typeof query == 'object' ) {
 				// The query is really a hash of name/value pairs. Build a query string.
 				real_query = '';
 				for ( var name in query ) {
 					if ( real_query != '' ) {
 						real_query += '&';
 					}

 					if ( typeof query[name] == 'object' ) {
 						query_frag = '';
 						for ( var key in query[name] ) {
 							if ( query_frag != '' ) {
 								query_frag += '&';
 							}

 							query_frag += escape(name + '[' + key + ']') + '=' + escape (query[name][key]);
 						}
 						real_query += query_frag;
 					} else {
	 					real_query += escape(name) + "=";
 						real_query += escape(query[name]);
 					}

 				}
 				query = real_query;
 			}

 			this.xmlHttpRequest.send(query);
		} catch (e){
			this.onerror();
		}
	}

	this.abort=function(){
		xmlHttpRequest.abort();
	}

	this.parseText=function(text) {
    	var xmlDoc=null;
    	try //Internet Explorer
		{
		  xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
		  xmlDoc.async="false";
		  xmlDoc.loadXML(text);
		  }
		catch(e)
		  {
		  try //Firefox, Mozilla, Opera, etc.
		    {
		    parser=new DOMParser();
		    xmlDoc=parser.parseFromString(text,"text/xml");
		    }
		  catch(e) {}

		  }
		  return xmlDoc;
	}

}
