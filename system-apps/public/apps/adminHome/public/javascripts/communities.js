//Methods that are specific to this page
var rsAppViewer = {

    //This function takes the hash and breaks it up according to the splitIndex
    //The value is retrieved and the tab is selected.
    //TODO: Use name value pairs instea of the split index
    checkHash: function(ref, defaultTab) {

        var isActive = false;

        //Grab the hash #menuid1_tabId-menuid2_tabId from the current url localhost:8080/page#menuid1_tabId-menuid2_tabId
        var hash = window.location.hash;

        if (hash.length > 0) {

            //Look for the menuid in the window hash and see if we have a match
            var re = new RegExp(ref.id + "_\\w+","i");
            var tab = hash.match(re); 

            if (tab) {

		//Extract the tab id in the hash by removing the menu id
                var id =  "#" + tab[0].replace(ref.id + "_","");
                var elem = jQuery( id );

                //if we have the element we need to select it and mark that it is active for future check
                if (elem) {
                    ref.select(elem);
                    isActive = true;
                }
                elem = null;
            }
        }

        //If we do not have a tab that is active [selected] than select the default index specified
        if (!isActive) {
          var elem = jQuery("#" + ref.id + " a:eq(" + defaultTab + ")" );
          if(elem) ref.select(elem);
        }

    },

   //Looks for the vert tab selection in the hash
   checkHashVert: function (defaultTab) {
        rsAppViewer.checkHash(this, defaultTab); //first part of the hash
    },

   //Looks for the hort tab selection in the hash
   checkHashHort:  function(defaultTab) {
        rsAppViewer.checkHash(this, defaultTab); //second part of the hash
    },

   updateLinks :  function() {

		//notify the history manager we just changed location
        

        //update the links based on the current selections [#menu1Selection-menu2Selection]
        menu1.updateHref("#", "-" + menu2.id + "_" + menu2.selectedIndex.attr("id"));
        menu2.updateHref("#" + menu1.id + "_" + menu1.selectedIndex.attr("id") + "-", "");

        // Make Ajax call to get the html
        rsAppViewer.ajaxCall();
        historyWatcher1.resetTimer(true);
        window.setTimeout(function(){historyWatcher1.updateWatcher();},100);

    },

    //function that is called when a vert link is clicked
    vertSelection : function() {
        rsAppViewer.updateLinks();

    },

    //function that is called when a hort link is clicked
    hortSelection : function() {
        rsAppViewer.updateLinks();
       

    },

    //Put the Ajax call here to fetch the data
    ajaxCall : function(method, optParams ) {

		jQuery("#loadingIcon").slideDown();
		jQuery("#output").html("loading");
		
		var params = null;
		if(optParams != null){
			params = optParams;
		}
		else if(menu2.selectedIndex.attr("params") != null ){
			params = menu2.selectedIndex.attr("params");
		}
			
        var url = menu2.selectedIndex.attr("page") + "&net=" + menu1.selectedIndex.attr("id"); 
        
        if(method == null || method.toUpperCase() == "GET"){
        	jQuery.get(url,params,rsAppViewer.showPage);
        }
        else{
        	jQuery.get(url,params,rsAppViewer.showPage); 	
        }
    },
    

    
    showPage : function( response ){

		//TODO Check for errors in the response!!    	
    	
    	jQuery("#output").html( response );
        jQuery("#loadingIcon").slideUp();
        jQuery("#output").fadeIn();

    }

}

var historyWatcher1 = new historyWatcher();

function loadMenus(){

	//Add our menus to the page!!!
	//Register our two menus - menu1 = vert, menu2=hort
	menu1 = new ItemSelector("navcontainerVert", rsAppViewer.vertSelection, rsAppViewer.checkHashVert, 0);
	menu2 = new ItemSelector("navcontainerHort", rsAppViewer.hortSelection, rsAppViewer.checkHashHort, 0);

	//Now both menus are initialized, we need to update the links to select defaults
	rsAppViewer.updateLinks();

	//Register what to do when we detect the user has used back/forward navigation
	var menuUpdatesBackForwardFnc = function() {
	    menu1.hashNotification();
	    menu2.hashNotification();
	    rsAppViewer.updateLinks();
	}
	
	historyWatcher1.registerNotifier(menuUpdatesBackForwardFnc);
	historyWatcher1.init();

}