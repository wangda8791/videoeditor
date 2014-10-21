<?php
	$command = $_REQUEST['cmd'];

	switch($command){
		case "list":
			echo json_encode(resources("."));
			break;
		case "upload":
			upload($_FILES);
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
	mkdir("./result/" . $prjname . "/video");
	mkdir("./result/" . $prjname . "/audio");
	mkdir("./result/" . $prjname . "/photo");

	$rel = $_SERVER["CONTEXT_DOCUMENT_ROOT"];
	$outdir = $rel . "/result/" . $prjname . "/video";
	vsplit($video, $vdur, $outdir);
/*	foreach ($audios as $audio) {
		shell_exec("cp ./audio/" . $audio . " ./result/" . $prjname . "/audio/" . $audio);
	}
	foreach ($photos as $photo) {
		shell_exec("cp ./photo/" . $audio . " ./result/" . $prjname . "/photo/" . $photo);
	}*/
	return $prjname;
}

function vsplit($video, $vdur, $outdir) {
	$rel = $_SERVER["CONTEXT_DOCUMENT_ROOT"];
	$filename = substr($video, 0, strrpos($video, "."));
	$ext = substr($video, strrpos($video, ".") + 1);
	$out_name = $filename . "-%05d." . $ext;
	shell_exec("sudo sh \"" . $rel . "/bin/ffsplit.sh\" \"" . $rel . "/video/" . $video . "\" " . $vdur . " \"" . $outdir . "/" . $out_name . "\"");
}

?>