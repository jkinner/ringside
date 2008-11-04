<?php
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
 
$fbml = stripslashes($_POST['fbml']);
?>
<h2>FBML tester</h2>
<fb:editor action="?render" labelwidth="100">
	<fb:editor-custom>
	 <textarea name="fbml" rows="20" cols="80"><?php print htmlspecialchars( $fbml, ENT_NOQUOTES) ?></textarea>
	</fb:editor-custom>
	<fb:editor-buttonset>
		<fb:editor-button value="Render" />
		<fb:editor-cancel />
	</fb:editor-buttonset>
</fb:editor>
Results:<br />
<hr />
<?php
//if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	print $fbml;
//}
?><hr />
