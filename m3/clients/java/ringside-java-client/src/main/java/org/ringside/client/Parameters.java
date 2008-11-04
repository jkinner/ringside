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

import java.util.Collection;
import java.util.Map;
import java.util.TreeMap;

/**
 * Represents parameters that are sent to the REST API.
 * This map is sorted by ascending keys.
 * 
 * @author John Mazzitelli
 */
public class Parameters
    extends TreeMap<String, CharSequence>
{
    private static final long serialVersionUID = 1L;

    /**
     * If you put a collection into this parameters map, it will be placed into
     * the map as an imploded, comma-separated list.
     * 
     * @param key
     * @param array the collection that is to be converted to a comma-separated list
     */
    public void put( String        key,
                     Collection<?> array )
    {
        put( key, commaSeparate( array ) );
    }

    /**
     * Takes the parameters and implodes them into a single string with each
     * name=value pair separated with the given separator. The name and value
     * will separated with an "=" sign.
     * 
     * @param separator separates each name/value pair
     * 
     * @return the impoded string with name/value pairs separated with the separator character
     */
    public CharSequence implode( char separator )
    {
        StringBuilder str = new StringBuilder();

        for ( Map.Entry<String, CharSequence> entry : this.entrySet() )
        {
            if ( str.length() > 0 )
            {
                str.append( separator );
            }

            str.append( entry.getKey() );
            str.append( '=' );
            str.append( entry.getValue() );
        }

        return str.toString();
    }

    /**
     * Returns all elements in the collection as a comma-separated string.
     * 
     * @param array the array to make into a comma-separated list
     * 
     * @return comma-separated list of all items in the array
     */
    private String commaSeparate( Collection<?> array )
    {
        StringBuilder str = new StringBuilder();

        for ( Object item : array )
        {
            if ( str.length() > 0 )
            {
                str.append( ',' );
            }

            str.append( item.toString() );
        }

        return str.toString();
    }
}