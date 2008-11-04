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

/**
 * A base class that can be the starting point for a REST client that is
 * used to access APIs hosted within a Ringside server.
 *
 * This client can be configured by contructing it with an optional set of properties
 * passed in via an associative array to the constructor.
 * Valid optional configuration settings are:
 * 
 * <ul>
 * <li>
 *    debug=true|false -- if true, this client is verbose in the PHP error log
 * </li>
 * <li>
 *    http_proxy=host:port -- if defined, the client will go through this prox
 * </li>
 * <li>
 *    non_proxy_hosts=array of hosts -- if defined, these hosts will be considered
 *                                      local and the request will not be proxied
 * </li>
 * </ul>
 *
 * @author John Mazzitelli
 */
abstract class M3_Client_AbstractRestClient
{
    const CONFIG_DEBUG = 'debug';
    const CONFIG_HTTP_PROXY = 'http_proxy';
    const CONFIG_NON_PROXY_HOSTS = 'non_proxy_hosts';

    private $serverAddress;
    private $secretKey;
    private $configuration;
    private $lastInvocationTime;
    private $currentRequestId; // used mainly for when generating debug information
    
    /**
     * Builds the client, with an optional array of configuration settings.
     * 
     * @param $serverAddr the endpoint of the server that this client will talk to
     * @param $secret the secret key used to sign the requests
     * @param $configArray array of configuration settings that control the behavior of the client
     */
    public function __construct($serverAddr, $secret, $configArray = null)
    {
        if (!isset($serverAddr) || empty($serverAddr))
        {
            throw Exception("REST client must be given an address to the REST server");
        }

        $this->serverAddress = $serverAddr;
        $this->secretKey = $secret;
        $this->currentRequestId = 0;
        $this->lastInvocationTime = 0;

        if (empty($configArray))
        {
            $configArray = array();
        }

        $this->configuration = $configArray;
        
        if ($this->isDebugEnabled())
        {
            error_log("Creating REST client to [$serverAddr] with config: " . var_export($this->configuration, true));

?>
<script type="text/javascript">
var types = ['params', 'xml', 'php', 'sxml'];
function toggleDisplay(id, type) {
  for each (var t in types) {
    if (t != type || document.getElementById(t + id).style.display == 'block') {
      document.getElementById(t + id).style.display = 'none';
    } else {
      document.getElementById(t + id).style.display = 'block';
    }
  }
  return false;
}
</script>
<?php
        }
    }

    /**
     * Makes a REST call, invoking the given method with the given parameters.
     * 
     * @param $method the name of the REST API to call, using dot-notation
     * @param $params array containing the parameters to pass to the REST API
     * 
     * @param array the results of the API invocation
     */
    public function callRestMethod($method, $params = null)
    {
        // we are now currently processing a request, bump up our counter/timestamp
        $this->currentRequestId++;
        $this->lastInvocationTime = microtime(true);

        $_config = $this->getConfiguration();
        
        if ($this->isDebugEnabled())
        {
            $_paramString = var_export($params, true);
            error_log("Client invoking method [$method] (params: ". $_paramString ."), response follows:");
        }

        // send the request via POST
        $_xml = $this->postRequest($method, $params);

        if ($this->isDebugEnabled())
        {
            error_log("Client received reply from post: $_xml");
        }
        
        // get the results in the proper format
        $_sxml = simplexml_load_string($_xml);
        $_result = $this->convertSimpleXmlToArray($_sxml);

        if ($this->isDebugEnabled())
        {
            // output the raw xml and its corresponding php object, for debugging:
            print '<div style="margin: 10px 30px; padding: 5px; border: 2px solid black; background: gray; color: white; font-size: 12px; font-weight: bold;">';
            print $this->currentRequestId . ': Called ' . $method . ', show ' .
            '<a href=# onclick="return toggleDisplay(' . $this->currentRequestId . ', \'params\');">Params</a> | '.
            '<a href=# onclick="return toggleDisplay(' . $this->currentRequestId . ', \'xml\');">XML</a> | '.
            '<a href=# onclick="return toggleDisplay(' . $this->currentRequestId . ', \'sxml\');">SXML</a> | '.
            '<a href=# onclick="return toggleDisplay(' . $this->currentRequestId . ', \'php\');">PHP</a>';
            print '<pre id="params'.$this->currentRequestId.'" style="display: none; overflow: auto;">'.print_r($params, true).'</pre>';
            print '<pre id="xml'.$this->currentRequestId.'" style="display: none; overflow: auto;">'.htmlspecialchars($_xml).'</pre>';
            print '<pre id="php'.$this->currentRequestId.'" style="display: none; overflow: auto;">'.print_r($_result, true).'</pre>';
            print '<pre id="sxml'.$this->currentRequestId.'" style="display: none; overflow: auto;">'.print_r($_sxml, true).'</pre>';
            print '</div>';
        }

        // make sure we were successful - if our request was a failure, this will throw an exception
        $this->verifySuccess($method, $params, $_result);

        // return the array of results back to the caller
        return $_result;
    }

