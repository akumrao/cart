var myApp = angular.module('myApp', []);

function toArrayarr ( objarr) {
	var rv = [] ;
	for (var mykey in objarr) {
		var myvalue = objarr[mykey];

		rv.push({
			key: mykey, 
			value: myvalue
		});

	}

	return rv;
}

function toObjectarr ( arr) {
	var rv = {} ;

	for (var i = 0; i < arr.length; i++) 
	{
		rv[ arr[i].key ]=arr[i].value;
	}

	return rv;
}

myApp.directive('ngFileModel', ['$parse', function ($parse) {
	return {
		restrict: 'A',
		link: function (scope, element, attrs) {
			var model = $parse(attrs.ngFileModel);
			var isMultiple = attrs.multiple;
			var modelSetter = model.assign;
			element.bind('change', function () {
				var values = [];
				angular.forEach(element[0].files, function (item) {
					var value = {
							// File Name 
							name: item.name,
							//File Size 
							size: item.size,
							//File URL to view 
							url: URL.createObjectURL(item),
							// File Input Value 
							_file: item
					};
					values.push(value);
				});
				scope.$apply(function () {
					if (isMultiple) {
						modelSetter(scope, values);
					} else {
						modelSetter(scope, values[0]);
					}
				});
			});
		}
	};
}]);


