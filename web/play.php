<?php
//proc_nice(19);
include('conf.php');
$showdebuginfo=0;



include('curl.php');


if(!isset($_GET['video']) || !isset($_GET['chunk']))
{
    die('missing vars...');
    //$_GET['video']='1acdff7e5609001b42739e5f0823d224';
    //$_GET['chunk']='0000000052_0000000078.xvid';
}


$keyname=md5($_SERVER['REMOTE_ADDR'].'_'.$_GET['video'].'_'.$_GET['chunk']);

$rezu='';
$countturns=0;
$maxturns=3;
while($rezu=='' && $countturns<$maxturns)
{
    $countturns++;
    $rezu=get_content_of_url($path_to_main_server.'checkkey.php?kn='.$keyname.'&k='.$_GET['key']);
}
if($rezu!='ok')
{die('invalid key...');}





$first_letter=substr($_GET['video'],0,1);
$second_letter=substr($_GET['video'],1,1);
$video_path=$videos_vault_dir.'/'.$first_letter.'/'.$second_letter.'/'.$_GET['video'].'/'.$_GET['chunk'];
//echo '$video_path: '.$video_path.'<br />';





// local file that should be send to the client
$local_file = $video_path;
// filename that the user gets as default
$download_file = $_GET['chunk'];












//testing another streamer from http://www.tuxxin.com/php-mp4-streaming/
$file = $local_file;
$fp = @fopen($file, 'rb');
$size = filesize($file); // File size
$length = $size; // Content length
$start = 0; // Start byte
$end = $size - 1; // End byte
header('Content-type: video/mp4');
//header("Accept-Ranges: 0-$length");
header("Accept-Ranges: bytes");
if (isset($_SERVER['HTTP_RANGE'])) {
	$c_start = $start;
	$c_end = $end;
	list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
	if (strpos($range, ',') !== false) {
		header('HTTP/1.1 416 Requested Range Not Satisfiable');
		header("Content-Range: bytes $start-$end/$size");
		exit;
	}
	if ($range == '-') {
		$c_start = $size - substr($range, 1);
	}else{
		$range = explode('-', $range);
		$c_start = $range[0];
		$c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
	}
	$c_end = ($c_end > $end) ? $end : $c_end;
	if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
		header('HTTP/1.1 416 Requested Range Not Satisfiable');
		header("Content-Range: bytes $start-$end/$size");
		exit;
	}
	$start = $c_start;
	$end = $c_end;
	$length = $end - $start + 1;
	fseek($fp, $start);
	header('HTTP/1.1 206 Partial Content');
}
header("Content-Range: bytes $start-$end/$size");
header("Content-Length: ".$length);
header('Access-Control-Allow-Origin: *');
//header('Access-Control-Allow-Origin: http://barbavid.com');
//$buffer = 1024 * 8;
$buffer=round($download_rate*1024);
while(!feof($fp) && ($p = ftell($fp)) <= $end) {
	if ($p + $buffer > $end) {
		$buffer = $end - $p + 1;
	}
	set_time_limit(0);
	echo fread($fp, $buffer);
	flush();

	// sleep one second
	if($p>($fast_track_first_x_kb*1024))
	{sleep(1);}

}
fclose($fp);
exit();



























//handle partial requests
$partialContent=0;
if(isset($_SERVER['HTTP_RANGE']))
{

    // if the HTTP_RANGE header is set we're dealing with partial content
    $partialContent=1;
    // find the requested range, this might be too simplistic, apparently the client can request multiple ranges, which can become pretty complex, so ignore it for now
    preg_match('/bytes=(\d+)-(\d+)?/',$_SERVER['HTTP_RANGE'],$matches);
    $range_start=intval($matches[1]);
    $range_end=intval($matches[2]);

    if($showdebuginfo) {file_put_contents('showrange.txt','
================================================================================
'.$_SERVER['HTTP_RANGE'].' $range_start:'.$range_start.', $range_end:'.$range_end,FILE_APPEND);}
}
else
{
    if($showdebuginfo) {file_put_contents('showrange.txt','
================================================================================
a request with no range',FILE_APPEND);}
}



//header('Content-Type: ' . mime_content_type($local_file));
//header('Content-Length: ' . filesize($local_file));
//header('Cache-control: private');
//header('Content-Disposition: attachment; filename='.$download_file);
//header('Accept-Ranges: bytes');
//readfile($local_file);
//die();



if(file_exists($local_file) && is_file($local_file))
{
    if($showdebuginfo) {file_put_contents('showrange.txt','
gotfile',FILE_APPEND);}
    $filesize=filesize($local_file);
    if($range_end==0)
    {$range_end=$filesize;}
    // send headers
    //header('Content-Type: application/octet-stream');
    //header('Content-type: video/mp4');
    header('Content-Type: ' . mime_content_type($local_file));
    header('Content-Length: ' . filesize($local_file));
    header('Cache-control: private');
    
    header('Content-Disposition: attachment; filename='.$download_file);
    header('Accept-Ranges: bytes');
    if($partialContent==1)
    {
        // output the right headers for partial content
        header('HTTP/1.1 206 Partial Content');
        header('Content-Range: bytes '.$range_start.'-'.$range_end.'/'.$filesize);
    }
    // flush content
    ob_clean();
    flush();
    // open file stream
    $file = fopen($local_file,"r");
    if($showdebuginfo) {file_put_contents('showrange.txt','
file opened',FILE_APPEND);}

    $current_end=0;
    if($partialContent==1)
    {
        fseek($file,$range_start);
        if($showdebuginfo) {file_put_contents('showrange.txt','
file seeked to '.$range_start,FILE_APPEND);}
        $current_end=$range_start;
    }

    $counturns=0;
    while(!feof($file))
    {
        $counturns++;
        if($showdebuginfo) {file_put_contents('showrange.txt','
while turn #'.$counturns,FILE_APPEND);}
        $readsize=round($download_rate*1024);
        if($partialContent==1)
        {
            $current_end=$current_end+$readsize;
            if($current_end>=$range_end)
            {
                $toomuch=$current_end-$range_end;
                $readsize=$readsize-$toomuch;
            }
        }
        // send the current file part to the browser
        if($showdebuginfo) {file_put_contents('showrange.txt','
$readsize:'.$readsize,FILE_APPEND);}
        print fread($file,$readsize);
        // flush the content to the browser
        flush();
        // sleep one second
        sleep(1);

        if($partialContent==1)
        {
            if($current_end>=$range_end)
            {
                if($showdebuginfo) {file_put_contents('showrange.txt','
i will break now',FILE_APPEND);}
                break;
                if($showdebuginfo) {file_put_contents('showrange.txt','
i have breaked',FILE_APPEND);}
            }
        }
    }
    // close file stream
    fclose($file);
}
else
{
    die('Error: The file '.$local_file.' does not exist!');
    if($showdebuginfo) {file_put_contents('showrange.txt','
================================================================================
file '.$local_file.' did not exist',FILE_APPEND);}
}




?>
