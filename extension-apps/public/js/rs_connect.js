/*******************************************************************************
 * Ringside Networks, Harnessing the power of social networks.
 * 
 * Copyright 2008 Ringside Networks, Inc., and individual contributors as indicated
 * by the @authors tag or express copyright attribution
 * statements applied by the authors.  All third-party contributions are
 * distributed under license by Ringside Networks, Inc.
 * 
 * This is free software; you can redistribute it and/or modify it
 * under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation; either version 2.1 of
 * the License, or (at your option) any later version.
 * 
 * This software is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this software; if not, write to the Free
 * Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA
 * 02110-1301 USA, or see the FSF site: http://www.fsf.org.
 ******************************************************************************/

 
 /*
  * RSPostConnection
  * This class is designed to be a connection wrapper class.  Calling code should
  * just need to specify the URL, the post_data, and the callback method when the
  * call has finished.  If you wish to use GET instead of post use RSGetConnection
  * 
  * URL: Any http url, can have querystring parameters if needed.
  * 		http://localhost/ringside/index.php?title=rsTestPage&content=&action=render
  * post_data: Post data must be one text string, or an & delimted list of key=value pairs that can be interpreted by the server as form variables.
  * 		var post_data = "key=" + encodeURI(id) + "&comment=" + encodeURI(comment) + "&location=" + encodeURI(location) + "&data=" + encodeURI(data) + "&type=" + encodeURI(type) + "&page_id=" + encodeURI(id);
  * callback: Callback method must be in the form
  * 		function handleResponse(responseText)
  * 	Where handleResponse is whatever method you want, but it must accept a string parameter which
  * 	is the response from the http server.
  */
RSPostConnection = function (url, post_data, callback) 
{
	var _url = url;

	// Make the call to Ringside to create the page for the item with this ID
	function connect() {
	    if(window.XMLHttpRequest && !(window.ActiveXObject)) {
	      try {
	        req = new XMLHttpRequest();
	      } catch(e) {
	        req = false;
	      }
	    // branch for IE/Windows ActiveX version
	    } else if(window.ActiveXObject) {
	      try {
	        req = new ActiveXObject("Msxml2.XMLHTTP");
	      } catch(e) {
	        try {
	              req = new ActiveXObject("Microsoft.XMLHTTP");
	        } catch(e) {
	              req = false;
	        }
	      }
	    }
	    req.onreadystatechange = handlePostDataResponse;
    	req.open("POST", _url);
		// Post Data: var1=blah&var2=blahmore&var3= etc.
		// key, user_id, comment, location, data, type, page_id
		req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		if(isNaN(post_data))
			post_data = "";
			
		req.send(post_data);
	}
	
	// Handle the request, once done create the image tagger
	function handlePostDataResponse() 
	{
		if ( req.readyState == 4 ) 
		{
			callback.call(this, req.responseText);
		} 
	}
	
	connect();
	
	//alert(_url);
}

 /*
  * RSPostConnection
  * This class is designed to be a connection wrapper class.  Calling code should
  * just need to specify the URL, the payload (XML String), and the callback method when the
  * call has finished.  If you wish to use Post instead of post use RSPostConnection
  * 
  * URL: Any http url, can have querystring parameters if needed.
  * 		http://localhost/ringside/index.php?title=rsTestPage&content=&action=render
  * post_data: Post data must be one text string, or an & delimted list of key=value pairs that can be interpreted by the server as form variables.
  * 		var post_data = "key=" + encodeURI(id) + "&comment=" + encodeURI(comment) + "&location=" + encodeURI(location) + "&data=" + encodeURI(data) + "&type=" + encodeURI(type) + "&page_id=" + encodeURI(id);
  * callback: Callback method must be in the form
  * 		function handleResponse(responseText)
  * 	Where handleResponse is whatever method you want, but it must accept a string parameter which
  * 	is the response from the http server.
  */
RSGetConnection = function (url, payload, callback) 
{
	//http://localhost/ringside/index.php?title=mark&content=~~~~~&action=render
	var _url = url;

	// Make the call to Ringside to create the page for the item with this ID
	function connect() {
	    if(window.XMLHttpRequest && !(window.ActiveXObject)) {
	      try {
	        req = new XMLHttpRequest();
	      } catch(e) {
	        req = false;
	      }
	    // branch for IE/Windows ActiveX version
	    } else if(window.ActiveXObject) {
	      try {
	        req = new ActiveXObject("Msxml2.XMLHTTP");
	      } catch(e) {
	        try {
	              req = new ActiveXObject("Microsoft.XMLHTTP");
	        } catch(e) {
	              req = false;
	        }
	      }
	    }
	    req.onreadystatechange = handleDataResponse;
    	req.open("GET", _url);
		// Post Data: var1=blah&var2=blahmore&var3= etc.
		// key, user_id, comment, location, data, type, page_id
		req.setRequestHeader('Content-Type', 'text/xml');

		if(isNaN(payload))
			payload = "";
		req.send(payload);
	}
	
	// Handle the request, once done create the image tagger
	function handleDataResponse() 
	{
		if ( req.readyState == 4 ) 
		{
			callback.call(this, req.responseText);
		} 
	}

	connect();
	
	//alert(_url);
}

String.prototype.endsWith = function(t, i) 
{ 
	if (i==false) 
	{ 
		return (t == this.substring(this.length - t.length)); 
	} else 
	{ 
		return (t.toLowerCase() == this.substring(this.length - t.length).toLowerCase()); 
	} 
}


/*
 * WikiPageCreator creates a page if it doesn't exist.
 * id = unique id or name of the page
 * callback method you wish to be called once the page has been created, method must be in the following form:
 * 		mycallback(reponse_text)
 */
function createWikiPage(id, url, callback)
{
	// URL to create a page takes the following form, you can put wikitext in content,
	// but then you should use caction to prepend or append to the page.  Not using
	// caction will cause the page to be rewritten with your wikitext.
	// http://localhost/ringside/index.php?title=mark&content=&action=render
	// ringsideServer comes form rs_config.js
	var _url = url;

	if(!_url.endsWith('/'))
		_url += "/";

	var page = _url + "index.php?title=" + id;
	_url += "index.php?title=" + id + "&content=&action=render";
	
	new RSGetConnection(_url, "", callback) 	
	return page;
}



