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

import java.io.InputStream;

/**
 * Interface that is responsible for handling a specific format of API responses.
 * 
 * @param <T> the type of responses that are handled
 *
 * @author John Mazzitelli
 */
public interface ResponseHandler<T>
{
    /**
     * Processes the response that is being received from the server.  The request
     * was for the given method with the given parameters being passed to the server.
     * The input stream is the data being streamed directly from the server.
     * 
     * Implementors must have this function convert the incoming response
     * data to the appropriate form. The stream will be closed by the caller,
     * implementations of this interface do not have to worry about it.
     * 
     * @param method the method that is being invoked
     * @param params the parameters that were sent to the server
     * @param context the context that is associated with the sender sending the request
     * @param inputStreamResponse the server's response data that needs to be processed
     * 
     * @return the results of the invocation
     * 
     * @throws Exception if failed to properly process the response
     */
    T processResponse( ApiMethod   method,
                       Parameters  params,
                       Context     context,
                       InputStream inputStreamResponse )
    throws Exception;

    /**
     * Converts the given response object to a string.
     * 
     * @param obj the response object to stringify
     *
     * @return the string version of the given object
     */
    String stringify( T obj );
}