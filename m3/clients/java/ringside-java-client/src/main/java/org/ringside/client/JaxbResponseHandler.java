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

import java.io.ByteArrayOutputStream;
import java.io.InputStream;

import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBException;
import javax.xml.bind.Marshaller;
import javax.xml.bind.Unmarshaller;

/**
 * Processes the responses in the form of Java POJOs produced by JAXB.
 *
 * @author John Mazzitelli
 */
public abstract class JaxbResponseHandler
    implements ResponseHandler<Object>
{
    // we cache the context so we don't waste time rebuilding it once we have it
    private JAXBContext jaxbContext;

    public Object processResponse( ApiMethod   method,
                                   Parameters  params,
                                   Context     context,
                                   InputStream inputStream )
    throws Exception
    {
        JAXBContext  jaxb         = getJaxbContext();
        Unmarshaller unmarshaller = jaxb.createUnmarshaller();
        Object       pojo         = unmarshaller.unmarshal( inputStream );
        return pojo;
    }

    public String stringify( Object obj )
    {
        try
        {
            JAXBContext           jaxb       = getJaxbContext();
            Marshaller            marshaller = jaxb.createMarshaller();
            ByteArrayOutputStream baos       = new ByteArrayOutputStream();
            marshaller.setProperty( Marshaller.JAXB_FORMATTED_OUTPUT, Boolean.TRUE );
            marshaller.marshal( obj, baos );
            return baos.toString();
        }
        catch ( JAXBException e )
        {
            return e.toString();
        }
    }

    public JAXBContext getJaxbContext()
    {
        if ( this.jaxbContext == null )
        {
            try
            {
                this.jaxbContext = createJaxbContext();
            }
            catch ( Exception e )
            {
                throw new RuntimeException( "Failed to create JAXB context", e );
            }
        }

        return this.jaxbContext;
    }

    /**
     * Subclasses must implement this method so it can create the proper context
     * for the schema that is supported.
     * 
     * @return the newly created JAXB context
     * 
     * @throws Exception if cannot create the context
     */
    protected abstract JAXBContext createJaxbContext()
    throws Exception;
}