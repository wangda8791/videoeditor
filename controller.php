<?php
	$command = $_REQUEST['cmd'];

	switch($command){
		case "list":
			echo json_encode(resources("."));
			break;
		case "upload":
			upload($_FILES);
			break;
		case "deleteresource":
			$type = $_REQUEST["type"];
			$name = $_REQUEST["name"];
			deleteResource($type, $name);
			header("location:" . "./index.php");
			break;
		case "project":
			echo json_encode(project());
			break;
		case "createprj":
			$prjname = createProject();
			header("location:" . "./project.php?name=" . $prjname);
			break;
		case "deleteprj":
			$prjname = $_REQUEST["name"];
			deleteProject($prjname);
			header("location:" . "./index.php");
			break;
		case "workspace":
			$prjname = $_REQUEST["name"];
			echo json_encode(resources("./result/" . $prjname));
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

function resources($path) {

	return array("videos"=>resource_path($path . "/video"), 
		"audios"=>resource_path($path . "/audio"),
		"photos"=>resource_path($path . "/photo"));
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

function deleteResource($type, $name) {
        $rel = $_SERVER["CONTEXT_DOCUMENT_ROOT"];
        shell_exec("rm -f \"" . $rel . "/" . $type . "/" . $name . "\"");
}

function project() {
	$dir = opendir("./result");
	while($file = readdir($dir)) {
		if ($file == "." || $file == ".." || filetype("./result/" . $file) == "file")
			continue;

		$vfile = opendir("./result/" . $file . "/video");
		$afile = opendir("./result/" . $file . "/audio");
		$pfile = opendir("./result/" . $file . "/photo");
		
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
	$photos = $_REQUEST["photo"];

	if (file_exists("./result/" . $prjname)) {
		json_encode("{result:'0', msg:'The project is alreay exists'}");
		exit;
	}
	
	$rel = $_SERVER["CONTEXT_DOCUMENT_ROOT"];
	shell_exec("mkdir \"" . $rel . "/result/" . $prjname . "\"");
	shell_exec("mkdir \"" . $rel . "/result/" . $prjname . "/video\"");
	shell_exec("mkdir \"" . $rel . "/result/" . $prjname . "/audio\"");
	shell_exec("mkdir \"" . $rel . "/result/" . $prjname . "/photo\"");

	$outdir = $rel . "/result/" . $prjname . "/video";

	foreach ($audios as $audio) {
		shell_exec("cp \"./audio/" . $audio . "\" \"./result/" . $prjname . "/audio/" . $audio . "\"");
	}
	foreach ($photos as $photo) {
		shell_exec("cp \"./photo/" . $photo . "\" \"./result/" . $prjname . "/photo/" . $photo . "\"");
	}
	vsplit($video, $vdur, $outdir);

	return $prjname;
}

function deleteProject($prjname) {
	$rel = $_SERVER["CONTEXT_DOCUMENT_ROOT"];
	shell_exec("rm -rf \"" . $rel . "/result/" . $prjname . "\"");
}

function vsplit($video, $vdur, $outdir) {
	$rel = $_SERVER["CONTEXT_DOCUMENT_ROOT"];
	$filename = substr($video, 0, strrpos($video, "."));
	$ext = substr($video, strrpos($video, ".") + 1);
	$out_name = $filename . "-%05d." . $ext;
	shell_exec("\"" . $rel . "/bin/ffsplit.sh\" \"" . $rel . "/video/" . $video . "\" " . $vdur . " \"" . $outdir . "/" . $out_name . "\"");
}

?>
