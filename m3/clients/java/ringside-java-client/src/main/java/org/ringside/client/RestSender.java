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
// Portions taken and/or modified from the Facebook Platform PHP5 client:
// +---------------------------------------------------------------------------+
// | Copyright (c) 2007 Facebook, Inc.                                         |
// | All rights reserved.                                                      |
// |                                                                           |
// | Redistribution and use in source and binary forms, with or without        |
// | modification, are permitted provided that the following conditions        |
// | are met:                                                                  |
// |                                                                           |
// | 1. Redistributions of source code must retain the above copyright         |
// |    notice, this list of conditions and the following disclaimer.          |
// | 2. Redistributions in binary form must reproduce the above copyright      |
// |    notice, this list of conditions and the following disclaimer in the    |
// |    documentation and/or other materials provided with the distribution.   |
// |                                                                           |
// | THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR      |
// | IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES |
// | OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.   |
// | IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,          |
// | INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT  |
// | NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, |
// | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY     |
// | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT       |
// | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF  |
// | THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.         |
// +---------------------------------------------------------------------------+

package org.ringside.client;

import java.io.InputStream;
import java.net.URL;
import java.net.URLConnection;
import java.util.Properties;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;

/**
 * A base class that can be the starting point for a sender that is
 * used to send API requests to a Ringside server.  This class is
 * responsible for sending the actual request to the server
 * and receiving the response from the server.
 * 
 * Each sender object will have a context that it can use to squirrel away things
 * that it needs to share with other senders or with itself across multiple requests.
 *
 * This client can be configured by contructing it with an optional set of configuration
 * properties passed inside the context. Valid optional configuration settings are:
 * 
 * <ul>
 * <li>
 *    http_proxy=host:port -- if defined, the client will go through this proxy
 * </li>
 * <li>
 *    non_proxy_hosts=host|host -- if defined, these hosts (|-separated) will be considered
 *                                 local and the request will not be proxied
 * </li>
 * </ul>
 *
 * @param <T> the type of objects that will be returned from the responses
 *
 * @author John Mazzitelli
 */
public class RestSender<T>
{
    private static final Log LOG = LogFactory.getLog( RestSender.class );

    public static final String CONFIG_HTTP_PROXY      = "http_proxy";
    public static final String CONFIG_NON_PROXY_HOSTS = "non_proxy_hosts";

    private Context            context;
    private RequestHandler     requestHandler;
    private ResponseHandler<T> responseHandler;

    /**
     * Builds the client and {@link #initialize(Context, RequestHandler, ResponseHandler) initializes} it.
     * 
     * @param context contains data that can be used by this sender for configuration
     * @param requestHandler the object that is responsible for preparing the requests
     * @param responseHandler the object that is responsible for processing the responses
     */
    public RestSender( Context            context,
                       RequestHandler     requestHandler,
                       ResponseHandler<T> responseHandler )
    {
        initialize( context, requestHandler, responseHandler );
    }

    /**
     * Initializes this sender with the given parameters.
     * 
     * @param context contains data that can be used by this sender for configuration
     * @param requestHandler the object that is responsible for preparing the requests
     * @param responseHandler the object that is responsible for processing the responses
     *
     * @throws IllegalArgumentException if the server address does not use either http nor https protocol
     *                                  or one of the non-nullable parameters is <code>null</code>
     */
    protected void initialize( Context            context,
                               RequestHandler     requestHandler,
                               ResponseHandler<T> responseHandler )
    {
        if ( context == null )
        {
            throw new IllegalArgumentException( "You must specify a context" );
        }

        this.context = context;

        URL serverAddr = context.getServerAddress();
        if ( ( serverAddr == null )
             || !( serverAddr.getProtocol().equals( "http" ) || ( serverAddr.getProtocol().equals( "https" ) ) ) )
        {
            throw new IllegalArgumentException( "Invalid URL, must be http or https: " + serverAddr );
        }

        String secret = context.getSecretKey();
        if ( ( secret == null ) || ( secret.length() == 0 ) )
        {
            throw new IllegalArgumentException( "You must specify a secret key" );
        }

        setRequestHandler( requestHandler );
        setResponseHandler( responseHandler );

        Properties config = context.getConfiguration();

        LOG.debug( "Creating sender to [" + serverAddr + "] with config [" + config + "]" );

        // can only override HTTP proxy per VM using system properties. If anyone knows
        // a better way to do this, so each instance has its own proxy settings, let me know.
        if ( config.containsKey( CONFIG_HTTP_PROXY ) )
        {
            // valid format is "host:port" or just "host"
            String[] hostPort = config.getProperty( CONFIG_HTTP_PROXY ).split( ":", 2 );

            // for the http.* settings, see http://java.sun.com/j2se/1.4/docs/guide/net/properties.html
            System.setProperty( "http.proxyHost", hostPort[0] );
            if ( hostPort.length == 2 )
            {
                System.setProperty( "http.proxyPort", hostPort[1] );
            }

            if ( config.containsKey( CONFIG_HTTP_PROXY ) )
            {
                System.setProperty( "http.nonProxyHosts", config.getProperty( CONFIG_NON_PROXY_HOSTS ) );
            }
        }

        return;
    }

