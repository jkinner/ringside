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
 * Object that defines the section of data that the owner wants to access.
 * A page control defines things like what page is to be viewed and what is the
 * size of the pages.
 *
 * @author John Mazzitelli
 *
 * @see M3_Paging_PageList
 */
class M3_Paging_PageControl
{
    const SIZE_UNLIMITED = -1;
    const SIZE_DEFAULT = 100;

    private $pageNumber;
    private $pageSize;

    /**
     * Factory method that creates a page control object that obtains
     * all data (i.e. with an unlimited page size).
     * 
     * @return M3_Paging_PageControl page control that will ask for all data
     */
    public static function getUnlimitedInstance()
    {
        return new M3_Paging_PageControl(0, M3_Paging_PageControl::SIZE_UNLIMITED);
    }

    /**
     * Factory method that creates a page control object that obtains
     * only a single row of data (i.e. with a page size of 1).
     * 
     * @return M3_Paging_PageControl page control that will ask for only 1 row
     */
    public static function getSingleRowInstance()
    {
        return new M3_Paging_PageControl(0, 1);
    }

    /**
     * Constructs a new page control that asks for the data from the
     * given page with pages of the given size.
     * 
     * @param $pageNumber the page to retrieve (page numbers start at #0)
     * @param $pageSize the size of the pages (if 0 or less, a default size will be assigned)
     */
    public function __construct($pageNumber = 0, $pageSize = M3_Paging_PageControl::SIZE_DEFAULT)
    {
        if ($pageSize < 1)
        {
            $pageSize = M3_Paging_PageControl::SIZE_DEFAULT;
        }

        $this->pageNumber = $pageNumber;
        $this->pageSize = $pageSize;
    }

    /**
     * Returns the page number that this page control specifies.
     * Page numbers start with #0.
     * 
     * @return int current page number
     */
    public function getPageNumber()
    {
        return $this->pageNumber;
    }

    /**
     * Sets the page number that this page control will want.
     * Page numbers start with #0.
     * 
     * @param $pageNumber the page number
     */
    public function setPageNumber($pageNumber)
    {
        $this->pageNumber = $pageNumber;
    }

    /**
     * Returns the size of the pages.
     * 
     * @return int size of all pages
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * Sets the size of the pages.
     * 
     * @param $pageSize the size of the data pages
     */
    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
    }

    /**
     * Get the index of the first item on the page as dictated by the page size and page number.
     *
     * @return int the index of the starting row for the page (indices start at #0)
     */
    public function getStartRow()
    {
        return $this->pageNumber * $this->pageSize;
    }

    /**
     * Get the index of the last item on the page as dictated by the page size and page number.
     *
     * @return int the index of the last row for the page (indices start at #0)
     */
    public function getEndRow()
    {
        return $this->getStartRow() + $this->pageSize - 1;
    }

    /**
     * Increments the page number so this page control will ask to retrieve the
     * next page.
     */
    public function gotoNextPage()
    {
        $this->pageNumber++;
    }
    
    public function __toString()
    {
        return "PageControl: "
               . "pageNumber=[" . $this->pageNumber
               . "],pageSize=[" . $this->pageSize
               . "]";
    }
}
?>