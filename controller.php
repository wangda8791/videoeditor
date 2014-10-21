<?php
	$command = $_REQUEST['cmd'];

	switch($command){
		case "list":
			echo json_encode(resources());
			break;
		case "upload":
			upload($_FILES);
			break;
		case "project":
			echo json_encode(project());
			break;
		case "createprj":
			createProject();
			break;
	}

function upload($files) {

	foreach ($_FILES as $key => $file) {
		$tmp_name = $file['tmp_name'];
		$folder = substr($key, 0, 5);

		if (move_uploaded_file($tmp_name, "./" . $folder)){
		}
	}
}

function resources() {

	return array("videos"=>resource_path("./video"), 
		"audios"=>resource_path("./audio"),
		"photos"=>resource_path("./photo"));
}

function resource_path($path) {

	$dir = opendir($path);
	while($file = readdir($dir)) {
		if (filetype($path . "/" . $file) == "dir")
			continue;
		if ($file == "index.php")
			continue;

		$files[] = array(
			"url"=>$path . "/" . $file,
			"name"=>$file, 
			"size"=>intval(filesize($path . "/" . $file) / 1024)
			);
	}
	closedir($dir);
	return $files;
}

function project() {
	$dir = opendir("./result");
	while($file = readdir($dir)) {
		if ($file == "." || $file == ".." || filetype("./result/" . $file) == "file")
			continue;

		$file_res = opendir("./result/" . $file);

		$files[] = array(
			"name"=>$file, 
			"nof"=>count(readdir($file_res)) - 1 
			);
	}
	closedir($dir);
	return $files;
}

function createProject() {
	$prjname = $_REQUEST["name"];
	$vdur = $_REQUEST["vdur"];
	$video = $_REQUEST["video"];
	$audios = $_REQUEST["audio"];
	$photos = $_REQUEST["photo"];

	if (file_exists("./result/" . $prjname)) {
		json_encode("{result:'0', msg:'The project is alreay exists'}");
		exit;
	}
	mkdir("./result/" . $prjname);
}

function vsplit($video, $vdur) {

}

?>