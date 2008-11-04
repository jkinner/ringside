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

require_once 'ringside/m3/AbstractRest.php';
include_once 'ringside/social/dsl/TagRegistry.php';

/**
 * M3 API that returns an inventory of all known APIs deployed inside the Ringside server.
 *
 * @author John Mazzitelli
 */
class InventoryGetTags extends M3_AbstractRest
{
    /**
     * Returns an array containing a flat list of names of all tags deployed in the social tier.
     *
     * @return array all tags deployed in the server. The returned array is a list of
     *         associative arrays with each associative array list item including information
     *         about each deployed tag
     */
    public function execute()
    {
        $_tagRegistry = Social_Dsl_TagRegistry::getInstance();
        $_metaInfoArray = $_tagRegistry->getAllTagMetaInfo();

        $_results = array();

        foreach ($_metaInfoArray as $_tagMetaInfo)
        {
            $_tagNamespace = $_tagMetaInfo->getTagNamespace();
            $_tagName = $_tagMetaInfo->getTagName();
            $_handlerClass = $_tagMetaInfo->getHandlerClassName();
            $_sourceFile = $_tagMetaInfo->getHandlerSourceFile();
            $_sourceFileLastModified = $_tagMetaInfo->getLastModified();
            // I don't think I care about the tidy metadata
            // $_tidyTagType = $_tagMetaInfo->getTagType();
            // $_tidyIsEmpty = $_tagMetaInfo->getIsEmpty();
            
            $_results[] = array('tag_namespace' => $_tagNamespace,
                                'tag_name' => $_tagName,
                                'handler_class' => $_handlerClass,
                                'source_file' => $_sourceFile,
                                'source_file_last_modified' => $_sourceFileLastModified);
        }
        
        return array('tag' => $_results);
    }
}
?>