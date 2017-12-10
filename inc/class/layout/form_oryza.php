<?php
$fertopts = $cls->getFertOpts();
$fertoptscount = count($fertopts);
$country_choice = dss_utils::getLastSelectValues('country');
$all_country = werise_stations_country::getAll();
?>

<script type="text/javascript">
    window.opt_show_alldates = <?php echo ((_opt(sysoptions::_ORYZACHART_SHOW_ALLDATES)) ? 'true' : 'false') ?>;
    window.opt_show_npk = <?php echo ((_opt(sysoptions::_ORYZACHART_SHOW_NPK)) ? 'true' : 'false') ?>;
    window.opt_show_newwindow = <?php echo ((_opt(sysoptions::_ORYZACHART_NEWWINDOW)) ? 'true' : 'false') ?>;
    window.opt_postvars = <?php echo $cls->postvars ?>;
</script>

<div class="width-center">

    <header>
        <h1 class="title"><?php echo _CURRENT_OPT ?></h1>
    </header>

    <div id ="cropseason" class="dselect" class="disabled-view" style="width:300px;text-align:center">
        <div style="margin:auto;width:260px;height:40px">
            <div class="pull-left">
                <button id="csonex" type="button" class="btn btn-large">WeRise v.1</button>
            </div>
            <div class="pull-right">
                <button id="cstwox" type="button" class="btn btn-large btn-success">WeRise (RR)</button>
            </div>
            <div style:clear:both></div>
        </div>
    </div>

    <div id ="dataselection" style="width:600px;display:none">

        <div class="alert alert-success">
            Click <a id="cstwo" href="#">here</a> to use the new version of this feature.
        </div>

        <form role="form" class="form-inline" id ="frm" name="frm" action="#" method="post">

            <fieldset style="margin-top:0">
                <legend><?php echo __('Dataset') ?></legend>

                <div class="form-group" style="padding: 3px">
                    <div class="btn-group">
                        <button class="btn btn-small dropdown-toggle country-dropdown" data-toggle="dropdown"><i class="icon-flag-<?php echo strtoupper($country_choice) ?>"> </i> <?php echo $all_country[$country_choice]['country'] ?> &nbsp;&nbsp;<span class="caret"></span></button>
                        <ul class="dropdown-menu">
                            <?php foreach ($all_country as $country_code => $country_attr): ?>
                                <li><a href="index.php?pageaction=oryza&country=<?php echo $country_code ?>"><i class="icon-flag-<?php echo $country_code ?>"> </i> <?php echo $country_attr['country'] ?></a></li>
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

                </div>

                <div class="form-group" style="padding: 3px">
                    <div id="set" style="padding:10px 0 0 0">
                        <label><?php echo __('Set') ?> 1: </label>

                        <select class="form-control" name="type_year" id="type_year" style="width:100px">
                            <option value=""><?php __('Year') ?> &raquo;</option>
                        </select>
                        &nbsp;&nbsp;

                        <select class="form-control" name="variety" id="variety" style="width:150px">
                            <option value=""><?php __('Variety') ?> &raquo;</option>
                        </select>
                        &nbsp;&nbsp;

                        <select class="form-control" name="fertil" id="fertil" style="width:210px">
                            <?php foreach ($cls->getFertOpts() as $opts) : ?>
                                <option value="<?php echo $opts[0] ?>" <?php echo ($opts[0] == '') ? 'class="select-extra"' : '' ?> <?php echo (dss_utils::getLastSelectValues('fert') == $opts[0]) ? 'selected="selected"' : '' ?> ><?php echo $opts[1] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div id="set2" style="display:none;padding:10px 0 0 0">
                        <label><?php echo __('Set') ?> 2: </label>

                        <select class="form-control" name="type_year2" id="type_year2" style="width:100px">
                            <option value=""><?php __('Year') ?> &raquo;</option>
                        </select>
                        &nbsp;&nbsp;

                        <select class="form-control" name="variety2" id="variety2" style="width:150px">
                            <option value=""><?php __('Variety') ?> &raquo;</option>
                        </select>
                        &nbsp;&nbsp;

                        <select class="form-control" name="ferti2" id="fertil2" style="width:210px">
                            <?php foreach ($cls->getFertOpts() as $opts) : ?>
                                <option value="<?php echo $opts[0] ?>" <?php echo ($opts[0] == '') ? 'class="select-extra"' : '' ?> <?php echo (dss_utils::getLastSelectValues('fert') == $opts[0]) ? 'selected="selected"' : '' ?> ><?php echo $opts[1] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div id="set3" style="display:none;padding:10px 0 0 0">
                        <label><?php echo __('Set') ?> 3: </label>

                        <select class="form-control" name="type_year3" id="type_year3" style="width:100px">
                            <option value=""><?php __('Year') ?> &raquo;</option>
                        </select>
                        &nbsp;&nbsp;

                        <select class="form-control" name="variety3" id="variety3" style="width:150px">
                            <option value=""><?php __('Variety') ?> &raquo;</option>
                        </select>
                        &nbsp;&nbsp;

                        <select class="form-control" name="fertil3" id="fertil3" style="width:210px">
                            <?php foreach ($cls->getFertOpts() as $opts) : ?>
                                <option value="<?php echo $opts[0] ?>" <?php echo ($opts[0] == '') ? 'class="select-extra"' : '' ?> <?php echo (dss_utils::getLastSelectValues('fert') == $opts[0]) ? 'selected="selected"' : '' ?> ><?php echo $opts[1] ?></option>
                            <?php endforeach; ?>
                        </select>

                    </div>

                    <div id="set4" style="display:none;padding:10px 0 0 0">
                        <label><?php echo __('Set') ?> 4: </label>

                        <select class="form-control" name="type_year4" id="type_year4" style="width:100px">
                            <option value=""><?php __('Year') ?> &raquo;</option>
                        </select>
                        &nbsp;&nbsp;

                        <select class="form-control" name="variety4" id="variety4" style="width:150px">
                            <option value=""><?php __('Variety') ?> &raquo;</option>
                        </select>
                        &nbsp;&nbsp;

                        <select class="form-control" name="fertil4" id="fertil4" style="width:210px">
                            <?php foreach ($cls->getFertOpts() as $opts) : ?>
                                <option value="<?php echo $opts[0] ?>" <?php echo ($opts[0] == '') ? 'class="select-extra"' : '' ?> <?php echo (dss_utils::getLastSelectValues('fert') == $opts[0]) ? 'selected="selected"' : '' ?> ><?php echo $opts[1] ?></option>
                            <?php endforeach; ?>
                        </select>

                    </div>

                    <div style="padding:10px 18px 0 0;height:30px">
                        <div class="pull-left">
                            <button class="form-control btn" id="compare" type="button"><i class="icon-chevron-down"></i> <?php __('Compare to another set') ?></button>
                        </div>
                        <div class="pull-right">
                            <button class="form-control btn btn-success" name="show" id="show" type="submit"><i class="icon-picture icon-white"></i> <?php __('Show Advisory') ?></button>
                        </div>
                        <div style:clear:both></div>
                    </div>

                </div>
            </fieldset>

        </form>
    </div>

    <div id="dataselection2" class="dselect" style="width:600px">

        <div class="alert alert-success disabled-view">
            Click <a id="csone" href="#">here</a> to use the previous version of this feature.
        </div>

        <form role="form" class="form" id ="frm" name="frm" action="#" method="post">
            <input type="hidden" name="cs2_type" value="recommend" />
            <input type="hidden" id="cs2_fertil1" name="cs2_fertil1" value="1" />
            <input type="hidden" id="cs2_fertil2" name="cs2_fertil2" value="1" />

            <fieldset style="margin-top:0">
                <legend><?php echo __('Dataset') ?></legend>

                <label class="control-label" for="cs2_station"><?php __('Station') ?></label>

                <input id="cs2_country" name="cs2_country" type="hidden" value="<?php echo $country_choice ?>">
                <div class="btn-group">
                    <button class="btn btn-small dropdown-toggle country-dropdown" data-toggle="dropdown"><i class="icon-flag-<?php echo strtoupper($country_choice) ?>"> </i> <?php echo $all_country[$country_choice]['country'] ?> &nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <?php foreach ($all_country as $country_code => $country_attr): ?>
                            <li><a href="index.php?pageaction=oryza&country=<?php echo $country_code ?>"><i class="icon-flag-<?php echo $country_code ?>"> </i> <?php echo $country_attr['country'] ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                &nbsp;
                <div class="input-append" id="location2_div">
                    <input class="form-control span2" id="location2_name" type="text" disabled="disabled" style="width:400px;padding:4px 10px">
                    <button id="location2_btn" class="form-control btn" type="button"><i class="icon-map-marker"></i></button>
                </div>

                <select class="form-control" style="width:450px;display:none" name="cs2_station" id="cs2_station">
                    <option value="0"><?php __('Location') ?> &raquo;</option>
                </select>

                <label class="control-label" for="cs2_type_year"><?php __('Year') ?></label>
                <select class="form-control" name="cs2_type_year" id="cs2_type_year" style="width:100px">
                    <option value=""><?php __('Year') ?> &raquo;</option>
                </select>

                <div class="cs2_recommend" style="padding:3px">
                    <legend><?php echo __('Select Rice Variety Combination') ?></legend>

                    <div id="combi-preview" class="panel panel-default">
                        <div class="panel-body" style="text-align: center">
                            <img id="fakeimg01" src="images/fake01.jpg" />
                        </div>
                    </div>

                    <label class="control-label" for="cs2_variety1"><?php echo __('Variety for first crop') ?></label>
                    <select class="form-control" name="cs2_variety1" id="cs2_variety1" style="width:150px">
                        <option value=""><?php __('Variety') ?> &raquo;</option>
                    </select>
                    <div id="variety-info-1" class="variety-info"></div>

                    <label class="control-label" for="cs2_variety2"><?php echo __('Variety for second crop') ?></label>
                    <select class="form-control" name="cs2_variety2" id="cs2_variety2" style="width:150px">
                        <option value=""><?php __('Variety') ?> &raquo;</option>
                    </select>
                    <div id="variety-info-2" class="variety-info"></div>
                </div>

                <div class="cs2_custom" style="padding:3px;display:none">
                    <legend><?php echo __('Advisory Options') ?></legend>

                    <label class="control-label" for="cs2_fertil0"><?php echo __('Fertilizer Application') ?></label>
                    <select class="form-control" name="cs2_fertil0" id="cs2_fertil0" size="<?php echo $fertoptscount ?>" style="width:210px">
                        <?php foreach ($fertopts as $opts) : ?>
                            <option value="<?php echo $opts[0] ?>" <?php echo ($opts[0] == '') ? 'class="select-extra"' : '' ?> <?php echo (dss_utils::getLastSelectValues('fert') == $opts[0]) ? 'selected="selected"' : '' ?> ><?php echo $opts[1] ?></option>
                        <?php endforeach; ?>
                    </select>

                    <div id="grain-yield-preview" class="panel panel-default">
                        <div class="panel-heading">Grain Yield Preview &nbsp;&nbsp; <button class="form-control btn btn-default" id="showcustomgy" type="button"><i class="icon-picture"></i> <?php __('Show') ?></button></div>
                        <div class="panel-body">
                            <img id="fakeimg03" src="images/fake02.jpg" style="width:100%;height:171px;display:none" />
                        </div>
                    </div>

                    <label class="control-label" for="cs2_month1"><?php echo __('First crop sowing date') ?></label>
                    <select class="form-control" name="cs2_month1" id="cs2_month1" style="width:120px">
                        <option value=""><?php __('Sowing Date') ?> &raquo;</option>
                    </select>

                    <label class="control-label" for="cs2_month2"><?php echo __('2nd crop sowing date') ?></label>
                    <select class="form-control" name="cs2_month2" id="cs2_month2" style="width:120px">
                        <option value=""><?php __('Sow Date') ?> &raquo;</option>
                    </select>

                </div>

            </fieldset>
            <button class="form-control btn btn-success showcombi" type="button"><i class="icon-certificate icon-white"></i> <?php __('Show advisory') ?></button>&nbsp;&nbsp;
            <button id="showcustom" class="form-control btn" type="button"><i class="icon-wrench"></i> <?php __('... More Options') ?></button>
        </form>
    </div>

    <div id="dss-error-box" class="alert alert-error alert-fixed"></div>

    <div id="homeimages" style="margin-top:30px">
        <div style="width:383px;position:relative;margin-left:auto; margin-right:auto">
            <img src="images/home02.jpg" width="383" height="322" style="width:383px;height:322px" />
            <div class="homeimages_overlay"><?php __('Grain Yield Advisory') ?></div>
        </div>
    </div>

    <div id="grainyield_chart">
        <h2 id="advisory_anchor" class="title" style="font-weight: 700; color:#547e1a"><?php __('Simulated Grain Yield') ?></h2>
        <div id="chart1" class="chart"></div>
    </div>

    <div id="advisory" class="disabled-view">

        <!-- START: one calendar -->
        <div id="opt-sow-adv" class="hide">
            <h2 class="title" style="font-weight: 700; color:#547e1a"><?php __('Optimum sowing dates for rainfed rice crop') ?></h2>

            <table id="opt-planting" class="table table-bordered table-condensed table-dss">
                <thead>
                    <tr style="background-color: #567B11">
                        <th style="width:100px;text-align:right"><?php __('Rainfall') ?> (mm)</th>
                        <th style="width:120px"><a style="color:#ffffff" href="javascript:launch_help('q1')"><?php __('Category') ?> <i class="icon-question-sign icon-white"> </i></a></th>
                        <th style="width:100px"><?php __('Sowing Date') ?></th>
                        <th style="width:90px;text-align:right"><?php __('Yield') ?> (t/ha)</th>
                        <th style="width:80px;text-align:center"><?php __('Advisory') ?></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <!-- END: one calendar -->

        <!-- START: two calendar -->
        <div id="twocropcal" class="hide">

            <h2 class="title" style="font-weight: 700; color:#547e1a">Optimum sowing dates for two cropping seasons</h2>

            <table id="twocropcalbest" class="table table-bordered table-condensed table-dss">
                <thead>
                    <tr style="background-color: #8CB730">
                        <th colspan="4"><span class="label label-info">1</span> First crop</th>
                        <th colspan="4"><span class="label label-warning">2</span> Second crop</th>
                        <th rowspan="2" style="width:80px">Total<br />Yield(t/ha)</th>
                        <th rowspan="2" style="width:100px;text-align:center">Advisory</th>
                    </tr>
                    <tr style="background-color: #8CB730">
                        <th style="width:200px">Variety</th>
                        <th style="width:150px"><a style="color:#ffffff" href="javascript:launch_help('q1')">Rainfall <i class="icon-question-sign icon-white"> </i></a></th>
                        <th style="width:150px">Sowing /<br />Harvest</th>
                        <th style="width:100px">Yield<br />(t/ha)</th>
                        <th style="width:200px">Variety</th>
                        <th style="width:150px"><a style="color:#ffffff" href="javascript:launch_help('q1')">Rainfall <i class="icon-question-sign icon-white"> </i></a></th>
                        <th style="width:150px">Sowing /<br />Harvest</th>
                        <th style="width:100px">Yield<br />(t/ha)</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

        </div>
        <!-- END: two calendar -->

        <div id="fert-apply-adv" class="disabled-view">
            <p class="form-inline">2. <?php __('Show the advisory if sowing date is ') ?>

            <table id="opt-planting3" class="table table-bordered table-condensed table-dss hide">
                <thead>
                    <tr style="background-color: #567B11">
                        <th rowspan="2" style="width:90px"><?php __('Sowing Date') ?></th>
                        <th rowspan="2" style="width:90px"><?php __('Panicle Init.') ?></th>
                        <th rowspan="2" style="width:90px"><?php __('Flowering') ?></th>
                        <th rowspan="2" style="width:90px"><?php __('Harvest Date') ?></th>
                        <th rowspan="2" style="width:70px;text-align:right"><?php __('Yield') ?><br />(t/ha)</th>
                        <th colspan="3" style="width:400px;text-align:center"><?php __('Fertilizer Schedule') ?></th>
                        <th colspan="3"  class="hide" style="width:250px;text-align:center"><?php __('Fertilizer recommendation') ?><br/>N-P-K (kg/ha)</th>
                    </tr>
                    <tr style="background-color: #567B11">
                        <th style="width:130px"><?php __('Basal') ?></th>
                        <th style="width:130px"><?php __('Top Dress 1') ?></th>
                        <th style="width:130px"><?php __('Top Dress 2') ?></th>
                        <th class="hide" style="width:80px"><?php __('Basal') ?></th>
                        <th class="hide" style="width:80px"><?php __('Top Dress 1') ?></th>
                        <th class="hide" style="width:80px"><?php __('Top Dress 2') ?></th>
                    </tr>
                    <tr>
                        <td id="adv2fld1"></td>
                        <td id="adv2fldp"></td>
                        <td id="adv2fldf"></td>
                        <td id="adv2fld2"></td>
                        <td id="adv2fld3" style=text-align:right"></td>
                        <td id="adv2fld4"></td>
                        <td id="adv2fld5"></td>
                        <td id="adv2fld6"></td>
                        <td id="adv2fld7" class="hide"></td>
                        <td id="adv2fld8" class="hide"></td>
                        <td id="adv2fld9" class="hide"></td>
                    </tr>
                    <tr>
                        <td id="adv2misc" colspan="9"></td>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <div id="highest-compare-diff" class="disabled-view">
            3. Rainfed rice yield of <span id="highest-compare-diff-seta"></span> is <span id="highest-compare-diff-yld"></span> t/ha higher than <span id="highest-compare-diff-setb"></span> for crops that are sown in <span id="highest-compare-diff-date"></span>
        </div>

        <h2 id="advisory_anchor" class="title" style="font-weight: 700; color:#547e1a"><?php __('Advisory') ?></h2>

        <div id="weather-advisory-div" class="disabled-view">
            <h3><?php __('Weather Advisory') ?></h3>
            <table id="rain-advisory" class="table" style="width:770px;">
                <thead>
                    <tr style="background-color: #ffffff;height:3px;">
                        <th style="width:70px"></th>
                        <th style="width:350px"></th>
                        <th style="width:350px"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="rainadv-twocc" style="background-color: #a9a9a9;">
                        <th>&nbsp;</th>
                        <th><span class="label label-info">1</span> <?php __('First crop') ?></th>
                        <th><span class="label label-warning">2</span> <?php __('Second Crop') ?></th>
                    </tr>
                    <tr style="background-color: #d9ff84">
                        <td><img class="img-circle" src="images/advisory01.png" style="width:50px;height:50px" /></td>
                        <td><span id="f-year"></span> <?php __('Total rainfall is') ?> <a href="javascript:launch_help('q1')"><span id="f-rain"></span></a></td>
                        <td class="rainadv-twocc"><span id="f-year2"></span> <?php __('Total rainfall is') ?> <a href="javascript:launch_help('q1')"><span id="f-rain2"></span></a></td>
                    </tr>
                    <tr>
                        <td><img class="img-circle" src="images/advisory02.png" style="width:50px;height:50px" /></td>
                        <td><a href="javascript:launch_help('q2')"><?php __('Onset of rain is on ') ?></a> <span id="rain-onset"></span></td>
                        <td class="rainadv-twocc"><a href="javascript:launch_help('q2')"><?php __('Onset of rain is on ') ?></a> <span id="rain-onset2"></span></td>
                    </tr>
                    <tr style="background-color: #d9ff84">
                        <td><img class="img-circle" src="images/advisory03.png" style="width:50px;height:50px" /></td>
                        <td><a href="javascript:launch_help('q3')"><?php __('Expected flooding dates:') ?></a><br /> <span id="wet-dates"></span></td>
                        <td class="rainadv-twocc"><a href="javascript:launch_help('q3')"><?php __('Expected flooding dates:') ?></a><br /> <span id="wet-dates2"></span></td>
                    </tr>
                    <tr>
                        <td><img class="img-circle" src="images/advisory04.png" style="width:50px;height:50px" /></td>
                        <td><a href="javascript:launch_help('q4')"><?php __('Expected drought dates:') ?></a><br /> <span id="dry-dates"></span></td>
                        <td class="rainadv-twocc"><a href="javascript:launch_help('q4')"><?php __('Expected drought dates:') ?></a><br /> <span id="dry-dates2"></span></td>
                    </tr>
                    <tr style="height:3px">
                        <td colspan="2" style="background-color: #d9ff84"></td>
                        <td class="rainadv-twocc" style="background-color: #d9ff84"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h3><?php __('Weather Chart') ?></h3>
        <div id="chart2" class="chart"></div>

        <h3><?php __('Calendar') ?></h3>
        <table id="opt-planting2" class="table table-bordered table-condensed table-dss">
            <thead>
                <tr style="background-color: #567B11">
                    <th rowspan="2" style="width:120px"><?php __('Crop') ?></th>
                    <th rowspan="2" style="width:105px"><?php __('Sowing Date') ?></th>
                    <th rowspan="2" class="alldates" style="width:90px"><?php __('Panicle Init.') ?></th>
                    <th rowspan="2" class="alldates" style="width:90px"><?php __('Flowering') ?></th>
                    <th rowspan="2" style="width:105px"><?php __('Harvest Date') ?></th>
                    <th rowspan="2" style="width:50px;text-align:right"><?php __('Yield') ?><br />(t/ha)</th>
                    <th colspan="3" style="text-align:center"><?php __('Fertilizer Schedule') ?></th>
                    <th colspan="3" class="npkamt" style="width:250px;text-align:center"><?php __('Fertilizer recommendation') ?><br/>N-P-K (kg/ha)</th>
                </tr>
                <tr style="background-color: #567B11">
                    <th style="width:145px"><?php __('Basal') ?></th>
                    <th style="width:145px"><?php __('Top Dress 1') ?></th>
                    <th style="width:145px"><?php __('Top Dress 2') ?></th>
                    <th class="npkamt" style="width:80px"><?php __('Basal') ?></th>
                    <th class="npkamt" style="width:80px"><?php __('Top Dress 1') ?></th>
                    <th class="npkamt" style="width:80px"><?php __('Top Dress 2') ?></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

        <h3><?php __("Farmer's Information") ?></h3>
        <table id="farm-info-table" class="table table-bordered table-condensed table-dss">
            <tr>
                <td style="font-weight: 700;background-color: #d9ff84"><?php __('Farm size') ?></td>
                <td>
                    <div class="input-append">
                        <input class="form-control" id="farm-size" type="text" style="width:35px" value="1" />
                        <span class="add-on">ha.</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="font-weight: 700;background-color: #d9ff84"><?php __('Number of family members') ?></td>
                <td>
                    <input class="form-control" id="family-num" type="text" style="width:35px" value="4" />
                </td>
            </tr>
        </table>

        <h3><?php __('Supplementary Irrigation') ?></h3>
        <table id="water-reqt-table" class="table table-bordered table-condensed table-dss">
            <tr style="background-color: #567B11">
                <th style="width:290px">&nbsp;</th>
                <th style="width:290px"><span class="label label-info">1</span> First crop</th>
                <th style="width:290px"><span class="label label-warning">2</span> Second crop</th>
            </tr>
            <tr>
                <td style="font-weight: 700;background-color: #d9ff84"><?php __('Rice Variety') ?></td>
                <td><span id="suppl-1-var">IR64</span></td>
                <td><span id="suppl-2-var">IR64</span></td>
            </tr>
            <tr>
                <td style="font-weight: 700;background-color: #d9ff84"><?php __('Total Rainfall') ?></td>
                <td><span id="suppl-1-1">0</span> mm</td>
                <td><span id="suppl-2-1">0</span> mm</td>
            </tr>
            <tr>
                <td style="font-weight: 700;background-color: #d9ff84"><?php __('Water requirement') ?></td>
                <td><span id="suppl-1-2">0</span> mm</td>
                <td><span id="suppl-2-2">0</span> mm</td>
            </tr>
            <tr>
                <td style="font-weight: 700;background-color: #d9ff84"><?php __('Water deficit') ?></td>
                <td><span id="suppl-1-3">0</span> mm</td>
                <td><span id="suppl-2-3">0</span> mm</td>
            </tr>
            <tr>
                <td colspan="3" style="font-weight: 700"><?php __('Water pump info') ?></td>
            </tr>
            <tr>
                <td style="font-weight: 700;background-color: #d9ff84"><?php __('Pump discharge rate') ?></td>
                <td colspan="2">
                    <div class="input-append">
                        <input class="form-control" id="pump-rate" type="text" style="width:35px" value="20" />
                        <span class="add-on">L / sec</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="font-weight: 700;background-color: #d9ff84"><?php __('Fuel consumption<br />rate') ?></td>
                <td colspan="2">
                    <div class="input-append">
                        <input class="form-control" id="fuel-rate" type="text" style="width:35px" value="1" />
                        <span class="add-on">L / hr</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="font-weight: 700;background-color: #d9ff84"><?php __('Fuel Price') ?></td>
                <td colspan="2">
                    <div class="input-append">
                        <input class="form-control" id="fuel-price" type="text" style="width:50px" value="9300" />
                        <span class="add-on currency-name"></span>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="font-weight: 700"><?php __('Guidelines') ?></td>
            </tr>
            <tr>
                <td style="font-weight: 700;background-color: #d9ff84"><?php __('Schedule') ?></td>
                <td><span id="suppl-1-sched">Drought period (5-6 day interval)</span></td>
                <td><span id="suppl-2-sched">Drought period (5-6 day interval)</span></td>
            </tr>
            <tr>
                <td style="font-weight: 700;background-color: #d9ff84"><?php __('Amount of time needed<br />to irrigate deficit') ?></td>
                <td>(<span id="suppl-1-4">55</span> hr/ha) X (<span id="suppl-1-fz">0</span> ha) = <span id="suppl-1-7">0</span> hr</td>
                <td>(<span id="suppl-2-4">55</span> hr/ha) X (<span id="suppl-2-fz">0</span> ha) = <span id="suppl-2-7">0</span> hr</td>
            </tr>
            <tr>
                <td style="font-weight: 700;background-color: #d9ff84"><?php __('Fuel consumption') ?></td>
                <td><span id="suppl-1-5">0</span> L</td>
                <td><span id="suppl-2-5">0</span> L</td>
            </tr>
            <tr>
                <td style="font-weight: 700;background-color: #d9ff84"><?php __('Fuel cost') ?></td>
                <td><span id="suppl-1-6">0</span> <span class="currency-name"></span></td>
                <td><span id="suppl-2-6">0</span> <span class="currency-name"></span></td>
            </tr>
        </table>

        <h3><?php __('Total Production') ?></h3>

        <table id="total-production-table">
            <tr>
                <td style="text-align:center;padding:5px">
                    <p style="padding:0;margin-bottom: 5px">
                        Grain Yield for<br />
                        <span class="label label-info">1</span> first crop is <span id="grain_yield1" style="font-weight: 700"></span> t/ha
                    </p>
                    <div id="rice-sack1" style="background-image:url('images/rice-sack.jpg');background-repeat:repeat-x;width:1px;height:62px;margin:auto"></div>
                </td>
                <td style="text-align:center;padding: 0 20px 0 20px;font-size: 30px;font-weight: 700">
                    <img src="images/plus.jpg" style="width:45px;height:45px;border:none" />
                </td>
                <td style="text-align:center;padding:5px">
                    <p style="padding:0;margin-bottom: 5px">
                        Grain Yield for<br />
                        <span class="label label-warning">2</span> second crop is <span id="grain_yield2" style="font-weight: 700"></span> t/ha
                    </p>
                    <div id="rice-sack2" style="background-image:url('images/rice-sack.jpg');background-repeat:repeat-x;width:1px;height:62px;margin:auto"></div>
                </td>
                <td style="text-align:center;padding: 0 20px 0 20px;font-size: 30px;font-weight: 700">
                    <img src="images/equals.png" style="width:80px;height:100px;border:none" />
                </td>
                <td style="text-align:center;padding:5px;margin:auto">
                    <p style="padding:0;margin:0;font-weight:700">TOTAL YIELD</p>
                    <p style="line-height:30px;font-size:24px;font-weight:700">
                        <span id="total_grain_yield"></span> t/ha
                    </p>
                </td>
            </tr>
        </table>

        <table id="farmer-advisory-table" class="table table-bordered table-condensed table-dss">
            <tr style="background-color: #567B11">
                <th style="width:290px">&nbsp;</th>
                <th style="width:290px"><span class="label label-info">1</span> First crop (t)</th>
                <th style="width:290px"><span class="label label-warning">2</span> Second crop (t)</th>
                <th style="width:290px">TOTAL (t)</th>
            </tr>
            <tr>
                <td style="font-weight: 700;background-color: #d9ff84"><?php __('Actual production') ?></td>
                <td><span id="actual-yield-1">0</span></td>
                <td><span id="actual-yield-2">0</span></td>
                <td><span id="actual-yield-3">0</span></td>
            </tr>
            <tr>
                <td style="font-weight: 700;background-color: #d9ff84"><?php __('Family consumption') ?></td>
                <td><span id="yield-consume-1">0</span></td>
                <td><span id="yield-consume-2">0</span></td>
                <td><span id="yield-consume-3">0</span></td>
            </tr>
            <tr>
                <td style="font-weight: 700;background-color: #d9ff84"><?php __('Surplus') ?></td>
                <td><span id="yield-surplus-1">0</span></td>
                <td><span id="yield-surplus-2">0</span></td>
                <td><span id="yield-surplus-3">0</span></td>
            </tr>
        </table>

