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

import java.net.URLConnection;

/**
 * Interface that is responsible for handling API requests that need
 * to be sent to the Ringside server. Implementations of this interface
 * are orthogonal to the response handler implementations - in other words,
 * implementations of this interface do not know what format the
 * responses will be handled in. This interface is solely interested
 * in setting up the request so it can be sent successfully to the
 * Ringside server.
 * 
 * @author John Mazzitelli
 */
public interface RequestHandler
{
    /**
     * Given the API to be invoked and the array of parameters to pass to that API,
     * this method must prepare those parameters to be sent to the server.
     * This method is free to add, remove, modify or encode the given parameters as
     * appropriate.
     * 
     * This method is <b>not</b> to generate any signature or sign
     * the parameters. That will be performed later, via the method
     * {@link #generateSignature(ApiMethod, Parameters, Context)}.
     * 
     * This method should also <b>not</b> encode the parameters in any way.
     * That will be performed later, via the method
     * {@link #encodeParameters(ApiMethod, Parameters, Context)}.
     *
     * @param method the API to be invoked
     * @param params the parameters that the client caller wants to send
     * @param context the context associated with the sender sending the request
     *
     * @return parameters that should be sent over the wire (this might not be
     *                    the same object as <code>params</code>)
     */
    Parameters prepareParameters( ApiMethod  method,
                                  Parameters params,
                                  Context    context );

    /**
     * Generates the signature that will validate this request on the server-side.
     * This should properly sign the request by placing the signature in the parameters
     * in the appropriate place.
     *
     * Subclasses are free to override this function if they know the server
     * will verify the API invocation in a way using a different
     * signature algorithm.
     *
     * @param method the API to be invoked
     * @param params the parameters that will be sent to the API
     * @param context the context that is associated with the sender sending the request
     *
     * @return parameters that should be sent over the wire (this might not be
     *                    the same object as <code>params</code>)
     */
    Parameters generateSignature( ApiMethod  method,
                                  Parameters params,
                                  Context    context );

    /**
     * Encodes the parameters so they can be sent over the wire.
     *
     * @param method the API to be invoked
     * @param params the parameters that will be sent to the API
     * @param context the context that is associated with the sender sending the request
     *
     * @return parameters that should be sent over the wire (this might not be
     *                    the same object as <code>params</code>)
     */
    Parameters encodeParameters( ApiMethod  method,
                                 Parameters params,
                                 Context    context );

    /**
     * Connects to the server at the address defined within the context.
     * The given API method with the given parameters tell you what API is to be invoked.
     * 
     * Handlers can do special connect processing here.
     * Implementations must ensure that the returned connection has an input stream
     * ready to be read.  Exceptions should be thrown if the connection could not be established.
     *
     * @param method the API to invoke
     * @param params the parameters to be sent over the wire
     * @param context the context associated with the sender sending the request
     * 
     * @return the connection to the server, with its response ready to be read
     * 
     * @throws Exception
     */
    URLConnection connectToServer( ApiMethod  method,
                                   Parameters params,
                                   Context    context )
    throws Exception;
}