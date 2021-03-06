<?php

require('config.php');
require('../LiveDataAPI.php');


$API = new LiveDataAPI($api_user, $api_pass, $api_id, $api_url);



//Add contact

$contact_email = 'scarlett@example.com';

$several_attributes = array(
	'title' => 'Mrs',
	'firstname' => 'Scarlett',
	'lastname' => 'Johansson',
	'birthdate' => '1984-11-22'
);
 

$result = $API->Add_Edit_Contact($contact_email, $several_attributes);

$decoded = json_decode($result, true);

if ($decoded != null) 
{
	echo "Response code: " . $decoded['statusCode'] . "\n";
	echo "Response status: " . $decoded['status'] . "\n";
	echo "Response message: " . $decoded['message'] . "\n";

	if (array_key_exists('links', $decoded) && array_key_exists('contact_url', $decoded['links'])) 
	{
		echo "Url to the contact: " . $decoded['links']['contact_url'] . "\n";
	}
}
else {
	echo "Response is not a json\n";
}


?>
