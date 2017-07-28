<?php

require('config.php');
require('../LiveDataAPI.php');


$API = new LiveDataAPI($api_user, $api_pass, $api_id, $api_url);

//Get stats for Jan 2017

$result = $API->Get_Statistics_Summary_for_single_campaigns($page=null, $fromDate='2017-01-01', $toDate='2017-01-31');
$decoded = json_decode($result, true);
process_results($decoded);


function process_results($decoded) {
if ($decoded != null) 
{
	echo "Response code: " . $decoded['statusCode'] . "\n";
	echo "Response status: " . $decoded['status'] . "\n";
	echo "Response message: " . $decoded['message'] . "\n";

	if (array_key_exists('result', $decoded)) 
	{
		echo "Number of contacts: " . $decoded['result']['total'] . "\n";
		echo "Current page: " . $decoded['result']['page'] . "\n";
		echo "Total number of pages: " . $decoded['result']['totalPages'] . "\n";

		echo "Prev page url: " .  $decoded['result']['links']['urlPreviousPage'] . "\n";
		echo "Next page url: " .  $decoded['result']['links']['urlNextPage'] . "\n";

		print_r($decoded['result']['data']);
	}
}
else {
	echo "Response is not a json\n";
}
}


?>
