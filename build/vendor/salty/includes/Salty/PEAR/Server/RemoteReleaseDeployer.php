<?php
/**
 * This file prepares the package, and deploys a PEAR Package .tgz release to Chiara_PEAR_Server.
 * 
 * @author Brett Bieber
 * @package Salty_PEAR_Server_RemoteReleaseDeployer
 */

/**
 * HTTP_Request is used to handle logging in and posting the file to the channel server.
 */
require_once 'HTTP/Request.php';

/**
 * Class which handles the remote deployment of a PEAR package release to a 
 * Chiara_PEAR_Server.
 * 
 * @package Salty_PEAR_Server_RemoteReleaseDeployer
 */
class Salty_PEAR_Server_RemoteReleaseDeployer
{
    /**
     * URI to the admin interface for the Chiara_PEAR_Server ex: http://pear.example.com/
     *
     * @var string
     */
    var $adminuri;
    
    /**
     * The admin.php page which is the broker for the UI interface.
     *
     * @var string
     */
    var $adminpage;
    
    
    /**
     * User handle to upload a release to the PEAR Channel Server.
     *
     * @var string
     */
    var $username;
    
    /**
     * Password for the handle used on the server.
     *
     * @var string
     */
    var $password;
    
    var $req;
    
    function __construct()
    {
        $this->req =& new HTTP_Request();
    }
    
    /**
     * Logs in and deploys the release on a Chiara_PEAR_Server.
     *
     * @param string $filename
     * @return bool true or false
     */
    function deployRelease($filename)
    {
        $this->login();
        $this->uploadRelease($filename);
        return $this->releaseWasSaved($filename);
    }
    
    /**
     * Logs in to the Chiara_PEAR_Server
     *
     */
    function login()
    {
        $this->req->setURL($this->adminuri . '/' . $this->adminpage);
        $this->req->setMethod(HTTP_REQUEST_METHOD_POST);
        $this->req->addPostData('login', 'Submit');
        $this->req->addPostData('user', $this->username);
        $this->req->addPostData('password', $this->password);
        if (!PEAR::isError($this->req->sendRequest())) {
            $this->_setResponseCookies();
        } else {
             echo 'Error connecting to admin.';
        }
    }
    
    /**
     * Actually uploads a release to the server.
     *
     * @param string $filename
     */
    function uploadRelease($filename)
    {
        $this->req->setURL($this->adminuri . '/' . $this->adminpage);
        $this->req->setMethod(HTTP_REQUEST_METHOD_POST);
        $this->req->addPostData('f', '0');
        $this->req->addPostData('Submit', 'Submit');
        $this->req->addPostData('submitted', '1');
        
        $result = $this->req->addFile('release', $filename);
        if (PEAR::isError($result)) {
            echo "Error : " . $result->getMessage();
        } else {
            $response = $this->req->sendRequest();
            if (PEAR::isError($response)) {
                echo "Error : " . $response->getMessage();
            } else {
                // release sent.
            }
        }
    }
    
    /**
     * Determines if a release was saved based to the channel server.
     * 
     */
    function releaseWasSaved($filename)
    {
        $release = pathinfo($filename);
        
        $this->req->setURL( $this->adminuri . '/get/'.$release['basename']);
        if (!PEAR::isError($this->req->sendRequest())) {
            if ($this->req->getResponseCode() == '200') {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    /**
     * Sets the cookies from the HTTP_Request member variable, specifically the PHPSESSID
     * once a user is logged in.
     *
     */
    function _setResponseCookies()
    {
        $c = $this->req->getResponseCookies();
        foreach ($c as $cookie) {
            $this->req->addCookie($cookie['name'],$cookie['value']);
        }
    }
} 

?>