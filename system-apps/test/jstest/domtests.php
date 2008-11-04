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

function testGetParentNode()
{
    var n = document.getElementById('someButton');
    
    var passed = 'FAILED';
    if (n.getParentNode) {
    	var p = n.getParentNode();    
    	if (p.name == 'myParentForm') {
    		passed = 'PASSED';
    	}
    }
    
    setTestStatus('testGetParentNode', passed);
}

function testSetLocation()
{
	var passed = 'FAILED';
	if (document.setLocation) {
		passed = 'PASSED';
	}
	setTestStatus('testSetLocation', passed);
}

function testGetSetAction()
{
	var f = document.getElementById('myParentForm');
	var passed = 'FAILED';
	
	if (f.getAction && f.setAction) {
		var a = f.getAction();
		if (endsWith(a, 'someAction')) {
			f.setAction('newAction');
			a = f.getAction();
			if (endsWith(a, 'newAction')) {
				f.setAction('someAction');
				passed = 'PASSED';
			}
		}
	}
	
	setTestStatus('testGetSetAction', passed);
}

function testGetHref()
{
	var l = document.location;
	
	var passed = 'FAILED';
	if (l.getHref) {
		var u = l.getHref();
		if (endsWith(u, 'domtests.php')) {
			passed = 'PASSED';
		}
	}
	
	setTestStatus('testGetHref', passed);
}

function testGetSetTarget()
{
	var a = document.getElementById('someLink');
	
	var passed = 'FAILED';
	if (a.getTarget && a.setTarget) {
		var t = a.getTarget();
		if (t == 'someTarget') {
			a.setTarget('newTarget');
			t = a.getTarget();
			if (t == 'newTarget') {
				a.setTarget('someTarget');
				passed = 'PASSED';
			}
		}
	}
	
	setTestStatus('testGetSetTarget', passed);
}

function testGetSetSrc()
{
	var i = document.getElementById('someImage');
	
	var passed = 'FAILED';
	if (i.getSrc && i.setSrc) {
		var t = i.getSrc();
		if (endsWith(t, 'badlink.png')) {
			i.setSrc('newbadlink.png');
			t = i.getSrc();
			if (endsWith(t, 'newbadlink.png')) {
				i.setSrc('badlink.png');
				passed = 'PASSED';
			}
		}
	}
	
	setTestStatus('testGetSetSrc', passed);
}

function testGetSetTabIndex()
{
	var i = document.getElementById('someButton');
	
	var passed = 'FAILED';
	if (i.getTabIndex && i.setTabIndex) {
		var t = i.getTabIndex();
		if (t >= 0) {
			i.setTabIndex(5);
			t = i.getTabIndex();
			if (t == 5) {
				i.setTabIndex(5);
				passed = 'PASSED';
			}
		}
	}
	
	setTestStatus('testGetSetTabIndex', passed);
}

function testGetTagName()
{
	var b = document.getElementById('someButton');
	
	var passed = 'FAILED';
	if (b.getTagName) {
		var t = b.getTagName();
		if (t == 'INPUT') {
			passed = 'PASSED';
		}
	}
	
	setTestStatus('testGetTagName', passed);
}


function testGetSetDir()
{
	var f = document.getElementById('someButton');
	var passed = 'FAILED';
	
	if (f.getDir && f.setDir) {
		f.setDir('ltr');
		var a = f.getDir();
		if (a == 'ltr') {
			passed = 'PASSED';
		}
	}
	
	setTestStatus('testGetSetDir', passed);
}

function testGetSetTitle()
{
	var passed = 'FAILED';
	if (document.setTitle && document.getTitle) {
    	var oldt = document.getTitle();
    	if (oldt.length > 0) {
    		document.setTitle('new title');
    		t = document.getTitle();
    		if (t == 'new title') {
    			document.setTitle(oldt);
    			passed = 'PASSED';
    		}
    	}
    }
    
	setTestStatus('testGetSetTitle', passed);
}

function testGetSetName()
{
	var f = document.getElementById('someButton');
	var passed = 'FAILED';
	
	if (f.getName && f.setName) {
		var oldn = f.getName();
		f.setName('newName');
		var n = f.getName();
		if (n == 'newName') {
			f.setName(oldn);
			passed = 'PASSED';
		}
	}
	
	setTestStatus('testGetSetName', passed);
}

