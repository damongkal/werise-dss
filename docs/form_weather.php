<?php
include('bootstrap.php');
define('_INIT','weather');
include('layout_header.php');
?>

<div id ="dataselection">
<form name="frm" action="#" method="post" id ="frm" class="form-inline">  
    
    <div style="padding: 3px">
        <i class="icon-filter"></i>
        Select Dataset:
    </div>
    
    <div style="padding: 3px">

        <div class="bfh-selectbox bfh-countries" data-countryList="ID,LA,PH,TH" data-country="ID" data-flags="true" style="float:left">
            <input id="country" name="country" type="hidden" value="">
            <a class="bfh-selectbox-toggle" role="button" data-toggle="bfh-selectbox" href="#">
                <span class="bfh-selectbox-option input-small" data-option=""></span>
                <b class="caret"></b>
            </a>
            <div class="bfh-selectbox-options" style="min-width:120px">
                <input type="text" class="bfh-selectbox-filter" style="display:none">
                <div role="listbox">
                    <ul role="option" style="width:100px">
                    </ul>
                </div>
            </div>
        </div>

        &nbsp;&nbsp;
        <select style="width:200px" name="station" id="station">
            <option value="0">Station »</option>
        </select>

        &nbsp;&nbsp;
        <select name="type_year" id="type_year" style="width:90px">
            <option value="">Year »</option>
        </select>
        
        &nbsp;&nbsp;
        
        <select name="wvar" id="wvar" style="width:210px">
            <option value="-1">Measured Variable »</option>
            <option value="0">Rainfall</option>
            <option value="1">Temperature</option>
            <option value="3">Solar Radiation</option>
            <option value="4">Early morning vapor pressure</option>
            <option value="5">Wind Speed</option>
        </select>
        
        &nbsp;&nbsp;
        
        <input style="font-size:12px" id="show" type="submit" value="Show Graph »" />

    </div>

</form>
</div>

<div id="dss-error-box" class="alert alert-error"></div>    

<div id="chart1" class="chart"></div>

<div id="advisory" style="padding:40px;display:none">

    <h3>Advisory</h3>

    <ul>
        <li>
            <span id="f-year"></span> wet-season rainfall is <span id="f-rain"></span>
        </li>
        <li>
            Fertilizer may be applied on these dates:<br />
            <ul id="fert-apply">
            </ul>            
        </li>        
    </ul>
    
</div>

<div id="datagrid1" class="datagrid-dss"></div>

<?php include('layout_footer.php'); ?>