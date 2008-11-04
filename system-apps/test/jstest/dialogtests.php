<script>

function testMessageDialog()
{
	var x = new Dialog(Dialog.DIALOG_POP);
	x.onconfirm = confirmClicked;
	x.showMessage('This is a title', 'This is the content', 'Confirm');
}

function testMessageDialogContext()
{
	var b = document.getElementById('someButton3');
	
	var x = new Dialog(Dialog.DIALOG_CONTEXTUAL);
	x.setContext(b);
	x.onconfirm = confirmClicked;
	x.showMessage('This is a title', 'This is the content', 'Confirm');
}


function testChoiceDialog()
{
	var x = new Dialog(Dialog.DIALOG_POP);
	x.onconfirm = confirmClicked;
	x.oncancel = cancelClicked;
	x.showChoice('This is a title', 'This is the content', 'Confirm', 'Nevermind');
}

function confirmClicked()
{
	alert('confirmClicked()');
}

function cancelClicked()
{
	alert('cancelClicked()');
}

function testAll()
{
	testDialog();
}

</script>


<form name="myParentForm" id="myParentForm" action="someAction">
	<input id="someButton1" type="button" value="Message Dialog" onclick="testMessageDialog()" />
	<input id="someButton2" type="button" value="Choice Dialog" onclick="testChoiceDialog()" />
	<br /><br /><br /><br /><br /><br /><br />
	<input id="someButton3" type="button" value="Message Dialog (contextual)" onclick="testMessageDialogContext()" />
</form>