function testGetSetChecked()
{
	var f = document.getElementById('someButton');
	var passed = 'FAILED';
	
	if (f.getChecked && f.setChecked) {
		f.setChecked(true);
		var a = f.getChecked();
		if (a) {
			passed = 'PASSED';
		}
	}
	
	setTestStatus('testGetSetChecked', passed);
}

function testGetClientProps()
{
	var f = document.getElementById('someButton');
	var passed = 'FAILED';
	
	if (f.getClientWidth && f.getClientHeight) {
		a1 = f.getClientWidth();
		a2 = f.getClientHeight();
		if (a1 && a2) {
			passed = 'PASSED';
		}
	}
	
	setTestStatus('testGetClientProps', passed);
}

function testGetOffsetProps()
{
	var f = document.getElementById('someButton');
	var passed = 'FAILED';
	
	if (f.getOffsetWidth && f.getOffsetHeight) {
		a1 = f.getOffsetWidth();
		a2 = f.getOffsetHeight();
		if (a1 && a2) {
			passed = 'PASSED';
		}
	}
	
	setTestStatus('testGetOffsetProps', passed);
}

function testGetSetTableProps()
{
	var t = document.getElementById('someTable');
	var passed = 'FAILED';
	
	if (t.getRows && t.setRows && t.getCols && t.setCols) {
		var r = t.getRows();
		var c = t.getCols();
		passed = 'PASSED';
	}
	
	setTestStatus('testGetSetTableProps', passed);
}

function testGetScrollProps()
{
	var f = document.getElementById('someButton');
	var passed = 'FAILED';
	
	if (f.getScrollWidth && f.getScrollHeight && f.getScrollTop && f.getScrollLeft) {
		a1 = f.getScrollWidth();
		a2 = f.getScrollHeight();
		a3 = f.getScrollTop();
		a4 = f.getScrollLeft();
		if ((a1 >= 0) && (a2 >= 0) && (a3 >= 0) && (a4 >= 0)) {
			passed = 'PASSED';
		}
	}
	
	setTestStatus('testGetScrollProps', passed);
}

function testGetRootElement()
{
	var passed = 'FAILED';
	
	if (document.getRootElement) {
		var c = document.getRootElement();
		if (c) {
			if (c.className == 'canvas') {
				passed = 'PASSED';
			}
		}
	}
	setTestStatus('testGetRootElement', passed);
}

function testGetAbsolute()
{
	var passed = 'FAILED';
	var e = document.getElementById('someButton');
	if (e.getAbsoluteTop && e.getAbsoluteLeft) {
		var at = e.getAbsoluteTop();
		var al = e.getAbsoluteLeft();
		//just do a rough comparison
		if ((at > 50) && (al > 100))  passed = 'PASSED';
	}
	setTestStatus('testGetAbsolute', passed);
}


function testAll()
{
	testGetParentNode();
	testGetSetAction();
	testGetHref();
	testGetSetTarget();
	testGetSetSrc();
	testGetTagName();
	testGetSetDir();
	testGetSetChecked();
	testGetClientProps();
	testGetOffsetProps();
	testGetScrollProps();
	testGetSetTabIndex();
	testGetSetTitle();
	testGetSetName();
	testGetSetTableProps();
	testGetRootElement();
	testSetLocation();
	testGetAbsolute();
}

</script>

<form name="myParentForm" id="myParentForm" action="someAction">
	<input id="someButton" type="button" value="Run Tests" onclick="testAll()" />
</form>

<a href="somelink.html" id="someLink" target="someTarget" />

<img src="badlink.png" id="someImage" />

<table id="someTable">
	<tr><td></td></tr>
	<tr><td></td></tr>
</table>

<div id="testGetParentNode"></div>
<div id="testGetSetAction"></div>
<div id="testGetHref"></div>
<div id="testGetSetTarget"></div>
<div id="testGetSetSrc"></div>
<div id="testGetTagName"></div>
<div id="testGetSetDir"></div>
<div id="testGetSetChecked"></div>
<div id="testGetClientProps"></div>
<div id="testGetOffsetProps"></div>
<div id="testGetScrollProps"></div>
<div id="testGetSetTabIndex"></div>
<div id="testGetSetTitle"></div>
<div id="testGetSetName"></div>
<div id="testGetSetTableProps"></div>
<div id="testGetRootElement"></div>
<div id="testSetLocation"></div>
<div id="testGetAbsolute"></div>
