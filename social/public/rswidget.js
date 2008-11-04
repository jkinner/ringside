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

function Querystring(qs) { // optionally pass a querystring to parse
	this.params = {};
	
	//this.get=Querystring_get;
	this.get=function(key, default_) {
		var value=this.params[key];
		return (value!=null) ? value : default_;
	}
		
	if (qs == null);
		qs=location.search.substring(1,location.search.length);

	if (qs.length == 0) 
		return;

	qs = qs.replace(/\+/g, ' ');
	var args = qs.split('&'); // parse out name/value pairs separated via &
	
	for (var i=0;i<args.length;i++) {
		var pair = args[i].split('=');
		var name = unescape(pair[0]);
		
		var value = (pair.length==2)
			? unescape(pair[1])
			: name;
		
		this.params[name] = value;
	}
}

//function Querystring_get(key, default_) {
//	var value=this.params[key];
//	return (value!=null) ? value : default_;
//}

/**
 * This class is responsible for finding, processing and displaying fbml inside
 * the div tags of class rs-fbml. For example it you add this onload handler to 
 * your html document: 
 *  <body onLoad="rswidget.renderAllWidgets('3ab2279e118ca848d58c20d9c4747451')">
 * where the number passed is your api key, you will be able to render fbml in
 * the context of your application. If authentication is required, the user will
 * be redirected to facebook for login. 
 *
 * For facebook to then be able to return control to your calling webpage its
 * callaback URL for your api key must be set to the ringside trust endpoint
 * which is usually /ringside/trust.php. Within the ringside you must also
 * register this api key and its secret key. This allows ringside to make
 * requests on behalf of your application.
 * 
 * Once this is done, declaring any div tag like the one below will force
 * its contents to be rendered as fbml.
 * Example:
 * 	<div id="sample1" class="rs-fbml">
 *	 <fb:success> <fb:message> Hello World.</fb:message></fb:success>
 *	</div>
 *
 * @author wreichardt@ringsidenetworks.com
 * @version 1.1
 */


