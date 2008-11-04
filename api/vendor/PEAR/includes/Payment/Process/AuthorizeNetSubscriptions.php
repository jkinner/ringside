<?php
require_once 'Payment/Process.php';
require_once 'Payment/Process/Common.php';
//require_once 'Net/Curl.php';

class Payment_Process_AuthorizeNetSubscriptions extends Payment_Process_Common
{
    /**
     * Front-end -> back-end field map.
     *
     * This array contains the mapping from front-end fields (defined in
     * the Payment_Process class) to the field names Authorize.Net requires.
     *
     * @see _prepare()
     * @access private
     */
    var $_fieldMap = array(
        // Required
    	'login'            => '/merchantAuthentication/name',
        'password'         => '/merchantAuthentication/transactionKey',
    	'intervalLength'   => '/subscription/paymentSchedule/interval/length',
    	'intervalUnit'     => '/subscription/paymentSchedule/interval/unit',
    	'startDate'        => '/subscription/paymentSchedule/startDate',
    	'totalOccurrences' => '/subscription/paymentSchedule/totalOccurrences',
        'amount'           => '/subscription/amount',
    	'refId'            => '/refId',
    
    	// Optional
    	'subscriptionName' => '/subscription/name',
    	'invoiceNumber'    => '/subscription/order/invoiceNumber',
        'email'            => '/subscription/customer/email',
        'phoneNumber'      => '/subscription/customer/phoneNumber'
    );
    
    /**
     * $_typeFieldMap
     *
     * @author Joe Stump <joe@joestump.net>
     * @access protected
     */
    var $_typeFieldMap = array(

           'CreditCard' => array(
                'firstName'  => '/subscription/billTo/firstName',
                'lastName'   => '/subscription/billTo/lastName',
                'cardNumber' => '/subscription/payment/creditCard/cardNumber',
                'expDate'    => '/subscription/payment/creditCard/expirationDate'
           )
    );

    /**
     * The response body sent back from the gateway.
     *
     * @access private
     */
    var $_responseBody = '';
    
    var $_refId;

    /**
     * Constructor.
     *
     * @param  array  $options  Class options to set.
     * @see Payment_Process::setOptions()
     * @return void
     */
    function __construct($options = false)
    {
        parent::__construct( $options );
        $this->_driver = 'AuthorizeNetSubscriptions';
        $this->_makeRequired( 'login', 'password', 'refId', 'intervalLength', 
        	'intervalUnit', 'startDate', 'totalOccurrences', 'amount' );
    }

    function Payment_Process_AuthorizeNetSubscriptions( $options = false )
    {
        $this->__construct( $options );
    }
    
    /**
     * Default options for this processor.
     *
     * @see Payment_Process::setOptions()
     * @access private
     */
    var $_defaultOptions = array(
         'authorizeUri' => 'https://apitest.authorize.net/xml/v1/request.api' //TODO: make configurable
    );
    
    /**
     * Processes the transaction.
     *
     * Success here doesn't mean the transaction was approved. It means
     * the transaction was sent and processed without technical difficulties.
     *
     * @return mixed Payment_Process_Result on success, PEAR_Error on failure
     * @access public
     */
    function &process()
    {
        // Sanity check
        $result = $this->validate();
        if (PEAR::isError($result)) {
            return $result;
        }

        // Prepare the data
        $result = $this->_prepare();
        if (PEAR::isError($result)) {
            return $result;
        }
        
//        error_log( 'AuthorizeNetSubscriptions->process(),Ê_data: ' . print_r( $this->_data, true ) );
        
        $xml = $this->_prepareXml();
        if ( PEAR::isError( $xml ) ) {
            return $xml;
        }

        // Don't die partway through
        PEAR::pushErrorHandling(PEAR_ERROR_RETURN);

        $result = null;
		if ( function_exists( 'curl_init' ) ) {
			// Use CURL if installed...
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->_options[ 'authorizeUri' ] );
	      	curl_setopt($ch, CURLOPT_POSTFIELDS, $xml );
	      	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	      	curl_setopt($ch, CURLOPT_USERAGENT, 'PEAR Payment_Process_AuthorizeNetSubscriptions 0.1' );
			
	      	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_CAINFO, "path:/ca-bundle.crt");
			curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));	      	
			
