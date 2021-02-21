<?php
include('conf.php');



include('allowed_ips.php');
//echo 'this IP: '.$_SERVER['REMOTE_ADDR'].'<br />';
//echo 'allowed IPs: ';
//print_r($allowed_ips);
//echo '<br />';
if(array_search($_SERVER['REMOTE_ADDR'],$allowed_ips)===false)
{die('unauthorized');}



if($_GET['video']!='')
{
    $firstchar=substr($_GET['video'],0,1);
    $secondchar=substr($_GET['video'],1,1);
    $vid_dir=$videos_vault_dir.'/'.$firstchar.'/'.$secondchar.'/'.$_GET['video'];
    $command='nice -n 19 rm -R '.$vid_dir;
    //$command=$command.' 2>&1';
    //echo '$command: '.$command.'<br />';
    exec($command,$rezu);
    //print_r($rezu); echo '<br />';
    if(!file_exists($vid_dir))
    {echo 'ok';}
    $stuffin_secondchar_folder=glob($videos_vault_dir.'/'.$firstchar.'/'.$secondchar.'/*');
    //print_r($stuffin_secondchar_folder); echo '<br />';
    if(count($stuffin_secondchar_folder)==0)
    {@rmdir($videos_vault_dir.'/'.$firstchar.'/'.$secondchar);}
    $stuffin_firstchar_folder=glob($videos_vault_dir.'/'.$firstchar.'/*');
    //print_r($stuffin_firstchar_folder); echo '<br />';
    if(count($stuffin_firstchar_folder)==0)
    {@rmdir($videos_vault_dir.'/'.$firstchar);}
}




?>