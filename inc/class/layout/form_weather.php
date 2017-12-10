<?php
$country_choice = dss_utils::getLastSelectValues('country');
$all_country = werise_stations_country::getAll();
?>
<div class="width-center">
    <header>
        <h1 class="title"><?php echo _CURRENT_OPT ?></h1>
    </header>

    <div id ="dataselection" style="width:600px">

        <form role="form" class="form" id ="frm" name="frm" action="#" method="post">

            <fieldset style="margin-top:0">
                <legend><?php echo __('Dataset')?></legend>

                <label class="control-label" for="station"><?php __('Station') ?></label>
                
                <input id="country" name="country" type="hidden" value="<?php echo $country_choice ?>">
                <div class="btn-group">
                    <button class="btn btn-small dropdown-toggle country-dropdown" data-toggle="dropdown"><i class="icon-flag-<?php echo strtoupper($country_choice) ?>"> </i> <?php echo $all_country[$country_choice]['country'] ?> &nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <?php foreach ($all_country as $country_code => $country_attr): ?>
                            <li><a href="index.php?pageaction=weather&country=<?php echo $country_code ?>"><i class="icon-flag-<?php echo $country_code ?>"> </i> <?php echo $country_attr['country'] ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>                
                &nbsp;
                <div class="input-append" id="location_div">
                  <input class="form-control span2" id="location_name" type="text" disabled="disabled" style="width:400px;padding:4px 10px">
                  <button id="location_btn" class="form-control btn" type="button"><i class="icon-map-marker"></i></button>
                </div>
                
                <select class="form-control" style="width:450px;display:none" name="station" id="station">
                    <option value="0"><?php __('Location') ?> &raquo;</option>
                </select>
                

                <label class="control-label" for="type_year"><?php __('Year') ?></label>
                <select class="form-control" name="type_year" id="type_year" style="width:90px">
                    <option value=""><?php __('Year') ?> &raquo;</option>
                </select>

                <legend><?php echo __('Weather data')?></legend>

                <label class="checkbox">
                    <input id="wvar1" name="wvar1" type="checkbox"checked="checked" disabled="1" /> <?php __('Rainfall') ?>
                </label>
                <label class="checkbox">
                    <input id="wvar2" name="wvar2" class="wvar_show" type="checkbox" /> <?php __('Temperature') ?>
                </label>
                <label class="checkbox">
                    <input id="wvar3" name="wvar3" class="wvar_show" type="checkbox" /> <?php __('Solar Radiation') ?>
                </label>
                <label class="checkbox">
                    <input id="wvar4" name="wvar4" class="wvar_show" type="checkbox" /> <?php __('Early Morning Vapor Pressure') ?>
                </label>
                <label class="checkbox">
                    <input id="wvar5" name="wvar5" class="wvar_show" type="checkbox" /> <?php __('Wind Speed') ?>
                </label>              
                
            </fieldset>
            <button class="form-control btn btn-success" id="show" type="submit"><i class="icon-picture icon-white"></i> <?php __('Show Advisory') ?></button>
        </form>
    </div>

    <div id="dss-error-box" class="alert alert-error alert-fixed"></div>

    <div id="homeimages" style="margin-top:30px;height:400px">
        <div style="width:513px;position:relative;margin-left:auto; margin-right:auto">
            <img src="images/home01-aug2015.gif" width="513" height="350" style="width:513px;height:350px" />
            <div class="homeimages_overlay" style="width:513px"><?php __('Weather Advisory')?></div>
        </div>
    </div>

    <div class="afterload" style="display:none">

        <div id="advisory" style="display:none">
            <h2 class="title" style="font-weight: 700; color:#547e1a"><?php __('Advisory')?></h2>

                <div class="adv-rainfall clearfix">
                    <div style="float:left;width:70px">
                        <img class="img-circle" src="images/advisory01.png" style="width:50px;height:50px" />
                    </div>
                    <div style="float:left;">
                        <span id="f-year"></span> <?php __('Wet-season rainfall is')?> <span id="f-rain"></span>
                    </div>
                </div>
                <div class="adv-rainfall clearfix" style="background-color: #ffffff">
                    <div style="float:left;width:70px">
                        <img class="img-circle" src="images/advisory02.png" style="width:50px;height:50px" />
                    </div>
                    <div style="float:left;">
                    <a href="javascript:launch_help('q2')"><?php __('Onset of rain is on ')?></a> <span id="rain-onset"></span>
                    </div>
                </div>
                <div class="adv-rainfall clearfix">
                    <div style="float:left;width:70px">
                        <img class="img-circle" src="images/advisory03.png" style="width:50px;height:50px" />
                    </div>
                    <div style="float:left;width:800px">
                    <a href="javascript:launch_help('q3')"><?php __('Expected flooding dates:')?></a><br /> <span id="wet-dates"></span>
                    </div>
                </div>
                <div class="adv-rainfall clearfix" style="background-color: #ffffff">
                    <div style="float:left;width:70px">
                        <img class="img-circle" src="images/advisory04.png" style="width:50px;height:50px" />
                    </div>
                    <div style="float:left;width:800px">
                    <a href="javascript:launch_help('q3')"><?php __('Expected drought dates:')?></a><br /> <span id="dry-dates"></span>
                    </div>
                </div>
                <div id="adv-fertilizer clearfix" style="display:none">
                    Fertilizer may be applied on these dates:<br />
                    <ul id="fert-apply">
                    </ul>
                </div>
        </div>

        <div id="ack_container" style="display:none">
            <button id="ack_btn" class="form-control btn btn-success" type="button"><i class="icon-info-sign icon-white"></i> <?php __('Show data source/s') ?></button>
            <h2><?php __('Data Source')?></h2>
            <pre id="acknowledgement"></pre>
        </div>
    </div>

    <div id="wvar_chart1" class="chartdiv">
        <h3 class="title" style="font-weight: 700; color:#547e1a"><?php echo _('Chart'). ' : '._t('Rainfall')?></h3>        
        <div id="chart1" class="chart"></div>        
        <h3><a href="javascript:launch_help('q4')">chart notes:</a></h3>
        <img src="images/chartdef04.jpg" />        
    </div>

    <div id="wvar_chart2" class="chartdiv">
        <h3 class="title" style="font-weight: 700; color:#547e1a"><?php __('Chart') ?> : <?php __('Temperature') ?></h3>
        <div id="chart2" class="chart"></div>
        <h3><a href="javascript:launch_help('q4')">chart notes:</a></h3>
        <img src="images/chartdef05.jpg" />
    </div>

    <div id="wvar_chart3" class="chartdiv">
        <h3 class="title" style="font-weight: 700; color:#547e1a"><?php __('Chart') ?> : <?php __('Solar Radiation') ?></h3>
        <div id="chart3" class="chart"></div>
        <h3><a href="javascript:launch_help('q4')">chart notes:</a></h3>
        <img src="images/chartdef05.jpg" />
    </div>

    <div id="wvar_chart4" class="chartdiv">
        <h3 class="title" style="font-weight: 700; color:#547e1a"><?php __('Chart') ?> : <?php __('Early Morning Vapor Pressure') ?></h3>
        <div id="chart4" class="chart"></div>
        <h3><a href="javascript:launch_help('q4')">chart notes:</a></h3>
        <img src="images/chartdef05.jpg" />
    </div>

    <div id="wvar_chart5" class="chartdiv">
        <h3 class="title" style="font-weight: 700; color:#547e1a"><?php __('Chart') ?> : <?php __('Wind Speed') ?></h3>
        <div id="chart5" class="chart"></div>
        <h3><a href="javascript:launch_help('q4')">chart notes:</a></h3>
        <img src="images/chartdef05.jpg" />
    </div>

     <div class="afterload" style="display:none">
        <?php if (_opt(sysoptions::_OPT_SHOW_DATAGRID)) : ?>
        <div id="rawcomputation">
            <h2>Raw Data and Computations</h2>
            <div class="alert" style="display:block;font-weight:700;width:300px"><i class="icon-warning-sign"> </i>
                CONFIDENTIAL INFORMATION:<br />For Internal use only! Make sure this is not shown during demonstration.
            </div>
            <h3>Chart Data</h3>
            <div id="datagrid1" class="datagrid-dss"></div>
            <h3>Rainfall Analysis</h3>
            <div id="raw_rain"></div>
        </div>
        <?php endif; ?>

    </div>

</div>

<script type="text/javascript" src="js/highcharts.js"></script>
<script type="text/javascript" src="js/highcharts-more.js"></script>
<?php if (_opt(sysoptions::_SHOW_MAP)) : ?>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCOk-7pjbZ3ip7vVLv443hfeNbwdkR5Qr0"></script>
<?php endif; ?>
<script type="text/javascript" src="gzip.php?group=weather"></script>