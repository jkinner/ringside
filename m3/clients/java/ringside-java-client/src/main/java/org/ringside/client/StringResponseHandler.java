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

import java.io.BufferedInputStream;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;

/**
 * Processes the responses in raw String form.
 *
 * @author John Mazzitelli
 */
public class StringResponseHandler
    implements ResponseHandler<String>
{
    public String processResponse( ApiMethod   method,
                                   Parameters  params,
                                   Context     context,
                                   InputStream inputStreamResponse )
    throws Exception
    {
        ByteArrayOutputStream outputStream = new ByteArrayOutputStream();
        copy( inputStreamResponse, outputStream, true );
        return (String) outputStream.toString( "UTF-8" );
    }

    public String stringify( String obj )
    {
        return obj;
    }

    /**
     * Copies data from the input stream to the output stream. The streams will be
     * closed when done if <code>closeStreams</code> is <code>true</code>, otherwise
     * the streams are left open and the caller must remember to close them.
     *
     * @param  input        the originating stream that contains the data to be copied
     * @param  output       the destination stream where the data should be copied to
     * @param  closeStreams if <code>true</code>, the streams will be closed before the method returns
     *
     * @return the number of bytes copied from the input to the output stream
     *
     * @throws RuntimeException if failed to read or write the data
     */
    protected long copy( InputStream  input,
                         OutputStream output,
                         boolean      closeStreams )
    throws RuntimeException
    {
        long numBytesCopied = 0;
        int  bufferSize     = 32768;

        try
        {
            // make sure we buffer the input
            input = new BufferedInputStream( input, bufferSize );

            byte[] buffer = new byte[bufferSize];

            for ( int bytesRead = input.read( buffer ); bytesRead != -1; bytesRead = input.read( buffer ) )
            {
                output.write( buffer, 0, bytesRead );
                numBytesCopied += bytesRead;
            }

            output.flush();
        }
        catch ( IOException ioe )
        {
            throw new RuntimeException( "Stream data cannot be copied", ioe );
        }
        finally
        {
            if ( closeStreams )
            {
                try
                {
                    output.close();
                }
                catch ( IOException ioe1 )
                {
                }

                try
                {
                    input.close();
                }
                catch ( IOException ioe2 )
                {
                }
            }
        }

        return numBytesCopied;
    }
}