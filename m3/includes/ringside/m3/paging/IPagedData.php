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

/**
 * An interface whose implementations represent data that can be paged.
 * Paged data includes zero, one or more pages of data, with a page of data
 * containing zero, one or more rows of data.
 *
 * @author John Mazzitelli
 */
interface M3_Paging_IPagedData
{
    /**
     * Returns the total amount of data the object has.
     * This is the count of all "rows" on all "pages".
     * 
     * @return int the sum of all rows on all pages 
     */
    function getTotalSize();

    /**
     * Asks for a page of data.  The given page control will indicate what
     * data to return.
     * 
     * @param $pc the page control that tells the function what data to retrieve
     *
     * @return M3_Paging_PageList the list of data that was retrieved
     */
    function getPageList(M3_Paging_PageControl $pc);
}
?>