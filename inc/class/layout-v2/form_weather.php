<?php
$country_choice = dss_utils::getLastSelectValues('country');
$all_country = werise_stations_country::getAll();
?>
<section id="main-content">
    <div class="container">
        <h2 id="page-title"><?php echo _CURRENT_OPT ?></h2>

        <div class="card mb-4">
            <div class="card-body">
                <form role="form" class="form" id ="frm" name="frm" action="#" method="post">
                    <input type="hidden" id="country" name="country" value="<?php echo $country_choice ?>">

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
                            <div id="location_div" class="input-group">
                                <input id="location_name" type="text" class="form-control" disabled="disabled">
                                <div class="input-group-append">
                                    <button id="location_btn" class="btn btn-outline-secondary" type="button"><i class="fas fa-map"></i></button>
                                </div>
                            </div>

                            <select class="form-control hide" name="station" id="station">
                                <option value="0"><?php __('Location') ?> &raquo;</option>
                            </select>
                        </div>

                    </div>

                    <div class="form-group">
                        <label for="type_year"><?php __('Year') ?></label>
                        <div class="row">
                            <div class="col-md-auto">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="type-year-pre">Historical</span>
                                    </div>
                                    <select class="form-control" name="type_year" id="type_year">
                                        <option value=""><?php __('Year') ?> &raquo;</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <legend><?php echo __('Weather data') ?></legend>

                    <?php foreach ($cls->getWvars() as $key_tmp => $wvar): ?>
                        <?php $key = $key_tmp + 1; ?>
                        <div class="form-check">
                            <input class="form-check-input" id="wvar<?php echo $key ?>" name="wvar<?php echo $key ?>" type="checkbox" <?php if ($key === 1) echo 'checked="checked"' ?> <?php if ($key === 1) echo 'disabled="1"' ?> />
                            <label class="form-check-label" for="wvar<?php echo $key ?>">
                                <?php echo $wvar ?>
                            </label>
                        </div>
                    <?php endforeach; ?>

                    <button class="btn mt-2" id="show" type="submit"><i class="fas fa-chart-area"></i> <?php __('Show Advisory') ?></button>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col text-center">
                <figure class="figure jamstec">
                    <img class="figure-img img-fluid rounded" src="images/ssta.glob.MAM2018.1mar2018.gif" alt="JAMSTEC" />
                    <figcaption class="figure-caption"><strong>image source:</strong> http://www.jamstec.go.jp/frsgc/research/d1/iod/sintex_f1_forecast.html.en</figcaption>
                </figure>
            </div>
        </div>

        <div class="afterload hide">
            
            <h4><?php __('Advisory') ?></h4>

            <?php foreach ($cls->getWvars() as $key_tmp => $wvar): ?>
                <?php $key = $key_tmp + 1; ?>
                <div id="wvar_chart<?php echo $key ?>" class="chartdiv mb-3 hide">
                    <h5><?php echo $wvar ?></h5>
                    <div class="table-responsive-sm">
                        <table class="table table-sm">
                            <tr>
                                <td>
                                    <div id="chart<?php echo $key ?>" class="chart mb-2"></div>
                                </td>
                            </tr>
                        </table>
                    </div> 
                </div>
            <?php endforeach; ?>

        </div>

        <div class="afterload hide">

            <div id="advisory" style="display:none">
                <h2 class="title" style="font-weight: 700; color:#547e1a"><?php __('Advisory') ?></h2>

                <div class="adv-rainfall clearfix">
                    <div style="float:left;width:70px">
                        <img class="img-circle" src="images/advisory01.png" style="width:50px;height:50px" />
                    </div>
                    <div style="float:left;">
                        <span id="f-year"></span> <?php __('Wet-season rainfall is') ?> <span id="f-rain"></span>
                    </div>
                </div>
                <div class="adv-rainfall clearfix" style="background-color: #ffffff">
                    <div style="float:left;width:70px">
                        <img class="img-circle" src="images/advisory02.png" style="width:50px;height:50px" />
                    </div>
                    <div style="float:left;">
                        <a href="javascript:launch_help('q2')"><?php __('Onset of rain is on ') ?></a> <span id="rain-onset"></span>
                    </div>
                </div>
                <div class="adv-rainfall clearfix">
                    <div style="float:left;width:70px">
                        <img class="img-circle" src="images/advisory03.png" style="width:50px;height:50px" />
                    </div>
                    <div style="float:left;width:800px">
                        <a href="javascript:launch_help('q3')"><?php __('Expected flooding dates:') ?></a><br /> <span id="wet-dates"></span>
                    </div>
                </div>
                <div class="adv-rainfall clearfix" style="background-color: #ffffff">
                    <div style="float:left;width:70px">
                        <img class="img-circle" src="images/advisory04.png" style="width:50px;height:50px" />
                    </div>
                    <div style="float:left;width:800px">
                        <a href="javascript:launch_help('q3')"><?php __('Expected drought dates:') ?></a><br /> <span id="dry-dates"></span>
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
                <h2><?php __('Data Source') ?></h2>
                <pre id="acknowledgement"></pre>
            </div>
        </div>

        <div class="afterload hide">
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
</section>

<script type="text/javascript" src="js/highcharts.js"></script>
<script type="text/javascript" src="js/highcharts-more.js"></script>
<?php if (_opt(sysoptions::_SHOW_MAP)) : ?>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCOk-7pjbZ3ip7vVLv443hfeNbwdkR5Qr0"></script>
<?php endif; ?>
<script type="text/javascript" src="gzip.php?group=weather"></script>