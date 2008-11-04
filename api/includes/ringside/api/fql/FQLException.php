<?php
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

DEFINE( "FQL_ERROR_CODE_FQLPARSING", 601 );
DEFINE( "FQL_ERROR_MSG_FQLPARSING", "Error while parsing FQL statement." );

DEFINE( "FQL_ERROR_CODE_NOSUCHFIELD", 602 );
DEFINE( "FQL_ERROR_MSG_NOSUCHFIELD", "The field you requested does not exist." );

DEFINE( "FQL_ERROR_CODE_NOSUCHTABLE", 603 );
DEFINE( "FQL_ERROR_MSG_NOSUCHTABLE", "The table you requested does not exist." );

DEFINE( "FQL_ERROR_CODE_NOINDEX", 604 );
DEFINE( "FQL_ERROR_MSG_NOINDEX", "Your statement is not indexable." );

DEFINE( "FQL_ERROR_CODE_NOFUNCTION", 605 );
DEFINE( "FQL_ERROR_MSG_NOFUNCTION", "The function you called down not exist." );

DEFINE( "FQL_ERROR_CODE_WRONGNUMARGS", 606 );
DEFINE( "FQL_ERROR_MSG_WRONGNUMARGS", "Wrong number of arguments passed into the function." );


class FQLException extends Exception
{
    public function __construct($message, $code = 0) {
        parent::__construct($message, $code);
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}

?>
