<?php
// Response item error codes
define('NOT_IMPLEMENTED', "notImplemented");
define('UNAUTHORIZED', "unauthorized");
define('FORBIDDEN', "forbidden");
define('BAD_REQUEST', "badRequest");
define('INTERNAL_ERROR', "internalError");

class GadgetDataServlet extends HttpServlet {
	private $handlers = array();
	
	public function __construct()
	{
		parent::__construct();
		$handlers = Config::get('handlers');
		if (empty($handlers)) {
			$this->handlers[] = new OpenSocialDataHandler();
			$this->handlers[] = new StateFileDataHandler();
		} else {
			$handlers = explode(',', $handlers);
			foreach ( $handlers as $handler ) {
				$this->handlers[] = new $handler();
			}
		}
	}
	
	public function doPost()
	{
		error_log("GadgetDataServlet received POST request.");
		
		$requestParam = isset($_POST['request']) ? $_POST['request'] : '';
		$token = isset($_POST['st']) ? $_POST['st'] : '';
		
		
		// detect if magic quotes are on, and if so strip them from the request
		if (get_magic_quotes_gpc()) {
			$requestParam = stripslashes($requestParam);
		}
		$request = json_decode($requestParam, true);
		error_log("Request was:\n".implode(",",$request));
		if ($request == $requestParam) {
			// oddly enough if the json_decode function can't parse the code,
			// it just returns the original string (instead of something usefull like 'null' or false :))
			error_log("Invalid request JSON");
			throw new Exception("Invalid request JSON");
		}
		
		try {
			$response = new DataResponse($this->createResponse($requestParam, $token));
		} catch ( Exception $e ) {
			error_log("ERROR ".$e);
			$response = new DataResponse(false, BAD_REQUEST);
		}
		
		// // Build assoc arrays for JSON encoding
		// foreach ($response->getResponses() as $arryResponses ){
		// 	foreach ($arryResponses as $aRespItem ){
		// 		$assocJsonResponses[]=$aRespItem;
		// 	}
		// }
		error_log("There were ".sizeof($response->getResponses()))." fields in the response.";
		$arryResponses['responses']=$response->getResponses();
		$reponse1=json_encode($arryResponses);
		error_log("Response was:\n ".$reponse1);
		echo $reponse1;
	}
	
	private function createResponse($requestParam, $token)
	{
		global $config;
		if (empty($token)) {
			throw new Exception("INVALID_GADGET_TOKEN");
		}
		$gadgetSigner = new $config['gadget_signer']();
		//FIXME currently don't have a propper token, impliment and re-enable this asap
		$securityToken = $gadgetSigner->createToken($token);
		$responseItems = array();
		error_log("createResponse:requestParam = $requestParam");
		$requests = json_decode($requestParam, true);
		if ($requests == $requestParam) {
			// oddly enough if the json_decode function can't parse the code,
			// it just returns the original string
			throw new Exception("Invalid request JSON");
		}
		foreach ( $requests as $request ) {
			error_log("Processing ".$request['type']);
			$requestItem = new RequestItem($request['type'], $request, $securityToken);
			$response = new ResponseItem(NOT_IMPLEMENTED, $request['type'] . " has not been implemented yet.", array());
			foreach ( $this->handlers as $handler ) {
				
				if ($handler->shouldHandle($request['type'])) {
					//error_log("Handler Chosen");
					$response = $handler->handleRequest($requestItem);
				}
			}
			$responseItems[] = $response;
		}
		//error_log("createResponse returns item count of ".sizeof($responseItems));
		//error_log("createResponse object is :".json_encode($responseItems));
		return $responseItems;
	}
	
	public function doGet()
	{
		error_log("GadgetDataServlet: Get requests are not permitted.");
		echo header("HTTP/1.0 400 Bad Request", true, 400);
		die("<h1>Bad Request</h1>");
	}
}