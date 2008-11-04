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

require_once ('PHPUnit/Framework.php');
require_once ('ringside/m3/util/File.php');

class FileTestCase extends PHPUnit_Framework_TestCase
{
    public function testFindFileInIncludePath()
    {
        $_path = M3_Util_File::findFileInIncludePath("ringside/m3/util/File.php");
        $this->assertTrue(is_file($_path));
        $_path = M3_Util_File::findFileInIncludePath("does/not/exist");
        $this->assertFalse($_path);
    }

    public function testMkdirRmdirRecursive()
    {
        $_dir = M3_Util_File::getTempDir() . "/test1";

        M3_Util_file::mkdirRecursive($_dir . "/test2/test3");
        $this->assertTrue(is_dir($_dir . "/test2/test3"));

        // create some test files
        $this->assertTrue(touch($_dir . "/test1fileA.txt"));
        $this->assertTrue(touch($_dir . "/test1fileB.txt"));
        $this->assertTrue(touch($_dir . "/test2/test2fileA.txt"));
        $this->assertTrue(touch($_dir . "/test2/test2fileB.txt"));
        $this->assertTrue(touch($_dir . "/test2/test3/test3fileA.txt"));
        $this->assertTrue(touch($_dir . "/test2/test3/test3fileB.txt"));

        $this->assertTrue(M3_Util_file::rmdirRecursive($_dir));
        $this->assertFalse(is_dir($_dir));
    }

    public function testNumberOfLinesAndTruncate()
    {
        $filename = M3_Util_File::getTempDir() . "/FileTestCase1.txt";

        @unlink($filename); // purge any old one that might be hanging out
        $this->assertFileNotExists($filename);
        
        // see that we can determine that a non-existing file has 0 lines in it
        $this->assertEquals(0, M3_Util_File::getNumberOfLines($filename));
        
        // create some lines (side testing lockAndAppendFile) and make sure we can count them
        M3_Util_File::lockAndAppendFile($filename, "first line\n");
        $this->assertEquals(1, M3_Util_File::getNumberOfLines($filename));
        M3_Util_File::lockAndAppendFile($filename, "another line to test\n");
        $this->assertEquals(2, M3_Util_File::getNumberOfLines($filename));
        M3_Util_File::lockAndAppendFile($filename, "\n"); // an empty line
        $this->assertEquals(3, M3_Util_File::getNumberOfLines($filename));
        M3_Util_File::lockAndAppendFile($filename, " \n"); // a blank line, one space
        $this->assertEquals(4, M3_Util_File::getNumberOfLines($filename));
        M3_Util_File::lockAndAppendFile($filename, "line that ends with CRLF\r\n");
        $this->assertEquals(5, M3_Util_File::getNumberOfLines($filename));

        // test our truncate file function, and see that it really empties the file
        M3_Util_File::truncateFile($filename);
        $this->assertEquals(0, M3_Util_File::getNumberOfLines($filename));
        
        unlink($filename);
    }

