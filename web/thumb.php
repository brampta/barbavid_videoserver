<?php
if(!isset($_GET['video']) || !isset($_GET['chunk']))
{die('missing vars...');}

include(dirname(dirname(__FILE__)).'/include/init.php');
include(BP.'/include/thumbalizer.php');
get_image($_GET['video'],$_GET['chunk'],390,219);