    /**
     * Returns the context associated with this sender. This provides information like
     * the server endpoint that this client will talk to, the secret key to authenticate
     * this sender, among other things.
     * 
     * See {@link Context} for more information.
     * 
     * @return context
     */
    public Context getContext()
    {
        return this.context;
    }

    /**
     * Returns the handler that is used to prepare the requests that are sent by this sender.
     * 
     * @return the request handler object that will be used to process requests
     */
    public RequestHandler getRequestHandler()
    {
        return this.requestHandler;
    }

    /**
     * Switch the handler that is used to prepare the requests that this sender object sends.
     * 
     * @param requestHandler the new request handler (must not be <code>null</code>)
     * 
     * @throws IllegalArgumentException if <code>requestHandler</code> is <code>null</code>
    */
    public void setRequestHandler( RequestHandler requestHandler )
    {
        if ( requestHandler == null )
        {
            throw new IllegalArgumentException( "You must specify a non-null request handler" );
        }

        LOG.debug( "Requests will be handled with: " + requestHandler.getClass().getName() );

        this.requestHandler = requestHandler;
    }

    /**
     * Returns the handler that is used to process the responses that this sender object receives.
     * 
     * @return the response handler object that will be used to process responses
     */
    public ResponseHandler<T> getResponseHandler()
    {
        return this.responseHandler;
    }

    /**
     * Switch the handler that is used to process the responses that this sender object receives.
     * 
     * @param responseHandler the new response handler (must not be <code>null</code>)
     * 
     * @throws IllegalArgumentException if <code>responseHandler</code> is <code>null</code>
     */
    public void setResponseHandler( ResponseHandler<T> responseHandler )
    {
        if ( responseHandler == null )
        {
            throw new IllegalArgumentException( "You must specify a non-null response handler" );
        }

        LOG.debug( "Responses will be handled with: " + responseHandler.getClass().getName() );

        this.responseHandler = responseHandler;
    }

    /**
     * Makes a API call, invoking the given method with the given parameters.
     *
     * @param method the API to call
     * @param params the parameters to pass to the API
     * 
     * @return the response, as produced by the response handler
     * 
     * @throws Exception on error
     */
    public T callRestMethod( ApiMethod  method,
                             Parameters params )
    throws Exception
    {
        // we are now currently processing a request, bump up our counter/timestamp
        this.getContext().updateForNewRequest();

        if ( params == null )
        {
            params = new Parameters();
        }

        LOG.debug( method + ": Sending method request with params: " + params );
        T response = this.sendRequest( method, params );

        if (LOG.isDebugEnabled())
        {
            LOG.debug( method + ": Received response: " + this.getResponseHandler().stringify( response ) );
        }

        return response;
    }

