<?php

//$videos_vault_dir='/videovault/videos1/videos';
$videos_vault_dir=dirname(__FILE__).'/videos';
$path_to_main_server='https://barba.local/';

$download_rate=240; //Kb per sec
$fast_track_first_x_kb=1024*3;

$allowed_ips=array(
    '127.0.0.1',
    '::1',
    'x.x.x.x', //main server ip
);

$allowed_image_size=array(
    array(390,220),
    array(960,540),
);
