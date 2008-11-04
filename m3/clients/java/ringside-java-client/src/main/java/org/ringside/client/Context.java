package org.ringside.client;

import java.net.URL;
import java.util.Hashtable;
import java.util.Properties;

/**
 * This context object can be used to share data with different
 * objects in the lifecycle of API requests and sender objects.
 * 
 * This context has some built-in data (like the server URL,
 * last invocation time and current request ID) but is also able
 * to store any custom objects that the clients/senders/handlers
 * want in the {@link #getContextData() custom context data}
 * hashtable.
 * 
 * This context object is thread-safe.
 * 
 * @author John Mazzitelli
 */
public class Context
{
    private long lastRequestTime = 0L;
    private long lastRequestId   = 0L;

    private final URL                       serverAddress;
    private final String                    secretKey;
    private final Properties                config;
    private final Hashtable<String, Object> data; // where custom data can be stored by our client/senders

    /**
     * Builds a context object.
     * 
     * @param serverAddr the endpoint of the server that this client will talk to
     * @param secretKey the secret key used to sign requests
     * @param config configuration settings that control the behavior of the senders/clients/handlers
     */
    public Context( URL        serverAddr,
                    String     secretKey,
                    Properties config )
    {
        this.serverAddress = serverAddr;
        this.secretKey     = secretKey;
        this.config        = ( config != null ) ? config : new Properties();
        this.data          = new Hashtable<String, Object>();
    }

    /**
     * The endpoint of the Ringside server.
     * 
     * @return endpoint URL
     */
    public URL getServerAddress()
    {
        return serverAddress;
    }

    /**
     * The secret key that should be used to sign requests.
     * 
     * @return secret key string
     */
    public String getSecretKey()
    {
        return secretKey;
    }

    /**
     * Returns a copy of the configuration.  The configuration is to be considered
     * read-only.  The caller will get back a copy of the configuration; changes made
     * to the returned properties set will have no effect on the configuration held
     * by this context.
     * 
     * @return config properties
     */
    public Properties getConfiguration()
    {
        Properties copy = new Properties();
        copy.putAll( this.config );
        return copy;
    }

    /**
     * Returns the time (in epoch millis) when the last request was made.
     * 
     * @return epoch millis time of the last request
     */
    public long getLastRequestTime()
    {
        synchronized ( this )
        {
            return this.lastRequestTime;
        }
    }

    /**
     * Returns the request ID of the last request that was sent.
     * 
     * @return last request ID number
     */
    public long getLastRequestId()
    {
        synchronized ( this )
        {
            return this.lastRequestId;
        }
    }

    /**
     * This will update the {@link #getLastRequestTime() last request time} to the current time
     * and will increment and return the {@link #getLastRequestId() request ID}.
     * 
     * You should only call this when preparing to send another request.
     * 
     * @return the new request ID that should be assigned to the request
     */
    public long updateForNewRequest()
    {
        synchronized ( this )
        {
            this.lastRequestTime = System.currentTimeMillis();
            this.lastRequestId++;
            return this.lastRequestId;
        }
    }

    /**
     * Returns the context data that corresponds to the given key.
     * 
     * @param key returns the context data associated with this key
     * 
     * @return the data associated with the given key, or <code>null</code> if there is no data
     */
    public Object getData( String key )
    {
        if ( key != null )
        {
            return this.data.get( key );
        }
        else
        {
            return null;
        }
    }

    /**
     * Stores the given object associated with the given key in the context data.
     * 
     * @param key how to associate the object in the context (must not be <code>null</code>)
     * @param object the data to store (must not be <code>null</code>)
     */
    public void setData( String key,
                         Object object )
    {
        this.data.put( key, object );
    }

    /**
     * Removes the data object that is associated with the given key.
     * 
     * @param key identifies the data to remove from the context
     */
    public void removeData( String key )
    {
        if ( key != null )
        {
            this.data.remove( key );
        }
    }
}