    public function testFilePage()
    {
        $filename = M3_Util_File::getTempDir() . "/FileTestCase2.txt";

        @unlink($filename); // purge any old one that might be hanging out
        $this->assertFileNotExists($filename);
        
        // see that we can get an empty page
        $pageList = M3_Util_File::getFilePage($filename, new M3_Paging_PageControl());
        $this->assertEquals(0, $pageList->getTotalSize());
        $this->assertEquals(0, count($pageList->getData()));
        
        // file with 1 line
        $line1 = "first line\n";
        M3_Util_File::lockAndAppendFile($filename, $line1);
        $this->assertEquals(1, M3_Util_File::getNumberOfLines($filename));
        $pageList = M3_Util_File::getFilePage($filename, new M3_Paging_PageControl(0, 1)); // page size is just enough
        $this->assertEquals(1, $pageList->getTotalSize());
        $data = $pageList->getData();
        $this->assertEquals(1, count($data));
        $this->assertEquals($line1, array_shift($data));
        $this->assertEquals(0, $pageList->getPageControl()->getPageNumber());
        $this->assertEquals(1, $pageList->getPageControl()->getPageSize());
        $this->assertEquals(0, $pageList->getPageControl()->getStartRow());
        $this->assertEquals(0, $pageList->getPageControl()->getEndRow());
        
        // file with 2 lines
        $line2 = "second line\n";
        M3_Util_File::lockAndAppendFile($filename, $line2);
        $this->assertEquals(2, M3_Util_File::getNumberOfLines($filename));
        $pageList = M3_Util_File::getFilePage($filename, new M3_Paging_PageControl(0, 5)); // page size is larger than needed
        $this->assertEquals(2, $pageList->getTotalSize(), "total size should have given the full number of rows in file");
        $data = $pageList->getData();
        $this->assertEquals(2, count($data));
        $this->assertEquals($line1, $data[0]);
        $this->assertEquals($line2, $data[1]);
        $this->assertEquals(0, $pageList->getPageControl()->getPageNumber());
        $this->assertEquals(5, $pageList->getPageControl()->getPageSize()); // page size is 5 still, even though we only have 2 rows
        $this->assertEquals(0, $pageList->getPageControl()->getStartRow());
        $this->assertEquals(4, $pageList->getPageControl()->getEndRow());
        
        // let's page our 2-rows into two 1-row pages
        $pageList = M3_Util_File::getFilePage($filename, new M3_Paging_PageControl(0, 1)); // first page
        $this->assertEquals(2, $pageList->getTotalSize(), "total number of rows should be 2, even though we only have 1 page worth");
        $data = $pageList->getData();
        $this->assertEquals(1, count($data)); // our page control should have limited this to 1
        $this->assertEquals($line1, $data[0]);
        $this->assertEquals(0, $pageList->getPageControl()->getPageNumber());
        $this->assertEquals(1, $pageList->getPageControl()->getPageSize());
        $this->assertEquals(0, $pageList->getPageControl()->getStartRow());
        $this->assertEquals(0, $pageList->getPageControl()->getEndRow());
        $pageList = M3_Util_File::getFilePage($filename, new M3_Paging_PageControl(1, 1)); // second page
        $this->assertEquals(2, $pageList->getTotalSize(), "Total number of rows should be 2, even though we only have 1 page worth.");
        $data = $pageList->getData();
        $this->assertEquals(1, count($data)); // our page control should have limited this to 1
        $this->assertEquals($line2, $data[0]); // first row in the page is the second overall row
        $this->assertEquals(1, $pageList->getPageControl()->getPageNumber());
        $this->assertEquals(1, $pageList->getPageControl()->getPageSize());
        $this->assertEquals(1, $pageList->getPageControl()->getStartRow());
        $this->assertEquals(1, $pageList->getPageControl()->getEndRow());
        $pageList = M3_Util_File::getFilePage($filename, new M3_Paging_PageControl(2, 1)); // third page (non-existent)
        $this->assertEquals(2, $pageList->getTotalSize(), "empty page, but we still should say how many total rows there are");
        $data = $pageList->getData();
        $this->assertEquals(0, count($data));
        $this->assertEquals(2, $pageList->getPageControl()->getPageNumber());
        $this->assertEquals(1, $pageList->getPageControl()->getPageSize());
        $this->assertEquals(2, $pageList->getPageControl()->getStartRow());
        $this->assertEquals(2, $pageList->getPageControl()->getEndRow());
        
        // let's page our 2-rows into two 1-row pages again, but explicitly indicate that we know total size is 2
        $pageList = M3_Util_File::getFilePage($filename, new M3_Paging_PageControl(0, 1), 2); // first page
        $this->assertEquals(2, $pageList->getTotalSize(), "total number of rows should be 2, even though we only have 1 page worth..");
        $data = $pageList->getData();
        $this->assertEquals(1, count($data)); // our page control should have limited this to 1
        $this->assertEquals($line1, $data[0]);
        $this->assertEquals(0, $pageList->getPageControl()->getPageNumber());
        $this->assertEquals(1, $pageList->getPageControl()->getPageSize());
        $this->assertEquals(0, $pageList->getPageControl()->getStartRow());
        $this->assertEquals(0, $pageList->getPageControl()->getEndRow());
        $pageList = M3_Util_File::getFilePage($filename, new M3_Paging_PageControl(1, 1), 2); // second page
        $this->assertEquals(2, $pageList->getTotalSize(), "Total number of rows should be 2, even though we only have 1 page worth..");
        $data = $pageList->getData();
        $this->assertEquals(1, count($data)); // our page control should have limited this to 1
        $this->assertEquals($line2, $data[0]); // first row in the page is the second overall row
        $this->assertEquals(1, $pageList->getPageControl()->getPageNumber());
        $this->assertEquals(1, $pageList->getPageControl()->getPageSize());
        $this->assertEquals(1, $pageList->getPageControl()->getStartRow());
        $this->assertEquals(1, $pageList->getPageControl()->getEndRow());
        $pageList = M3_Util_File::getFilePage($filename, new M3_Paging_PageControl(2, 1), 2); // third page (non-existent)
        $this->assertEquals(2, $pageList->getTotalSize(), "empty page, but we still should say how many total rows there are");
        $data = $pageList->getData();
        $this->assertEquals(0, count($data));
        $this->assertEquals(2, $pageList->getPageControl()->getPageNumber());
        $this->assertEquals(1, $pageList->getPageControl()->getPageSize());
        $this->assertEquals(2, $pageList->getPageControl()->getStartRow());
        $this->assertEquals(2, $pageList->getPageControl()->getEndRow());

        // file with 6 lines
        $line3 = "third line\n";
        $line4 = "fourth line\n";
        $line5 = "fifth line\n";
        $line6 = "sixth line\n";
        M3_Util_File::lockAndAppendFile($filename, $line3);
        M3_Util_File::lockAndAppendFile($filename, $line4);
        M3_Util_File::lockAndAppendFile($filename, $line5);
        M3_Util_File::lockAndAppendFile($filename, $line6);
        $this->assertEquals(6, M3_Util_File::getNumberOfLines($filename));
        $pageList = M3_Util_File::getFilePage($filename, new M3_Paging_PageControl(0, 10)); // page size is larger than needed
        $this->assertEquals(6, $pageList->getTotalSize(), "total size should have given the full number of rows in file");
        $data = $pageList->getData();
        $this->assertEquals(6, count($data));
        $this->assertEquals($line1, $data[0]);
        $this->assertEquals($line2, $data[1]);
        $this->assertEquals($line3, $data[2]);
        $this->assertEquals($line4, $data[3]);
        $this->assertEquals($line5, $data[4]);
        $this->assertEquals($line6, $data[5]);
        $this->assertEquals(0, $pageList->getPageControl()->getPageNumber());
        $this->assertEquals(10, $pageList->getPageControl()->getPageSize()); // page size is 10 still, even though we only have 6 rows
        $this->assertEquals(0, $pageList->getPageControl()->getStartRow());
        $this->assertEquals(9, $pageList->getPageControl()->getEndRow());

        // let's page our 6-rows into two 3-row pages
        $pageList = M3_Util_File::getFilePage($filename, new M3_Paging_PageControl(0, 3)); // first page
        $this->assertEquals(6, $pageList->getTotalSize(), "total number of rows should be 6, even though we only have 1 page worth");
        $data = $pageList->getData();
        $this->assertEquals(3, count($data), "our page control should have limited this to 3");
        $this->assertEquals($line1, $data[0]);
        $this->assertEquals($line2, $data[1]);
        $this->assertEquals($line3, $data[2]);
        $this->assertEquals(0, $pageList->getPageControl()->getPageNumber());
        $this->assertEquals(3, $pageList->getPageControl()->getPageSize());
        $this->assertEquals(0, $pageList->getPageControl()->getStartRow());
        $this->assertEquals(2, $pageList->getPageControl()->getEndRow());
        $pageList = M3_Util_File::getFilePage($filename, new M3_Paging_PageControl(1, 3)); // second page
        $this->assertEquals(6, $pageList->getTotalSize(), "Total number of rows should be 6, even though we only have 1 page worth.");
        $data = $pageList->getData();
        $this->assertEquals(3, count($data), "Our page control should have limited this to 3.");
        $this->assertEquals($line4, $data[0], "first row in the page should be the fourth overall row");
        $this->assertEquals($line5, $data[1], "second row in the page should be the fifth overall row");
        $this->assertEquals($line6, $data[2], "third row in the page should be the sixth overall row");
        $this->assertEquals(1, $pageList->getPageControl()->getPageNumber());
        $this->assertEquals(3, $pageList->getPageControl()->getPageSize());
        $this->assertEquals(3, $pageList->getPageControl()->getStartRow());
        $this->assertEquals(5, $pageList->getPageControl()->getEndRow());
        $pageList = M3_Util_File::getFilePage($filename, new M3_Paging_PageControl(2, 3)); // third page (non-existent)
        $this->assertEquals(6, $pageList->getTotalSize(), "empty page, but we still should say how many total rows there are");
        $data = $pageList->getData();
        $this->assertEquals(0, count($data));
        $this->assertEquals(2, $pageList->getPageControl()->getPageNumber());
        $this->assertEquals(3, $pageList->getPageControl()->getPageSize());
        $this->assertEquals(6, $pageList->getPageControl()->getStartRow());
        $this->assertEquals(8, $pageList->getPageControl()->getEndRow());

        // let's page our 6-rows into two 3-row pages again, but explicitly indicate that we know total size is 6
        $pageList = M3_Util_File::getFilePage($filename, new M3_Paging_PageControl(0, 3), 6); // first page
        $this->assertEquals(6, $pageList->getTotalSize(), "total number of rows should be 6, even though we only have 1 page worth");
        $data = $pageList->getData();
        $this->assertEquals(3, count($data), "our page control should have limited this to 3");
        $this->assertEquals($line1, $data[0]);
        $this->assertEquals($line2, $data[1]);
        $this->assertEquals($line3, $data[2]);
        $this->assertEquals(0, $pageList->getPageControl()->getPageNumber());
        $this->assertEquals(3, $pageList->getPageControl()->getPageSize());
        $this->assertEquals(0, $pageList->getPageControl()->getStartRow());
        $this->assertEquals(2, $pageList->getPageControl()->getEndRow());
        $pageList = M3_Util_File::getFilePage($filename, new M3_Paging_PageControl(1, 3), 6); // second page
        $this->assertEquals(6, $pageList->getTotalSize(), "Total number of rows should be 6, even though we only have 1 page worth.");
        $data = $pageList->getData();
        $this->assertEquals(3, count($data), "Our page control should have limited this to 3.");
        $this->assertEquals($line4, $data[0], "first row in the page should be the fourth overall row");
        $this->assertEquals($line5, $data[1], "second row in the page should be the fifth overall row");
        $this->assertEquals($line6, $data[2], "third row in the page should be the sixth overall row");
        $this->assertEquals(1, $pageList->getPageControl()->getPageNumber());
        $this->assertEquals(3, $pageList->getPageControl()->getPageSize());
        $this->assertEquals(3, $pageList->getPageControl()->getStartRow());
        $this->assertEquals(5, $pageList->getPageControl()->getEndRow());
        $pageList = M3_Util_File::getFilePage($filename, new M3_Paging_PageControl(2, 3), 6); // third page (non-existent)
        $this->assertEquals(6, $pageList->getTotalSize(), "empty page, but we still should say how many total rows there are");
        $data = $pageList->getData();
        $this->assertEquals(0, count($data));
        $this->assertEquals(2, $pageList->getPageControl()->getPageNumber());
        $this->assertEquals(3, $pageList->getPageControl()->getPageSize());
        $this->assertEquals(6, $pageList->getPageControl()->getStartRow());
        $this->assertEquals(8, $pageList->getPageControl()->getEndRow());

        // let's page our 6-rows into three 2-row pages
        $pageList = M3_Util_File::getFilePage($filename, new M3_Paging_PageControl(0, 2)); // first page
        $this->assertEquals(6, $pageList->getTotalSize(), "total number of rows should be 6, even though we only have 1 page worth");
        $data = $pageList->getData();
        $this->assertEquals(2, count($data), "our page control should have limited this to 2");
        $this->assertEquals($line1, $data[0]);
        $this->assertEquals($line2, $data[1]);
        $this->assertEquals(0, $pageList->getPageControl()->getPageNumber());
        $this->assertEquals(2, $pageList->getPageControl()->getPageSize());
        $this->assertEquals(0, $pageList->getPageControl()->getStartRow());
        $this->assertEquals(1, $pageList->getPageControl()->getEndRow());
        $pageList = M3_Util_File::getFilePage($filename, new M3_Paging_PageControl(1, 2)); // second page
        $this->assertEquals(6, $pageList->getTotalSize(), "Total number of rows should be 6, even though we only have 1 page worth.");
        $data = $pageList->getData();
        $this->assertEquals(2, count($data), "Our page control should have limited this to 2.");
        $this->assertEquals($line3, $data[0], "first row in the page should be the third overall row");
        $this->assertEquals($line4, $data[1], "second row in the page should be the fourth overall row");
        $this->assertEquals(1, $pageList->getPageControl()->getPageNumber());
        $this->assertEquals(2, $pageList->getPageControl()->getPageSize());
        $this->assertEquals(2, $pageList->getPageControl()->getStartRow());
        $this->assertEquals(3, $pageList->getPageControl()->getEndRow());
        $pageList = M3_Util_File::getFilePage($filename, new M3_Paging_PageControl(2, 2)); // third page
        $this->assertEquals(6, $pageList->getTotalSize(), "Total number of rows should be 6, even though we only have 1 page worth..");
        $data = $pageList->getData();
        $this->assertEquals(2, count($data), "Our page control should have limited this to 2.");
        $this->assertEquals($line5, $data[0], "first row in the page should be the fifth overall row");
        $this->assertEquals($line6, $data[1], "second row in the page should be the sixth overall row");
        $this->assertEquals(2, $pageList->getPageControl()->getPageNumber());
        $this->assertEquals(2, $pageList->getPageControl()->getPageSize());
        $this->assertEquals(4, $pageList->getPageControl()->getStartRow());
        $this->assertEquals(5, $pageList->getPageControl()->getEndRow());
        $pageList = M3_Util_File::getFilePage($filename, new M3_Paging_PageControl(3, 2)); // fourth page (non-existent)
        $this->assertEquals(6, $pageList->getTotalSize(), "empty page, but we still should say how many total rows there are");
        $data = $pageList->getData();
        $this->assertEquals(0, count($data));
        $this->assertEquals(3, $pageList->getPageControl()->getPageNumber());
        $this->assertEquals(2, $pageList->getPageControl()->getPageSize());
        $this->assertEquals(6, $pageList->getPageControl()->getStartRow());
        $this->assertEquals(7, $pageList->getPageControl()->getEndRow());

        // test our truncate file function, and see that it really empties the file
        M3_Util_File::truncateFile($filename);
        $pageList = M3_Util_File::getFilePage($filename, new M3_Paging_PageControl());
        $this->assertEquals(0, $pageList->getTotalSize());
        $this->assertEquals(0, count($pageList->getData()));
                
        unlink($filename);
    }
}

?>