//			error_log( 'about to curl, sending xml: ' . "\n" . $xml );
	      	$result = curl_exec($ch);
	      	if ( !$result ) {
	         	$ce = curl_error($ch);
	         	// Throw an exception if there is a communication failure
	         	throw new Exception("Exception during communication: $ce ( $this->_options[ 'authorizeUri' ] )" );
		    }
	      	curl_close($ch);
   		}
   		else {
   			return PEAR::raiseError( "Curl must be installed in order to send subscription requests to Authorize.Net" );
//      		// Non-CURL based version...
//      		$context = array('http' =>
//      			array('method' => 'POST',
//                'header' => 'Content-type: application/x-www-form-urlencoded'."\r\n".
//                    'User-Agent: PEAR Payment_Process_AuthorizeNetSubscriptions 0.1 (non-curl) '.phpversion()."\r\n".
//                    'Content-length: ' . strlen( $xml ),
//                	'content' => $xml ) );
//        	error_log( 'about to send: ' . print_r( $context, true ) );
//      		$contextid = stream_context_create( $context );
////      		error_log( 'sent.  result: ' . $result );
//		    $sock = fopen( $this->_options[ 'authorizeUri' ], 'r', false, $contextid );
//      		if ( $sock ) {
//         		$result='';
//         		while ( !feof( $sock ) )
//					$result.=fgets($sock, 4096);
//				fclose($sock);
//			}
		}
        
//
        $this->_responseBody = trim( $result );
//        error_log( 'AuthorizeNetSubscriptions->process(),Ê_responseBody: ' . "\n" . print_r( $this->_responseBody, true ) );
        
        // Restore error handling
        PEAR::popErrorHandling();

        $response = Payment_Process_Result::factory($this->_driver,
                                                     $this->_responseBody,
                                                     $this);

        if (!PEAR::isError( $response ) ) {
            $response->parse();

            $r = $response->isLegitimate();
            if( PEAR::isError( $r ) ) {
                return $r;
            } elseif ( $r === false ) {
                return PEAR::raiseError( 'Illegitimate response from gateway' );
            }
        }

        return $response;
    }
    
    /**
     * Prepare the POST query string.
     *
     * You will need PHP_Compat::str_split() if you run this processor
     * under PHP 4.
     *
     * @access private
     * @return string The query string
     */
    function _prepareXml()
    {
    	$xmlStr = self::getGatewayXml();
    	if( PEAR::isError( $xmlStr ) ) {
    		return $xmlStr;
    	}
    	$xml = new SimpleXMLElement( $xmlStr );
		$merchant = $xml->addChild( 'merchantAuthentication' );
		$merchant->addChild( 'name', $this->_data[ '/merchantAuthentication/name' ] );
		$merchant->addChild( 'transactionKey', $this->_data[ '/merchantAuthentication/transactionKey' ] );
		$xml->addChild( 'refId', $this->_data[ '/refId' ] );
		$this->_refId = $this->_data[ '/refId' ];
		$subscription = $xml->addChild( 'subscription' );
		$subscription->addChild( 'name', $this->_data[ '/subscription/name' ] );
		$paymentSchedule = $subscription->addChild( 'paymentSchedule' );
		$interval = $paymentSchedule->addChild( 'interval' );
		$interval->addChild( 'length', $this->_data[ '/subscription/paymentSchedule/interval/length' ] );
		$interval->addChild( 'unit', $this->_data[ '/subscription/paymentSchedule/interval/unit' ] );
		$paymentSchedule->addChild( 'startDate', $this->_data[ '/subscription/paymentSchedule/startDate' ] );
		$paymentSchedule->addChild( 'totalOccurrences', $this->_data[ '/subscription/paymentSchedule/totalOccurrences' ] );
		$subscription->addChild( 'amount', $this->_data[ '/subscription/amount' ] );
		$payment = $subscription->addChild( 'payment' );
		$creditCard = $payment->addChild( 'creditCard' );
		$creditCard->addChild( 'cardNumber', $this->_data[ '/subscription/payment/creditCard/cardNumber' ] );

		$split = split( '/', $this->_data[ '/subscription/payment/creditCard/expirationDate' ] );
		$tstamp = mktime( null, null, null, $split[0] + 1, null, $split[1], null );
		$expDate = date( 'Y-m', $tstamp );
		$creditCard->addChild( 'expirationDate', $expDate );
		
		if( isset( $this->_data[ '/subscription/order/invoiceNumber' ] ) ) {
			$order = $subscription->addChild( 'order' );
			$order->addChild( 'invoiceNumber', $this->_data[ '/subscription/order/invoiceNumber' ] );
		}
        if( isset( $this->_data[ '/subscription/customer/email' ] ) || isset( $this->_data[ '/subscription/customer/phoneNumber' ] ) ) {
            $customer = $subscription->addChild( 'customer' );
            if( isset( $this->_data[ '/subscription/customer/email' ] ) ) {
            	$customer->addChild( 'email', $this->_data[ '/subscription/customer/email' ] );
            }
            if( isset( $this->_data[ '/subscription/customer/phoneNumber' ] ) ) {
                $customer->addChild( 'phoneNumber', $this->_data[ '/subscription/customer/phoneNumber' ] );
            }
        }
		$billTo = $subscription->addChild( 'billTo' );
		$billTo->addChild( 'firstName', $this->_data[ '/subscription/billTo/firstName' ] );
		$billTo->addChild( 'lastName', $this->_data[ '/subscription/billTo/lastName' ] );
		
		return $xml->asXML();
    }
    
    private function getGatewayXml() {

    	if( $this->_options[ 'action' ] == 'subscribe' ) {
			$subscribeXml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<ARBCreateSubscriptionRequest xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xsi:schemaLocation="AnetApi/xml/v1/schema/AnetApiSchema.xsd https://api.authorize.net/xml/v1/schema/AnetApiSchema.xsd"
 xmlns:xsd="http://www.w3.org/2001/XMLSchema"
 xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
</ARBCreateSubscriptionRequest>
XML;
			return $subscribeXml;
		}
		else {
			return PEAR::raiseError( "No action identified in processor options.  Must be one of 'subscribe', 'update', or 'cancel'." );
		}
    }
}

