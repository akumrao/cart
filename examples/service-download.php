<?php
/*
 * Copyright 2013 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

include_once __DIR__ . '/google/vendor/autoload.php';
include_once "templates/base.php";

echo pageHeader("Service Account Access");

$date1 = date(DATE_RFC3339, strtotime('+2 minutes'));

print "date1" .  $date1;

$date2 = date(DATE_RFC3339);

print "date2" .  $date2;




/************************************************
  Make an API request authenticated with a service
  account.
 ************************************************/

$client = new Google_Client();

/************************************************
  ATTENTION: Fill in these values, or make sure you
  have set the GOOGLE_APPLICATION_CREDENTIALS
  environment variable. You can get these credentials
  by creating a new Service Account in the
  API console. Be sure to store the key file
  somewhere you can get to it - though in real
  operations you'd want to make sure it wasn't
  accessible from the webserver!
  Make sure the Books API is enabled on this
  account as well, or the call will fail.
 ************************************************/

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

//'q' =>  "mimeType = 'application/vnd.google-apps.folder' and trashed = false",

$optParams = array(
		
		'pageSize' => 200,
		'fields' => "nextPageToken, files(id, name, size, properties, mimeType, description, webContentLink, thumbnailLink)"
);
$results = $service->files->listFiles($optParams);

if (count($results->getFiles()) == 0) {
	print "No files found.\n";
} else {
	print "Files:\n";

	print count($results->getFiles());



	foreach ($results->getFiles() as $file) {
		//printf("%s (%s)\n", $file->getName(), $file->getId());


		echo "<br>";
	    print "name: " . $file->getName();
		
		echo "<br>";
		
		print "id: " . $file->getId();
		
		echo "<br>";

		print "Size: " . $file->getSize();
		
		echo "<br>";
		
		print "version: " . $file->getVersion();
		
		echo "<br>";
		
		print "weblink: " . $file->getWebContentLink();
		
		echo "<br>";
		
		print "thumnail: " . $file->getThumbnailLink();
		
		echo "<br>";
		
		print "getMimeType: " . $file->getMimeType();
		
		echo "<br>";
		
		print "Description: " . $file->getDescription();
		
		echo "<br>";
		
		print "getProperties: " . $file->getProperties();
		
		echo "<br>";
		
		$fpro = $file->getProperties();
		
		
		echo "<pre>";
	//	print_r($results);
	echo "</pre>";
    }
    
    $array = array(
    		"foo" => "bar",
    		"bar" => "foo",
    );
    /*
    $fileId = '0B4MinpbTTsM-MDk3UXdrR0MySTQ';
    $fileMetadata = new Google_Service_Drive_DriveFile(array(
    		'properties' => $array));
    $file = $service->files->update($fileId, $fileMetadata, array(
    		'fields' => 'id, properties'));
    print "getProperties: " . $file->getProperties();
    printf("getProperties time: %s\n", $file->getProperties());
    */
    
    
    /*
    $fileId = '0B4MinpbTTsM-MDk3UXdrR0MySTQ'
    $fileMetadata = new Google_Service_Drive_DriveFile(array(
    		'modifiedTime' => date('Y-m-d\TH:i:s.uP')));
    $file = $service->files->update($fileId, $fileMetadata, array(
    		'fields' => 'id, modifiedTime'));
    printf("Modified time: %s\n", $file->modifiedTime);
    */
}

return ;















DEFINE("TESTFILE", 'testfile-small.txt');


$fileMetadata = new Google_Service_Drive_DriveFile(array(
		'name' => 'Invoices',
		'mimeType' => 'application/vnd.google-apps.folder'));
$fileF = $service->files->create($fileMetadata, array(
		'fields' => 'id'));
printf("Folder ID: %s\n", $fileF->id);


$folderId = $fileF->id;
$fileMetadata = new Google_Service_Drive_DriveFile(array(
		'name' => 'ravind.txt',
		'parents' => array($folderId)
));
$content = file_get_contents('files/photo.jpg');

$result = $service->files->create(
		$fileMetadata,
		array(
				'data' => file_get_contents(TESTFILE),
				'mimeType' => 'text/plain'
		)
);

print "name" . $result->name;


