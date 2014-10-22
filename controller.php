<?php
	$command = $_REQUEST['cmd'];

	switch($command){
		case "list":
			echo json_encode(resources("."));
			break;
		case "upload":
			upload($_FILES["resource"]);
			header("location:" . "./index.php");
			break;
		case "project":
			echo json_encode(project());
			break;
		case "createprj":
			$prjname = createProject();
			header("location:" . "./project.php?name=" . $prjname);
			break;
		case "workspace":
			$prjname = $_REQUEST["name"];
			echo json_encode(resources("./result/" . $prjname));
			break;
	}

function upload($files) {
	$tmp_names = $file['tmp_name'];
	$types = $file['type'];
	foreach ($tmp_names as $key => $tmp_name) {
		$type = $types[$key];
		if (@move_uploaded_file($tmp_name, "./" . $type)){
		}
	}
}

function resources($path) {

	return array("videos"=>resource_path($path . "/video"), 
		"audios"=>resource_path($path . "/audio"),
		"images"=>resource_path($path . "/image"));
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

		$vfile = opendir("./result/" . $file . "/video");
		$afile = opendir("./result/" . $file . "/audio");
		$pfile = opendir("./result/" . $file . "/image");
		
		$files[] = array(
			"name"=>$file, 
			"vfiles"=>count(readdir($vfile)) - 1, 
			"afiles"=>count(readdir($afile)) - 1, 
			"pfiles"=>count(readdir($pfile)) - 1
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
	$images = $_REQUEST["image"];

	if (file_exists("./result/" . $prjname)) {
		json_encode("{result:'0', msg:'The project is alreay exists'}");
		exit;
	}

	$rel = $_SERVER["CONTEXT_DOCUMENT_ROOT"];
	mkdir("./result/" . $prjname);
	mkdir("./result/" . $prjname . "/video");
	mkdir("./result/" . $prjname . "/audio");
	mkdir("./result/" . $prjname . "/image");
	shell_exec("chmod 777 -R \"" . $rel . "/result/" . $prjname . "\"");

	$outdir = $rel . "/result/" . $prjname . "/video";
	foreach ($audios as $audio) {
		shell_exec("cp ./audio/" . $audio . " ./result/" . $prjname . "/audio/" . $audio);
	}
	foreach ($images as $image) {
		shell_exec("cp ./image/" . $audio . " ./result/" . $prjname . "/image/" . $image);
	}
	vsplit($video, $vdur, $outdir);
	return $prjname;
}

function vsplit($video, $vdur, $outdir) {
	$rel = $_SERVER["CONTEXT_DOCUMENT_ROOT"];
	$filename = substr($video, 0, strrpos($video, "."));
	$ext = substr($video, strrpos($video, ".") + 1);
	$out_name = $filename . "-%05d." . $ext;
	shell_exec("\"" . $rel . "/bin/ffsplit.sh\" \"" . $rel . "/video/" . $video . "\" " . $vdur . " \"" . $outdir . "/" . $out_name . "\"");
}

?>