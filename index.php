<!DOCTYPE html>
<html>

<head>
<link rel="stylesheet" href = "http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="http://bootstrapdocs.com/v3.2.0/docs/dist/js/bootstrap.min.js"></script>

<script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.2.15/angular.min.js"></script>
</head>

<body>

<form class="row form-horizontal" ng-app="" ng-controller="controller" name="projectForm" action="controller.php?cmd=createprj" method="post">
  <div class="pull-left col-md-6">
    <div class="container col-md-12">
      <h3>
	Videos
	<a class="btn btn-primary btn-lg pull-right" data-toggle="modal" data-target="#uploadDlg">Upload Files...</a>
      </h3>
      <table class="table table-striped">
        <thead>
          <th></th>
          <th>Name</th>
          <th>Size(KB)</th>
          <th></th>
        </thead>
        <tbody>
          <tr ng-repeat="video in resource.videos | orderBy: 'ctime': true">
            <td><input name="video" type="radio" value="{{ video.name }}"/></td>
            <td><a target="__new" href="{{ video.url }}"/>{{ video.name }}</a></td>
	        <td>{{ video.size }}</td>
	        <td><a href="controller.php?cmd=deleteresource&type=video&name={{ video.name }}" class="btn btn-default">Delete</a></td>
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
          <tr ng-repeat="audio in resource.audios | orderBy: 'ctime': true">
            <td><input name="audio[]" type="checkbox" value="{{ audio.name }}"/></td>
            <td><a target="__new" href="{{ audio.url }}"/>{{ audio.name }}</a></td>
            <td>{{ audio.size }}</td>
            <td><a href="controller.php?cmd=deleteresource&type=audio&name={{ audio.name }}" class="btn btn-default">Delete</a></td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="container col-md-12">
      <h3>Image</h3>
      <table class="table table-striped">
        <thead>
          <th></th>
          <th>Name</th>
          <th>Size(KB)</th>
          <th></th>
        </thead>
        <tbody>
          <tr ng-repeat="image in resource.images | orderBy: 'ctime': true">
            <td><input name="image[]" type="checkbox" value="{{ image.name }}"/></td>
            <td><a target="__new" href="{{ image.url }}"/>{{ image.name }}</a></td>
            <td>{{ image.size }}</td>
            <td><a href="controller.php?cmd=deleteresource&type=image&name={{ image.name }}" class="btn btn-default">Delete</a></td>
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
          <th>Video/Audio/Image</th>
          <th></th>
        </thead>
        <tbody>
          <tr ng-repeat="project in projects | orderBy: 'ctime': true">
            <td><a href="./project.php?name={{ project.name }}"/>{{ project.name }}</a></td>
            <td>{{ project.vfiles }}/{{ project.afiles }}/{{ project.pfiles }}</td>
            <td><a href="controller.php?cmd=deleteprj&name={{ project.name }}" class="btn btn-default">Delete</a></td>
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

<form class="row form-horizontal" name="uploadForm" enctype="multipart/form-data" action="controller.php?cmd=upload" method="post">
	<div class="modal fade" id="uploadDlg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			<h4 class="modal-title" id="myModalLabel">Add Files...</h4>
		  </div>
		  <div class="modal-body" id="filegrp">
			<div class="row">
			  <div class="col-md-8"><input type="file" name="resource[]"/></div>
			  <div class="col-md-4"><a class="btn btn-sm btn-default" onclick="$(this).parent().parent().remove();">Delete</a></div>
			</div>
			<div class="row">
			  <div class="col-md-8"><input type="file" name="resource[]"/></div>
			  <div class="col-md-4"><a class="btn btn-sm btn-default" onclick="$(this).parent().parent().remove();">Delete</a></div>
			</div>
			<div class="row">
			  <div class="col-md-8"><input type="file" name="resource[]"/></div>
			  <div class="col-md-4"><a class="btn btn-sm btn-default" onclick="$(this).parent().parent().remove();">Delete</a></div>
			</div>
			<div class="row">
			  <div class="col-md-8"><input type="file" name="resource[]"/></div>
			  <div class="col-md-4"><a class="btn btn-sm btn-default" onclick="$(this).parent().parent().remove();">Delete</a></div>
			</div>
			<div class="row">
			  <div class="col-md-8"><input type="file" name="resource[]"/></div>
			  <div class="col-md-4"><a class="btn btn-sm btn-default" onclick="$(this).parent().parent().remove();">Delete</a></div>
			</div>
		  </div>
		  <div class="modal-footer">
		    <button type="button" class="btn btn-warning pull-left" onclick="addFile();">Add File</button>
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			<button type="submit" class="btn btn-primary">Upload</button>
		  </div>
		</div>
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
function addFile() {
	$('<div class="row">' + 
	  '<div class="col-md-8"><input type="file" name="resource[]"/></div>' +
	  '<div class="col-md-4"><a class="btn btn-sm btn-default" onclick="$(this).parent().parent().remove();">Delete</a></div>' +
	  '</div>').appendTo($("#filegrp"));
}
</script>

</body>
</html>
