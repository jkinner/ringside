<?php
 /*
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
  */

/**
 * Document this file.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */

?>
				<div class="recipe-to-rate" style="background-color:#fff;">
				    <img src="<?php echo $item['img'] ?>" />
				    <div>		
					<h2><?php echo $item['name'] ?></h2>
					<p class="courtesy-of">courtesy of <?php echo $item['courtesy'] ?></p>
					<p class="what-do-you-think">What do you think of this recipe?</p>		
					<div class="ratings">						
						<input type="radio" name="scale_<?php echo $item['id'] ?>" id="tried-liked" value="3" />
						<label for="tried-liked">This one was good!</label>	
					
						<input type="radio" name="scale_<?php echo $item['id'] ?>" id="want-to-try" value="2" />
						<label for="want-to-try">Would like to try</label>
						
						<input type="radio" name="scale_<?php echo $item['id'] ?>" id="did-not-like" value="1" />
						<label for="did-not-like">Didn't like this one</label>	
					
						<input type="radio" name="scale_<?php echo $item['id'] ?>" id="not-interested" value="0" />	
						<label for="not-interested" class="last">Not Interested</label>	
					</div><!-- end div#ratings -->
					</div>				
				</div><!-- end div#recipe-to-rate -->