function fileCtrl($scope, $http) {
//	"productid": "7B8BCC0B-2616-4A72-89E2-DA51E2FDD935",
//	"imagename": "freemovies.jpg",
//	"storeid": "7cc6cb94-0938-4675-b84e-6b97ada53978",	
//	"categoryname": "cool",
//	"productname": "",
//	"imageurl": null,

	$scope.filedynamic = 10;
	$scope.filemax = 100;
	var properties=[
	                {"key":"header","value":"<b> header </b>"},
	                {"key":"shortdesc", "value": ""},
	                {"key":"description","value": ""},
	                {"key":"link","value": ""},
	                {"key":"linktext","value": "try it"},
	                {"key":"carousel","value": true},
	                {"key":"carousel_caption","value": ""},
	                {"key":"tube","value": "youtube"},
	                {"key":"videoid","value": ""},
	                {"key":"showvideo","value": "false"},
	                {"key":"unitprice","value": 0},
	                {"key":"saleprice","value": 0},
	                {"key":"unitsinstock","value": 1},
	                {"key":"unitsonorder","value": 1},
	                {"key":"reorderlevel","value": 1},
	                {"key":"expecteddate","value": "1970-01-01T00:00:00.000Z"},
	                {"key":"discontinued","value": "false"},
	                {"key":"notes","value": ""},
	                {"key":"faux","value": false},
	                {"key":"sortorder","value": 1},
	                ];

	$scope.disablepropertes = true;

	$scope.filechange = function() {
		$scope.showProperties = true;
		// Create the http post request
		// the data holds the keywords
		// The request is a JSON request.
		$scope.propertresult = "";

		$http.post('getproperties.php', { "data" : $scope.selectedFile.id}).
		success(function(data, status) {
			//$scope.data = data;
			//$scope.result = data; // Show result from server in our <pre></pre> element
			$scope.properties = toArrayarr(data);

			if( (data == "null") ||  ( data.length == 0) )
			{
				$scope.properties = properties;

			}
			$scope.propertresult = "Properties listed abvoe";	                        
		})
		.
		error(function(data, status) {
			$scope.propertresult = status || "Request failed";			
		});
	};

	$scope.restProperties = function() {

		$http.post('addproperties.php', { "data" : toObjectarr(properties) , "fileid" : $scope.selectedFile.id }).
		success(function(data, status) {


			if( (data == "null") ||  ( data.length == 0) )
			{
				$scope.properties = [];
				$scope.propertresult = status || "Request failed";	
			}else
			{
				$scope.propertresult = "Reset successfully";	
				$scope.properties = properties;
			}

		})
		.
		error(function(data, status) {
			$scope.propertresult = status || "Request failed";	
		});

	};

	$scope.saveProperties = function() {
		//alert($scope.contents[0].value);

		$http.post('addproperties.php', { "data" : toObjectarr($scope.properties) , "fileid" : $scope.selectedFile.id }).
		success(function(data, status) {
			$scope.properties = toArrayarr(data);

			if( (data == "null") ||  ( data.length == 0) )
			{
				$scope.properties = [];
			}
			$scope.propertresult = "Saved successfully";	               
		})
		.
		error(function(data, status) {
			$scope.propertresult = status || "Request failed";	
		});

	};

////////get fodler list	
	$http.get('listfolder.php')
	.then(function(response) {
		$scope.cars = response.data;
	});

//	add new folder
	$scope.addfolder = function() {

		$scope.folderresult ="";
		// Create the http post request
		// the data holds the keywords
		// The request is a JSON request.
		$http.post('addfolder.php', { "data" : $scope.keywords}).
		success(function(data, status) {
			$scope.status = status;
			//$scope.data = data;
			//$scope.result = data; // Show result from server in our <pre></pre> element
			$scope.cars = data;
			$scope.folderresult = "Added folder " + $scope.keywords;	                  

		})
		.
		error(function(data, status) {
			//$scope.data = data || "Request failed";
			$scope.folderresult = status + data || "Request failed";			
		});
	};

	$scope.folderchange = function() {

		$scope.showProperties = false;
		$scope.folderresult = "";
		// Create the http post request
		// the data holds the keywords
		// The request is a JSON request.
		$http.post('listfiles.php', { "data" : $scope.selectedFolder.id}).
		success(function(data, status) {
			$scope.status = status;
			//$scope.data = data;
			//$scope.result = data; // Show result from server in our <pre></pre> element
			$scope.carfiles = data;
		})
		.
		error(function(data, status) {
			$scope.data = data || "Request failed";
			$scope.status = status;			
		});
	};

////////////////////////////////////////////////////////to load file begin

	$scope.uploadFile = function(){

		$("body").css("cursor", "progress")
		var elem = document.getElementById("fileBar");   
		var inc = 1;
		var width = 1;

		$scope.statusmyfile = "";
		for (i = 0; i < $scope.files.length; i++) 
		{
			var fd = new FormData();
			fd.append('file', $scope.files[i]._file);
			fd.append('folder',  $scope.selectedFolder.id);
			$http.post("addfile.php", fd, {
				transformRequest: angular.identity,
				headers: {'Content-Type': undefined,'Process-Data': false}
			})
			.success(function(data, status){
				console.log("Success");
				$scope.statusmyfile = "Status  :" + status + " File :" + data ;

				if(inc == $scope.files.length )
				{
					$scope.folderchange();
					$("body").css("cursor", "default");
				}

				width = 100*inc/$scope.files.length  ;

				elem.style.width = width + '%'; 

				inc++;
			})
			.error(function(data, status){
				console.log("Success");
				$scope.statusmyfile = "Error code:" +status + "," + data ;
				$("body").css("cursor", "default");

			});

		}

	};

////////////////////////////////////////////////////////update database end


	$scope.clickFile = function(){  
		$scope.statusmyfile = "";
		$scope.showProperties = false;

	};
///////////////////////////////////////////////////////////to load file end
////////////////////////////////////////////////////////update database begin

	$scope.UpdateDatabase = function(){  
		$("body").css("cursor", "progress")	
		var elem = document.getElementById("databaseBar");   
		var width = 1;
		var id = setInterval(frame, 200);
		function frame() {
			if (width >= 100) {
				clearInterval(id);
				$("body").css("cursor", "default");
			} else {
				width++; 
				elem.style.width = width + '%'; 
			}
		}

		$scope.updatedatabaseresult ="";
		// Create the http post request
		// the data holds the keywords
		// The request is a JSON request.
		$http.post('updateDatabase.php', { "data" : "", "name" : "" }).
		success(function(data, status) {
			$scope.status = status;
			//$scope.data = data;
			//$scope.result = data; // Show result from server in our <pre></pre> element
			width = 100;
			elem.style.width = width + '%'; 
			clearInterval(id);
			$("body").css("cursor", "default");
			$scope.updatedatabaseresult = "Saved database";	                  

		})
		.
		error(function(data, status) {
			//$scope.data = data || "Request failed";
			$("body").css("cursor", "default");
			$scope.updatedatabaseresult = status  || "Request failed";			
		});

	};

}

