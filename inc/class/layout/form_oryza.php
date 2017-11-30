<?php
$fertopts = $cls->getFertOpts();
$fertoptscount = count($fertopts);
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

    <div id ="cropseason" class="dselect" style="width:300px;text-align:center;display:none">
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
        <legend><?php echo __('Dataset')?></legend>

        <div class="form-group" style="padding: 3px">

            <div class="bfh-selectbox bfh-countries" data-countryList="ID,LA,PH,TH" data-country="<?php echo dss_utils::getLastSelectValues('country')?>" data-flags="true" style="float:left">
                <input id="country" name="country" type="hidden" value="<?php echo dss_utils::getLastSelectValues('country')?>">
                <a class="bfh-selectbox-toggle" role="button" data-toggle="bfh-selectbox" href="#">
                    <span class="bfh-selectbox-option input-small" data-option=""></span>
                    <b class="caret"></b>
                </a>
                <div class="bfh-selectbox-options" style="min-width:130px">
                    <input type="text" class="bfh-selectbox-filter" style="display:none">
                    <div role="listbox">
                        <ul role="option" style="width:130px">
                        </ul>
                    </div>
                </div>
            </div>

            &nbsp;
            <div class="input-append" id="location_div">
                <input class="form-control span2" id="location_name" type="text" disabled="disabled" style="width:400px;padding:4px 10px">
                <button id="location_btn" class="form-control btn" type="button"><i class="icon-map-marker"></i></button>
            </div>

            <select class="form-control" style="width:450px;display:none" name="station" id="station">
                <option value="0"><?php __('Location') ?> &raquo;</option>
            </select>

            <div id="set" style="padding:10px 0 0 0">
                <label><?php echo __('Set')?> 1: </label>

                <select class="form-control" name="type_year" id="type_year" style="width:100px">
                    <option value=""><?php __('Year') ?> &raquo;</option>
                </select>
                &nbsp;&nbsp;

                <select class="form-control" name="variety" id="variety" style="width:150px">
                    <option value=""><?php __('Variety')?> &raquo;</option>
                </select>
                &nbsp;&nbsp;

                <select class="form-control" name="fertil" id="fertil" style="width:210px">
                    <?php foreach ($cls->getFertOpts() as $opts) : ?>
                        <option value="<?php echo $opts[0]?>" <?php echo ($opts[0]=='')?'class="select-extra"' :'' ?> <?php echo (dss_utils::getLastSelectValues('fert')==$opts[0]) ? 'selected="selected"' : '' ?> ><?php echo $opts[1]?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="set2" style="display:none;padding:10px 0 0 0">
                <label><?php echo __('Set')?> 2: </label>

                <select class="form-control" name="type_year2" id="type_year2" style="width:100px">
                    <option value=""><?php __('Year') ?> &raquo;</option>
                </select>
                &nbsp;&nbsp;

                <select class="form-control" name="variety2" id="variety2" style="width:150px">
                    <option value=""><?php __('Variety')?> &raquo;</option>
                </select>
                &nbsp;&nbsp;

                <select class="form-control" name="ferti2" id="fertil2" style="width:210px">
                    <?php foreach ($cls->getFertOpts() as $opts) : ?>
                        <option value="<?php echo $opts[0]?>" <?php echo ($opts[0]=='')?'class="select-extra"' :'' ?> <?php echo (dss_utils::getLastSelectValues('fert')==$opts[0]) ? 'selected="selected"' : '' ?> ><?php echo $opts[1]?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="set3" style="display:none;padding:10px 0 0 0">
                <label><?php echo __('Set')?> 3: </label>

                <select class="form-control" name="type_year3" id="type_year3" style="width:100px">
                    <option value=""><?php __('Year') ?> &raquo;</option>
                </select>
                &nbsp;&nbsp;

                <select class="form-control" name="variety3" id="variety3" style="width:150px">
                    <option value=""><?php __('Variety')?> &raquo;</option>
                </select>
                &nbsp;&nbsp;

                <select class="form-control" name="fertil3" id="fertil3" style="width:210px">
                    <?php foreach ($cls->getFertOpts() as $opts) : ?>
                        <option value="<?php echo $opts[0]?>" <?php echo ($opts[0]=='')?'class="select-extra"' :'' ?> <?php echo (dss_utils::getLastSelectValues('fert')==$opts[0]) ? 'selected="selected"' : '' ?> ><?php echo $opts[1]?></option>
                    <?php endforeach; ?>
                </select>

            </div>

            <div id="set4" style="display:none;padding:10px 0 0 0">
                <label><?php echo __('Set')?> 4: </label>

                <select class="form-control" name="type_year4" id="type_year4" style="width:100px">
                    <option value=""><?php __('Year') ?> &raquo;</option>
                </select>
                &nbsp;&nbsp;

                <select class="form-control" name="variety4" id="variety4" style="width:150px">
                    <option value=""><?php __('Variety')?> &raquo;</option>
                </select>
                &nbsp;&nbsp;

                <select class="form-control" name="fertil4" id="fertil4" style="width:210px">
                    <?php foreach ($cls->getFertOpts() as $opts) : ?>
                        <option value="<?php echo $opts[0]?>" <?php echo ($opts[0]=='')?'class="select-extra"' :'' ?> <?php echo (dss_utils::getLastSelectValues('fert')==$opts[0]) ? 'selected="selected"' : '' ?> ><?php echo $opts[1]?></option>
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
        
        <div class="alert alert-success">
          Click <a id="csone" href="#">here</a> to use the previous version of this feature.
        </div>    
        <form role="form" class="form" id ="frm" name="frm" action="#" method="post">
            <input type="hidden" name="cs2_type" value="recommend" />
            <input type="hidden" id="cs2_fertil1" name="cs2_fertil1" value="1" />
            <input type="hidden" id="cs2_fertil2" name="cs2_fertil2" value="1" />

            <fieldset style="margin-top:0">
                <legend><?php echo __('Dataset')?></legend>

                <label class="control-label" for="cs2_station"><?php __('Station') ?></label>

                <div class="bfh-selectbox bfh-countries" data-countryList="ID,LA,PH,TH" data-country="<?php echo dss_utils::getLastSelectValues('country')?>" data-flags="true" style="float:left">
                    <input id="cs2_country" name="country" type="hidden" value="<?php echo dss_utils::getLastSelectValues('country')?>">
                    <a class="bfh-selectbox-toggle" role="button" data-toggle="bfh-selectbox" href="#">
                        <span class="bfh-selectbox-option input-small" data-option=""></span>
                        <b class="caret"></b>
                    </a>
                    <div class="bfh-selectbox-options" style="min-width:130px">
                        <input type="text" class="bfh-selectbox-filter" style="display:none">
                        <div role="listbox">
                            <ul role="option" style="width:130px">
                            </ul>
                        </div>
                    </div>
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

                <div class="cs2_recommend" style="padding:3px;display:none">
                    <legend><?php echo __('Select Combination')?></legend>

                    <label class="control-label" for="fakeimg01"><?php echo __('Varietal combination')?></label>
                    <img id="fakeimg01" src="images/fake01.jpg" />

                    <label class="control-label" for="cs2r_variety1"><?php echo __('Variety for first crop')?></label>
                    <select class="form-control" name="cs2r_variety1" id="cs2r_variety1" style="width:150px">
                        <option value=""><?php __('Variety')?> &raquo;</option>
                    </select>
                    <div id="variety-info-1" class="variety-info"></div>

                    <label class="control-label" for="cs2r_variety2"><?php echo __('Variety for second crop')?></label>
                    <select class="form-control" name="cs2r_variety2" id="cs2r_variety2" style="width:150px">
                        <option value=""><?php __('Variety')?> &raquo;</option>
                    </select>
                    <div id="variety-info-2" class="variety-info"></div>

                    <div>
                        <button class="form-control btn btn-success showcombi" type="button"><i class="icon-thumbs-up icon-white"></i> <?php __('Show advisory') ?></button>
                    </div>
                </div>

                <div class="cs2_custom" style="padding:3px;display:none">
                    <legend><?php echo __('Select Combination')?></legend>

                    <label class="control-label" for="cs2c_variety0"><?php echo __('Variety')?></label>
                    <select class="form-control" name="cs2c_variety0" id="cs2c_variety0" style="width:150px" size="6" multiple="multiple">
                        <option value="">IR64</option>
                        <option value="">CIHERANG</option>
                        <option value="">INPARI 6</option>
                        <option value="">INPARI 10</option>
                        <option value="">INPARI 19</option>
                        <option value="">INPARI 21</option>
                    </select>

                    <label class="control-label" for="cs2_fertil0"><?php echo __('Fertilizer Application')?></label>
                    <select class="form-control" name="cs2_fertil0" id="cs2_fertil0" size="<?php echo $fertoptscount ?>" style="width:210px">
                        <?php foreach ($fertopts as $opts) : ?>
                            <option value="<?php echo $opts[0]?>" <?php echo ($opts[0]=='')?'class="select-extra"' :'' ?> <?php echo (dss_utils::getLastSelectValues('fert')==$opts[0]) ? 'selected="selected"' : '' ?> ><?php echo $opts[1]?></option>
                        <?php endforeach; ?>
                    </select>

                    <div>
                        <button class="form-control btn btn-success" id="showcustomgy" type="button"><i class="icon-picture icon-white"></i> <?php __('Show Grain Yields') ?></button>
                    </div>

                    <label class="control-label" for="fakeimg03"><?php echo __('Grain Yield')?></label>
                    <div style="background-color: #e9e9e9;width:520px;height:171px">
                        <img id="fakeimg03" src="images/fake02.jpg" style="width:520px;height:171px;display:none" />
                    </div>

                    <legend><?php echo __('First Crop')?></legend>

                    <label class="control-label" for="cs2_month1"><?php echo __('Sowing Date')?></label>
                    <select class="form-control" name="cs2_month1" id="cs2_month1" style="width:120px">
                        <option value=""><?php __('Sowing Date')?> &raquo;</option>
                    </select>

                    <label class="control-label" for="cs2_variety1"><?php echo __('Variety')?></label>
                    <select class="form-control" name="cs2_variety1" id="cs2_variety1" style="width:150px">
                        <option value=""><?php __('Variety')?> &raquo;</option>
                    </select>

                </div>

                <div class="cs2_custom" style="padding: 3px;display:none">
                    <legend><?php echo __('Second Crop')?></legend>

                    <label class="control-label" for="cs2_month2"><?php echo __('Sowing Date')?></label>
                    <select class="form-control" name="cs2_month2" id="cs2_month2" style="width:120px">
                        <option value=""><?php __('Sow Date')?> &raquo;</option>
                    </select>

                    <label class="control-label" for="cs2_variety2"><?php echo __('Sowing Date')?></label>
                    <select class="form-control" name="cs2_variety2" id="cs2_variety2" style="width:150px">
                        <option value=""><?php __('Variety')?> &raquo;</option>
                    </select>

                    <div>
                    <button class="form-control btn btn-success showcombi" type="button"><i class="icon-thumbs-up icon-white"></i> <?php __('Show advisory') ?></button>
                    </div>
                </div>

            </fieldset>
            <button class="form-control btn btn-success" name="showreco" id="showreco" type="button"><i class="icon-wrench icon-white"></i> <?php __('Show recommended advisory') ?></button>&nbsp;&nbsp;
            <button class="form-control btn" name="showcustom" id="showcustom" type="button"><i class="icon-wrench"></i> <?php __('Show customized advisory') ?></button>
        </form>
    </div>

    <div id="dss-error-box" class="alert alert-error alert-fixed"></div>

    <div id="homeimages" style="margin-top:30px">
        <div style="width:383px;position:relative;margin-left:auto; margin-right:auto">
            <img src="images/home02.jpg" width="383" height="322" style="width:383px;height:322px" />
            <div class="homeimages_overlay"><?php __('Grain Yield Advisory')?></div>
        </div>
    </div>

    <div id="grainyield_chart">
        <h2 id="advisory_anchor" class="title" style="font-weight: 700; color:#547e1a"><?php __('Simulated Grain Yield')?></h2>    
        <div id="chart1" class="chart"></div>
    </div>

    <div id="advisory" style="display:none">

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
                            <th colspan="4">First Cropping</th>
                            <th colspan="4">Second Cropping</th>
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

            <div id="fert-apply-adv" style="display:none">
                <p class="form-inline">2. <?php __('Show the advisory if sowing date is ')?>

                <table id="opt-planting3" class="table table-bordered table-condensed table-dss hide">
                    <thead>
                        <tr style="background-color: #567B11">
                            <th rowspan="2" style="width:90px"><?php __('Sowing Date')?></th>
                            <th rowspan="2" style="width:90px"><?php __('Panicle Init.')?></th>
                            <th rowspan="2" style="width:90px"><?php __('Flowering')?></th>
                            <th rowspan="2" style="width:90px"><?php __('Harvest Date')?></th>
                            <th rowspan="2" style="width:70px;text-align:right"><?php __('Yield')?><br />(t/ha)</th>
                            <th colspan="3" style="width:400px;text-align:center"><?php __('Fertilizer Schedule')?></th>
                            <th colspan="3"  class="hide" style="width:250px;text-align:center"><?php __('Fertilizer recommendation')?><br/>N-P-K (kg/ha)</th>
                        </tr>
                        <tr style="background-color: #567B11">
                            <th style="width:130px"><?php __('Basal')?></th>
                            <th style="width:130px"><?php __('Top Dress 1')?></th>
                            <th style="width:130px"><?php __('Top Dress 2')?></th>
                            <th class="hide" style="width:80px"><?php __('Basal')?></th>
                            <th class="hide" style="width:80px"><?php __('Top Dress 1')?></th>
                            <th class="hide" style="width:80px"><?php __('Top Dress 2')?></th>
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

            <div id="highest-compare-diff" style="display:none">
                3. Rainfed rice yield of <span id="highest-compare-diff-seta"></span> is <span id="highest-compare-diff-yld"></span> t/ha higher than <span id="highest-compare-diff-setb"></span> for crops that are sown in <span id="highest-compare-diff-date"></span>
            </div>

        <h2 id="advisory_anchor" class="title" style="font-weight: 700; color:#547e1a"><?php __('Advisory')?></h2>

        <div id="weather-advisory-div" style="display:none">
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
                    <th><?php __('First Cropping') ?></th>
                    <th><?php __('Second Cropping') ?></th>
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

        <h3><?php __('Weather Chart')?></h3>
        <div id="chart2" class="chart"></div>

        <h3><?php __('Calendar') ?></h3>
        <table id="opt-planting2" class="table table-bordered table-condensed table-dss">
            <thead>
                <tr style="background-color: #567B11">
                    <th rowspan="2" style="width:50px"><?php __('Crop') ?></th>
                    <th rowspan="2" style="width:100px"><?php __('Sowing Date') ?></th>
                    <th rowspan="2" class="alldates" style="width:90px"><?php __('Panicle Init.') ?></th>
                    <th rowspan="2" class="alldates" style="width:90px"><?php __('Flowering') ?></th>
                    <th rowspan="2" style="width:90px"><?php __('Harvest Date') ?></th>
                    <th rowspan="2" style="width:50px;text-align:right"><?php __('Yield') ?><br />(t/ha)</th>
                    <th colspan="3" style="width:400px;text-align:center"><?php __('Fertilizer Schedule') ?></th>
                    <th colspan="3" class="npkamt" style="width:250px;text-align:center"><?php __('Fertilizer recommendation') ?><br/>N-P-K (kg/ha)</th>
                </tr>
                <tr style="background-color: #567B11">
                    <th style="width:130px"><?php __('Basal') ?></th>
                    <th style="width:130px"><?php __('Top Dress 1') ?></th>
                    <th style="width:130px"><?php __('Top Dress 2') ?></th>
                    <th class="npkamt" style="width:80px"><?php __('Basal') ?></th>
                    <th class="npkamt" style="width:80px"><?php __('Top Dress 1') ?></th>
                    <th class="npkamt" style="width:80px"><?php __('Top Dress 2') ?></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

        <div id="supplement-irrig" style="display:none">
        <h3><?php __('Supplementary Irrigation') ?></h3>
        <table id="suppl_irrig" class="table table-bordered table-condensed table-dss">
            <tr style="background-color: #567B11">
                <th style="width:290px"><?php __('Amount (water deficit)') ?></th>
                <th style="width:290px">400mm</th>
            </tr>
            <tr>
                <td style="font-weight: 700;background-color: #d9ff84"><?php __('Schedule') ?></td>
                <td><span id="suppl_1">Drought period (5-6 day interval)</span></td>
            </tr>
            <tr>
                <td style="font-weight: 700;background-color: #d9ff84"><?php __('Time needed to irrigate deficit') ?></td>
                <td><span id="suppl_2">55 hr/ha</span></td>
            </tr>
            <tr>
                <td style="font-weight: 700;background-color: #d9ff84"><?php __('Fuel consumption rate') ?></td>
                <td><span id="suppl_3">1 L/hr</span></td>
            </tr>
            <tr>
                <td style="font-weight: 700;background-color: #d9ff84"><?php __('Fuel consumption') ?></td>
                <td><span id="suppl_3">55 L/ha</span></td>
            </tr>
        </table>
        </div>

        <div id="total-production-div" style="display:none">
        <h3><?php __('Total Production') ?></h3>
        <table id="total_grain_yield_p" style="display:none">
            <tr>
                <td style="text-align: center;border:1px solid #000;padding:5px">
                    <p style="font-size: 11px;padding:0;margin: 0">
                        Grain Yield for<br />
                        1st crop is <span id="grain_yield1" style="font-weight: 700"></span> t/ha
                    </p>
                    <div id="rice-sack1" style="background-image:url('images/rice-sack.jpg');background-repeat:repeat-x;width:1px;height:62px;margin:auto"></div>
                </td>
                <td style="padding: 0 20px 0 20px;font-size: 30px;font-weight: 700">+</td>
                <td style="text-align: center;border:1px solid #000;padding:5px">
                    <p style="font-size: 11px;padding:0;margin: 0">
                        Grain Yield for<br />
                        2nd crop is <span id="grain_yield2" style="font-weight: 700"></span> t/ha
                    </p>
                    <div id="rice-sack2" style="background-image:url('images/rice-sack.jpg');background-repeat:repeat-x;width:1px;height:62px;margin:auto"></div>
                </td>
                <td style="padding: 0 20px 0 20px;font-size: 30px;font-weight: 700">=</td>
                <td style="text-align: center;border:1px solid #000;padding:5px;margin:auto">
                    Total<br />
                    <p style="line-height:30px;font-size: 22px;font-weight: 700">
                        <span id="total_grain_yield"></span> t/ha
                    </div>
                </td>
                <td style="padding: 0 20px 0 0;">&nbsp;</td>
                <td style="text-align: center;border:1px solid #000;padding:5px;margin:auto">
                    Actual<br />Production (t)<br />(under construction)
                </td>
                <td style="padding: 0 20px 0 0">&nbsp;</td>
                <td style="text-align: center;border:1px solid #000;padding:5px;margin:auto">
                    Actual<br />Surplus (t)<br />(under construction)
                </td>
            </tr>
        </table>
        </div>

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
    <h4>Info on {{variety_name}}:</h4>
    <p>
        &raquo; Maturity: {{maturity}}<br />
        &raquo; Yield Average: {{yield_avg}}<br />
        &raquo; Yield Potential: {{yield_potential}}
    </p>
</div>



<script type="text/javascript" src="js/highcharts.js"></script>
<script type="text/javascript" src="js/highcharts-more.js"></script>
<?php if (_opt(sysoptions::_SHOW_MAP)) : ?>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCOk-7pjbZ3ip7vVLv443hfeNbwdkR5Qr0"></script>
<?php endif; ?>
<script type="text/javascript" src="gzip.php?group=oryza"></script>