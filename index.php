<!DOCTYPE html>
<html>

<head>
<link rel="stylesheet" href = "http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.2.15/angular.min.js"></script>
</head>

<body>

<form class="row" ng-app="" ng-controller="controller" name="projectForm" action="controller.php?cmd=createprj" method="post">
  <div class="pull-left col-md-6">
    <div class="container col-md-12">
      <h3>Videos</h3>
      <table class="table table-striped">
        <thead>
          <th></th>
          <th>Name</th>
          <th>Size(KB)</th>
          <th></th>
        </thead>
        <tbody>
          <tr ng-repeat="video in resource.videos">
            <td><input name="video" type="radio" value="{{ video.name }}"/></td>
            <td><a target="__new" href="{{ video.url }}"/>{{ video.name }}</a></td>
	        <td>{{ video.size }}</td>
	        <td><button class="btn btn-default">Delete</button></td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="container col-md-12">
      <h3>Audio</h3>
      <table class="table table-striped">
        <thead>
          <th></th>
          <th>Name</th>
          <th>Size(KB)</th>
          <th></th>
        </thead>
        <tbody>
          <tr ng-repeat="audio in resource.audios">
            <td><input name="audio[]" type="checkbox" value="{{ audio.name }}"/></td>
            <td><a target="__new" href="{{ audio.url }}"/>{{ audio.name }}</a></td>
            <td>{{ audio.size }}</td>
            <td><button class="btn btn-default">Delete</button></td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="container col-md-12">
      <h3>Photo</h3>
      <table class="table table-striped">
        <thead>
          <th></th>
          <th>Name</th>
          <th>Size(KB)</th>
          <th></th>
        </thead>
        <tbody>
          <tr ng-repeat="photo in resource.photos">
          	<td><input name="photo[]" type="checkbox" value="{{ photo.name }}"/></td>
          	<td><a target="__new" href="{{ photo.url }}"/>{{ photo.name }}</a></td>
          	<td>{{ photo.size }}</td>
          	<td><button class="btn btn-default">Delete</button></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="pull-right col-md-6">
    <div class="container col-md-12">
      <h3>Workspace</h3>
      <table class="table table-striped">
        <thead>
          <th>Name</th>
          <th>Number of Files</th>
          <th></th>
        </thead>
        <tbody>
          <tr ng-repeat="project in projects">
          	<td><a href="./project.php?name={{ project.name }}"/>{{ project.name }}</a></td>
          	<td>{{ project.nof }}</td>
          	<td><button class="btn btn-default">Delete</button></td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="container col-md-12">
	  <fieldset>
		<legend>Project Information</legend>
		<div class="control-group">
		  <label class="control-label" for="name">Name</label>
		  <div class="controls">
		    <input type="text" name="name" placeholder="Project Name" />
		  </div>
	    </div>
		<div class="control-group">
		  <label class="control-label" for="vdur">Clip Duration(sec)</label>
		  <div class="controls">
		    <input type="text" name="vdur" placeholder="Duration(sec)"/>
			<button type="submit" class="btn">Create</button>
		  </div>
	    </div>
	  </fieldset>
    </div>
  </div>
</form>

<script>
function controller($scope,$http) {
  $http.get("./controller.php?cmd=list")
  .success(function(response) {$scope.resource = response;});

  $http.get("./controller.php?cmd=project")
  .success(function(response) {$scope.projects = response;});
}

</script>

</body>
</html>