class Payment_Process_Result_AuthorizeNetSubscriptions extends Payment_Process_Result {

    /**
     * To hold the refId returned
     *
     * @var string
     * @access private
     */
    var $_refId;

    function Payment_Process_Response_AuthorizeNetSubscriptions( $rawResponse )
    {
        $this->Payment_Process_Response( $rawResponse );
    }

    /**
     * Parses the data received from the payment gateway
     *
     * @access public
     */
    function parse()
    {
//    	error_log( 'AuthorizeNetSubscriptionsResponse->parse(), response: ' . $this->_rawResponse );
    	$xml = new SimpleXMLElement( $this->_rawResponse );

//    	$children = $xml->children();
//    	error_log( 'children: ' . print_r( $children, true ) );

    	$messages = $xml->messages;
    	$resultCode = $messages[0]->resultCode;
    	$code = $messages[0]->message[0]->code;
    	$text = $messages[0]->message[0]->text;
    	$subscriptionId = null;
    	if( $resultCode == 'Ok' && $this->_request->getOption( 'action' ) == 'subscribe' ) {
    		$subscriptionId = $xml->subscriptionId;
    	}
    	$this->_refId = $xml->refId;
    	$this->code = $resultCode;
    	$this->messageCode = $code;
    	$this->message = $text;
    	$this->transactionId = $subscriptionId;
    	
//    	error_log( 'ref id: ' . $xml->refId );
//    	error_log( 'ref id: ' . $this->_refId );
//    	error_log( 'result code: ' . $resultCode );
//    	error_log( 'code: ' . $code );
//    	error_log( 'text: ' . $text );
//    	error_log( 'subscription id: ' . $subscriptionId );
    }

    /**
     * Parses the data received from the payment gateway callback
     *
     * @access public
     */
    function parseCallback()
    {
    }

    /**
     * Validates the legitimacy of the response
     *
     * To be able to validate the response, the md5Value option
     * must have been set in the processor. If the md5Value is not set this
     * function will fail gracefully, but this MAY CHANGE IN THE FUTURE!
     *
     * Check if the response is legitimate by matching MD5 hashes.
     * To avoid MD5 mismatch while the key is being renewed
     * the md5Value can be an array with 2 indexes: "new" and "old"
     * respectively holding the new and old MD5 values.
     *
     * Note: If you're having problem passing this check: be aware that
     * the login name is CASE-SENSITIVE!!! (even though you can log in
     * using it all lowered case...)
     *
     * @return mixed TRUE if response is legitimate, FALSE if not, PEAR_Error on error
     * @access public
     * @author Philippe Jausions <Philippe.Jausions@11abacus.com>
     */
    function isLegitimate()
    {
        $originalRefId = $this->_request->_refId;
//        error_log( 'original refId: ' . $originalRefId );
//        error_log( 'response refId: ' . $this->_refId );
        if( !isset( $this->_refId ) ) {
            return PEAR::raiseError( 'No refId from gateway' );
        }
        else if( $originalRefId != $this->_refId ) {
        	return false;
        }
        return true;
    }
}
?>
