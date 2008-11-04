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

require_once 'ringside/m3/paging/PageControl.php';

/**
 * A list of data that is a subset of a fuller set of data. This contains a page of data
 * as defined by its page control object.
 *
 * @author John Mazzitelli
 * 
 * @see M3_Paging_PageControl
 */
class M3_Paging_PageList
{
    private $data; // array of the actual data that this list contains (i.e. a page of data)
    private $isUnbounded; // Is the total size of the list known? if true, $totalSize is useless and forced to 0
    private $totalSize; // the total amount of data (i.e. sum of all rows of all pages)
    private $pageControl; // M3_Paging_PageControl

    /**
     * Creates a page list and preloads it with the given data and defines the bounds of the full
     * dataset by providing the sum of all rows in all pages.
     *
     * If you do not specify data, the list will be empty.
     *
     * If you do not specify a total size, the page list will consider its data unbounded,
     * which means this object will not know the total size of all rows in all pages.
     * 
     * @param $pageControl defines the view of the data that is stored in the list
     * @param $data the actual data stored in this list (if not specified, no data will be preloaded)
     * @param $totalSize total size of the full dataset (if not specified, this list will be unbounded)
     */
    public function __construct(M3_Paging_PageControl $pageControl, array &$data = null, $totalSize = null)
    {
        if (empty($pageControl))
        {
            throw Exception("Must provide a page control object to a page list");
        }

        $this->data = is_null($data) ? array() : $data;
        $this->isUnbounded = is_null($totalSize); // true if we do not know the total dataset size
        $this->totalSize = is_null($totalSize) ? 0 : $totalSize;
        $this->pageControl = $pageControl;
    }

    /**
     * Returns the page control that is assigned to this list.
     * 
     * @return M3_Paging_PageControl
     */
    public function getPageControl()
    {
        return $this->pageControl;
    }

    /**
     * Sets the page control that is assigned to this list.
     * 
     * @param $pageControl
     */
    public function setPageControl(M3_Paging_PageControl $pageControl)
    {
        $this->pageControl = $pageControl;
    }

    /**
     * Returns the array of data stored in this list.
     * 
     * @return array the actual data stored in this list
     */
    public function& getData()
    {
        return $this->data;
    }

    /**
     * Overwrites whatever data was in this list already and uses the given data
     * as its new set of data.
     * 
     * @param $data the new data
     */
    public function setData(array &$data = null)
    {
        $this->data = is_null($data) ? array() : $data;
    }

    /**
     * Returns the total size of the "master list" that this page is a subset of.
     * This is the total number of rows found in all pages. If you want to know
     * how much data this page list actually has stored in memory, use
     * "count(getData())".
     * 
     * If this list was unbounded (i.e. its total size was not known), this returns
     * the size of the data stored in the list ("count(getData()").
     *
     * @return int sum of all rows in all pages
     */
    public function getTotalSize()
    {
        return max(count($this->getData()), $this->totalSize);
    }

    /**
     * Sets the total size of the "master list" that this page is a subset of.
     * This is the total number of rows found in all pages.
     *
     * @param $totalSize the count of all rows of all pages
     */
    public function setTotalSize($totalSize)
    {
        $this->isUnbounded = false;
        $this->totalSize = $totalSize;
    }

    /**
     * Returns true if this list is unbounded meaning it does not know
     * the total size of all rows in all pages, false if the total
     * data set size is known.
     *
     * The total size will be reported as the amount of data that
     * is stored directly in this list if this list is unbounded.
     * 
     * @return bool if the list's size is unbounded 
     */
    public function isUnbounded()
    {
        return $this->isUnbounded;
    }

    /**
     * Tells this list if it is unbounded or not.  If it is unbounded,
     * it will not know the full size of the data, that is, it will not
     * know the sum of all rows in all pages.
     *
     * The total size will be reported as the amount of data that
     * is stored directly in this list if this list is unbounded.
     * 
     * @param $isUnbounded the boolean flag
     */
    public function setUnbounded(bool $isUnbounded)
    {
        $this->isUnbounded = $isUnbounded;
    }
    
    /**
     * Walks the data and returns a string containing all the data.
     * 
     * @return string version of the data
     */
    public function __toString()
    {
        $_str = "PageList: "
              . "totalSize=[" . $this->getTotalSize()
              . "], " . $this->getPageControl()->__toString();

        $_data = $this->getData();
        foreach ($_data as $_d)
        {
            $_str .= "\n" . rtrim($_d);
        }

        return $_str;
    }
}
?>