<!DOCTYPE html>
<html>

<head>
<link rel="stylesheet" href = "http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.2.15/angular.min.js"></script>
</head>

<body>
<center><h1>Workspace (<?php echo $_REQUEST["name"]; ?>)</h1></center>
<center><h2><div id="result_panel"></div></h2></center>
<div ng-app="" ng-controller="controller">
<div class="row container col-md-9">
  <div class="container col-md-12">
    <h3>Video Clip</h3>
      <div ng-repeat="video in resource.videos | orderBy: 'name'" class="col-md-2">
        <button class="btn btn-success col-md-12" ng-click="select('video', video.name);">{{ video.name }}</button>
      </div>
  </div>
  <div class="container col-md-12">
    <h3>Audio</h3>
      <div ng-repeat="audio in resource.audios | orderBy: 'name'" class="col-md-2">
        <button class="btn btn-info col-md-12" ng-click="select('audio', audio.name);">{{ audio.name }}</button>
      </div>
  </div>
  <div class="container col-md-12">
    <h3>Image</h3>
      <div ng-repeat="image in resource.images | orderBy: 'name'" class="col-md-2">
        <button class="btn btn-warning col-md-12" ng-click="select('image', image.name);">{{ image.name }}</button>
      </div>
  </div>
</div>
<div class="row container col-md-3">
  <div class="container col-md-12">
    <h3>
      Resource
      <button class="btn btn-danger pull-right" ng-click="generate();">Generate</button>
      <button class="btn btn-warning pull-right" style="margin-right:10px" ng-click="save();">Save</button>
    </h3>
    <div id="resource_panel"></div>
  </div>
</div>
</div>

<div class="modal fade" id="progressing" tabindex="-1" role="dialog" aria-hidden="true" style="background-color:gray;opacity:0.5;">
	<div style="margin-left:40%; margin-top:20%; color:yello; font-size: 40px;">Generating Video...</div>
</div>

<script>

var $global_scope = null;

function controller($scope,$http) {
  $global_scope = $scope;
  $http.get("./controller.php?cmd=workspace&name=<?php echo $_REQUEST['name']; ?>")
  .success(function(response) {$scope.resource = response;});
  
  $scope.workspace = {profile:[]};
  $scope.selected = -1;

  $scope.select = function($type, $name) {
    var json_resource = {'type':$type, 'name':$name};
    var dom_resource = '';
    var btn_type = '';
    
    if ($type == 'video')
      $btn_type = "btn-success";
    if ($type == 'audio')
      $btn_type = "btn-info";
    if ($type == 'image')
      $btn_type = "btn-warning";

    dom_resource = '<button class="btn ' + $btn_type + ' col-md-12"><a class="btn btn-danger btn-xs pull-left" href="#" onclick="up(\'' + $type + '\', \'' + $name + '\', this);"><i class="glyphicon glyphicon-arrow-up"></i></a><a class="btn btn-danger btn-xs pull-left" href="#" onclick="down(\'' + $type + '\', \'' + $name + '\', this);"><i class="glyphicon glyphicon-arrow-down"></i></a>' + $name + '<a class="btn btn-default btn-xs pull-right" href="#" onclick="javascript:delete_resource(\'' + $type + '\', \'' + $name + '\', this);"><i class="glyphicon glyphicon-remove"></i></a></button>';

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
    document.getElementById("progressing").style.display="block";
    var res = $http.post('./controller.php?cmd=generate&prjname=<?php echo $_REQUEST['name']; ?>', $scope.workspace);
    res.success(function(data, status, headers, config) {
      		  alert("Video generation is succeeded");
		  document.getElementById("result_panel").innerHTML = data;
		  document.getElementById("progressing").style.display="none";
		});
		res.error(function(data, status, headers, config) {
		  alert("Video generation is failed.");
		});
  }

  $scope.save = function() {
    var res = $http.post('./controller.php?cmd=save&prjname=<?php echo $_REQUEST['name']; ?>', $scope.workspace);
    res.success(function(data, status, headers, config) {
                  alert("Project is successfully saved.");
                });
                res.error(function(data, status, headers, config) {
		  alert("Project saving is failed.");
                });
  }

  $scope.fillresourcepanel = function(profile) {
    for (var i=0; i<profile.length; i++) {
      var dom_resource = '';
      var $type = profile[i].type;
      var $name = profile[i].name;
      var btn_type = '';

      if ($type == 'video')
        $btn_type = "btn-success";
      if ($type == 'audio')
        $btn_type = "btn-info";
      if ($type == 'image')
        $btn_type = "btn-warning";

      dom_resource = '<button class="btn ' + $btn_type + ' col-md-12"><a class="btn btn-danger btn-xs pull-left" href="#" onclick="up(\'' + $type + '\', \'' + $name + '\', this);"><i class="glyphicon glyphicon-arrow-up"></i></a><a class="btn btn-danger btn-xs pull-left" href="#" onclick="down(\'' + $type + '\', \'' + $name + '\', this);"><i class="glyphicon glyphicon-arrow-down"></i></a>' + $name + '<a class="btn btn-default btn-xs pull-right" href="#" onclick="javascript:delete_resource(\'' + $type + '\', \'' + $name + '\', this);"><i class="glyphicon glyphicon-remove"></i></a></button>';

      document.getElementById("resource_panel").innerHTML += dom_resource;
    }
  }

  loadproject($scope, $http);
}

