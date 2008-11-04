/*
function ItemSelector(id, onclickAction, hashAction, defaultTab){

  var ref = this;

  this.id = id;
  this.selectedIndex = null;
  this.load();

  //See if we have a bookmarked link to load certain tabs
  if(hashAction) hashAction.call(this,defaultTab);
  this.hashNotification = function(){hashAction.call(ref,defaultTab);};

  //Set the action to perform when a tab is clicked
  this.clickAction = function(){onclickAction.call(ref);};

}

ItemSelector.prototype.load = function(){

  var ref = this;
  //Bind onclick handlers to all of the menu tabs
  jQuery("#"+this.id + " a").bind( "click", function(){ ref.select(jQuery(this)) ;} );

}

//Set the href based on paremeters sent in and the links id
ItemSelector.prototype.updateHref = function(prefixcode,suffixcode){
  
  var ref = this;

  //Grabs all of the links within the specified div and sets the href
  var items = jQuery("#" + this.id + " a").each( function(item,link){  
    link.href = prefixcode + ref.id + "_" + link.id + suffixcode;
  }); 
  
}


//Remove the previous selected tag and select the new one by setting css classnames
ItemSelector.prototype.select = function(elem){

  //If a tab is already selected, remove it!
  if(this.selectedIndex) this.selectedIndex.removeClass("selected");

  //Set the current tab as selected and set it for future look ups
  elem.addClass("selected");
  this.selectedIndex = elem;

  //If we have a userdefined action fire it off 
  if(this.clickAction) this.clickAction.call(this);

}
*/



function ItemSelector(id, onclickAction, hashAction, defaultTab, cancelClick){

  var ref = this;

  this.id = id;
  this.selectedIndex = null;  
  this.defaultTab = defaultTab;

  //See if we have a bookmarked link to load certain tabs
  if(hashAction) hashAction.call(this,defaultTab);
  this.hashNotification = function(){hashAction.call(ref,defaultTab);};

  //determine if we should block the links action from firing 
  //[cancelClick is optionsal so if not included we do not cancel it]
  this.bubbleAction = (cancelClick)?false:true;
  
  //Set the action to perform when a tab is clicked
  this.clickAction = function(){ onclickAction.call(ref); };

  this.load();
}

ItemSelector.prototype.load = function(){

  var ref = this;
  //Bind onclick handlers to all of the menu tabs
  jQuery("#"+this.id + " a").bind( "click", function(){ ref.select(jQuery(this)); return ref.bubbleAction;} );

}

//Set the href based on paremeters sent in and the links id
ItemSelector.prototype.updateHref = function(prefixcode,suffixcode){
  
  var ref = this;

  //Grabs all of the links within the specified div and sets the href
  var items = jQuery("#" + this.id + " a").each( function(item,link){  
    link.href = prefixcode + ref.id + "_" + link.id + suffixcode;
  }); 
  
}


//Remove the previous selected tag and select the new one by setting css classnames
ItemSelector.prototype.select = function(elem){

  //If a tab is already selected, remove it!
  if(this.selectedIndex) this.selectedIndex.removeClass("selected");

  //Set the current tab as selected and set it for future look ups
  elem.addClass("selected");
  this.selectedIndex = elem;

  //If we have a userdefined action fire it off 
  if(this.clickAction) this.clickAction.call(this);

}

ItemSelector.prototype.selectDefault = function(){

  var elem = items = jQuery("#" + this.id + " a:eq(" + this.defaultTab + ")");

  this.select(elem);
}





//This object allows us to register events to watch for the user to click the 
//forward and backwards buttons on an Ajax based navigation and display the 
//correct document. This is not too advanced since the tab menus handles the hash.
var historyWatcher = function(){}

historyWatcher.prototype =  {

    //Stores an array of methods to call
    whoToCall: [],
    
    //Tells us if we have run the setup yet
    isInit : false,

    //Add a new method to call
    registerNotifier: function(fnc) {
        this.whoToCall.push(fnc);
    },

    //Call this when we change the hash in the menus
    updateWatcher: function() {
    	if(!this.isInit)return;
        this.resetTimer();
        this.setPage(); 
    },

    //Stores where we are right now
    currentPage: null,

    //holds our interval object
    timer: null,

    //Call this to start the watching
    init: function() {
        this.setPage();
        this.resetTimer();
        this.isInit = true;        
    },

    //Interval fires this to see if user has use navigation
    check: function() {
    	
        if (this.currentPage != window.location.hash) {
            this.setPage();        	        	
            this.notify();
        }
        this.resetTimer();
    },

    //Allow us to stop and restart the timer
    resetTimer: function(stop) {
        var ref = this;
        if (this.timer) var x = window.clearTimeout(this.timer);
        this.timer = null;
        if (!stop) this.timer = window.setTimeout(function() {
            ref.check();
        },
        100);
    },

    //Loops through the array when we detect the page url has changed.
    notify: function() {
        for (var i = 0; i < this.whoToCall.length; i++){
        	this.whoToCall[i]();        	
        }
    },

    //set the current page's hash to our variable
    setPage: function() {
        this.currentPage = window.location.hash;
    }

}