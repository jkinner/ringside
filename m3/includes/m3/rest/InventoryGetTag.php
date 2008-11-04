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
 * M3 API that returns an inventory of a known tags deployed inside the Ringside server's social tier.
 *
 * @author John Mazzitelli
 */
class InventoryGetTag extends M3_AbstractRest
{
    private $tagNamespace;
    private $tagName;

    public function validateRequest()
    {
        $this->tagNamespace = $this->getRequiredApiParam("tagNamespace");
        $this->tagName = $this->getRequiredApiParam("tagName");
    }

    /**
     * Returns an array containing information on a tag.
     *
     * @return array info on a tag
     */
    public function execute()
    {
        $_tagRegistry = Social_Dsl_TagRegistry::getInstance();
        $_metaInfoArray = $_tagRegistry->getTagMetaInfo($this->tagNamespace, $this->tagName);

        $_info = array();
        
        if (!is_null($_metaInfoArray))
        {
            $_tagNamespace = $_metaInfoArray->getTagNamespace();
            $_tagName = $_metaInfoArray->getTagName();
            $_handlerClass = $_metaInfoArray->getHandlerClassName();
            $_sourceFile = $_metaInfoArray->getHandlerSourceFile();
            $_sourceFileLastModified = $_metaInfoArray->getLastModified();
            // I don't think I care about the tidy metadata
            // $_tidyTagType = $_metaInfoArray->getTagType();
            // $_tidyIsEmpty = $_metaInfoArray->getIsEmpty();
            
            $_info['tag_namespace'] = $_tagNamespace;
            $_info['tag_name'] = $_tagName;
            $_info['handler_class'] = $_handlerClass;
            $_info['source_file'] = $_sourceFile;
            $_info['source_file_last_modified'] = $_sourceFileLastModified;
        }
        
        return array('tag' => $_info);
    }
}
?>