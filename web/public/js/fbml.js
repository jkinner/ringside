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
/**
 * Returns a document wide list of elements of a particular class.
 */
function getElementsByClassName(className){
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

/**
 * Updates the selected count field and the value of the element with
 * the id <uidListId> which will receive a comma separated list of
 * uids of selected users.
 */
function updateFbMultiFriendSelectedField(selectedAnchor){
    var classToFind="friend-selected";
    var selectedFriends=getElementsByClassName(classToFind);
    var selectedCountElement=document.getElementById("selectedCount");
    var selectedFriendsCount=selectedFriends.length;
    var uidListId="payment-ids";
    var uidListElement=document.getElementById(uidListId);
    
    selectedCountElement.innerHTML="("+selectedFriendsCount+")";
    
    var uidListValue="";
    for(var elemIndex=0;elemIndex<selectedFriendsCount;elemIndex++){
        var isLast=false;
        if(selectedFriendsCount-1==elemIndex){
            isLast=true;
        }
        uidListValue=uidListValue+getMultiFriendSelectorUidFromAnchor(selectedFriends[elemIndex]);
        if(!isLast){
            uidListValue=uidListValue+",";
        }       
    }
    uidListElement.value=uidListValue;
    
}

function getMultiFriendSelectorUidFromAnchor(selectedAnchor){
    var myChildren=selectedAnchor.childNodes;
    for(var elemIndex=0;elemIndex<myChildren.length;elemIndex++){
        var classTestName=myChildren[elemIndex].className;
        if(classTestName=="MultiFriendUid"){
            return myChildren[elemIndex].innerHTML;
        }       
    }
}

function countCurrentlySelectedFields(){
	var classToFind="friend-selected";
    var selectedFriends=getElementsByClassName(classToFind);
	return selectedFriends.length;
}

/**
 * On Click Handler called by Anchor tag for friend selector.
 * Changes class of anchor and updates total count and id list.
 */
function onUpdateFbMultiFriendSelected(selectedAnchor){
    var selectStyleName="friend-selected";
    var unselectStyleName="";
	var numFriendsElem=document.getElementById("numfriends");
	var numFriendsMax=numFriendsElem.innerHTML;
	var numRemainingElem=document.getElementById("social_paymentNumRemaining");
    
    var count=countCurrentlySelectedFields();
    if( selectedAnchor.className==unselectStyleName ){
    	var  remaining =numFriendsMax- count-1;
    	if(count<numFriendsMax){
        	selectedAnchor.className=selectStyleName;
    	} else {
    		alert("You have used up all your friend selections.");
    	}
    } else {
    	var  remaining =numFriendsMax- count+1;
        selectedAnchor.className=unselectStyleName;
    }

	if(remaining>0){
		numRemainingElem.innerHTML=""+remaining;
	} else {
		numRemainingElem.innerHTML="no";
	}
    updateFbMultiFriendSelectedField(selectedAnchor);
}

