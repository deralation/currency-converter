<?php

require_once('../../config/config.php');
header('Content-Type: application/json');
$response = array();

try {

    $api = new APIRequest();
	$api->setRequestParameters($_REQUEST);
	$action = $api->getAction();
    
    if($action=="getExchangeRate"){
        // Check requirements parameters for api
		$api->setRequiredParameters(array("sourceCurrency", "targetCurrency", "sourceAmount"));
		if (!$api->checkForRequiredParameters())
			throw new Exception("400: Required fields are missing: " . $api->getError());

        $sourceCurrency = $_REQUEST["sourceCurrency"];
        $targetCurrency =$_REQUEST["targetCurrency"];

        $c = new Converter($sourceCurrency,$targetCurrency);
        $c->setSourceAmount($_REQUEST["sourceAmount"]);
        $rates = $c->getLatestRate();

        $response["result"] = true;
        $response["data"] = $rates;
    }else{
        throw new Exception("Unknown action sent to Converter Api");
    }

    echo $api->formatResponse($response);
} catch (Exception $e) {
	echo $api->formatException($e);
}