    /**
     * Invoke the REST API on the server by submitting the request via POST.
     * If the invocation failed for some reason, an exception will be thrown.
     *
     * @param $method the REST API to invoke
     * @param $params the parameters the caller wants to pass to the REST API
     * 
     * @return string the results of the successful REST API invocation
     */
    protected function postRequest($method, $params)
    { 
        $_postParams = $this->prepareParameters($method, $params);

        $_config = $this->getConfiguration();

        if ($this->isDebugEnabled())
        {
            error_log('Prepared params: ' . var_export($_postParams, true));
        }

        $_postString = implode('&', $_postParams);

        if (function_exists('curl_init'))
        {
            // Use CURL if installed...
            $_ch = $this->prepareCurlHandle($method, $params);
            curl_setopt($_ch, CURLOPT_POSTFIELDS, $_postString);

            $_result = curl_exec($_ch);
            if (!$_result)
            {
                $_ce = curl_error($_ch);
                if (empty($_ce))
                {
                   throw new Exception("No results from API: (" . var_export(curl_getinfo($_ch), true) . ")");
                }
                else
                {
                   throw new Exception("Exception during communication: $_ce (" . var_export(curl_getinfo($_ch), true) . ")");
                }
            }

            curl_close($_ch);
        }
        else
        {
            // Non-CURL based version...note that we do not support proxies in this mode
            $_context =
            array('http' =>
            array('method' => 'POST',
                  'header' => 'Content-type: application/x-www-form-urlencoded'."\r\n".
                              'User-Agent: Ringside API PHP5 Client 1.0 (non-curl) '.phpversion()."\r\n".
                              'Content-length: ' . strlen($_postString),
                  'content' => $_postString));
            $_contextid = stream_context_create($_context);
            $_sock=fopen($this->getServerAddress(), 'r', false, $_contextid);
            if ($_sock)
            {
                $_result='';
                while (!feof($_sock))
                {
                    $_result .= fgets($_sock, 4096);
                }
                fclose($_sock);
            }
        }

        return $_result;
    }

