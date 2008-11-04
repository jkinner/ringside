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

import java.util.ArrayList;
import java.util.Map;

import org.testng.annotations.Test;

@Test
public class ParametersTest
{
    public void testImplode()
    {
        Parameters p = new Parameters();
        p.put( "ZZZhello", "world" );

        ArrayList<Integer> intList = new ArrayList<Integer>();
        intList.add( 1 );
        intList.add( 22 );
        intList.add( 333 );
        p.put( "MMMints", intList );

        ArrayList<String> stringList = new ArrayList<String>();
        stringList.add( "one" );
        stringList.add( "twotwo" );
        stringList.add( "threethreethree" );
        p.put( "AAAstrs", stringList );

        // note that the order is important too - it should be sorted
        CharSequence imploded = p.implode('&');
        assert imploded.toString().equals("AAAstrs=one,twotwo,threethreethree&MMMints=1,22,333&ZZZhello=world");
    }

    public void testPutArray()
    {
        Parameters p = new Parameters();
        p.put( "hello", "world" );

        ArrayList<Integer> intList = new ArrayList<Integer>();
        intList.add( 1 );
        intList.add( 22 );
        intList.add( 333 );
        p.put( "ints", intList );

        ArrayList<String> stringList = new ArrayList<String>();
        stringList.add( "one" );
        stringList.add( "twotwo" );
        stringList.add( "threethreethree" );
        p.put( "strs", stringList );

        assert p.get( "hello" ).equals( "world" );
        assert p.get( "ints" ).equals( "1,22,333" );
        assert p.get( "strs" ).equals( "one,twotwo,threethreethree" );
    }

    public void testSort()
    {
        Parameters p = new Parameters();
        p.put( "zzz", "this should be last" );
        p.put( "aaa", "this should be first" );
        p.put( "mmm", "this should be in the middle" );

        int i = 0;
        for ( Map.Entry<String, CharSequence> entry : p.entrySet() )
        {
            switch ( i++ )
            {
                case 0:
                {
                    assert entry.getKey().equals( "aaa" );
                    assert entry.getValue().equals( "this should be first" );
                    break;
                }

                case 1:
                {
                    assert entry.getKey().equals( "mmm" );
                    assert entry.getValue().equals( "this should be in the middle" );
                    break;
                }

                case 2:
                {
                    assert entry.getKey().equals( "zzz" );
                    assert entry.getValue().equals( "this should be last" );
                    break;
                }

                default:
                {
                    assert false : "bad test - should only have 3 items: " + p;
                }
            }
        }
    }
}