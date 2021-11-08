<?php



function get_video_original_image_path($video,$chunk){
    //find image, look for orig, if not available look for large, then for thumb
    global $videos_vault_dir;
    
    $look_for_sizes=array('orig.jpg','large.png','thumb.png');
    $image_file=false;
    
    foreach($look_for_sizes as $size){
        $dotinchunk=strripos($chunk,'.');
        $dotlesschunk=substr($chunk,0,$dotinchunk);
        $imagefilename=$dotlesschunk.'_'.$size;
        $first_letter=substr($video,0,1);
        $second_letter=substr($video,1,1);
        $image_file_path=$videos_vault_dir.'/'.$first_letter.'/'.$second_letter.'/'.$video.'/'.$imagefilename;
        //var_dump($video_path);
        if(file_exists($image_file_path)){
            $image_file=$image_file_path;
            break;
        }
    }
    
    return $image_file;
}

function get_cached_image_path($image_path,$wanted_width,$wanted_height){
    $cache_path=BP.'/web/thumbalizer/cache/'.md5($wanted_width.'-'.$wanted_height.'-'.$image_path).'.png';
    return $cache_path;
}

function create_image($picture_path,$wanted_width,$wanted_height){
    $pic_mime=mime_content_type($picture_path);
    list($width, $height)=getimagesize($picture_path);
    $tn = imagecreatetruecolor($wanted_width, $wanted_height);
    //var_dump($tn);
    if ($pic_mime == "image/gif") {$image = imagecreatefromgif($picture_path);}
    else if($pic_mime == "image/png") {$image = imagecreatefrompng($picture_path);}
    else if($pic_mime == "image/jpeg") {$image = imagecreatefromjpeg($picture_path);}
    else {die("error, the file type \"".$pic_mime."\" is not supported");}
    $picwidth_pourun_ofwantedwidth=$width/$wanted_width;
    $picheight_pourun_ofwantedheight=$height/$wanted_height;
    if($picwidth_pourun_ofwantedwidth>$picheight_pourun_ofwantedheight) {
	//use full width, proportional height
	$dst_w=$wanted_width;
	$dst_h=($height*$wanted_width)/$width;
	$dst_x=0;
	$dst_y=($wanted_height-$dst_h)/2;
    } else{
        //use full height, proportinal width
        $dst_w=($width*$wanted_height)/$height;
        $dst_h=$wanted_height;
        $dst_x=($wanted_width-$dst_w)/2;
        $dst_y=0;
    }
    imagecopyresampled($tn, $image, $dst_x, $dst_y, 0, 0, $dst_w, $dst_h, $width, $height);
    $cache_path = get_cached_image_path($picture_path,$wanted_width,$wanted_height);
    imagepng($tn,$cache_path);
    //imagepng($tn);
    imagedestroy($tn);
    return $cache_path;
}

function render_image($image_file){
    header('Content-Type: image/png');
    header("Content-Length: " . filesize($image_file));
    readfile($image_file);
}

function get_image($video,$chunk,$width,$height){
   $original = get_video_original_image_path($video,$chunk);
   //var_dump($original);
   $cache_path = get_cached_image_path($original,$width,$height);
   //var_dump($cache_path);
   if(!file_exists($cache_path)){//echo 'did not exist';
       $cache_path = create_image($original,$width,$height);
   }//else{echo 'exists';}
   //die($cache_path);
   render_image($cache_path);
}