var rswidget={
	 // NOTE: You may have to change this in your copy of this script if you change
	 // the directory you install to on your web server.

	/** 
	 * Can be widget or app,  Specified if div continas fbml to render or if if it should
	 * be filled with the full app specified by the apiKey param.  
	 */
	mode:"widget", 
	/** 
	 * The path on your server where the script responsible for fbml rendering is located.
	 */
	renderEndpoint:"/render.php",
	/** 
	 * The path on your server where the script responsible for establishing a 
	 * trust relationship is located.
	 */
	trustEndpoint:"/trust.php",
	/** 
	 * Used by the ringside server to determine which authentication authority will 
	 * be used to authenticate your users. This is used to establish a trust relationship
	 * with a remote service such as facebook, which the ringside server will use to
	 * oerate on your script's behalf.
	 */
	trustKey:"ringside",

	/**
	 * The hostname and potentially the path on teh server that your login.php page
	 *  is located on. This string will have login.php appened to it a runtime.
	 */
	loginServer:"http://api.facebook.com",

	/**
	 * Sets the HTML that will be displayed in all widgets while they are requesting
	 * rendering from the server.
	 */
	waitingHtml:"<b>Loading...</b>",
	
	/** 
	 * The path on your web site server where the script responsible for resizing iframes is located.
	 */
	resizeUrl:null,
	
	/**
	 * If set to true, the widget writes debug informaiton to an external log
	 * browser window.
	 */
	logging:true,

	/**
	 * Forces the browser to re-direct to a login page to create a session with a
	 * user.
	 */
	forceAuthentication:false,
	
	authenticationLogonFbml:"<input name='Login' type='button' id='Authenticate' value='Login' onClick='rswidget.requiresAuthentication();'/>",		
	authenticationLogoffFbml:"<input name='Logout' type='button' id='Authenticate' value='logout' onClick='rswidget.logout();'>",
	social_session_key:null,
	uid:null,
	supressErrors:true,
	//riginalUrl:null,
	
	onBeforeRenderAllWidgets:null,	
	/**
	 * Call to render all rswidgets on page. Locates all rs-fbml divs on the page
	 * and renders them from the ringside server.
	 */
	renderAllWidgets:function(param){
		
		if('string' == typeof param){
			this.apiKey=param;
		} else {
			// In case you want to pass settings you can do that using
			// a load handler like this which loads a graphic while the widgets build.
			// rswidget.renderAllWidgets({apiKey:'3ab2279e118ca848d58c20d9c4747451',
			this.apiKey=param.apiKey;
			if (typeof param.mode != "undefined") {
				this.mode=param.mode;
			}
			if (typeof param.waitingHtml != "undefined") {
				this.waitingHtml=param.waitingHtml;
			}
			if (typeof param.loginServer != "undefined") {
				this.loginServer=param.loginServer;
			}
			if (typeof param.trustKey != "undefined") {
				this.trustKey=param.trustKey;
			}
			if (typeof param.trustEndpoint != "undefined") {
				this.trustEndpoint=param.trustEndpoint;
			}
			if (typeof param.renderEndpoint != "undefined") {
				this.renderEndpoint=param.renderEndpoint;
			}
			if (typeof param.logging != "undefined") {
				this.logging=param.logging;
			}
			if (typeof param.forceAuthentication != "undefined") {
				this.forceAuthentication=param.forceAuthentication;
			}
			if (typeof param.onBeforeRenderAllWidgets != "undefined") {
				this.onBeforeRenderAllWidgets=param.onBeforeRenderAllWidgets;
			}
			if (typeof param.uid != "undefined") {
				this.uid=param.uid;
			}
			if (typeof param.social_session_key != "undefined") {
				this.social_session_key=param.social_session_key;
			}
			if (typeof param.supressErrors != "undefined") {
				this.supressErrors=param.supressErrors;
			}
			if (typeof param.authenticationLogonFbml != "undefined") {
				this.authenticationLogonFbml=param.authenticationLogonFbml;
			}
			if (typeof param.authenticationLogoffFbml != "undefined") {
				this.authenticationLogoffFbml=param.authenticationLogoffFbml;
			}
			if ( typeof param.resizeUrl != "undefined" ) {
				this.resizeUrl = param.resizeUrl;
			}
		}
	
		if(this.onBeforeRenderAllWidgets!=null){
			this.onBeforeRenderAllWidgets();
		}
		
		//log("RETURNED control to widget<p>");
		var isAuthenticated=this.extractAuthFromQuery();
		if(this.forceAuthentication){
			if(!isAuthenticated){
				this.requiresAuthentication();
				return;				
			}
//			var qs = new Querystring();
//			if(qs.get("session_key")==null){
//				this.requiresAuthentication();
//				return;
//			}
		}
		var widget=this;
				var arryElements=widget.getElementsByClassName("rs-fbml");
				for(var elemIndex=0;elemIndex<arryElements.length;elemIndex++){
					var isLast=false;
					if(arryElements.length-1==elemIndex){
						isLast=true;
					}
					if(this.mode=="app"){
						this.renderAsApp(arryElements[elemIndex]);
					} else {
						this.fbmlRenderViaJsonp(arryElements[elemIndex]);
					}		
				}
	},	
	
	logout: function (param) {
			if(param)
				window.location.href=this.loginServer+ '/logoff.php?'+"social_callback="+escape(param);
			else		
				window.location.href=this.loginServer+ '/logoff.php';
	},	
	
	/**
	 * Attemps to extract a uid and social_session_key from they query params. 
	 * If returns true, social_session_key was found
	 */
	extractAuthFromQuery: function(){
			var qs = new Querystring();
			var success=false;
			if(qs.get("social_session_key")!=null){
				this.social_session_key=qs.get("social_session_key");
				success=true;
			}
			if(qs.get("uid")!=null){
				this.uid=qs.get("uid");
			}
			return success;
	},
	
	isAuthenticated: function(){
			var qs = new Querystring();
			if(qs.get("social_session_key")==null){
				return false;
			}
			return true;
	},
	

	requiresAuthentication: function(){
		//var widgetid="";	
			//rswidget.originalUrl=location.href;
			var trustEndpoint=this.trustEndpoint;
				setTimeout(
			function() {
				  // If the script block exists, remove it
//				  var script=document.getElementById("script_"+widgetid+"timeout");
//				  if (script) {
//				    script.parentNode.removeChild(script);
//				  }
				  
				  var theDate = new Date( );
				  var script=document.createElement('SCRIPT');
				  script.id="script_Auth";//+widgetid
				  script.type="text/javascript";
				  script.src=trustEndpoint+"?method=verify&time="+theDate;
				  //log(script.src);
				  var head=document.getElementsByTagName('HEAD')[0];
				  head.appendChild(script);
			},
			
		 10);
		
	},
	
	/**
	 * Loads a widget using script tags and JSONP into the provided element.
	 */
	fbmlRenderViaJsonp:function(elementToUpdate){		
		var fbml=elementToUpdate.innerHTML;
		var widgetid=elementToUpdate.id;	
		elementToUpdate.innerHTML=this.waitingHtml;
		var api_key=this.apiKey;
		var render_endpoint=this.renderEndpoint;
		var socialsessionkey=this.social_session_key;
		var _uid=this.uid;
		setTimeout(
			function() {
				  // If the script block exists, remove it
				  var script=document.getElementById("script_"+widgetid);
				  if (script) {
				    script.parentNode.removeChild(script);
				  }
				  
				  var theDate = new Date( );
				  var query="method=fbml&format=JSON&callback=renderJsonFbml&api_key="+api_key+"&widgetid="+widgetid+"&fbml="+escape(fbml);
				  if(socialsessionkey){
				  	query=query+'&social_session_key='+socialsessionkey;
				  }
				  
				  if(_uid){
				  	query=query+'&uid='+_uid;
				  }
				  
				  script=document.createElement('SCRIPT');
				  script.id="script_"+widgetid;
				  script.type="text/javascript";
				  script.src=render_endpoint+"?"+query+"&time="+theDate;
				  //log("Widget Calling "+script.src);
				  var head=document.getElementsByTagName('HEAD')[0];
				  head.appendChild(script);
			},
			
		 10);

	},

	/**
	 * Loads a widget using the apiKey to find and app and render its 
	 * index into this div.
	 */
	renderAsApp:function(elementToUpdate){		
		var api_key=this.apiKey;
		var render_endpoint=this.renderEndpoint;
		var socialsessionkey=this.social_session_key;
		var _uid=this.uid;

		  var query="method=app&api_key="+api_key;
		  if(socialsessionkey){
		  	query=query+'&social_session_key='+socialsessionkey;
		  }
		  
		  if(_uid){
		  	query=query+'&uid='+_uid;
		  }
		  if ( this.resizeUrl != null ) {
		  	query += '&resizeUrl='+escape(this.resizeUrl);
		  }
		var Url=render_endpoint+"?"+query;
		elementToUpdate.innerHTML="<iframe id=\"if_"+api_key+"\" src=\""+Url+"\" width=\"100%\" frameborder=\"no\"/>";
	},
	
	/**
	 * Construct a URL to redirect to for authentication.
	 */
    createLoginUrl: function () {//
    	var next="?api_key="+this.apiKey+"&method=widget&social_callback="+escape(location.href)+"&trust_key="+this.trustKey+"&auth_key="+this.trustKey;
		var url=this.loginServer+ '/login.php?skipcookie&return_session=1&api_key=' + this.apiKey + '&next=' + escape(next) +'&v=1.0';
		//var url=this.loginServer+ '/login.php?return_session=1&api_key=' + this.apiKey + '&v=1.0';
        //log("Redirecting to:<p>"+url);
		return url;
    },
	
	/**
	 * Returns an array of elements with a specific class attribue class="className".
	 */
	getElementsByClassName:function(className){
		var allElements = (document.all) ? document.all:document.getElementsByTagName("*");
		var results = new Array();
		var re=new RegExp("\\b"+className+"\\b");
		for( var i=0;i<allElements.length;i++){
			if(re.test(allElements[i].className)){
				results.push(allElements[i]);
			}
		}
		return results;
	}
}

	function log(message) {
	    if (!log.window_ || log.window_.closed) {
	        var win = window.open("", null, "width=400,height=200," +
	                              "scrollbars=yes,resizable=yes,status=no," +
	                              "location=no,menubar=no,toolbar=no");
	        if (!win) return;
	        var doc = win.document;
	        doc.write("<html><head><title>Debug Log</title></head>" +
	                  "<body></body></html>");
	        doc.close();
	        log.window_ = win;
	    }
	    var logLine = log.window_.document.createElement("div");
	    logLine.appendChild(log.window_.document.createTextNode(message));
	    log.window_.document.body.appendChild(logLine);
	}



