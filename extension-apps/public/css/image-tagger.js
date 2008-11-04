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

ImageTagger = function ( image, config, url ) {
  var _self = this;
  
  var _img = YAHOO.util.Dom.get(image);
  
  var _cropper;

  var _url = url;
  
  var _tagPlane;
  var _tagList;
  
  var _tagRegions = new Array();
  
  var defaultWidth = 100;
  var defaultHeight = 150;

  function defineConfig(object, event) {
    if ( ! event ) { event = window.event; }
    var config = new Object();
    var mousePos = translateMouseToRelative(object, event);
    config.h = defaultHeight;
    config.w = defaultWidth;
    config.x = Math.min(Math.max(0, mousePos.x-(config.w / 2)), object.width - config.w);
    config.y = Math.min(Math.max(0, mousePos.y-(config.h / 2)), object.height - config.h);
    return config;
  }
  
  function translateMouseToRelative(object, e) {
    var objPos = findPos(object);
    return { 
      x: event.clientX - objPos[0],
      y: event.clientY - objPos[1]
    }
  }
  
	function createTagControl(config) {
	  var editElem = document.createElement("DIV");
	    editElem.className = "image-tagger";
	    editElem.style.position = "absolute";
	    editElem.style.left = '50px';
	    editElem.style.top = '50px';
	    editElem.style.zIndex = 250;
	    if ( config.className ) {
	      editElem.className = className;
	    }
	    
	    var formElem = document.createElement("FORM");
	    // Prevent Safari from submitting the page EVEN THOUGH we say not to.
	      formElem.action="javascript:";
	      addEventHandler(formElem, 'submit', function() {  submitCrop(_cropper, inputElem.value); hideCropper();  return false; });
	        var inputElem = document.createElement("INPUT");
	        inputElem.setAttribute('type', "TEXT");
	        inputElem.setAttribute('size', '30');
	        inputElem.setAttribute('name', 'comment');
	        inputElem.style.fontFamily = 'Arial';
	        inputElem.style.fontSize = '8pt';
	
	        var saveElem = document.createElement("INPUT");
	        saveElem.setAttribute('type', 'button');
	        saveElem.value = "Save";
	        addEventHandler(saveElem, 'click', function() { submitCrop(_cropper, inputElem.value);  hideCropper();  return false; });
	
	        var cancelElem = document.createElement("INPUT");
	        cancelElem.setAttribute('type', 'button');
	        cancelElem.value = "Cancel";
	        addEventHandler(cancelElem, 'click', function() { hideCropper();  return false; });
	
	    formElem.appendChild(inputElem);
	    formElem.appendChild(saveElem);
	    formElem.appendChild(cancelElem);
	  editElem.appendChild(formElem);
	  return editElem;
	}

  function createTagRegion(config, comment) {
//    alert("Creating tag region at " + config.x + ", " + config.y + " for '" + comment + "'");
    var tagElem = document.createElement("DIV");
      tagElem.className = "image-tag-region";
      tagElem.style.position = "absolute";
      tagElem.style.left = config.x+'px';
      tagElem.style.top = config.y+'px';
      tagElem.style.width = config.w+'px';
      tagElem.style.height = config.h+'px';
      tagElem.style.border = 'none';
      tagElem.style.borderColor = '#cccccc';
//      tagElem.style.background = '#ffffff';
//      tagElem.style.opacity = '0%';
//      tagElem.className = "image-tag-region";
//      tagElem.style.zIndex = -20;
    return tagElem;
  }
  
  function addEventHandler(object, event, handler, debug) {
    if ( object.addEventListener ) {
      object.addEventListener(event,handler,false);
    } else if ( object.attachEvent ) {
      object.attachEvent('on'+event,handler);
    } else {
      object['on'+event] = handler;
    }
  }

  function removeEventHandler(object, event, handler, debug) {
    if ( object.removeEventListener ) {
      object.removeEventListener(event,handler,false);
    } else if ( object.detachEvent ) {
      object.detachEvent('on'+event,handler);
    } else {
      delete object['on'+event];
    }
  }

  function createXMLHttpRequest() {
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
    
    return req;
  }
  
  function submitCrop(cropper, comment) {
    r = cropper.getCropRegion();
//    alert("(" + r.x + ", " + r.y + ") " + r.w + "x" + r.h + " (" + comment + ")");
    req = createXMLHttpRequest();
    
    req.onreadystatechange = handleGetTagsResponse;
//    alert("Posting to URL " + _url );
    req.open("POST", _url);
    req.setRequestHeader("Content-Type", "text/xml");
    commentText = "";
    if ( comment ) { commentText = comment.innerText; }
    req.send("<tagregion x='" + r.x + "' y='" + r.y + "' width='" + r.w + "' height='" + r.h + "'>" + comment + "</tagregion>\n");
//    alert('Sent request');
  }

  function showCropper(imagename, config, object) { 
    if ( ! _cropper ) {
      _cropper = new YAHOO.widget.ImageCropper( imagename, config );
      editControl = createTagControl(config);
      _cropper.addControl(editControl);
      editControl.style.zIndex = 100;
      
      inputElem = editControl.getElementsByTagName('INPUT')[0];
      if ( config ) {
          //editElem.style.left = Math.min(Math.max(0, object.x+config.x+(config.w / 2)), object.x + object.width - inputElem.w) + 'px';
//              alert(''+object.offsetLeft+' '+object.width+' '+this.offsetWidth);
//              inputElem.parentNode.parentNode.style.left = object.left+object.width-this.offsetWidth + 'px';
        inputElem.parentNode.style.offsetLeft = object.left+object.width-inputElem.offsetWidth + 'px';
      }
      if ( config ) {
//              inputElem.parentNode.parentNode.style.top = (config.y + config.h) + 'px';
        inputElem.parentNode.style.top = (config.y + config.h) + 'px';
      }

      inputElem.focus();
    }
  }

  function hideCropper() {
    if ( _cropper ) {
      _cropper.destroy();
    }
    _cropper = null;
  }
  
  this.destroy = function () {
    hideCropper();
  }
  
	TagHoverManager = function(commentSpan, tagRegion, commentDelLink, tagid) {
	  var _tagRegion = tagRegion;
	  var _tagid = tagid;
	  var _self = this;
	  
	  function show(e) {
      _tagRegion.showRegionBox();
      return false;
	  }
	
	  function hide(e) {
      _tagRegion.hideRegionBox();
      return false;
	  }
	  
	  function remove() {
	    req = createXMLHttpRequest();
	    
	    req.onreadystatechange = handleGetTagsResponse;
	    var requestUrl;
	    if ( _url.indexOf('?') >= 0 ) {
	      requestUrl = _url + '&delete=' + _tagid;
	    } else {
        requestUrl = _url + '?delete=' + _tagid;
	    }
	    
//      alert("Posting to URL for delete: " + requestUrl );
	    req.open("POST", requestUrl);
	    req.setRequestHeader("Content-Type", "text/xml");
	    req.send("");
	  }
	  
    addEventHandler(commentSpan, 'mouseover', show);
    addEventHandler(commentSpan, 'mouseout', hide);
    addEventHandler(commentDelLink, 'click', remove);
	}
	 
  RegionHoverManager = function (commentSpan, tagregionControl) {
    var _commentSpan = commentSpan;
    var _saveBackground = commentSpan.style.backgroundColor;
    
    function show(e) {
      _commentSpan.style.backgroundColor = '#ffff11';
      _commentSpan.style.borderColor = '#000000';
      _commentSpan.style.border = 'solid';
      _commentSpan.style.borderSize = '1px';
      _commentSpan.style.padding = '4px';
      return true;
    }
  
    function hide(e) {
      _commentSpan.style.backgroundColor = _saveBackground;
      _commentSpan.style.border = 'none';
      _commentSpan.style.padding = '0px';
      return true;
    }
    
    addEventHandler(tagregionControl, 'mouseover', show);
    addEventHandler(tagregionControl, 'mouseout', hide);
  }

  TagRegion = function (img, x, y, w, h, tagPlane, commentSpan, id) {
    // Raw x,y,width,height from the tag store
    var _x = x;
    var _y = y;
    var _w = w;
    var _h = h;
    var _counter = 0;
    var _img = img;
    var _imgPos = findPos(img);
    
    var _commentSpan = commentSpan;
    var _tagPlane = tagPlane;
    var _self = this;
    var _regionBox;
    var _showMode = true;
    var _saveBackground;
    
    function highlightSpan() {
      _saveBackground = commentSpan.style.backgroundColor
      _commentSpan.style.backgroundColor = '#ffff11';
//      _commentSpan.style.borderColor = '#000000';
//      _commentSpan.style.border = 'solid';
//      _commentSpan.style.borderSize = '1px';
//      _commentSpan.style.padding = '4px';
      return true;
    }
  
    function unhighlightSpan() {
      _commentSpan.style.backgroundColor = _saveBackground;
      _commentSpan.style.border = 'none';
      _commentSpan.style.padding = '0px';
      return true;
    }
    
//    _tagList.appendChild(document.createTextNode("[" + w + "x" + h + "@(" + x + "," + y + ")]"));
    function showRegionBox() {
      if ( _regionBox == undefined ) {
		    _regionBox = document.createElement("DIV");
		      _regionBox.className = "image-tag-region";
		      _regionBox.style.position = "absolute";
          var imgPos = findPos(_img);
		      var x = _x+imgPos[0];
          var y = _y+imgPos[1];
		      _regionBox.style.left = x+"px";
		      _regionBox.style.top = y+"px";
		      _regionBox.style.width = _w+"px";
		      _regionBox.style.height = _h+"px";
		      _regionBox.style.border = 'solid';
		      _regionBox.style.borderColor = '#cccccc';
		      _regionBox.style.backgroundColod = 'transparent';
		      _regionBox.id = "tag-region-" + id;
		    _tagPlane.appendChild(_regionBox);
//        alert("Appending _regionBox " + _regionBox + " at " + _regionBox.offsetLeft + ", " + _regionBox.offsetTop + " with _tagPlane " + _tagPlane);
//		    alert("Registering mouseout on myself");
        addEventHandler(_regionBox, "mouseout", handleMouseOut);
        addEventHandler(_regionBox, "mousemove", handleMoveRegion );
        highlightSpan();
      }
    }
    
    function handleMoveRegion(e) {
      if (!e) var e = window.event;
      var mousex = YAHOO.util.Event.getPageX(e);
      var mousey = YAHOO.util.Event.getPageY(e);
//      _counter++;
//      var imgPos = findPos(_img);
//      if ( (_counter % 50) == 0) { alert( "Still moving events along at " + mousex + ", " + mousey ); counter = 50; }
      return true;
    }
    
    function hideRegionBox() {
      if ( _regionBox != undefined ) {
//        alert("Removing _regionBox " + _regionBox + " with _tagPlane " + _tagPlane);
        _tagPlane.removeChild(_regionBox);
        _regionBox = undefined;
//        alert("Removing mouseout on myself");
        removeEventHandler(_self, "mouseout", handleMouseOut);
        unhighlightSpan();
      }
    }
    
    function mousePos(e) {
      var posx = 0;
		  var posy = 0;
		  if (!e) var e = window.event;
		  if (e.pageX || e.pageY)   {
		    if ( _showMode ) {
//		      alert( "Using pageX and pageY");
		      _showMode = false;
		    }
		    posx = e.pageX;
		    posy = e.pageY;
		  }
		  else if (e.clientX || e.clientY)  {
        if ( _showMode ) {
//          alert( "Using clientX and clientY");
          _showMode = false;
        }
		    posx = e.clientX + document.body.scrollLeft
		      + document.documentElement.scrollLeft;
		    posy = e.clientY + document.body.scrollTop
		      + document.documentElement.scrollTop;
		  }
		  
		  return { x: posx, y: posy };
    }
    
    function handleMove(e) {
      if ( !e ) { e = window.event; }
      var mousex = YAHOO.util.Event.getPageX(e);
      var mousey = YAHOO.util.Event.getPageY(e);
      
      var imgPos = findPos(_img, true);
      var x = mousex - imgPos[0];
      var y = mousey - imgPos[1];
      var checkx = _x;
      var checky = _y;
      
//      _counter++;
//      var imgPos = findPos(_img);
//      if ( (_counter % 50) == 0) { alert( "Mouse at: "+ x + ", " + y ); }

      if ( checkx <= x && x <= (checkx + _w) 
           && checky <= y && y <= (checky + _h) ) {
        showRegionBox();
      } else {
        hideRegionBox();
      }
      
      return true;
    }
    
    function handleMouseOut(e) {
      if ( !e ) { e = window.event; }
      var target = e.relatedTarget?e.relatedTarget:e.toElement;
      
//      alert("Target is " + target.id );
      if ( target != _regionBox ) {
//        alert("Mousing out"); 
        hideRegionBox();
      }
      return true;
    }
    
//    alert("Registering move handler for " + _w + "x" + _h + "@(" + _x + "," + _y + ")");
    addEventHandler(_img, "mousemove", handleMove);
    addEventHandler(_img, "mouseout", handleMouseOut);
    
    
    this.showRegionBox = showRegionBox;
    this.hideRegionBox = hideRegionBox;
    
//    addEventHandler(_img, "mouseover", handleMove);
  }
  
	function resetTagregions() {
	  var tagPlane = _tagPlane;
    _tagPlane = document.createElement("DIV");
    _tagPlane.className = 'tag-plane';
//    _tagPlane.style.border="solid";
    _img.parentNode.parentNode.replaceChild(_tagPlane, tagPlane);
    _tagPlane.appendChild(_img);
    _tagList = document.createElement("DIV");
    _tagList.className = "tag-list";
//    _tagList.style.zIndex = 75;
    var addTagElem = document.createElement("A");
    addTagElem.appendChild(document.createTextNode("(add tag) "));
    addTagElem.setAttribute("href", "javascript:");
    addEventHandler(addTagElem,  'click', function (event) {if ( ! event ) { event = window.event; } showCropper(_img.id, defineConfig(this, event), this);});
    _tagList.appendChild(addTagElem);
    _tagPlane.appendChild(_tagList);
	}
	
	// From: http://www.quirksmode.org/js/findpos.html
	function findPos(obj, includeBody, debug) {
	  var curleft = curtop = 0;
	  var lastObj;
	  if (obj.offsetParent) {
	    curleft = obj.offsetLeft;
	    curtop = obj.offsetTop;
	    while (obj = obj.offsetParent) {
        if ( ! includeBody && ( obj.offsetParent == document.body || obj.offsetParent.offsetParent == null ) ) break;
	      curleft += obj.offsetLeft;
	      curtop += obj.offsetTop;
	    }
	  }
//	  alert("left: " + curleft + " top: " + curtop);
	  return [curleft,curtop];
	}
	
  function handleGetTagsResponse() {
    if ( req.readyState == 4 ) {
      if ( req.responseXML != null ) {
        if ( req.responseXML.documentElement.nodeName != 'tags' ) {
          alert( 'Response: ' + req.responseXML.documentElement.nodeName );
        } else {
			    resetTagregions();

          // Process each tagregion and tag
          var tagregions = req.responseXML.documentElement.getElementsByTagName('tagregion');
          var imgPos = findPos(_img);
//          alert("Image position is " + imgPos[0] + ", " + imgPos[1]);
          for ( var i = 0; i < tagregions.length; i++ ) {
            var tagregion = tagregions[i];
            var config = new Object();
            config.x = parseInt(tagregion.getAttribute('x')) + imgPos[0];
            config.y = parseInt(tagregion.getAttribute('y')) + imgPos[1];
//            alert("Region position is " + tagregion.getAttribute('x') + ", " + tagregion.getAttribute('y') + " with offset " + imgPos[0] + ", " + imgPos[1] + ": " + config.x + ", " + config.y);
            config.w = parseInt(tagregion.getAttribute('width'));
            config.h = parseInt(tagregion.getAttribute('height'));
//            alert("Region is at (" + config.x + ", " + config.y + ")");
            tagid = tagregion.getAttribute('id');
//            var tagregionControl = createTagRegion(config, tagregion.text);
//            tagregionControl.id = tagid;
//            tagregionControl.style.zIndex = 500;
//            _tagPlane.appendChild(tagregionControl);
            commentSpan = document.createElement("SPAN");
            commentDelLink = document.createElement("A");
            commentDelLink.setAttribute("href", "javascript:");
            var commentText = document.createElement("span");
            commentText.innerHTML = tagregion.firstChild.nodeValue;

            if ( commentDelLink.appendChild) {
              commentDelLink.appendChild(document.createTextNode("(del)"));
            } else if ( commentDelLink.add ) {
              commentDelLink.add(document.createTextNode("(del)"));
            }

            commentSpan.appendChild(commentText);
            commentSpan.appendChild(commentDelLink);
            // TODO: Need to de-reference this on clean up
//            new RegionHoverManager(commentSpan, tagregionControl);
            var tagRegion = new TagRegion(_img, parseInt(tagregion.getAttribute('x')), parseInt(tagregion.getAttribute('y')), config.w, config.h, _tagPlane, commentSpan, tagid);
            new TagHoverManager(commentSpan, tagRegion, commentDelLink, tagid);
//            var numManagers = _tagregionManagers.length;
            _tagList.appendChild(commentSpan);
            if ( i+1 < tagregions.length ) {
              _tagList.appendChild(document.createTextNode(" | "));
            }
          }
        }
      } else {
        alert( 'No response received from script: ' + req.responseText );
      }
    } 

    return true;
  }
  
  function importNode(node, document) {
    var resultNode;

    if ( node.nodeType == 1 ) {
      resultNode = document.createElement(node.nodeName);
      if ( node.attributes ) {
        for ( var i = 0; i < node.attributes.length; i++ ) {
          alert("copying attribute " + node.attributes[i].nodeName + ": " + node.attributes[i].nodeValue);
          node.setAttribute(node.attributes[i].nodeName, node.attributes[i].nodeValue);
          alert(node.attributes[i].nodeName + " = " + node.getAttribute(node.attributes[i].nodeName));
        }
      } else {
        alert("No attributes to copy");
      }
      
      for ( var i = 0; i < node.childNodes.length; i++ ) {
        var childNode = importNode(node.childNodes[i], document);
        if ( childNode ) {
          resultNode.appendChild(childNode);
        }
      }
    } else if ( node.nodeType == 3 || node.nodeType == 4 ) {
      resultNode = document.createTextNode(node.nodeValue);
    }
    
    return resultNode;
  }
  
  function getTags() {
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
    req.onreadystatechange = handleGetTagsResponse;
//    alert("Getting from URL " + _url );
    req.open("GET", _url);
    req.setRequestHeader("Content-Type", "text/xml");
    req.send("");
  }
  
  function _init() {
	  _tagPlane = document.createElement("DIV");
	  _tagPlane.className = 'tag-plane';
	  _tagList = document.createElement("DIV");
	  _tagList.className = 'tag-list';
	  var addTagElem = document.createElement("A");
	  addTagElem.innerText = "(add tag) ";
    addTagElem.setAttribute("href", "javascript:");
//    addTagElem.style.zIndex = 75;
    addEventHandler(addTagElem,  'click', function (event) {if ( ! event ) { event = window.event; } showCropper(_img.id, defineConfig(this, event), this);});
    _tagList.appendChild( addTagElem );
	  _img.parentNode.replaceChild(_tagPlane, _img);
    _tagPlane.appendChild(_img);
    _tagPlane.appendChild(_tagList);
    getTags();
  }
  
    // Ruthlessly stolen from image-cropper.js
    // Figure out whether the rendered size of the image is known.
    // In WebKit, don't use the onload event. It seems unreliable.

    if ( YAHOO.env.ua.webkit ) {

        _loadTimer = setInterval( function () {
            if ( _img.width !== 0 || _img.height !== 0 ) {
                clearInterval( _loadTimer );
                _init();
            }
        }, 100 );

    } else if ( !_img.complete || _img.naturalWidth === 0 ) {

        // The browser is either still loading the image, or it may be done,
        // but the image was not loaded (network problem, missing resource)
        // We don't need to differentiate between these two cases.

        YAHOO.util.Event.addListener( _img, "load", _init );

    } else {

        // The image seems to be loaded.
        _init();

    }

  
};
