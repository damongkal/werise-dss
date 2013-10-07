<?php
include('bootstrap.php'); 
admin_auth(); 
define('_INIT','admin');
include('layout_header.php'); 
?>

<div id ="dataselection" style="margin-bottom: 20px">
    <span style="font-weight: 700">Administration</span>
</div>    

<div id="intro" class="hero-unit" style="margin: auto; width:500px;background-color: #FFDB58;"> 
    <a href="admin_weather_file.php"><i class="icon-download"></i> Weather Data Files</a> <br />
    <a href="admin_stations.php"><i class="icon-globe"></i> Stations</a> <br />
    <a href="admin_phpinfo.php"><i class="icon-globe"></i> PHP Information</a> <br />
    <a href="admin_help.php"><i class="icon-question-sign"></i> Admin Help Guide</a> <br />
</div>

<?php include('layout_footer.php'); ?>