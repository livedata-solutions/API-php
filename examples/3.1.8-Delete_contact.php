<?php

require('config.php');
require('../LiveDataAPI.php');


$API = new LiveDataAPI($api_user, $api_pass, $api_id, $api_url);



//Delete contact

$contact_email = 'scarlett@example.com';

$result = $API->Delete_Contact($contact_email);

$decoded = json_decode($result, true);

if ($decoded != null) 
{
	echo "Response code: " . $decoded['statusCode'] . "\n";
	echo "Response status: " . $decoded['status'] . "\n";
	echo "Response message: " . $decoded['message'] . "\n";
}
else {
	echo "Response is not a json\n";
}


?>
