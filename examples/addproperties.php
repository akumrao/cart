<?php
// The request is a JSON request.
// We must read the input.
// $_POST or $_GET will not work!

$data = file_get_contents("php://input");

$objData = json_decode($data);

$output = print_r($objData->data,1);
ini_set("log_errors", 1);
ini_set("error_log", "/tmp/php-error.log");
error_log( $output );

$output = print_r($objData->fileid,1);
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
$client->setSubject('tester.sprintwell@gmail.com');
$client->addScope('https://www.googleapis.com/auth/drive');
$client->setSubject('tester.sprintwell@gmail.com');

$service = new Google_Service_Drive($client);

$optParams = array(
			'fields' => "properties"
);

$array =  $objData->data ;
		


$fileId = $objData->fileid ;


$fileMetadata = new Google_Service_Drive_DriveFile(array(
		'properties' => $array));
$file = $service->files->update($fileId, $fileMetadata, array(
		'fields' => 'id, properties'));
/*
print "getProperties: " . $file->getProperties();
printf("getProperties time: %s\n", $file->getProperties());

//https://developers.google.com/drive/v3/reference/files/get#try-it

$file = $service->files->get('0B4MinpbTTsM-MDk3UXdrR0MySTQ', $optParams);

$fpro = $file->getProperties();

$results = $file->getName();
*/

echo json_encode($file->getProperties());


?>