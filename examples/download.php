<?php
// The request is a JSON request.
// We must read the input.
// $_POST or $_GET will not work!

$data = file_get_contents("php://input");
$objData = json_decode($data);

ini_set("log_errors", 1);
ini_set("error_log", "/tmp/php-error.log");

$output = print_r($objData->data,1);
error_log( $output );

include_once __DIR__ . '/google/vendor/autoload.php';
include_once "templates/base.php";


/************************************************
 Make an API request authenticated with a service
account.
************************************************/

$client = new Google_Client();



if ($credentials_file = checkServiceAccountCredentialsFile()) {
	// set the location manually
	$client->setAuthConfig($credentials_file);
} elseif (getenv('GOOGLE_APPLICATION_CREDENTIALS')) {
	// use the application default credentials
	$client->useApplicationDefaultCredentials();
} else {
	echo missingServiceAccountDetailsWarning();
	return;
}

$client->setApplicationName("sprintwell");
$client->addScope('https://www.googleapis.com/auth/drive');
$client->setSubject('tester.sprintwell@gmail.com');

$service = new Google_Service_Drive($client);
/*
$client->setClientId("116719030591544390701");

if (file_exists("token")) {
	$accessToken = json_decode(file_get_contents("token"), true);
	if($accessToken)
	{
		$client->setAccessToken($accessToken);
	}
}

if($client->isAccessTokenExpired()) {
	$client->refreshTokenWithAssertion();
	$dd = $client->getAccessToken();
	$dd['created'] = time();

	$result = file_put_contents('token', json_encode($dd));

	if($result === false) {
		echo "Error";
	} else {
		echo "All good, $result bytes written";
	}
}
*/

$optParams = array(
		'fields' => "webContentLink, name"
);

$domainPermission = new Google_Service_Drive_Permission(array(
		'type' => 'anyone',
		'role' => 'reader'
));



$arrfiles = array();

foreach ($objData->data as $fileId)
{

	
	$request = $service->permissions->create(
			$fileId, $domainPermission, array('fields' => 'id'));
	
	
	$file = $service->files->get($fileId, $optParams);


	$array1 = array(
			"url" => $file->getWebContentLink(),
			"name" => $file->getName()
	);

	array_push($arrfiles,$array1);
}

$output = print_r($arrfiles,1);
error_log( $output );


echo json_encode($arrfiles);


?>
