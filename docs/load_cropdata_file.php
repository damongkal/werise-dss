<?php

include_once('../inc/class/db.php');
include_once('../inc/class/crop_data.php');

if (isset($_GET['outfile']))
{
    $cls = new crop_data;
    $cls->load('../inc/data/'.$_GET['outfile']);
}


echo 'done';
