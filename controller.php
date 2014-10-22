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
	$names = $files['name'];
	$tmp_names = $files['tmp_name'];
	$types = $files['type'];
	$sizes = $files['size'];

	foreach ($tmp_names as $key => $tmp_name) {
		$type = substr($types[$key], 0, strrpos($types[$key], "/"));
		$name = $names[$key];
		$size = $sizes[$key];
		if ($size == 0) continue;

		if (@move_uploaded_file($tmp_name, "./" . $type . "/" . $name)){
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
			"size"=>intval(filesize($path . "/" . $file) / 1024),
			"ctime"=>filectime($path . "/" . $file)
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

		$vfile = countfile("./result/" . $file . "/video");
		$afile = countfile("./result/" . $file . "/audio");
		$pfile = countfile("./result/" . $file . "/image");
		
		$files[] = array(
			"name"=>$file, 
			"vfiles"=>$vfile, 
			"afiles"=>$afile, 
			"pfiles"=>$pfile,
			"ctime"=>filectime("./result/" . $file)
			);
	}
	closedir($dir);
	return $files;
}

function countfile($path) {
	$dir = opendir($path);
	$count = 0;
	while ($file = readdir($dir)) {
		if (is_dir($file)) continue;
		$count++;
	}
	return $count;
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

	shell_exec("mkdir \"" . $rel . "/result/" . $prjname . "\"");
	shell_exec("mkdir \"" . $rel . "/result/" . $prjname . "/video\"");
	shell_exec("mkdir \"" . $rel . "/result/" . $prjname . "/audio\"");
	shell_exec("mkdir \"" . $rel . "/result/" . $prjname . "/image\"");

	$outdir = $rel . "/result/" . $prjname . "/video";

	foreach ($audios as $audio) {
		shell_exec("cp \"./audio/" . $audio . "\" \"./result/" . $prjname . "/audio/" . $audio . "\"");
	}

	foreach ($images as $image) {
		shell_exec("cp \"./image/" . $image . "\" \"./result/" . $prjname . "/image/" . $image . "\"");
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
