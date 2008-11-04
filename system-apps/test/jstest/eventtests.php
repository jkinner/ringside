<script type="text/javascript">

function endsWith(str, s)
{
	var reg = new RegExp(s + "$");
	return reg.test(str);
}

function setTestStatus(fname, status)
{
	var tdiv = document.getElementById(fname);
    tdiv.innerHTML = fname + ': ' + status;
}

var eventHandler1Called = false;
function eventHandler1(e) {
	eventHandler1Called = true;
}

var eventHandler2Called = false;
function eventHandler2(e) {
	eventHandler2Called = true;
}

var eventHandler3Called = false;
function eventHandler3(e) {
	eventHandler3Called = true;
}

function testAddEventListener()
{
    var n = document.getElementById('testButton1');
    
    var passed = 'FAILED';
    if (n.addEventListener) {
    	if (!eventHandler1Called) {    
        	n.addEventListener('click', eventHandler1);
        	n.click();
        	if (eventHandler1Called) {
        		passed = 'PASSED';	
        	}
        }
    }
    
    setTestStatus('testAddEventListener', passed);
}

function testRemoveEventListener()
{
    var n = document.getElementById('testButton3');
    
    var passed = 'FAILED';
    n.addEventListener('click', eventHandler2);
    n.addEventListener('click', eventHandler3);
    var elist = n.listEventListeners('click');
    if (elist.length == 2) {
    	n.removeEventListener('click', eventHandler2);
    	if (elist.length == 1) {
    		n.removeEventListener('click', eventHandler3);
    		if (elist.length == 0) {
    			passed = 'PASSED';
    		}
    	}
    }
    
    setTestStatus('testRemoveEventListener', passed);
}

function testListEventListeners()
{
	var n = document.getElementById('testButton2');
    
    var passed = 'FAILED';
    if (n.addEventListener) {
    	n.addEventListener('click', eventHandler2);
    	n.addEventListener('click', eventHandler3);
    	if (n.listEventListeners) {
        	var elist = n.listEventListeners('click');
        	if (elist.length == 2) {
        		
        		if (!document.all) {
            		if ((elist[0].name == 'eventHandler2') && (elist[1].name == 'eventHandler3')) {
            			passed = 'PASSED';
            		}
            	} else {
            		//IE doesn't support function.name
            		passed = 'PASSED';
            	}
        	}
        }
    }
    
    setTestStatus('testListEventListeners', passed);
}

function testPurgeEventListeners()
{
	var n = document.getElementById('testButton4');
    
    var passed = 'FAILED';
    n.addEventListener('click', eventHandler2);
    n.addEventListener('click', eventHandler3);
    if (n.purgeEventListeners) {
        var elist = n.listEventListeners('click');
        if (elist.length == 2) {
        	n.purgeEventListeners('click');
        	elist = n.listEventListeners('click');
        	if (elist.length == 0) {
        		passed = 'PASSED';
        	} else {
        		alert('Wrong length (2): ' + elist.length);
        	}
        } else {
        	alert('Wrong length: ' + elist.length);
        }
        
    } else {
    	alert('No function found');
    }
    
    setTestStatus('testPurgeEventListeners', passed);
}

function testAll()
{
	testAddEventListener();
	testListEventListeners();
	testRemoveEventListener();
	testPurgeEventListeners();
}

</script>

<form name="myParentForm" id="myParentForm" action="someAction">
	<input id="someButton" type="button" value="Run Tests" onclick="testAll()" />
	<br /><br />
	
	<input id="testButton1" type="button" value="Test Button 1" />
	<input id="testButton2" type="button" value="Test Button 2" />
	<input id="testButton3" type="button" value="Test Button 3" />
	<input id="testButton4" type="button" value="Test Button 4" />
</form>

<a href="somelink.html" id="someLink" target="someTarget" />

<img src="badlink.png" id="someImage" />

<table id="someTable">
	<tr><td></td></tr>
	<tr><td></td></tr>
</table>

<div id="testAddEventListener"></div>
<div id="testListEventListeners"></div>
<div id="testRemoveEventListener"></div>
<div id="testPurgeEventListeners"></div>