    /**
     * This function must return an initialized CURL handle. The returned
     * CURL handle can have zero, one or more of its options defined.
     * Note that the parameters need not be set in the returned CURL handle's
     * options; the caller will set them appropriately. If a subclass
     * wants to alter the parameters that are sent to the REST server,
     * override the prepareParameters() function. This method is here
     * so all subclasses can set their own options in the CURL handle that
     * is to be used to communicate with the server, if they so choose.
     * 
     * This implementation will set the URL to the server address and will
     * set the HTTP proxy if one is configured for this client.
     * 
     * This function will only be called if CURL is available.
     * 
     * @param $method the name of the REST API that is being invoked
     * @param $params the parameters that are to be passed to the REST API
     * 
     * @return resource CURL handle that was initialized
     * 
     * @see curl_init()
     * @see curl_setopt()
     */
    protected function prepareCurlHandle($method, $params)
    {
        $_ch = curl_init();

        $_config = $this->getConfiguration();
        
        curl_setopt($_ch, CURLOPT_URL, $this->getServerAddress());
        curl_setopt($_ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($_ch, CURLOPT_USERAGENT, 'Ringside API PHP5 Client 1.0 (curl) ' . phpversion());

        // we assume we don't need cookies for session persistence, do worry about setting these
        // if we resurrect this, we need to have one cookie file per client instance
        //curl_setopt($_ch, CURLOPT_COOKIEFILE, dirname(__FILE__)."/../cookie.txt");
        //curl_setopt($_ch, CURLOPT_COOKIEJAR, dirname(__FILE__)."/../cookie.txt");

        $_httpProxy = "";
        if (isset($_config[self::CONFIG_HTTP_PROXY]))
        {
            $_httpHost = parse_url($this->getServerAddress(), PHP_URL_HOST);

            // never proxy to localhost
            $_isRemote = !($_httpHost === 'localhost' || $_httpHost === '127.0.0.1');
            
            // non_proxy_hosts is a way for you to configure hosts that are not to be considered "remote"
            if (isset($_config[self::CONFIG_NON_PROXY_HOSTS]))
            {
                foreach ($_config[self::CONFIG_NON_PROXY_HOSTS] as $_nonProxyHost)
                {
                    // The host is still remote (if it is already) iff the host is not listed as a non-proxy host
                    $_isRemote = $_isRemote && !($_httpHost === $_nonProxyHost);
                }
            }

            if ($_isRemote)
            {
                $_httpProxy = $_config[self::CONFIG_HTTP_PROXY];
                 
                curl_setopt($_ch, CURLOPT_PROXY, $_httpProxy);
                curl_setopt($_ch, CURLOPT_HTTPPROXYTUNNEL, 1);
                curl_setopt($_ch, CURLOPT_FOLLOWLOCATION, 1);
            }
            else
            {
                error_log('Skipping proxy because of local URL: ' . $this->getServerAddress());
            }
        }
        
        return $_ch;
    }

    /**
     * This method is to look at the results of a REST API invocation
     * and to verify it was successful. If the results indicate that an error
     * occurred, this method should throw an exception.
     * 
     * Subclasses are free to override this function to make their own judgement
     * as to whether a specific invocation succeeded or failed.
     *
     * @param $method the name of the REST API that was invoked
     * @param $params the parameters that were passed to the REST API
     * @param $result the results of the invocation that need to be verified
     */
    protected function verifySuccess($method, $params, $result)
    {
        if (is_array($result) && isset($result['error_code']))
        {
            throw new Exception($result['error_msg'], $result['error_code']);
        }

        return; // success!
    }

    /**
     * Given the REST API to be invoked and the array of parameters to pass to that API,
     * this method must prepare those parameters to be sent over the wire to the server.
     * This method is free to add, remove or modify the given parameters as appropriate.
     *
     * Subclasses are free to override this if they know the server will require
     * special parameters in order to process the invocation successfully.
     *
     * @param $method the REST API to be invoked
     * @param $params the parameters that the client caller wants to send
     * 
     * @return array the array of parameters that should be sent over the wire
     */
    protected function prepareParameters($method, $params)
    {
        $params['method'] = $method;
        $params['call_id'] = $this->getLastInvocationTime();

        if (!isset($params['v']))
        {
            $params['v'] = '1.0';
        }

        $_postParams = array();

        foreach ($params as $_key => &$_val)
        {
            if (is_array($_val)) $_val = implode(',', $_val);
            $_postParams[] = $_key.'='.urlencode($_val);
        }

        $_sig = $this->generateSignature($method, $params);
        $_postParams[] = "sig=$_sig";
        
        return $_postParams;
    }

    /**
     * Generates the signature that will validate this request on the server-side.
     * 
     * Subclasses are free to override this function if they know the server
     * will verify the REST API invocation in a way using a different
     * signature algorithm.
     *
     * @param $method the REST API to be invoked
     * @param $params the parameters that will be sent to the REST API
     * 
     * @return string the signature string that should be sent along with the request
     */
    protected function generateSignature($method, $params)
    {
        $_str = '';
    
        ksort($params);

        // Note: make sure that the signature parameter is not already included in $params_array.
        foreach ($params as $_k=>$_v)
        {
          $_str .= "$_k=$_v";
        }

        $_str .= $this->getSecretKey();
    
        $_hash = md5($_str);

        if ($this->isDebugEnabled())
        {
            error_log('Generated signature: str=[' . $_str . '], sig=[' . $_hash . ']' );
        }

        return $_hash;
    }

    protected function convertSimpleXmlToArray($sxml)
    {
        $_arr = array();
        if ($sxml)
        {
            foreach ($sxml as $_k => $_v)
            {
                if ($sxml['list'])
                {
                    $_arr[] = $this->convertSimpleXmlToArray($_v);
                }
                else
                {
                    if (isset($_arr[$_k]))
                    {
                        // we've already seen this key - must be multiple values (aka array)
                        if (!is_array($_arr[$_k]))
                        {
                            // this is only the 2nd time we've seen it, we need to convert to an array
                            $_arr[$_k] = array ($_arr[$_k]);
                        }
                        array_push($_arr[$_k], $this->convertSimpleXmlToArray($_v));
                    }
                    else
                    {
                        $_arr[$_k] = $this->convertSimpleXmlToArray($_v);
                    }
                }
            }
        }

        if (sizeof($_arr) > 0)
        {
            return $_arr;
        }
        else
        {
            // the $sxml was probably just element data
            return (string)$sxml;
        }
    }

    /**
     * Returns the server endpoint that this client will talk to when submitting REST invocations.
     * Note that this may not be the actual server address used if this client has
     * an HTTP proxy configured for it.
     *
     * @return string server endpoint address
     */
    protected function getServerAddress()
    {
        return $this->serverAddress;
    }

    /**
     * The secret key used to sign all requests this client makes. This can be used
     * to generate the signature.
     * 
     * @return string secret key
     * 
     * @see generateSignature()
     */
    protected function getSecretKey()
    {
        return $this->secretKey;
    }

    /**
     * Returns the configuration of the client.
     * 
     * @return array configuration settings
     */
    protected function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Returns the last time that this client invoked a REST API.
     * If the client is currently processing a request, this is the
     * time that this current request was made.
     * 
     * @return the last invocation time
     */
    protected function getLastInvocationTime()
    {
        return $this->lastInvocationTime;
    }
    
    protected function isDebugEnabled()
    {
        $_config =& $this->configuration;
        return (isset($_config[self::CONFIG_DEBUG]) && $_config[self::CONFIG_DEBUG]);
    }
}

?>
