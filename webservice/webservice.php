<?php

require_once("../credentials.inc.php");
require_once("../api_constants.inc.php");

define("ANY_FLIGHTS", "T00:00:00");
define("MORNING_FLIGHTS", "T08:00:00");
define("AFTERNOON_FLIGHTS", "T14:00:00");
define("EVENING_FLIGHTS", "T19:00:00");
define("NIGHT_FLIGHTS", "T01:00:00");

if(isset($_POST["method"]))
{
	switch ($_POST["method"]) {
		case "authenticate":
			echo authenticate();
			break;

		case "search_flights":
			if(isset($_POST["token_id"]))
			{
				echo search_flights($_POST["token_id"],$_POST["origin"],$_POST["destination"],$_POST["flight_cabin_class"],$_POST["departure_time"],$_POST["arrival_time"],$_POST["adult_count"],$_POST["child_count"],$_POST["infant_count"],$_POST["journey_type"]);
			}
			break;
		
		default:
			
			break;
	}
}


////////////////////////////////////////////////////////////////////////////////////////////////////////
//***API FUNCTIONS STARTS FROM HERE***//////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////

function search_flights($token_id,$origin,$destination,$flight_cabin_class,$departure_time,$arrival_time,$adult_count,$child_count,$infant_count,$journey_type){

	$segments[]=array(
 			"Origin"=>$origin, 
            "Destination"=>$destination, 

            // 1 for All 2 for Economy 3 for PremiumEconomy 4 for Business 5 for PremiumBusiness 6 for First
            "FlightCabinClass"=>$flight_cabin_class,  

            "PreferredDepartureTime"=>$departure_time.ANY_FLIGHTS, 
            "PreferredArrivalTime"=>$arrival_time.ANY_FLIGHTS,
		);

	$sources[]="6E";

	$post=array(
			"EndUserIp"=>"192.168.10.10", 
		    "TokenId"=>$token_id, 
		    "AdultCount"=>$adult_count, 
		    "ChildCount"=>$child_count, 
		    "InfantCount"=>$infant_count, 
		    "DirectFlight"=>"false", 
		    "OneStopFlight"=>"false", 

		    //1 - OneWay 2 - Return 3 - Multi Stop 4 - AdvanceSearch 5 - Special Return
		    "JourneyType"=>$journey_type, 
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


function logout($token_agency_id,$token_member_id,$token_id){

	$post = array(
			'ClientId'=>CLIENT_ID,
			'EndUserIp'=>END_USER_IP,
			'TokenAgencyId'=>$token_agency_id,
			'TokenMemberId'=>$token_member_id,
			'TokenId'=>$token_id,
		);

	return execute($post,LOGOUT);

}


function execute($post,$url_method){

	// make json request string
	$post_json=json_encode($post);

	// create a new cURL resource
	$ch = curl_init();

	// set URL and other appropriate options
	curl_setopt($ch, CURLOPT_URL, $url_method);

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