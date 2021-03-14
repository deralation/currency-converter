<?php
// PHP Settings
date_default_timezone_set('UTC');

// Server Differentiation
error_reporting(E_ALL); 
ini_set('display_errors', 1);
define('ENV','LOCAL');
define('ROOTPATH',$_SERVER['DOCUMENT_ROOT'].'/');
define('BASEURL',"http://local.curreny-converter.com/");

define('MYSQLDB','currency_converter');
define('MYSQLHOST','mysql');
define('MYSQLUSER','root');
define('MYSQLPASS','root');

// Paths and URLs
define('CLASSPATH',ROOTPATH.'classes/');
define('VENDORPATH',ROOTPATH.'vendor/');

// Benchmarking
require_once VENDORPATH.'devster/ubench/src/Ubench.php';
$bench = new Ubench;
$bench->start();

// Vendors
require_once(ROOTPATH.'vendor/autoload.php');

// Manuel Classes Loader
function useClasses($classes) {
	$classesArray = explode(",",$classes);
	foreach($classesArray as $c) {
		include_once CLASSPATH.$c.".php";
	}
}

$database = new MySQL();
$database->setHost(MYSQLHOST);
$database->setUser(MYSQLUSER);
$database->setPassword(MYSQLPASS);
$database->setDatabase(MYSQLDB);

// API Request
$api = new APIRequest();

// HTTP Header
$httpPost = file_get_contents("php://input");
$_JSON = json_decode($httpPost, true);
if(is_array($_REQUEST) && is_array($_JSON))
	$_REQUEST = array_merge($_REQUEST,$_JSON);
else if(is_array($_JSON))
	$_REQUEST = $_JSON;
else
	$_REQUEST = $_REQUEST;

// Cross domain
if(isset($_SERVER['HTTP_ORIGIN']) && strpos($_SERVER['HTTP_ORIGIN'],"currency-converter.com")!==false) {
	if(!headers_sent()) {
      header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
      header('Access-Control-Allow-Credentials: true');
      header('Access-Control-Allow-Headers: content-type');
    }
}

if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT");
	header("HTTP/1.1 200 OK");
}
?>