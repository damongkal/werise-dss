<?php if (!defined('_INIT')) return; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link href="favicon.ico" rel="icon" type="image/x-icon" />
		
        <title>WeRise - Decision Support System</title>

        <link type="text/css" rel="stylesheet" href="css/bootstrap/bootstrap.min.css" />
        <link type="text/css" rel="stylesheet" href="css/bootstrap/bootstrap-formhelpers.css" />
        <link type="text/css" rel="stylesheet" href="css/bootstrap/bootstrap-formhelpers-countries.flags.css" />
        <link type="text/css" rel="stylesheet" href="css/jquery/jquery.tidy.table.min.css" />
        <link rel="stylesheet" type="text/css" href="css/dss-common.css"/>
        <link rel="stylesheet" type="text/css" href="css/dss-pages.css"/>        

        <script type="text/javascript" src="js/jquery/jquery-1.7.1.min.js"></script>
        <script type="text/javascript">
           jQuery.noConflict();
        </script>        
        <script type="text/javascript" src="js/jquery/jquery.tidy.table.min.js"></script>        
        <script type="text/javascript" src="js/highcharts.js"></script>
        <script type="text/javascript" src="js/highcharts-more.js"></script>
        <script type="text/javascript" src="js/bootstrap/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/bootstrap/bootstrap-formhelpers-selectbox.js"></script>
        <script type="text/javascript" src="js/bootstrap/bootstrap-formhelpers-countries.en_US.js"></script>
        <script type="text/javascript" src="js/bootstrap/bootstrap-formhelpers-countries.js"></script>

        <script type="text/javascript" src="js/ccara-dss-common.js"></script>
        <?php if(_INIT=='weather'): ?>
            <script type="text/javascript" src="js/ccara-dss-form-weather.js"></script>
        <?php endif; ?>
        <?php if(_INIT=='oryza'): ?>
            <script type="text/javascript" src="js/ccara-dss-form-oryza.js"></script>
        <?php endif; ?>    

    </head>
    
<body>
<!-- Page header-->
<div id="pageheader">
	<div id="pagebanner">
    	<div id="pagelogo"><a href="index.php"><img src="images/pagelogo.jpg" width="298" height="105" border="0" /></a></div>
        <div id="bannernav">
            <ul>               
                            <li><a href="index.php">Home</a></li>
                            <li><a <?php if (_INIT=='weather'): ?>class="selected"<?php endif; ?> href="form_weather.php">Weather Data</a></li>                            
                            <li><a <?php if (_INIT=='oryza'): ?>class="selected"  <?php endif; ?> href="form_oryza.php">Grain Yield Simulation</a></li>
                            <?php if (_ADM_SHOW_MENU) : ?>
                                <li><a <?php if (_INIT=='admin'): ?>class="selected"<?php endif; ?> href="admin.php">Admin</a></li>
                            <?php endif; ?>                                
            </ul>
        </div>
    </div>
</div>
<!-- End Page header-->    

<!-- Page Body -->
<div id="pagebody">
    
    