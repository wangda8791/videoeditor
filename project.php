<!DOCTYPE html>
<html>

<head>
<link rel="stylesheet" href = "http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.2.15/angular.min.js"></script>
</head>

<body>
<center><h1>Workspace (<?php echo $_REQUEST["name"]; ?>)</h1></center>
<div class="row" ng-app="" ng-controller="controller">
  <div class="container col-md-12">
    <h3>Video Clip</h3>
    <button class="btn btn-success col-md-2" ng-repeat="video in resource.videos" onclick="select('video', '{{ video.name }}');">{{ video.name }}</button>
  </div>
  <div class="container col-md-12">
    <h3>Audio</h3>
    <button class="btn btn-info col-md-2" ng-repeat="audio in resource.audios" onclick="select('audio', '{{ audio.name }}');">{{ audio.name }}</button>
  </div>
  <div class="container col-md-12">
    <h3>Photo</h3>
    <button class="btn btn-warning col-md-2" ng-repeat="photo in resource.photos" onclick="select('photo', '{{ photo.name }}');">{{ photo.name }}</button>
  </div>
  <hr/>
</div>

<script>
function controller($scope,$http) {
  $http.get("./controller.php?cmd=workspace&name=<?php echo $_REQUEST['name']; ?>")
  .success(function(response) {$scope.resource = response;});
}

function select($type, $name) {
	alert($type + "," + $name);
	return false;
}
</script>

</body>
</html>