function authneticateResponse(authReponse){
	if(authReponse.authenticated==false){
			var loginUrl=rswidget.createLoginUrl();
			window.location.href=loginUrl;		
	}
}

function renderJsonFbml(renderResponse){

	var responseType=null;
	if (typeof renderResponse.response != "undefined") {
		responseType=renderResponse.response;
	}
	
	var widgetid=null;
	if (typeof renderResponse.widgetid != "undefined") {
		widgetid=renderResponse.widgetid;
	} else {
		alert("An unexpected error has occured. widgetid was not provided.");
		return;
	}
	var element=document.getElementById(widgetid);
	
	if(responseType=="success"){
		// Render the returned fbml
				
		var content=null;
		if (typeof renderResponse.content != "undefined") {
			if(renderResponse.content==""||renderResponse.content==null){
				if(!rswidget.supressErrors){
					var error="<font color='red'>An unexpected error has occured. No FBML was returned.</font><p>";
					error=error+"The fbml that caused this error was<br>";
				    error=error+"<textarea name='textarea' cols='60' rows='5'>"+renderResponse.fbml+"</textarea><br>";
				    error=error+"<p>Logging in might fix this problem. "+rswidget.authenticationLogonFbml+"</p>";
					element.innerHTML=error;
				} else {
					content="";
				}
			} else {
				content=renderResponse.content;
			}
		} else {
			if(!rswidget.supressErrors){
				var error="<font color='red'>An unexpected error has occured. No FBML was returned.</font><p>";
				error=error+"The fbml that caused this error was<br>";
				error=error+"<textarea name='textarea' cols='60' rows='5'>"+renderResponse.fbml+"</textarea><br>";
				element.innerHTML=error;
			} else {
				content="";
			}			
		}
		var element=document.getElementById(widgetid);
		//log("The returned HTML is:"+content);
		element.innerHTML=content;
		
	} else if (responseType=="error") {
		// Render the returned error
		var error="<font color='red'><b>An Error has Occured</b><p>";
		error = error +"message: "+renderResponse.message+"<br>";
		error = error +"code: "+renderResponse.code+"<br>";
		error = error +"file: "+renderResponse.file+"<br>";
		error = error +"line: "+renderResponse.line+"<br>";
		if(renderResponse.fbml!=null){
	    	error=error+"<textarea name='textarea' cols='60' rows='5'>"+renderResponse.fbml+"</textarea><br>";
		}
		error = error +"</font><p>";
		if(!rswidget.supressErrors){
			element.innerHTML=error;
		} else {
			element.innerHTML="";
		}
		if(renderResponse.code==104){
			var unsessionedUrl=location.protocol+"//"+location.host;
			if(confirm('Your session may no longer be valid. Do you want to log in again?'+unsessionedUrl)){
				window.location.href=unsessionedUrl;		
			} else {
				element.innerHTML=error;				
			}
		}
		
	} else {
		if(!rswidget.supressErrors){
			element.innerHTML="<font color='red'>An unexpected response was returned from this request.</font><p>";
		} else {
			element.innerHTML="";
		}
		return;
	}	
}

