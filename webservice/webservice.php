<?php

require_once("../credentials.inc.php");
require_once("../api_constants.inc.php");

if(isset($_POST["method"]))
{
	switch ($_POST["method"]) {
		case "authenticate":
			echo authenticate();
			break;

		case "search_flights":
			if(isset($_POST["token_id"]))
			{
				echo search_flights($_POST["token_id"]);
			}
			break;
		
		default:
			
			break;
	}
}


////////////////////////////////////////////////////////////////////////////////////////////////////////
//***API FUNCTIONS STARTS FROM HERE***//////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////

function search_flights($token_id){

	$segments[]=array(
 			"Origin"=>"DEL", 
            "Destination"=>"BOM", 
            "FlightCabinClass"=>"1", 
            "PreferredDepartureTime"=>"2016-09-06T00: 00: 00", 
            "PreferredArrivalTime"=>"2016-09-06T00: 00: 00",
		);

	$sources[]="6E";

	$post=array(
			"EndUserIp"=>"192.168.10.10", 
		    "TokenId"=>"dc20812c-2a8e-4481-b355-39a069de17e3", 
		    "AdultCount"=>"1", 
		    "ChildCount"=>"0", 
		    "InfantCount"=>"0", 
		    "DirectFlight"=>"false", 
		    "OneStopFlight"=>"false", 
		    "JourneyType"=>"1", 
		    "PreferredAirlines"=>null, 
		    "Segments"=>$segments,
		    "Sources"=>$sources,
		);

	return execute($post,SEARCH_FLIGHT);

}


function authenticate(){

	$post = array(
    'ClientId' 	=>	CLIENT_ID,
    'UserName' 	=>	USER_NAME,
    'Password' 	=>	PASSWORD,
    'LoginType'	=>	LOGIN_TYPE,
    'EndUserIp'	=>	END_USER_IP,
	);

	return execute($post,AUTHENTICATE);

}


function execute($post,$url_method){

	// make json request string
	$post_json=json_encode($post);

	// create a new cURL resource
	$ch = curl_init();

	// set URL and other appropriate options
	curl_setopt($ch, CURLOPT_URL, URL.$url_method);

	curl_setopt($ch, CURLOPT_POST, 1);

	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	   'Content-Type: application/json',  
	   'Content-Length: ' . strlen($post_json),
	   'Accept-Encoding: gzip',
	));

	curl_setopt($ch, CURLOPT_HEADER, true);

	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	curl_setopt($ch, CURLOPT_HEADER, false);

	curl_setopt($ch, CURLOPT_ENCODING, 'gzip');


	// grab URL and pass it to the browser
	$response= curl_exec($ch);

	// close cURL resource, and free up system resources
	curl_close($ch);

	return $response;

}


?>