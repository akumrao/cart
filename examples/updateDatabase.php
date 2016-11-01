<?php
/*$data = file_get_contents("php://input");

$objData = json_decode($data);
ini_set("log_errors", 1);
ini_set("error_log", "/tmp/php-error.log");

$output = print_r($objData,1);
error_log( $output );
*/

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

//$folder_id = $objData->data; //'0B4MinpbTTsM-ejlBNWV0YWszbmc';
//$folder_name = $objData->name;  // images

//$folder_id = '0B4MinpbTTsM-SGNjc0VDcUZJcFk';
//$folder_name = 'images';



$optParamsFolder = array(
		'q' =>  "mimeType = 'application/vnd.google-apps.folder' and trashed = false",
		'pageSize' => 200,
		'fields' => "nextPageToken, files(id, name)"
);
$resultsFolder = $service->files->listFiles($optParamsFolder);

$databasefiles = array();

foreach ($resultsFolder->getFiles() as $folder)
{
	$folder_id = $folder->getId();
	$folder_name = $folder->getName();
	

	$optParams = array(
			"q" => "'$folder_id' in parents and mimeType != 'application/vnd.google-apps.folder'",
			'pageSize' => 800,
			'fields' => "nextPageToken, files(id, name, properties)"
	);
	$results = $service->files->listFiles($optParams);
	
//     	"productid": "7B8BCC0B-2616-4A72-89E2-DA51E2FDD935",
//      "imagename": "freemovies.jpg",
//      "storeid": "7cc6cb94-0938-4675-b84e-6b97ada53978",
//      "categoryname": "cool",
//       "productname": "",
//       "imageurl": null,
//       {"key":"sku", "value":"barcode"},


	foreach ($results->getFiles() as $file)
	{
		$thumbnail= "https://drive.google.com/thumbnail?authuser=0&id=" . $file->getId();
		
		$array1 = array(
				"productid" => $file->getId(),
				"imagename" => $thumbnail,
				"storeid" => $folder_id,
				"categoryname" => $folder_name,
				"productname" => $file->getName(),
				"imageurl" => "",
				"sku"=> $file->getId()
		);
		
	
		$fpro = $file->getProperties();
		if($fpro)
		{
			$array2 = $fpro + $array1;
			array_push($databasefiles,$array2);
		}
	
	}// end file loop


}//end folder looop



$result = file_put_contents('ac_products/products.js', json_encode( $databasefiles, JSON_PRETTY_PRINT));

if($result === false) {
	echo "Error";
} else {
	echo "All good, $result bytes written";
}



if($result === false) {
	echo "Error";
} else {
	echo "All good, $result bytes written";
}

?>