<?php if (_opt(sysoptions::_OPT_SHOW_DATAGRID)) : ?>
            <div id="rawcomputation">
                <h2>Raw Data and Computations</h2>
                <div class="alert"><i class="icon-warning-sign"> </i>
                    CONFIDENTIAL INFORMATION: For Internal use only!. Make sure this is not shown during demonstration.
                </div>
                <h3>Chart Data</h3>
                <div id="datagrid1" class="datagrid-dss"></div>
                <h3>Rainfall Schedule</h3>
                <div id="raw_rain" class="datagrid-dss"></div>
            </div>
<?php endif; ?>

    </div>

</div>

<!-- TEMPLATE: Fertilizer Schedule -->
<div id="fert_sched_template3" class="html-template"></div>
<!-- TEMPLATE: Observed Rainfall -->
<div id="fert_sched_template2" class="html-template">
    <p><b>Observed Rainfall</b></p>
    <table class="table table-bordered table-condensed" style="width:440px">
        <thead>
            <tr style="background-color: #8CB730">
                <th style="width:210px">Date</th>
                <th style="width:150px">Rainfall (mm/decade)</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<!-- TEMPLATE: Variety Info -->
<div id="variety-info-template" class="html-template">
    <h4><i class="icon-book"></i> Info on {{variety_name}}:</h4>
    <p>
        &bull; Maturity: {{maturity}} days ({{maturity_grp}})<br />
        &bull; Yield Average: {{yield_avg}} t.<br />
        &bull; Yield Potential: {{yield_potential}} t.
    </p>
</div>



<script type="text/javascript" src="js/highcharts.js"></script>
<script type="text/javascript" src="js/highcharts-more.js"></script>
<?php if (_opt(sysoptions::_SHOW_MAP)) : ?>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCOk-7pjbZ3ip7vVLv443hfeNbwdkR5Qr0"></script>
<?php endif; ?>
<script type="text/javascript" src="gzip.php?group=oryza"></script>