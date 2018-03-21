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

<section id="main-content">
    <div class="container">
        <h2 id="page-title"><?php echo _CURRENT_OPT ?></h2>

        <div class="card mb-4">
            <div class="card-body">

                <form id ="frm" name="frm" action="#" method="post">

                    <input type="hidden" id="cs2_country" name="cs2_country" value="<?php echo $country_choice ?>">
                    <input type="hidden" id="cs2_type" name="cs2_type" value="recommend" />
                    <input type="hidden" id="cs2_fertil1" name="cs2_fertil1" value="1" />
                    <input type="hidden" id="cs2_fertil2" name="cs2_fertil2" value="1" />

                    <legend><?php echo __('Dataset') ?></legend>

                    <div class="form-group m-0">
                        <label for="station"><?php __('Location') ?></label>
                    </div>

                    <div class="form-row">

                        <div class="col-md-auto mb-2">
                            <button id="btn-select-country" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="flag-icon flag-icon-<?php echo strtolower($country_choice) ?>"></span> <?php echo $all_country[$country_choice]['country'] ?>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="btn-select-country">
                                <?php foreach ($all_country as $country_code => $country_attr): ?>
                                    <a class="dropdown-item" href="index.php?pageaction=weather&country=<?php echo $country_code ?>"><span class="flag-icon flag-icon-<?php echo strtolower($country_code) ?>"></span> <?php echo $country_attr['country'] ?></a>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="col">
                            <div class="input-group">
                                <input id="location2_name" type="text" class="form-control" disabled="disabled">
                                <div id="location_div" class="input-group-append">
                                    <button id="location2_btn" class="btn btn-outline-secondary" type="button"><i class="fas fa-map"></i></button>
                                </div>
                            </div>

                            <select class="form-control" name="cs2_station" id="cs2_station">
                                <option value="0"><?php __('Location') ?> &raquo;</option>
                            </select>
                        </div>

                    </div>

                    <div class="form-group">
                        <label for="cs2_type_year"><?php __('Year') ?></label>
                        <div class="row">
                            <div class="col-md-auto">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="type-year-pre">Historical</span>
                                    </div>
                                    <select class="form-control" name="cs2_type_year" id="cs2_type_year">
                                        <option value=""><?php __('Year') ?> &raquo;</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="cs2_recommend">
                        <legend><?php echo __('Select Rice Variety Combination') ?></legend>

                        <div id="combi-preview" class="card mb-2">
                            <div class="card-body">
                                <img id="fakeimg01" class="img-fluid" src="images/fake01.jpg" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="cs2_variety1"><?php echo __('Variety for first crop') ?></label>
                            <select class="form-control" name="cs2_variety1" id="cs2_variety1">
                                <option value=""><?php __('Variety') ?> &raquo;</option>
                            </select>
                            <div id="variety-info-1" class="variety-info"></div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="cs2_variety2"><?php echo __('Variety for second crop') ?></label>
                            <select class="form-control" name="cs2_variety2" id="cs2_variety2">
                                <option value=""><?php __('Variety') ?> &raquo;</option>
                            </select>
                            <div id="variety-info-2" class="variety-info"></div>
                        </div>

                    </div>

                    <div class="cs2_custom">
                        <legend><?php echo __('Advisory Options') ?></legend>

                        <div id="grain-yield-preview" class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo __('Grain Yield Preview') ?></h5>
                                <img id="fakeimg03" class="img-fluid" src="images/fake02.jpg" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="cs2_fertil0"><?php echo __('Fertilizer Application') ?></label>
                            <select class="form-control" name="cs2_fertil0" id="cs2_fertil0" size="<?php echo $fertoptscount ?>">
                                <?php foreach ($fertopts as $opts) : ?>
                                    <option value="<?php echo $opts[0] ?>" <?php echo ($opts[0] == '') ? 'class="select-extra"' : '' ?> <?php echo (dss_utils::getLastSelectValues('fert') == $opts[0]) ? 'selected="selected"' : '' ?> ><?php echo $opts[1] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="cs2_month1"><?php echo __('First crop sowing date') ?></label>
                            <select class="form-control" name="cs2_month1" id="cs2_month1">
                                <option value=""><?php __('Sowing Date') ?> &raquo;</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="cs2_month2"><?php echo __('2nd crop sowing date') ?></label>
                            <select class="form-control" name="cs2_month2" id="cs2_month2">
                                <option value=""><?php __('Sow Date') ?> &raquo;</option>
                            </select>
                        </div>

                    </div>

                    <button id="show" class="btn" type="submit"><i class="fas fa-chart-area"></i> <?php __('Show Advisory') ?></button>
                    <button id="showcustom" class="btn" type="button"><i class="fas fa-wrench"></i> <?php __('... More Options') ?></button>

                </form>

            </div>
        </div>

        <div class="alert alert-danger">
            <span class="fa-stack">
                <i class="fas fa-circle fa-stack-2x"></i>
                <i class="fas fa-exclamation fa-stack-1x fa-inverse"></i>
            </span>
            <span id="dss-error-box">error message here</span>
        </div>

        <div id="homeimages" class="card">
            <div class="card-body text-center">
                <img src="images/home02.jpg" />
            </div>
        </div>

        <div id="advisory">

            <h2 class="title">Dataset</h2>

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-auto"><?php __('Location') ?></div>
                        <div class="col"><span id="adv-location"></span></div>
                    </div>
                    <div class="row">
                        <div class="col-auto"><?php __('Year') ?></div>
                        <div class="col"><span id="adv-year"></span></div>
                    </div>
                </div>
            </div>

            <!-- START: two calendar -->
            <div id="twocropcal">

                <h2 class="title">Optimum sowing dates for two cropping seasons</h2>

                <div class="table-responsive">
                    <table id="twocropcalbest" class="table table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th colspan="4"><span class="badge badge-info">First crop</span></th>
                                <th colspan="4"><span class="badge badge-warning">Second crop</span></th>
                                <th rowspan="2" style="width:80px">Total<br />Yield(t/ha)</th>
                                <th rowspan="2" style="width:100px;text-align:center">Advisory</th>
                            </tr>
                            <tr>
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

            </div>
            <!-- END: two calendar -->

            <h2 id="advisory_anchor" class="title"><?php __('Advisory') ?></h2>

            <!-- START: calendar details -->
            <h3><?php __('Calendar') ?></h3>
            <div class="chartdiv table-responsive mb-3">
                <table class="table table-sm">
                    <tr>
                        <td>
                            <div id="chart2" class="chart mb-2"></div>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="table-responsive">
                <table id="opt-planting2" class="table table-bordered">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width:120px"><?php __('Crop') ?></th>
                            <th rowspan="2" style="width:105px"><?php __('Sowing Date') ?></th>
                            <th rowspan="2" class="alldates" style="width:90px"><?php __('Panicle Init.') ?></th>
                            <th rowspan="2" class="alldates" style="width:90px"><?php __('Flowering') ?></th>
                            <th rowspan="2" style="width:105px"><?php __('Harvest Date') ?></th>
                            <th rowspan="2" style="width:50px;text-align:right"><?php __('Yield') ?><br />(t/ha)</th>
                            <th colspan="3" style="text-align:center"><?php __('Fertilizer Schedule') ?></th>
                            <th colspan="3" class="npkamt" style="width:250px;text-align:center"><?php __('Fertilizer recommendation') ?><br/>N-P-K (kg/ha)</th>
                        </tr>
                        <tr>
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
            </div>
            <!-- END: calendar details -->

            <!-- START: farmers details -->
            <h3><?php __("Farmer's Information") ?></h3>

            <div id="farm-info-table" class="card mb-3">
                <div class="card-body">

                    <div class="form-group">
                        <label class="control-label" for="farm-size"><?php echo __('Farm size') ?></label>
                        <div class="input-group">
                            <input class="form-control" id="farm-size" type="text" value="1" />
                            <div class="input-group-prepend">
                                <div class="input-group-text">ha.</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label" for="family-num-young""><?php echo __('Number of family members') ?></label>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">0-14 yrs. old</div>
                            </div>
                            <input class="form-control" id="family-num-young" type="text" value="2" />
                        </div>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">15+ yrs. old</div>
                            </div>
                            <input class="form-control" id="family-num-old" type="text" value="2" />
                        </div>
                    </div>

                </div>
            </div>
            <!-- END: farmers details -->

            <!-- START: irrigation -->
            <h3><?php __('Supplementary Irrigation') ?></h3>

            <div class="table-responsive">
                <table id="water-reqt-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">&nbsp;</th>
                            <th scope="col"><span class="badge badge-info">First crop</span></th>
                            <th scope="col"><span class="badge badge-warning">Second crop</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row"><?php __('Rice Variety') ?></th>
                            <td><span id="suppl-1-var"></span></td>
                            <td><span id="suppl-2-var"></span></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php __('Total Rainfall') ?></th>
                            <td><span id="suppl-1-1">0</span> mm</td>
                            <td><span id="suppl-2-1">0</span> mm</td>
                        </tr>
                        <tr>
                            <th scope="row"><?php __('Water requirement') ?></th>
                            <td><span id="suppl-1-2">0</span> mm (<span id="suppl-1-method">0</span>)</td>
                            <td><span id="suppl-2-2">0</span> mm (<span id="suppl-2-method">0</span>)</td>
                        </tr>
                        <tr>
                            <th scope="row"><?php __('Water deficit') ?></th>
                            <td><span id="suppl-1-3">0</span> mm</td>
                            <td><span id="suppl-2-3">0</span> mm</td>
                        </tr>
                        <tr>
                            <th scope="row" colspan="3"><?php __('Water pump info') ?></th>
                        </tr>
                        <tr>
                            <td colspan="3">

                                <div class="form-group">
                                    <label class="control-label" for="pump-rate"><?php echo __('Pump discharge rate') ?></label>
                                    <div class="input-group">
                                        <input class="form-control" id="pump-rate" type="text" value="20" />
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">liters / second</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label" for="fuel-rate"><?php echo __('Fuel consumption rate') ?></label>
                                    <div class="input-group">
                                        <input class="form-control" id="fuel-rate" type="text" value="1" />
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">liters / hour</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label" for="fuel-price"><?php echo __('Fuel Price') ?></label>
                                    <div class="input-group">
                                        <input class="form-control" id="fuel-price" type="text" value="9300" />
                                        <div class="input-group-prepend">
                                            <div id="currency-name" class="input-group-text">rupiah</div>
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>
                        <tr>
                            <th scope="row" colspan="3"><?php __('Guidelines') ?></th>
                        </tr>
                        <tr>
                            <th scope="row"><?php __('Schedule') ?></th>
                            <td id="suppl-1-sched">Drought period (5-6 day interval)</td>
                            <td id="suppl-2-sched">Drought period (5-6 day interval)</td>
                        </tr>
                        <tr>
                            <th scope="row"><?php __('Amount of time needed<br />to irrigate deficit') ?></th>
                            <td id="suppl-1-4">(55 hr/ha) X (1 ha) = 55 hr</td>
                            <td id="suppl-2-4">(55 hr/ha) X (1 ha) = 55 hr</td>
                        </tr>
                        <tr>
                            <th scope="row"><?php __('Fuel consumption') ?></th>
                            <td id="suppl-1-5">0 L</td>
                            <td id="suppl-2-5">0 L</td>
                        </tr>
                        <tr>
                            <th scope="row"><?php __('Fuel cost') ?></th>
                            <td id="suppl-1-6">0 Pesos</td>
                            <td id="suppl-2-6">0 Pesos</td>
                        </tr>
                    </tbody>
                </table>
                <!--END: irrigation -->

                <!-- START: summary -->
                <h3><?php __('Total Production') ?></h3>

                <div class="card mb-3">
                    <div class="card-body">

                        <div class="table-responsive-sm">
                            <table id="total-production-table">
                                <tr>
                                    <td>
                                        <p>
                                            Grain Yield for<br />
                                            <span class="badge badge-info">first crop</span> is <span id="grain_yield1" style="font-weight: 700"></span> t/ha
                                        </p>
                                        <div id="rice-sack1" style="background-image:url('images/rice-sack.jpg');background-repeat:repeat-x;width:1px;height:62px;margin:auto"></div>
                                    </td>
                                    <td>
                                        <i class="fas fa-plus-circle"></i>
                                    </td>
                                    <td>
                                        <p>
                                            Grain Yield for<br />
                                            <span class="badge badge-warning">second crop</span> is <span id="grain_yield2" style="font-weight: 700"></span> t/ha
                                        </p>
                                        <div id="rice-sack2" style="background-image:url('images/rice-sack.jpg');background-repeat:repeat-x;width:1px;height:62px;margin:auto"></div>
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-right"></i>
                                    </td>
                                    <td>
                                        <p>TOTAL YIELD</p>
                                        <p>
                                            <span id="total_grain_yield"></span> t/ha
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="farmer-advisory-table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">&nbsp;</th>
                                <th scope="col"><span class="badge badge-info">First crop</span> (t)</th>
                                <th scope="col"><span class="badge badge-warning">Second crop</span> (t)</th>
                                <th scope="col">TOTAL (t)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row"><?php __('Actual production') ?></th>
                                <td><span id="actual-yield-1">0</span></td>
                                <td><span id="actual-yield-2">0</span></td>
                                <td><span id="actual-yield-3">0</span></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php __('Family consumption') ?></th>
                                <td><span id="yield-consume-1">0</span></td>
                                <td><span id="yield-consume-2">0</span></td>
                                <td><span id="yield-consume-3">0</span></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php __('Surplus') ?></th>
                                <td><span id="yield-surplus-1">0</span></td>
                                <td><span id="yield-surplus-2">0</span></td>
                                <td><span id="yield-surplus-3">0</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- END: summary -->

            </div>

            <div id="width-ref">&nbsp;</div>


        </div>
</section>



<script type="text/javascript" src="js/highcharts.js"></script>
<script type="text/javascript" src="js/highcharts-more.js"></script>
<?php if (_opt(sysoptions::_SHOW_MAP)) : ?>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCOk-7pjbZ3ip7vVLv443hfeNbwdkR5Qr0"></script>
<?php endif; ?>
<script type="text/javascript" src="gzip.php?group=oryza"></script>