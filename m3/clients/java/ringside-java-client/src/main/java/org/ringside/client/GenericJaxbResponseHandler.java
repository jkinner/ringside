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

package org.ringside.client;

import java.util.HashMap;
import java.util.Map;

import javax.xml.bind.JAXBContext;

/**
 * Processes the responses in the form of Java POJOs produced by JAXB.
 * This is a generic class in which you can pass in the JAXB context path
 * in the constructor, with an optional classloader so you can
 * explicitly tell JAXB where to find the generated schema objects.
 * 
 * @author John Mazzitelli
 */
public class GenericJaxbResponseHandler
    extends JaxbResponseHandler
{
    private String      jaxbContextPath;
    private ClassLoader jaxbClassLoader;

    // to avoid creating new contexts over multiple instances of jaxb handlers, we will
    // cache the contexts here.  The key is a hash code that combines the context path
    // string hash and the hash of its class loader( I'm paranoid and didn't want to
    // explictly cache ClassLoader references in the key of the map)
    private static final Map<Integer, JAXBContext> JAXB_CONTEXT_CACHE = new HashMap<Integer, JAXBContext>();

    /**
     * Constructor that lets you define the context path.  To find
     * the generated JAXB objects, this will use the same
     * classloader in which this class is found.
     *  
     * @param jaxbContextPath
     */
    public GenericJaxbResponseHandler( String jaxbContextPath )
    {
        this( jaxbContextPath, GenericJaxbResponseHandler.class.getClassLoader() );
    }

    /**
     * Constructor that lets you define the context path and the class loader
     * where the generated JAXB objects can be found.
     *
     * @param jaxbContextPath
     * @param jaxbClassLoader
     */
    public GenericJaxbResponseHandler( String      jaxbContextPath,
                                       ClassLoader jaxbClassLoader )
    {
        this.jaxbContextPath = jaxbContextPath;
        this.jaxbClassLoader = jaxbClassLoader;
    }

    @Override
    protected JAXBContext createJaxbContext()
    throws Exception
    {
    	// first see if another instance has already created the context and stored it in cache
    	// if it isn't in cache yet, we'll create the context and cache it ourselves
        JAXBContext context = getCachedJaxbContext();

        if ( context == null )
        {
            context = JAXBContext.newInstance( this.jaxbContextPath, this.jaxbClassLoader );
            cacheJaxbContext( context );
        }

        return context;
    }

    private Integer generateCacheKey()
    {
        int hash = 7;
        hash = ( 31 * hash ) + this.jaxbContextPath.hashCode();
        hash = ( 31 * hash ) + this.jaxbClassLoader.hashCode();
        return Integer.valueOf( hash );
    }

    private JAXBContext getCachedJaxbContext()
    {
        synchronized ( JAXB_CONTEXT_CACHE )
        {
            return JAXB_CONTEXT_CACHE.get( generateCacheKey() );
        }
    }

    private void cacheJaxbContext( JAXBContext context )
    {
        synchronized ( JAXB_CONTEXT_CACHE )
        {
            JAXB_CONTEXT_CACHE.put( generateCacheKey(), context );
        }
    }
}