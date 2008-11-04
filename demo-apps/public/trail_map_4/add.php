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

$button_label = 'Create Suggestion';
?>
<body>
<fb:editor action="index.php">
	<fb:editor-custom>
		<input type="hidden" name="action" value="add_suggestion" id="action"/>
	</fb:editor-custom>
	<fb:editor-text label="New Topic" name="newtopic" value="newtopic"/>
	<fb:editor-custom label="Existing Topic">
		<select name="existingtopic">
			<option selected="true" value="none">-</option>
<?php
			foreach($topics as $topic)
			{
				echo '<option value="'.$topic['owneruid'].':'.$topic['topic'].'">'.$topic['topic'].'</option>';
			}
?>
		</select>
	</fb:editor-custom>
	<fb:editor-text label="Suggestion" name="suggestion" value="suggestion"/>
	<fb:editor-buttonset>
		<fb:editor-button name="submit_button" value="<?php echo $button_label ?>" />
		<fb:editor-cancel href="index.php?action=list"/>
	</fb:editor-buttonset>
</fb:editor>
</body>
