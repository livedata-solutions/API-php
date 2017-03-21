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
	'birtdate' => '1984-11-22'
);


$x = $API->Add_Edit_Contact($contact_email, $several_attributes);

print_r($x);


?>
