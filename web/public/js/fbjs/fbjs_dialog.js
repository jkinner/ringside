
var activeDialog = null;

function dialogConfirmClicked()
{
	activeDialog.onconfirm();
	dialogCloseActive();
};

function dialogCancelClicked()
{
	activeDialog.oncancel();
	dialogCloseActive();
};

function dialogCloseActive()
{
	var jid = '#' + activeDialog.divId;
	$(jid).dialog("close");
	$(jid).dialog("destroy");
	activeDialog.destroyDialog();
	activeDialog = null;
};

Dialog.DIALOG_POP = 0;
Dialog.DIALOG_CONTEXTUAL = 1;

function Dialog(dType) {

	this.dialogType = dType;
	
	this.context = null;
	
	this.divClass = 'flora';
	
	this.divId = -1;
	
	this.onconfirm = function() {;},
	
	this.oncancel = function() {;},
	 
	this.setStyle = function(s) {
		context.setStyle(s);
	},
	
	this.initDialog = function(title, content)
	{
		if (this.divId != -1) this.destroyDialog();
		
		activeDialog = this;
		
		var newDiv = document.createElement('div');
    	var did = 'someDiv_' + Math.floor(Math.random()*100000);
    	newDiv.setAttribute('id', did);
    	newDiv.setAttribute('class', this.divClass);
    	newDiv.setAttribute('title', title);
    	newDiv.innerHTML = content;
    	document.body.appendChild(newDiv);
    	this.divId = did; 
	}
	
	this.destroyDialog = function()
	{
		var tempDiv = document.getElementById(this.divId);
    	document.body.removeChild(tempDiv);
    	this.divId = -1;
    	this.context = null;
    	this.onconfirm = function() {;};
		this.oncancel = function() {;};
	}
	
	this.getStyleJSON = function() {
    	if (this.dialogType == Dialog.DIALOG_CONTEXTUAL) {
    		if (this.context != null) {
    			var pleft = this.context.getAbsoluteLeft();
    			var ptop = this.context.getAbsoluteTop();
    			
    			var j = ", position: [" + pleft + "," + ptop + "]";
    			j = j + ", height: 125 ";
    			
    			return j;
    		}
    	} else {
    		//var j = ", modal: true";
    		var j = "";
    		return j;
    	}	
    },
	 
    this.showMessage = function(title, content, button_confirm)
    {
    	if (!button_confirm) button_confirm = 'Confirm';
    	
    	this.initDialog(title, content);
    	var jDivId = '#' + this.divId;
    	
    	var bjson = "buttons: {\"" + button_confirm + "\":dialogConfirmClicked}";
    	var djson = "dialogJSON = { " + bjson + this.getStyleJSON() + " }; ";
    	eval(djson);
    	$(jDivId).dialog(dialogJSON);
    	$(jDivId).dialog("open");
    },
    
    this.showChoice = function(title, content, button_confirm, button_cancel)
    {
    	if (!button_confirm) button_confirm = 'Okay';
    	if (!button_cancel) button_cancel = 'Cancel';
    	
    	this.initDialog(title, content);
    	var jDivId = '#' + this.divId;
    	
    	var bjson = "buttons: {\"" + button_confirm + "\":dialogConfirmClicked,\"" +
    								button_cancel + "\":dialogCancelClicked}";
    	var djson = "dialogJSON = { " + bjson + this.getStyleJSON() + " }; ";
    	eval(djson);
    	$(jDivId).dialog(dialogJSON);
    	$(jDivId).dialog("open");
    },
     
	this.setContext = function(c) {
		this.context = c;
		return this;
	},
	
	this.hide = function() {
		var jDivId = '#' + this.divId;
		$(jDivId).dialog("hide");
	}
};