    /**
     * Invoke the API on the server by submitting the request to the server.
     * If the invocation failed for some reason, an exception will be thrown.
     *
     * @param method the API to invoke
     * @param params the parameters the caller wants to pass to the API
     *
     * @return the results of the successful API invocation
     * 
     * @throws Exception if the request failed
     */
    protected T sendRequest( ApiMethod  method,
                             Parameters params )
    throws Exception
    {
        Parameters postParams = this.getRequestHandler().prepareParameters( method, params, this.getContext() );
        LOG.debug( method + ": The prepared parameters are [" + postParams + "]" );

        postParams = this.getRequestHandler().generateSignature( method, postParams, this.getContext() );
        LOG.debug( method + ": The signed parameters are [" + postParams + "]" );

        postParams = this.getRequestHandler().encodeParameters( method, postParams, this.getContext() );
        LOG.debug( method + ": The encoded parameters are [" + postParams + "]" );

        URLConnection conn = this.getRequestHandler().connectToServer( method, postParams, this.getContext() );
        LOG.debug( method + ": Connected to server; now waiting to process response" );

        // now process the response from the server
        InputStream inputStream = conn.getInputStream();
        T           response    = this.getResponseHandler().processResponse( method,
                                                                             postParams,
                                                                             this.getContext(),
                                                                             inputStream );

        try
        {
            inputStream.close();
        }
        catch ( Exception e )
        {
            // ignore - perhaps its already closed? nothing we can do about it anyway
        }

        LOG.debug( method + ": Finished request and processed response" );

        return response;
    }

    /**
     * Very simple command line processing tool that can be used to issue a single API call.
     * This method is used mainly for testing.
     * 
     * The following system properties are used:
     *    -Durl="the URL of the server"
     *    -Dsecret="the secret key"
     *    -Dhttp_proxy="host:port"
     *    -Dnon_proxy_hosts="host|host..."
     * 
     * @param args response handler class name, API method name, name=value parameters
     * 
     * @throws Exception on error
     */
    @SuppressWarnings( "unchecked" )
    public static void main( String[] args )
    throws Exception
    {
        if ( args.length == 0 )
        {
            System.out.println( "Usage: " + RestSender.class.getName()
                                + " <RequestHandlerClass> <ResponseHandlerClass> <API name> [param1=value1] ..." );
            System.out.println( "System properties:" );
            System.out.println( "-Durl = the server's URL endpoing" );
            System.out.println( "-Dsecret = the secret key" );
            System.out.println( "-D" + CONFIG_HTTP_PROXY + " = host:port of the proxy" );
            System.out.println( "-D" + CONFIG_NON_PROXY_HOSTS + " = hosts that do not need proxies, |-separated" );
            return;
        }

        String url             = System.getProperty( "url", "http://localhost:82/api/restserver.php" );
        String secret          = System.getProperty( "secret", "r1ngs1d3" );
        String http_proxy      = System.getProperty( CONFIG_HTTP_PROXY );
        String non_proxy_hosts = System.getProperty( CONFIG_NON_PROXY_HOSTS );

        Properties config = new Properties();
        if ( http_proxy != null )
        {
            config.setProperty( CONFIG_HTTP_PROXY, non_proxy_hosts );
        }

        if ( non_proxy_hosts != null )
        {
            config.setProperty( CONFIG_NON_PROXY_HOSTS, non_proxy_hosts );
        }

        Class           requestHandlerClass  = Class.forName( args[0] );
        RequestHandler  requestHandler       = (RequestHandler) requestHandlerClass.newInstance();
        Class           responseHandlerClass = Class.forName( args[1] );
        ResponseHandler responseHandler      = (ResponseHandler) responseHandlerClass.newInstance();
        ApiMethod       method               = new ApiMethod( args[2] );
        Parameters      params               = new Parameters();
        for ( int i = 3; i < args.length; i++ )
        {
            String[] nameValue = args[i].split( "=", 2 );
            params.put( nameValue[0], ( nameValue.length == 2 ) ? nameValue[1] : "" );
        }

        Context    context = new Context( new URL( url ), secret, config );
        RestSender client  = new RestSender( context, requestHandler, responseHandler );

        Object response = client.callRestMethod( method, params );

        System.out.println( "==========RESPONSE [" + response.getClass().getName() + "]==========" );

        // stringify the response so we can see it in the console output
        System.out.println( responseHandler.stringify( response ) );

        return;
    }
}