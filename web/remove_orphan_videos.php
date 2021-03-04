<?php
include(dirname(dirname(__FILE__)).'/settings.php');
include(dirname(dirname(__FILE__)).'/include/curl.php');

$green='<span style="color:green;">';
$red='<span style="color:red;">';
$close='</span>';
$trash_exists=false;
$trashfolder_path=$videos_vault_dir."/trash";
$goforeal=false;
if(isset($_GET["goforreal"]))$goforeal=true;

//check every video file and for each check that the video exists in video table
//video vault is $videos_vault_dir
$glob_reg=$videos_vault_dir.'/*/*/*';
echo 'glob_reg: '.$glob_reg.'<br />';
$find_vids=glob($glob_reg);
//print_r($find_vids);
foreach($find_vids as $key => $value)
{
	if(stripos($value,"/trash/"))continue;//not retrashing videos already in trash....
	/*$exploded_by_slashes=explode('/',$value);
	$totalchunks=count($exploded_by_slashes);
	$lastchunk=$exploded_by_slashes[$totalchunks-1];*/
	$lastchunk=get_video_path_last_part($value);
	if(strlen($lastchunk)==1)continue;//its just container folder, not actual video folder when its 1 char long
	echo $lastchunk.'<br />';
	
	$video_exists='';
	$countturns=0;
	$maxturns=3;
	while($video_exists=='' && $countturns<$maxturns)
	{
		$countturns++;
		$video_exists=get_content_of_url($path_to_main_server.'curl/element_exists.php?hash='.urlencode($lastchunk).'&index_file='.urlencode('videos_index.dat'));
	}
	if($video_exists!='true' && $video_exists!='false')
	{die('error, lost connection with main server (3)');}
	if($video_exists=='true')
	{echo $green.'video exists in library, good!'.$close.'<br />';}
	else if($video_exists=='false')
	{
		//if not delete
		echo $red.'video does not exist in library, not good.. will move to trash...'.$close.'<br />';
		move_to_trash($value);
	}
}


function make_trash_if_not_exist(){
	global $trash_exists, $trashfolder_path;
	if($trash_exists)return;
	else{
		
		echo "checking that $trashfolder_path exists or creating<br>";
		$does_it_already_exist=file_exists($trashfolder_path);
		$is_it_a_dir=is_dir($trashfolder_path);
		if($does_it_already_exist && $is_it_a_dir){
			$trash_exists=true;
			return;
		}else{
			if($does_it_already_exist && !$is_it_a_dir)unlink($trashfolder_path);//what it exists but its a file?? wtf, lets delete that random file to clear the way...
			mkdir($trashfolder_path,0777);
		}
	}
}
function move_to_trash($video_folder){
	global $trashfolder_path, $goforeal;
	make_trash_if_not_exist();
	$oldpath=$video_folder;
	$newpath=$trashfolder_path."/".get_video_path_last_part($video_folder);
	echo "renaming $oldpath to $newpath<br />";
	if($goforeal)
		rename($oldpath,$newpath);
	else
		echo "goforreal is false, didnt really remove..<br>";
}
function get_video_path_last_part($video_folder){
	$exploded_by_slashes=explode('/',$video_folder);
	$totalchunks=count($exploded_by_slashes);
	$lastchunk=$exploded_by_slashes[$totalchunks-1];
	return $lastchunk;
}


?>