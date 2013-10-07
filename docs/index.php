<?php
include('bootstrap.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="favicon.ico" rel="icon" type="image/x-icon" />
<link rel="stylesheet" type="text/css" href="css/dss-common.css"/>
<link rel="stylesheet" type="text/css" href="css/dss-home.css"/>
<title>WeRise - Decision Support System</title>
</head>

<body>
<!-- header -->
<div id="header">
    <div id="banner">
    	<div id="banner-image"><img src="images/banner-image.jpg" width="466" height="338" /></div>
    	<div id="banner-text">
    		<div id="logo"><img src="images/homelogo.jpg" width="275" height="102" /></div>
            <div id="bannerbox">
    		<div id="description">The   IRRI-Japan Collaborative Research Project  is being funded by the   government of Japan  through the Ministry of Agriculture, Forestry, and   Fisheries (MAFF) since 1984. It was established by Japanese Scientists   from the Japan International Research Center for Agricultural Sciences   (JIRCAS) who work at IRRIHQ. </div>
    		<div id="getstarted"><a href="form_weather.php">Get Started »</a></div>
            <div class="clr"></div>
            </div>
   		</div>
    </div>
</div>
<!-- end header -->

<!-- body -->
<div id="homebody">
<div id="navigation" style="height:20px">      
    </div>
	<div class="clr"></div>
    <div id="introduction">
      <h1><strong>WeRise</strong><strong> (<u>We</u>ather-<u>r</u>ice-nutrient <u>i</u>ntegrated decision <u>s</u>upport syst<u>e</u>m)</strong></h1>
      <p>Rainfed rice yields are low and unstable mainly because of  uncertainty in rainfall amount and distribution, existence of nutrient stress,  and pest occurrence. Climate change poses the grave threat of extreme weather  events that could further reduce rice productivity in rainfed areas.</p>
      <p>The IRRI-Japan project on Climate Change Adaptation in  Rainfed Rice Areas (CCARA) is developing a decision support system (WeRise) to  improve the livelihood of rainfed rice farmers under current and future climate  scenarios. This system uses a seasonal-weather forecast that provides crucial  weather information such as the start and end of the rainy season and rainfall  distribution during the crop growing season to farmers. It also advises farmers  when to sow and transplant the crop, what variety is appropriate, and how fertilizer  and other inputs may be efficiently applied.</p>
  </div>
  <div id="homeimages"><img src="images/homepage-images-v2.jpg" width="800" height="211" /></div>
</div>

<!-- end body -->

<?php include('layout_footer.php') ?>