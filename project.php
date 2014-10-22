<!DOCTYPE html>
<html>

<head>
<link rel="stylesheet" href = "http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.2.15/angular.min.js"></script>
</head>

<body>
<center><h1>Workspace (<?php echo $_REQUEST["name"]; ?>)</h1></center>
<div ng-app="" ng-controller="controller">
<div class="row container col-md-9">
  <div class="container col-md-12">
    <h3>Video Clip</h3>
      <div ng-repeat="video in resource.videos" class="col-md-2">
        <button class="btn btn-success col-md-12" ng-click="select('video', video.name);">{{ video.name }}</button>
      </div>
  </div>
  <div class="container col-md-12">
    <h3>Audio</h3>
      <div ng-repeat="audio in resource.audios" class="col-md-2">
        <button class="btn btn-info col-md-12" ng-click="select('audio', audio.name);">{{ audio.name }}</button>
      </div>
  </div>
  <div class="container col-md-12">
    <h3>Image</h3>
      <div ng-repeat="image in resource.images" class="col-md-2">
        <button class="btn btn-warning col-md-12" ng-click="select('image', image.name);">{{ image.name }}</button>
      </div>
  </div>
</div>
<div class="row container col-md-3">
  <div class="container col-md-12">
    <h3>
      Resource
      <button class="btn btn-danger pull-right" ng-click="generate();">Generate</button>
    </h3>
    <div id="resource_panel"></div>
  </div>
</div>
</div>

<script>

function controller($scope,$http) {
  $http.get("./controller.php?cmd=workspace&name=<?php echo $_REQUEST['name']; ?>")
  .success(function(response) {$scope.resource = response;});
  
  $scope.workspace = {profile:[]};
  $scope.selected = -1;

  $scope.select = function($type, $name) {
    var json_resource = {'type':$type, 'name':$name};
    var dom_resource = '';
    if ($type == 'video')
      dom_resource = '<button class="btn btn-success col-md-12">' + $name + '</button>';
    if ($type == 'audio')
      dom_resource = '<button class="btn btn-info col-md-12">' + $name + '</button>';
    if ($type == 'image')
      dom_resource = '<button class="btn btn-warning col-md-12">' + $name + '</button>';
    
    if ($scope.selected == -1) {
      $scope.workspace.profile.push(json_resource);
      document.getElementById("resource_panel").innerHTML += dom_resource;
    } else {
      workspace.profile.insert(selected, json_resource);
      dom_resource.insertAfter($("#resource_panel").find('button:nth(' + selected + ')'));
    }
     
    return false;
  }

  $scope.generate = function() {
    var res = $http.post('./controller.php?cmd=generate&prjname=<?php echo $_REQUEST['name']; ?>', $scope.workspace.profile);
    res.success(function(data, status, headers, config) {
			alert(data);
      		  $scope.message = data;
		});
		res.error(function(data, status, headers, config) {i
		  alert( "failure message: " + JSON.stringify({data: data}));
		});
  }

  $scope.fillresourcepanel = function(profile) {
    for (var i=0; i<profile.length; i++) {
      var dom_resource = '';
      var $type = profile[i].type;
      var $name = profile[i].name;
      if ($type == 'video')
        dom_resource = '<button class="btn btn-success col-md-12">' + $name + '</button>';
      if ($type == 'audio')
        dom_resource = '<button class="btn btn-info col-md-12">' + $name + '</button>';
      if ($type == 'image')
        dom_resource = '<button class="btn btn-warning col-md-12">' + $name + '</button>';

      document.getElementById("resource_panel").innerHTML += dom_resource;
    }
  }

  loadproject($scope, $http);
}

function loadproject($scope, $http) {
  $http.get("./controller.php?cmd=loadproject&name=<?php echo $_REQUEST['name']; ?>")
  .success(function(response) { $scope.fillresourcepanel(response);});
}

</script>

</body>
</html>
