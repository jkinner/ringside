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
  * id - the unique id of the thing you are tagging
  	$_POST['delete']
	$_POST["favorite_url"];
	$_POST["favorite_type"];
	$_POST["favorite_data_type"];
  */
RSFavoriteTagger = function()
{
	// Get the config
	var config = new RingsideConfig();
	
	// Set the page URL
	var url = config.getRingsideServer();
	url += "index.php?action=favorite";
	
	function deleteFavorite(id)
	{
		//var post_data = "key=" + encodeURI(id) + "&comment=" + encodeURI(comment) + "&location=" + encodeURI(location) + "&data=" + encodeURI(data) + "&type=" + encodeURI(type) + "&page_id=" + encodeURI(id);
		var post_data = "title=" + id + "&delete=" + id;
		//function (url, post_data, callback) 
		
		new RSPostConnection(url, post_data, handleDelete);
	}
	
	function save()
	{
		/*
		$_POST['delete']
	$_POST["favorite_url"];
	$_POST["favorite_type"];
	$_POST["favorite_data_type"];
	* */
		//var post_data = "key=" + encodeURI(id) + "&comment=" + encodeURI(comment) + "&location=" + encodeURI(location) + "&data=" + encodeURI(data) + "&type=" + encodeURI(type) + "&page_id=" + encodeURI(id);
		var post_data = "title=" + id + "&delete=" + id;
		//function (url, post_data, callback) 
		
		new RSPostConnection(url, post_data, handleDelete);
	}
	
	function handleDelete(data)
	{
		
	}
	
	/*
// Logging in URL
// api.php?action=login&lgname=user&lgpassword=password
RSGetConnection = function (url, payload, callback) 
function handleResponse(response_text)
{
	alert(response_text);
}

//var post_data = "key=" + encodeURI(id) + "&comment=" + encodeURI(comment) + "&location=" + encodeURI(location) + "&data=" + encodeURI(data) + "&type=" + encodeURI(type) + "&page_id=" + encodeURI(id);

new RSPostConnection("http://localhost/ringside/index.php?title=mark&content=&action=render", "", handleResponse);
*/
}