function loadproject($scope, $http) {
  $http.get("./controller.php?cmd=loadproject&name=<?php echo $_REQUEST['name']; ?>")
  .success(function(response) { $scope.workspace = response; $scope.fillresourcepanel(response.profile);});

  $http.get("./controller.php?cmd=result&prjname=<?php echo $_REQUEST['name']; ?>")
  .success(function(response) { document.getElementById("result_panel").innerHTML = response; });
}

function delete_resource($type, $name, $obj) {
  for (var i=0; i<$global_scope.workspace.profile.length; i++){
    var profile = $global_scope.workspace.profile[i];
    if (profile.type == $type && profile.name == $name) {
      $global_scope.workspace.profile.splice(i, 1);
      break;
    }
  }
  $obj.parentNode.parentNode.removeChild($obj.parentNode);
}

function up($type, $name, $obj) {
  for (var i=0; i<$global_scope.workspace.profile.length; i++){
    var profile = $global_scope.workspace.profile[i];
    if (profile.type == $type && profile.name == $name) {
      if (i == 0) {
        alert("This " + $type + " is top most.");
        return;
      }
      var item = $global_scope.workspace.profile[i];
      $global_scope.workspace.profile.splice(i, 1);
      $global_scope.workspace.profile.splice(i - 1, 0, item);
      
      var buttons = document.getElementById("resource_panel").getElementsByTagName("button");
      var prev = buttons[i - 1];
      var me = buttons[i];
      document.getElementById("resource_panel").insertBefore(me, prev);

      break;
    }
  }
}

function down($type, $name, $obj) {
  for (var i=0; i<$global_scope.workspace.profile.length; i++){
    var profile = $global_scope.workspace.profile[i];
    if (profile.type == $type && profile.name == $name) {
      if (i == $global_scope.workspace.profile.length - 1) {
        alert("This " + $type + " is at most bottom.");
        return;
      }
      var item = $global_scope.workspace.profile[i + 1];
      $global_scope.workspace.profile.splice(i + 1, 1);
      $global_scope.workspace.profile.splice(i, 0, item);

      var buttons = document.getElementById("resource_panel").getElementsByTagName("button");
      var next = buttons[i + 1];
      var me = buttons[i];
      document.getElementById("resource_panel").insertBefore(next, me);

      break;
    }
  }
}

</script>

</body>
</html>
