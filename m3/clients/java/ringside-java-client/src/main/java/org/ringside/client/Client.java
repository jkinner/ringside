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

import java.net.URL;
import java.util.Properties;

/**
 * A base class that can be the starting point for a client that is
 * used to access APIs hosted within a Ringside server.
 *
 * @author John Mazzitelli
 */
public abstract class Client
{
    private Context context;

    /**
     * Builds the client, with an optional array of configuration settings.
     * 
     * @param serverAddr the endpoint of the server that this client will talk to
     * @param secret the secret key used to sign the requests
     * @param config configuration settings that control the behavior of the client
     *               and its components
     */
    public Client( URL        serverAddr,
                   String     secret,
                   Properties config )
    {
        this.context = new Context( serverAddr, secret, ( config != null ) ? config : new Properties() );
    }

    /**
     * This context includes things like the server endpoint that this client
     * will talk to when calling APIs.
     *
     * @return the context
     */
    protected Context getContext()
    {
        return this.context;
    }
}