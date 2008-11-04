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

import java.io.ByteArrayInputStream;
import java.io.UnsupportedEncodingException;
import java.net.HttpURLConnection;
import java.net.URLConnection;
import java.net.URLEncoder;
import java.security.MessageDigest;
import java.util.Map;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;

/**
 * Performs the basic request handling.  This does not know anything about user IDs,
 * application IDs or other "social" specific things.  It is used to set up the
 * basic request such as signing the request with an appropriate signature.
 * 
 * @author John Mazzitelli
 */
public class BaseRequestHandler
    implements RequestHandler
{
    private static final Log LOG = LogFactory.getLog( BaseRequestHandler.class );

    private static final String DEFAULT_VERSION = "1.0"; // server-side REST API version that we are compatible with
    private static final String USER_AGENT      = "Ringside API Java Client 1.0";

    public Parameters prepareParameters( ApiMethod  method,
                                         Parameters params,
                                         Context    context )
    {
        // make sure the method's version if specified (callers will normally not specify this)
        if ( !params.containsKey( "v" ) )
        {
            params.put( "v", DEFAULT_VERSION );
        }

        // The provided params should not have method, call_id, or sig values.
        // To avoid any ambiguity - overwrite ones that may be in params with our own generated values.
        params.put( "method", method.getMethodName() );
        params.put( "call_id", Long.toString( context.getLastRequestTime() ) );

        return params;
    }

    public Parameters generateSignature( ApiMethod  method,
                                         Parameters params,
                                         Context    context )
    {
        StringBuilder string = new StringBuilder();

        // Note: what if the signature parameter is already included in params? it shouldn't be but...
        for ( Map.Entry<String, CharSequence> entry : params.entrySet() )
        {
            string.append( entry.getKey() );
            string.append( "=" );
            string.append( entry.getValue() );
        }

        string.append( context.getSecretKey() );

        CharSequence signature = md5( string.toString() );
        params.put( "sig", signature );

        return params;
    }

    public Parameters encodeParameters( ApiMethod  method,
                                        Parameters params,
                                        Context    context )
    {
        Parameters overTheWireParams = new Parameters();

        for ( Map.Entry<String, CharSequence> entry : params.entrySet() )
        {
            overTheWireParams.put( entry.getKey(), urlencode( entry.getValue() ) );
        }
        
        return overTheWireParams;
    }

    public URLConnection connectToServer( ApiMethod  method,
                                          Parameters params,
                                          Context    context )
    throws Exception
    {
        // are we ensured that our protocol is always one of either http or https
        // build our HTTP (or HTTPS) connection? this method assumes so
        CharSequence      postString = params.implode( '&' );
        HttpURLConnection conn       = (HttpURLConnection) context.getServerAddress().openConnection();
        conn.setAllowUserInteraction( false );
        conn.setConnectTimeout( 60000 );
        conn.setReadTimeout( 60000 );
        conn.setUseCaches( false );
        conn.setDoInput( true );
        conn.setDoOutput( true );
        conn.setRequestProperty( "Content-type", "application/x-www-form-urlencoded" );
        conn.setRequestProperty( "User-Agent", USER_AGENT );
        conn.setRequestProperty( "Content-length", Integer.toString( postString.length() ) );
        conn.setInstanceFollowRedirects( true );
        conn.setRequestMethod( "POST" );

        // connect to the server and write the parameters to the output
        conn.connect();
        conn.getOutputStream().write( postString.toString().getBytes() );
        return conn;
    }

    protected CharSequence urlencode( CharSequence seq )
    {
        try
        {
            return URLEncoder.encode( seq.toString(), "UTF-8" );
        }
        catch ( UnsupportedEncodingException e )
        {
            LOG.error( "Cannot encode string, falling back to the string itself: " + seq, e );
            return seq;
        }
    }

    protected CharSequence md5( CharSequence str )
    {
        try
        {
            MessageDigest        md    = MessageDigest.getInstance( "MD5" );
            ByteArrayInputStream bais  = new ByteArrayInputStream( str.toString().getBytes() );
            byte[]               bytes = new byte[1024];
            int                  len;

            while ( ( len = bais.read( bytes, 0, bytes.length ) ) != -1 )
            {
                md.update( bytes, 0, len );
            }

            bytes = md.digest();

            StringBuffer sb = new StringBuffer( bytes.length * 2 );

            for ( int i = 0; i < bytes.length; i++ )
            {
                int hi = ( bytes[i] >> 4 ) & 0xf;
                int lo = bytes[i] & 0xf;
                sb.append( Character.forDigit( hi, 16 ) );
                sb.append( Character.forDigit( lo, 16 ) );
            }

            return sb.toString();
        }
        catch ( Exception e )
        {
            LOG.error( "Failed to generate MD5", e );
            return "";
        }
    }
}