<?php


//from now on, all video image requests will go through here, the desired size should be provided in the get parameters
//the wanted size for thumb and large will be specified in the main site settings
//but there will be a list of allowed sizes in the video host, to prevent ppl from causing many images to be created


//find image, look for orig, if not available look for large, then for thumb
$look_for_sizes=array('orig','large','thumb');
$image_file=null;
foreach($look_for_sizes as $size){

    $dotinchunk=strripos($_GET['chunk'],'.');
    $dotlesschunk=substr($_GET['chunk'],0,$dotinchunk);
    $largename=$dotlesschunk.'_'.$size.'.jpg';
    $first_letter=substr($_GET['video'],0,1);
    $second_letter=substr($_GET['video'],1,1);
    $video_path=$videos_vault_dir.'/'.$first_letter.'/'.$second_letter.'/'.$_GET['video'].'/'.$largename;
    //var_dump($video_path);
    if(file_exists($video_path)){
        $image_file=$video_path;
        break;
    }

}

//die($image_file);
if($image_file){
    header('Content-Type: image/png');
    readfile($image_file);
}