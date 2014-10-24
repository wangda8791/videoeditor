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
		case "loadproject":
			$prjname = $_REQUEST["name"];
			if (file_exists("./result/" . $prjname . "/prj.save"))
				$profile = file_get_contents("./result/" . $prjname . "/prj.save");
			else
				$profile = "{\"profile\":[]}";
			header('Content-type: text/json');
			echo $profile;
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
        case "generate":
			$prjname = $_REQUEST["prjname"];
			$request_body = file_get_contents('php://input');
			file_put_contents("./result/" . $prjname . "/prj.save", $request_body);
			$data = json_decode($request_body);
			generate($prjname, $data);
			break;
		case "save":
			$prjname = $_REQUEST["prjname"];
			$request_body = file_get_contents('php://input');
			file_put_contents("./result/" . $prjname . "/prj.save", $request_body);
			break;
		case "result":
			$prjname = $_REQUEST["prjname"];
			if (file_exists("./result/" . $prjname . ".mp4")) {
				echo "<a href=\"./result/" . $prjname . ".mp4" . "\" target=\"__new\">" . $prjname . ".mp4</a>";
			} else {
				echo "No result file exists. Please generate one and check again.";
			}
			break;
	}

function generate($prjname, $gendata) {
	$inputs = "";
	$image_video_flag = false;
	$image_video_file = "";
	$image_video_file_type = "";
	foreach ($gendata->profile as $file) {

		$file_url = "./result/" . $prjname . "/" . $file->type . "/" . $file->name;
		if ($file->type == "audio") {
			if ($image_video_file_type == "audio") {
				continue;
			}

			if ($image_video_flag == true) {
				$result_file = mergemp4($image_video_file, $file_url, getfilename($file->name) . time() . ".mp4");
				$flag = false;
			} else {
				$image_video_file = $file_url;
				$image_video_file_type = "audio";
				$image_video_flag = true;
				continue;
			}
		}

		if ($file->type == "image") {
			$tmp = generatemp4("./result/" . $prjname . "/" . $file->type . "/" . $file->name, getfilename($file->name) . time() . ".mp4");

			if ($image_video_flag == false) {
				$image_video_file = $tmp;
				$image_video_file_type = "image";
				$image_video_flag = true;
				continue;
			} else if ($image_video_file_type == "image"){
				$image_video_file = joinmp4($image_video_file, $tmp, getfilename($image_video_file) . getfilename($tmp) . time() . ".mp4");
				continue;
			} else if ($image_video_file_type == "audio"){
				$result_file = mergemp4($tmp, $image_video_file, getfilename($tmp) . getfilename($image_video_file) . time() . ".mp4");
				$image_video_flag = false;
			}
		}

		if ($file->type == "video") {
			if ($image_video_flag == true && $image_video_file_type == "image") {
				$inputs .= $result_file . " ";
				$image_video_flag = false;
			}
			$inputs .= "./result/" . $prjname . "/" . $file->type . "/" . $file->name . " ";
		}
	}

	if ($image_video_flag == true && $image_video_file_type == "image") {
		$inputs .= $result_file . " ";
		$image_video_flag = false;
	}

	//file_put_contents("./result/" . $prjname . ".txt", $inputs);
    	$command = "./bin/ffmerge.sh " . $inputs;
//echo $command . "<br/>";
//exit;
	shell_exec($command);
	shell_exec("mv ./result.mp4 " . "./result/" . $prjname . ".mp4");
	echo "<a href=\"./result/" . $prjname . ".mp4" . "\" target=\"__new\">" . $prjname . ".mp4</a>";
}

function mergemp4($video, $audio, $filename) {
	$target = "./tmp/" . $filename;

	$command = "./bin/ffmpeg -y -i " . $video . " -i " . $audio . " -c:v copy -c:a aac -strict experimental -map 0:v:0 -map 1:a:0 -shortest " . $target;
//	echo $command . "<br/>";
	shell_exec($command);
	return $target;
}

function joinmp4($video1, $video2, $filename) {
	$target = "./tmp/" . $filename;

	$command = "ls " . $video1 . " " . $video2 . " | perl -ne 'print \"file " . '$' . "_\"' | ./bin/ffmpeg -f concat -i - -c copy " . $target;
    	//$command .= './bin/ffmpeg -y -i "concat:' . $video1 . '|' . $video2 .'" -c copy ' . $target;
//	echo $command . "<br/>";
	shell_exec($command);
	return $target;
}

function generatemp4($image, $filename) {
	$target = "./tmp/" . $filename;
	$command = "./bin/ffmpeg -y -loop 1 -f image2 -i \"" . $image . "\" -vcodec h264 -s 640*480 -pix_fmt yuv420p -r 30 -t 2 " . $target;
//	echo $command . "<br/>";
	shell_exec($command);
	return $target;
}

function getfilename($filename) {
	$pos = strrpos($filename, "/");
	$len = strrpos($filename, ".") - $pos - 1;
	return substr($filename, $pos + 1, $len);
}

function upload($files) {
	$names = $files['name'];
	$tmp_names = $files['tmp_name'];
	$types = $files['type'];
	$sizes = $files['size'];

	foreach ($tmp_names as $key => $tmp_name) {
		$type = substr($types[$key], 0, strrpos($types[$key], "/"));
		$name = $names[$key];
		$name = str_replace(')', '', str_replace('(', '', str_replace(' ', '', $name)));
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
		$result = 0;
		if (file_exists("./result/" . $file . ".mp4")) {
                	$result = 1;
                }

		$files[] = array(
			"name"=>$file, 
			"result"=>$result,
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
	$prjname = str_replace(' ', '', $prjname);
	$vdur = $_REQUEST["vdur"];
	$videos = $_REQUEST["video"];
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
	
	foreach ($videos as $video) {
		vsplit($video, $vdur, $outdir);
	}

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
	shell_exec("\"./bin/ffsplit.sh\" \"" . $rel . "/video/" . $video . "\" " . $vdur . " \"" . $outdir . "/" . $out_name . "\"");
}

?>

