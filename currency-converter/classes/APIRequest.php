<?php

class APIRequest
{

	public static $version = 1;
	public static $format = "json";

	public static $apiURL = null;

	private $requestParameters = array();
	private $requestMethod = "GET";

	private $errorMessage;

	public function __construct($endPoint = null)
	{	
		SELF::$apiURL = "http://local.currency-converter.com/api/v1";
		return SELF::$apiURL;
	}

    public function checkForRequiredParameters()
	{
		try {
			if (isset($this->requiredParameters) && isset($this->requestParameters)) {
				$providedParameters = array_keys($this->requestParameters);
				foreach ($this->requiredParameters as $r) {
					if (!in_array($r, $providedParameters))
						throw new Exception("Missing parameter: " . $r);
				}
				return true;
			} else {
				throw new Exception("Parameters are not set");
			}
		} catch (Exception $e) {
			$this->errorMessage = $e->getMessage();
			return false;
		}
	}

	public function setRequestParameters($url)
	{
		try {
			if ($url == null || $url == null) {
				return true;
			} else if (is_array($url)) {
				$this->requestParameters = $url;
			} else if (is_string($url)) {
				parse_str($url, $urlAsArray);
				$this->requestParameters = $urlAsArray;
			}

			if (isset($this->requestParameters["action"])) {
				$this->action = (string)$this->requestParameters["action"];
			}

			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	public function setRequiredParameters($input)
	{
		try {
			if (is_array($input)) {
				$this->requiredParameters = $input;
				//echo var_dump($input);
			} else if (is_string($input)) {
				$this->requiredParameters = explode(",", $input);
			} else {
				throw new Exception("Unknown input");
			}
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	public function getRequestParameters()
	{
		return $this->requestParameters;
	}

	public function getAction()
	{
		$this->requestMethod = $_SERVER['REQUEST_METHOD'];
		if ($this->requestMethod === "PUT" && (!isset($_REQUEST["action"]) || $_REQUEST["action"] === "edit")) {
			$this->action = "edit";
		} else if ($this->requestMethod === "POST" && (!isset($_REQUEST["action"]) || $_REQUEST["action"] === "add")) {
			$this->action = "add";
		} else if ($this->requestMethod === "DELETE") {
			$this->action = "delete";
		} else if (isset($this->action)) {
			$this->action = (string) $this->action;
		} else {
			$this->action = "get";
		}
		return $this->action;
	}

	public function getError()
	{
		return $this->errorMessage;
	}

	public function showErrorCode($input = null)
	{
		if (isset($input)) {
			$err = $input;
		} else {
			$err = $this->errorMessage;
		}
		$parts = explode(": ", $err);
		if (count($parts) > 1) {
			if (is_numeric($parts[0]))
				return (int) $parts[0];
			else
				return 500;
		} else if (is_numeric($parts[0])) {
			return (int) $parts[0];
		} else {
			if (stripos($err, "duplicate") !== false) {
				return 409;
			} else {
				return 500;
			}
		}
	}

	public function showFormattedErrorFormattedText($input)
	{

		$regex = preg_match_all('/\d{3}:/', $input, $matches, PREG_SET_ORDER, 0);
		if ($regex === 0) {
			return $input;
		}

		$inputParts = explode(": ", $input);
		array_shift($inputParts);
		return implode(": ", $inputParts);
	}


	public function formatException($exception)
	{
		$response = array();
		$response["result"] = false;
		$response["code"] = $this->showErrorCode($exception->getMessage());
		$response["message"] = $this->showFormattedErrorFormattedText($exception->getMessage());

		if (in_array($response["code"], array("400", "401", "403", "404", "500"))) {
			http_response_code($response["code"]);
		}

		return json_encode($response);
	}

	public function formatResponse($response)
	{
		try {

			if ($response["result"] === true) {
				if (isset($this->action) && $this->action === "add" && $this->requestMethod === "POST") {
					http_response_code(201);
				}
				if (isset($response["code"]) && $response["code"] === 201) {
					http_response_code(201);
				}
			}

			global $bench;
			$bench->end();
			$response["debug"] = array(
				"benchmark" => array(
					"elapsedTime" 	=> $bench->getTime(),
					"memoryPeak"	=> $bench->getMemoryPeak(),
					"memoryUsage"	=> $bench->getMemoryUsage()
				)
			);

			return json_encode($response);
		} catch (Exception $e) {

			$this->errorMessage = $e->getMessage();

			$response["result"] = false;
			$response["message"] = $this->errorMessage;

			return json_encode($response);
		}
	}
}
?>