<?php
if(!isset($_GET['video']) || !isset($_GET['chunk']) || !isset($_GET['w']) || !isset($_GET['h']))
{die('missing vars...');}

include(dirname(dirname(dirname(__FILE__))).'/include/init.php');

if(!in_array(array($_GET['w'],$_GET['h']),$allowed_image_sizes)){
    die('image size not allowed');
}
    
include(BP.'/include/thumbalizer.php');
get_image($_GET['video'],$_GET['chunk'],$_GET['w'],$_GET['h']);