$dd = $client->getAccessToken();

 $optParams = array(
 		'pageSize' => 20,
 		'fields' => "nextPageToken, files(id, name, webContentLink)"
 );
$results = $service->files->listFiles($optParams);

if (count($results->getFiles()) == 0) {
print "No files found.\n";
} else {
print "Files:\n";

print count($results->getFiles());



foreach ($results->getFiles() as $file) {
printf("%s (%s)\n", $file->getName(), $file->getId());

try {
	

	$content =  $service->files->get($file->getId(), array(	'alt' => 'media'));
	$content2222 = $content->getStatusCode();
	
	$content22 = $content->getReasonPhrase();
	
	$content122 = $content->getProtocolVersion();
	$content252 = $content->getBody();
	$intx = 1;
	$contentsdddd = $content252->getContents(); // returns all the contents
	
	//$co =    $service->files->delete($file->getId());
} catch (Exception $e) {
	print "Error: " . $e->getMessage();
}

$weblink =  $file->getWebContentLink();

print "Size: " . $file->getSize();
print "version: " . $file->getVersion();
print "weblink: " . $file->getWebContentLink();

}

$results = $service->files->listFiles($optParams);

print count($results->getFiles());


echo "<pre>";
//	print_r($results);
echo "</pre>";
}



/************************************************
  We're just going to make the same call as in the
  simple query as an example.
 ************************************************/


  if (!file_exists(TESTFILE)) {
    $fh = fopen(TESTFILE, 'w');
    fseek($fh, 1024 * 1024);
    fwrite($fh, "!", 1);
    fclose($fh);
  }

  $tmpString = file_get_contents(TESTFILE);

  // Now lets try and send the metadata as well using multipart!
  $file = new Google_Service_Drive_DriveFile();
  $file->setName("arvind");
  $result = $service->files->create(
      $file,
      array(
        'data' => file_get_contents(TESTFILE),
        'mimeType' => 'text/plain'
      )
  );

  print "result->id" . $result->id;
  
  $domainPermission = new Google_Service_Drive_Permission(array(
  		'type' => 'anyone',
  		'role' => 'reader',
  		'expirationTime' => $date1,
  ));
  

  $request = $service->permissions->create(
  		$result->id, $domainPermission, array('fields' => 'id'));
  
  

  
  /**
   *   $file = new Google_Service_Drive_DriveFile();
  $file->setName("Hello");
  $result = $service->files->create(
      $file,
      array(
        'data' => file_get_contents(TESTFILE),
        'mimeType' => 'application/octet-stream',
        'uploadType' => 'multipart'
      )
  );

   * Download a file's content.
   *
   * @param Google_Service_Drive $service Drive API service instance.
   * @param File $file Drive File instance.
   * @return String The file's content if successful, null otherwise.
   */
  
 
  
  $pageToken = null;
  do {
  	$response = $service->files->listFiles();
  	foreach ($response->files as $file) {
  		printf("Found file: %s (%s)\n", $file->name, $file->id);
  	}
  } while ($pageToken != null);
  
  
  
  
  //
   /* 
  function downloadFile($service, $file) {
  	$downloadUrl = $file->getDownloadUrl();
  	if ($downloadUrl) {
  		$request = new Google_Http_Request($downloadUrl, 'GET', null, null);
  		$httpRequest = $service->getClient()->getAuth()->authenticatedRequest($request);
  		if ($httpRequest->getResponseHttpCode() == 200) {
  			return $httpRequest->getResponseBody();
  		} else {
  			// An error occurred.
  			return null;
  		}
  	} else {
  		// The file doesn't have any content stored on Drive.
  		return null;
  	}
  }
  
*/
      
  
?>

<div class="box">
  <img src= "<?= $weblink ?>" alt="Smiley face"> 
  <h3>Results Of Drive List:</h3>
  <?php foreach ($results as $item): ?>
    <?= $item->name ?><br />
  <?php endforeach ?>
  
  <div class="shortened">
    <p>Your call was successful! Check your drive for the following files:</p>
    <ul>
      <li><a href="https://drive.google.com/open?id=<?= $result->id ?>" target="_blank"><?= $result->name ?></a></li>

    </ul>
  </div>

</div>

<?= pageFooter(__FILE__) ?>

