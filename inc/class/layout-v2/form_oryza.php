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

        <p class="lead">
            <a href="http://irri.org/resources/publications/books/item/oryza2000-modeling-lowland-rice">ORYZA2000</a> was used to simulate grain yield scenarios.
            This allows us to predict the optimum crop schedule based on forecasted weather data.
            From these choices of possible scenarios, you can select the specific crop schedule that suits you best.
            In addition to that, we will guide you on several aspects to plan your cropping schedule.
        </p>

        <div id="crop-advisory-form" class="card">
            <div class="card-body">

                <form action="#">

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

                        <div class="col mb-2">
                            <div id="location2_div" class="input-group">
                                <input id="location2_name" type="text" class="form-control" disabled="disabled">
                                <div class="input-group-append">
                                    <button id="location2_btn" class="btn btn-outline-secondary" type="button"><i class="fas fa-map"></i></button>
                                </div>
                            </div>

                            <select class="form-control hide" name="cs2_station" id="cs2_station">
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
                        <legend><?php echo __('Rice Variety Combination') ?></legend>

                        <div id="combi-preview" class="card mb-2 hide">
                            <div class="card-body">
                                <img id="fakeimg01" class="img-fluid" src="images/fake01.jpg" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="cs2_variety1"><?php echo __('Variety') ?>: <span class="badge badge-info"><?php echo __('First crop') ?></span></label>
                            <select class="form-control" name="cs2_variety1" id="cs2_variety1">
                                <option value=""><?php __('Variety') ?> &raquo;</option>
                            </select>
                            <div class="row">
                                <div class="col-md-5">
                                    <div id="variety-info-1"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="cs2_variety2"><?php echo __('Variety') ?>: <span class="badge badge-warning"><?php echo __('Second crop') ?></span></label>
                            <select class="form-control" name="cs2_variety2" id="cs2_variety2">
                                <option value=""><?php __('Variety') ?> &raquo;</option>
                            </select>
                            <div class="row">
                                <div class="col-md-5">
                                    <div id="variety-info-2"></div>
                                </div>
                            </div>
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
                            <label class="control-label" for="cs2_month1"><?php echo __('Sowing date') ?>: <span class="badge badge-info"><?php echo __('First crop') ?></span></label>
                            <select class="form-control" name="cs2_month1" id="cs2_month1">
                                <option value=""><?php __('Sowing Date') ?> &raquo;</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="cs2_month2"> <?php echo __('Sowing date') ?>: <span class="badge badge-warning"><?php echo __('Second crop') ?></span></label>
                            <select class="form-control" name="cs2_month2" id="cs2_month2">
                                <option value=""><?php __('Sow Date') ?> &raquo;</option>
                            </select>
                        </div>

                    </div>

                    <button id="showcombi" class="btn btn-success mb-2 mr-2" type="button"><i class="fas fa-chart-area"></i> <?php __('Show Advisory') ?></button>
                    <button id="showcustom" class="btn mb-2" type="button"><i class="fas fa-wrench"></i> <?php __('... More Options') ?></button>

                </form>

            </div>
        </div>

        <div id="advisory" class="hide">

            <!-- START: two calendar -->
            <div id="twocropcal">

                <h4>Optimum sowing dates for two cropping seasons</h4>
                <p>
                    Below is the list of best schedules based on simulated grain yield values from ORYZA2000.
                    The colored rows are the the currently chosen schedule.
                    You can choose an alternate schedule by clicking on the "Choose" button at the right side.
                </p>                
                <p class="mb-0">
                    <strong><?php __('Location') ?>:</strong> <span id="adv-location"></span>, <?php echo $all_country[$country_choice]['country'] ?> <span class="flag-icon flag-icon-<?php echo strtolower($country_choice) ?>"></span><br />
                    <strong><?php __('Year') ?>:</strong> <span id="adv-year"></span>
                </p>
                <p id="target-sowdates" class="mt-0 hide">
                    <strong><?php __('Target sowing date') ?> <span class="badge badge-info"><?php echo __('First crop') ?></span>:</strong> <span id="adv-sowing1"></span><br />
                    <strong><?php __('Target sowing date') ?> <span class="badge badge-warning"><?php echo __('Second crop') ?></span>:</strong> <span id="adv-sowing2"></span>
                </p>

                <div class="table-responsive">
                    <table id="twocropcalbest" class="table table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th>
                                    <span class="badge badge-info"><?php echo __('First crop') ?></span><br />
                                    Sowing /<br />Harvest
                                </th>
                                <th>
                                    <span class="badge badge-warning"><?php echo __('Second crop') ?></span><br />
                                    Sowing /<br />Harvest
                                </th>
                                <th>Variety</th>
                                <th>Rainfall (mm)</th>
                                <th class="text-right">Yield (t/ha)</th>
                                <th class="text-right">Yield Total<br />(t/ha)</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                
                <div class="card mt-1">
                    <div class="card-body help-box">
                        <h5 id="help-rainfall-category" class="card-title"><i class="fas fa-book"></i> Rainfall Category:</h5>
                        <ul>
                            <li><strong>normal:</strong> Rainfall amount is similar to previous years</li>
                            <li><strong>above normal:</strong> Rainfall amount is greater than previous years</li>
                            <li><strong>below normal:</strong> Rainfall amount is less than previous years</li>
                        </ul>
                    </div>
                </div>                
                
            </div>
            <div id="advisory-title">&nbsp</div>
            <!-- END: two calendar -->

            <h4 class="mt-5"><?php __('Advisory') ?></h4>
            <p>
                You have chosen <span id="adv-sowdate1"></span> as the sowing date for the first crop and <span id="adv-sowdate2"></span> for the second crop.
                The following sections will guide you to maximize cropping inputs such as fertilizer application and irrigation requirements.
            </p>
            
            <!-- START: calendar details -->
            <h5><?php __('Calendar') ?></h5>
            <p>
                This is the schedule of the entire cropping calendar from sowing to harvest including the fertilizer application to attain the expected grain yield.
            </p>

            <div class="table-responsive">
                <table id="opt-planting2" class="table table-bordered">
                    <thead>
                        <tr>
                            <th rowspan="2"><?php __('Sowing Date') ?></th>
                            <th rowspan="2" class="alldates"><?php __('Panicle Init.') ?></th>
                            <th rowspan="2" class="alldates"><?php __('Flowering') ?></th>
                            <th rowspan="2"><?php __('Harvest Date') ?></th>
                            <th colspan="3" style="text-align:center"><?php __('Fertilizer Schedule') ?></th>
                            <th colspan="3" class="npkamt text-center"><?php __('Fertilizer recommendation') ?><br/>N-P-K (kg/ha)</th>
                        </tr>
                        <tr>
                            <th><?php __('Basal') ?></th>
                            <th><?php __('Top Dress 1') ?></th>
                            <th><?php __('Top Dress 2') ?></th>
                            <th class="npkamt"><?php __('Basal') ?></th>
                            <th class="npkamt"><?php __('Top Dress 1') ?></th>
                            <th class="npkamt"><?php __('Top Dress 2') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            
            <p>&nbsp;</p>

            <div class="chartdiv table-responsive">
                <table class="table table-sm">
                    <tr>
                        <td>
                            <div id="chart2" class="chart"></div>
                        </td>
                    </tr>
                </table>
            </div>
            <p>
                The red circle signifies dates where expected rainfall is less than what was observed in previous years. 
                The blue square signifies dates where expected rainfall is greater than what was observed in previous years.
            </p>
            <!-- END: calendar details -->

            <!-- START: farmers details -->
            <h5><?php __("Farmer's Information") ?></h5>
            <p>
                Please supply the information so we can compute the total grain yield with respect to the actual farm scenario.
            </p>

            <div class="row">
                <div class="col-md-6">
                    <div id="farm-info-table" class="card mb-3">
                        <div class="card-body">

                            <div class="form-group">
                                <label class="control-label" for="farm-size"><?php echo __('Farm size') ?></label>
                                <div class="row">
                                    <div class="col-auto">
                                        <div class="input-group">
                                            <input class="form-control" id="farm-size" type="text" size="3" maxlength="3" value="1" />
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">ha.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group m-0">
                                <label class="control-label" for="family-num-young""><?php echo __('Number of family members') ?></label>
                            </div>

                            <div class="form-row">
                                <div class="col-auto">
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">0-14 yrs. old</div>
                                        </div>
                                        <input class="form-control" id="family-num-young" type="text" size="2" maxlength="2" value="2" />
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">15+ yrs. old</div>
                                        </div>
                                        <input class="form-control" id="family-num-old" type="text" size="2" maxlength="2" value="2" />
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- END: farmers details -->

            <!-- START: irrigation -->
            <h5><?php __('Supplementary Irrigation') ?></h5>

            <p>
                This is advisory for supplemental irrigation and calculate costs.
            </p>

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-2">
                        <div class="card-body">

                            <p>
                                Please supply the information so we can compute the irrigation requirements.
                            </p>

                            <div class="form-group">
                                <label class="control-label" for="pump-rate"><?php echo __('Water pump discharge rate') ?></label>
                                <div class="row">
                                    <div class="col-auto">
                                        <div class="input-group">
                                            <input class="form-control" id="pump-rate" type="text" size="3" value="20" />
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">liters / second</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="fuel-rate"><?php echo __('Fuel consumption rate') ?></label>
                                <div class="row">
                                    <div class="col-auto">
                                        <div class="input-group">
                                            <input class="form-control" id="fuel-rate" type="text" size="2" value="1" />
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">liters / hour</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="fuel-price"><?php echo __('Fuel Price') ?></label>
                                <div class="row">
                                    <div class="col-auto">
                                        <div class="input-group">
                                            <input class="form-control" id="fuel-price" type="text" size="6" value="9300" />
                                            <div class="input-group-prepend">
                                                <div id="currency-name" class="input-group-text">rupiah</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>


            <div class="table-responsive">
                <table id="water-reqt-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">&nbsp;</th>
                            <th scope="col"><span class="badge badge-info"><?php echo __('First crop') ?></span></th>
                            <th scope="col"><span class="badge badge-warning"><?php echo __('Second crop') ?></span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="d-none">
                            <th scope="row"><?php __('Rice Variety') ?></th>
                            <td><span id="suppl-1-var"></span></td>
                            <td><span id="suppl-2-var"></span></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php __('Crop Establishment') ?></th>
                            <td><span id="suppl-1-method"></span></td>
                            <td><span id="suppl-2-method"></span></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php __('Rainfall') ?></th>
                            <td>
                                Expected rainfall is <span id="suppl-1-rain-amt"></span> mm. This is <span id="suppl-1-rain-code"></span> compared
                                to previous years.
                            </td>
                            <td>
                                Expected rainfall is <span id="suppl-2-rain-amt"></span> mm. This is <span id="suppl-2-rain-code"></span> compared
                                to previous years.
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php __('Water requirement') ?></th>
                            <td><span id="suppl-1-2">0</span> mm</td>
                            <td><span id="suppl-2-2">0</span> mm</td>
                        </tr>
                        <tr>
                            <th scope="row"><?php __('Water deficit') ?></th>
                            <td><span id="suppl-1-3">0</span> mm</td>
                            <td><span id="suppl-2-3">0</span> mm</td>
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
            </div>
            <!--END: irrigation -->

            <!-- START: summary -->
            <h5><?php __('Total Production') ?></h5>
            
            <div class="table-responsive-sm">
                <table id="total-production-table">
                    <tr>
                        <td class="border border-success p-2">

                            <p class="text-center">
                                Grain Yield for<br />
                                <span class="badge badge-info"><?php echo __('First crop') ?></span> is <span id="grain_yield1" class="font-weight-bold"></span> t/ha
                            </p>
                            <div id="rice-sack1" style="background-image:url('images/rice-sack.jpg');background-repeat:repeat-x;width:1px;height:62px;margin:auto"></div>

                        </td>
                        <td>
                            <i class="fas fa-plus-circle yield-op"></i>
                        </td>
                        <td class="border border-success p-2">

                            <p class="text-center">
                                Grain Yield for<br />
                                <span class="badge badge-warning"><?php echo __('Second crop') ?></span> is <span id="grain_yield2" class="font-weight-bold"></span> t/ha
                            </p>
                            <div id="rice-sack2" style="background-image:url('images/rice-sack.jpg');background-repeat:repeat-x;width:1px;height:62px;margin:auto"></div>

                        </td>
                        <td>
                            <i class="fas fa-arrow-alt-circle-right yield-op"></i>
                        </td>
                        <td class="border border-success p-2">

                            <p class="text-center font-weight-bold">
                                TOTAL YIELD<br />
                                <span id="total_grain_yield"></span> t/ha
                            </p>
                        </td>
                    </tr>
                </table>
            </div>   
            
            <p class="mt-4">
                The total rice production of the entire cropping season is calculated with respect to the specific farmer's information supplied above.
            </p>

            <div class="table-responsive">
                <table id="farmer-advisory-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th class="text-right"><span class="badge badge-info"><?php echo __('First crop') ?></span> (t)</th>
                            <th class="text-right"><span class="badge badge-warning"><?php echo __('Second crop') ?></span> (t)</th>
                            <th class="text-right">TOTAL (t)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th><?php __('Actual production') ?></th>
                            <td class="text-right"><span id="actual-yield-1">0</span></td>
                            <td class="text-right"><span id="actual-yield-2">0</span></td>
                            <td class="text-right"><span id="actual-yield-3">0</span></td>
                        </tr>
                        <tr>
                            <th>
                                <?php __('Family consumption') ?> <span class="badge badge-secondary">1</span>                                
                            </th>
                            <td class="text-right"><span id="yield-consume-1">0</span></td>
                            <td class="text-right"><span id="yield-consume-2">0</span></td>
                            <td class="text-right"><span id="yield-consume-3">0</span></td>
                        </tr>
                        <tr>
                            <th><?php __('Surplus') ?></th>
                            <td class="text-right"><span id="yield-surplus-1">0</span></td>
                            <td class="text-right"><span id="yield-surplus-2">0</span></td>
                            <td class="text-right"><span id="yield-surplus-3">0</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p>
                <span class="badge badge-secondary">1</span> <small>Rice consumption of one adult person for 6-month period is 59.75 kilograms.</small>
            </p>  
            <!-- END: summary -->            
            
        </div>

    </div>
</section>

<!-- TEMPLATE: Variety Info -->
<div id="variety-info-template" class="hide">
    <div class="card mt-1">
        <div class="card-body help-box">
            <h5 class="card-title"><i class="fas fa-book"></i> Info on {{variety_name}}:</h5>
            <ul>
                <li>Maturity: {{maturity}} days ({{maturity_grp}})</li>
                <li>Yield Average: {{yield_avg}} t.</li>
                <li>Yield Potential: {{yield_potential}} t.</li>
            </ul>
        </div>
    </div>
</div>



<script type="text/javascript" src="js/highcharts.js"></script>
<script type="text/javascript" src="js/highcharts-more.js"></script>
<?php if (_opt(sysoptions::_SHOW_MAP)) : ?>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCOk-7pjbZ3ip7vVLv443hfeNbwdkR5Qr0"></script>
<?php endif; ?>
<script type="text/javascript" src="gzip.php?group=oryza"></script>