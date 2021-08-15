<?php

/*
include(dirname(dirname(__FILE__)).'/settings.php');


if(!isset($_GET['video']) || !isset($_GET['chunk']))
{die('missing vars...');}




$dotinchunk=strripos($_GET['chunk'],'.');
$dotlesschunk=substr($_GET['chunk'],0,$dotinchunk);
$largename=$dotlesschunk.'_large.png';
$first_letter=substr($_GET['video'],0,1);
$second_letter=substr($_GET['video'],1,1);
$video_path=$videos_vault_dir.'/'.$first_letter.'/'.$second_letter.'/'.$_GET['video'].'/'.$largename;


header('Content-Type: image/png');
readfile($video_path);
*/

if(!isset($_GET['video']) || !isset($_GET['chunk']) || !isset($_GET['w']) || !isset($_GET['h']))
{die('missing vars...');}

include(dirname(dirname(__FILE__)).'/include/init.php');
$size=array($_GET['w'],$_GET['h']);
include(BP.'/include/thumbalizer/thumbalize.php');
