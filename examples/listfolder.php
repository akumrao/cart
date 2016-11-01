<?php


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

$service = new Google_Service_Drive($client);


$optParams = array(
		'q' =>  "mimeType = 'application/vnd.google-apps.folder' and trashed = false",
		'pageSize' => 200,
		'fields' => "nextPageToken, files(id, name, size, properties, mimeType, description, webContentLink, thumbnailLink)"
);
$results = $service->files->listFiles($optParams);

$cars = array();

	foreach ($results->getFiles() as $file) 
	{
		
		$array1 = array(
				"id" => $file->getId(),
				"name" => $file->getName()
		);
		
		array_push($cars,$array1);
    }

echo json_encode($cars);

?>

