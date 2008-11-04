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

require_once 'ringside/api/AbstractRest.php';
require_once 'ringside/m3/util/Settings.php';

/**
 * Abstract class that all M3 server-side REST API classes extend.
 *
 * Subclasses must still implement:
 * <ul>
 *    <li>validateRequest()</li>
 *    <li>execute()</li>
 * </ul>
 *
 * @author John Mazzitelli
 */
abstract class M3_AbstractRest extends Api_AbstractRest
{
    // many of the abstract functions that need to be implemented are no-ops for M3
    public function loadSession()     {}
    public function delegateRequest() {}
    public function validateSession() {}
    public function validateApiKey( ) {}
    public function validateCallId()  {}

    public function getSessionValue($key) { return null; } // why do I need this? RS-413

    /**
     * Validate the request has what it needs. Implementations typically examine
     * the incoming request's parameters to ensure they match was is to be expected.
     * This base implementation is a no-op.
     *
     * @see getRequiredApiParam()
     * @see getApiParam()
     * @see getApiParams()
     */
    public function validateRequest()
    {
        return; // no-op
    }

    /**
     * Validate that the incoming request corresponds to a supported version
     * (as specified in the request context's "Version" paramter).
     * All M3 REST APIs will support a base version - this method implementation
     * checks for that base verison. Individual REST APIs can override this method
     * if they need to validate to a more recent version.
     *
     * @see Api_AbstractRest::getContext()
     */
    public function validateVersion()
    {
        $_version = $this->getContext()->getVersion();

        if ( !isset($_version) && $_version != '1.0')
        {
            $_msg = "Unsupported version [$version] - cannot process M3 REST API request";
            throw new Exception($_msg);
        }
    }

    /**
     * Validate the M3 request by ensuring the request was properly signed
     * with the M3 secret key. The verification algorithm is as follows:
     * <ol>
     *    <li>Ensure the request has a signature string (found in the context)</li>
     *    <li>Sort all request parameters via PHP ksort() method</li>
     *    <li>Build a single string with request parameter "name=value" pairs</li>
     *    <li>Append to the string the M3 secret key that is configured in the server</li>
     *    <li>Calculate an MD5 hashcode for the string</li>
     *    <li>Verify that the MD5 hashcode matches the signature string passed in with the request</li>
     * </ol>
     */
    public function validateSig()
    {
        $_sig = $this->getContext()->getSig();
        $_request = $this->getContext()->getInitialRequest();
        $_secret = M3_Util_Settings::getM3SecretKey();

        if (!isset($_sig) || empty($_sig))
        {
            throw new Exception('M3 request rejected - it is missing a signature');
        }

        ksort($_request);
         
        $_str='';
        foreach ($_request as $_k=>$_v)
        {
            if ( $_k != 'sig' )
            {
                $_str .= "$_k=$_v";
            }
        }
        $_str .= $_secret;
        $_md5sig = md5($_str);

        if ( $_md5sig != $_sig )
        {
            $_emsg = 'M3 request rejected - incorrect signature';
            error_log("$_emsg: _str=[$_str], _md5sig=[$_md5sig], _sig=[$_sig]");
            throw new Exception($_emsg);
        }
        
        return;
    }
}
?>