<?php
// The request is a JSON request.
// We must read the input.
// $_POST or $_GET will not work!

$folderId = $_POST['folder'];



ini_set("log_errors", 1);
ini_set("error_log", "/tmp/php-error.log");
//$output = print_r($properties,1);
//error_log( $output );
//error_log( $_FILES["file"]["name"] );
//error_log(basename($_FILES["file"]["name"]));


//error_log( $outfolder );
//error_log( $output );

//$target_dir = "/var/tmp/";
$target_file = $_FILES["file"]["name"];
$source_file = $_FILES["file"]["tmp_name"];
$source_type = $_FILES["file"]["type"];

//$target_dir . basename($_FILES["file"]["name"]);
//move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);

$arrProp = array
(
"header" => "<b> header </b>",
"shortdesc" =>"",
"description" =>"",
"link" =>"",
"linktext" => "try it",
"carousel" => 1,
"carousel_caption" =>"",
"tube" => "youtube",
"videoid" =>"",
"showvideo" => false,
"unitprice" => 0,
"saleprice" => 0,
"unitsinstock" => 1,
"unitsonorder" => 1,
"reorderlevel" => 1,
"expecteddate" => "1970-01-01T00:00:00.000Z",
"discontinued" => false,
"notes" =>"",
"faux" =>"",
"sortorder" => 1
);



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

$fileMetadata = new Google_Service_Drive_DriveFile(array(
		'name' => $target_file,
		'parents' => array($folderId)
));
//$content = file_get_contents();

$result = $service->files->create(
		$fileMetadata,
		array(
				'data' => file_get_contents($source_file),
				'mimeType' =>$source_type 
		)
);

/////////////////////
$optParams = array(
		'fields' => "properties"
);

$arrProp['description'] = $result->name;
$arrProp[header]="<b> ". $result->name . " <b>";
$array = $arrProp ;


$fileId =$result->id;


$fileMetadata = new Google_Service_Drive_DriveFile(array(
		'properties' => $array));
$file = $service->files->update($fileId, $fileMetadata, array(
		'fields' => 'id, properties'));
/////////////////////


echo $result->name;

?>