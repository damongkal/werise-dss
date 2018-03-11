<div class="width-center">

    <div id="help-btn">
        <p>Store the raw weather data files to the database.</p>
        <button class="btn btn-info btn-small" onclick="javascript:launch_help('q11')"><i class="icon-question-sign"> </i> Help</button>
    </div>

    <?php if ($cls->action !== 'list' && $cls->action !== 'detail'): ?>
        <p><a class="btn btn-small" href="admin.php?pageaction=weatherfile"><i class="icon-repeat"></i> Back to Directory List</a></p>
    <?php endif; ?>

    <?php if (($cls->action == 'load' || $cls->action == 'del') && $cls->action_ret) : ?>
        <h3>Results</h3>
        <?php echo $cls->action_ret ?>
    <?php endif; ?>

    <?php if ($cls->action === 'list'): ?>

        <div id ="dataselection" style="width:500px">
            <h3 style="margin-top: 0">List Options</h3>
            <form id="list-options" class="form">
                <label class="checkbox">
                    <input type="checkbox" name="show_only_loaded" id="show_only_loaded" value="1" /> Only show stations already loaded to database?
                </label>
            </form>
            
            <p><a class="btn btn-small" href="admin.php?pageaction=weatherfile&action=clean"><i class="icon-repeat"></i> Reset stations with PRN files</a></p>
        </div>

        <h3>File Location</h3>
        <p style="font-family:Courier, serif"><i class="icon-folder-open"> </i> <?php echo _DATA_DIR . werise_weather_file::getFolder('r') ?></p>        

        <?php if ($cls->files['a']) : ?>

            <?php foreach ($cls->files['a'] as $country => $topregions) : ?>

                <h3><img class="icon-flag-<?php echo $country ?>" style="margin-top: 8px" /><?php echo werise_stations_country::getName($country) ?></h3>

                <table class="table table-bordered adm-table">      

                    <?php foreach ($topregions as $topregion_id => $topregion_info) : ?>

                        <tr class="tr-gray region-td">
                            <td colspan="6"><?php echo $topregion_info['name'] ?></td>
                        </tr>

                        <?php foreach ($topregion_info['subregion'] as $subregion_id => $subregion_info) : ?>        

                            <tr class="tr-gray subregion-td">
                                <td>&nbsp;</td>
                                <td colspan="5"><?php echo $subregion_info['name'] ?></td>
                            </tr>                                

                            <?php foreach ($subregion_info['station'] as $station_id => $station_info) : ?>                                        

                                <?php foreach (array('r', 'f') as $wtype) : ?>                                                                                            

                                    <?php if (isset($station_info[$wtype])): ?>
                            
                                    <tr class="tr-gray">
                                        <th width="20">&nbsp;</th>
                                        <th width="120">File</th>
                                        <th width="70">Year</th>
                                        <th width="150">Is displayed to<br /> weather chart?</th>
                                        <th width="180">Action</th>
                                        <th width="400">Remarks</th>
                                    </tr>                                     

                                    <tr>
                                        <td>&nbsp;</td>
                                        <td colspan="3" class="station-td"><?php echo $station_info['name'] ?> &raquo; <?php echo werise_weather_properties::getTypeDesc($wtype) ?> Data</td>
                                        <td colspan="2">
                                            <?php if (isset($station_info[$wtype])) : ?>
                                                <a class="btn btn-small" href="<?php echo $cls->getBtnUrl($country . '-' . $station_id . '-ALL', 'load', $wtype) ?>"><i class="icon-download"></i> Load All</a>
                                                <a class="btn btn-small" href="<?php echo $cls->getBtnUrl($country . '-' . $station_id . '-ALL', 'del', $wtype) ?>"><i class="icon-download"></i> Delete All</a>
                                                <a class="btn btn-small" href="<?php echo $cls->getBtnUrl($country . '-' . $station_id . '-ALL' . '-ALL', 'pctile', $wtype) ?>"><i class="icon-search"></i> Percentile</a>
                                            <?php else : ?>
                                                <?php if ($wtype === werise_weather_properties::_REALTIME) : ?>
                                                    <a class="btn btn-small" href="<?php echo $cls->getBtnUrl($country . '-' . $station_id, 'grasp', $wtype) ?>"><i class="icon-download"></i> Upload GRASP data</a>
                                                <?php endif; ?>&nbsp;
                                            <?php endif; ?>&nbsp;
                                        </td>
                                    </tr>                                                                 

                                        <?php foreach ($station_info[$wtype] as $file) : ?>                                        

                                            <tr class="<?php echo ( $file['is_loaded'] ) ? 'tr_loaded' : 'tr_nloaded' ?>">
                                                <td>&nbsp;</td>
                                                <td><?php echo $file['file'] ?></td>
                                                <td><?php echo $file['year'] ?></td>
                                                <td>
                                                    <?php if ($file['is_loaded']): ?>
                                                        <?php echo $file['is_loaded']->upload_date . ' <a href="admin.php?pageaction=weatherfile&action=detail&id=' . $file['is_loaded']->id . '"><span class="badge">' . "{$file['is_loaded']->id}</span></a>" ?>
                                                    <?php else: ?>
                                                        <span style="color:#ff0000"><i class="icon-eye-close"></i> NO</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a class="btn btn-small" href="admin.php?pageaction=weatherfile&action=load&amp;dst=w&amp;type=<?php echo $wtype ?>&amp;prnfile=<?php echo $file['subdir'] . '/' . $file['file'] ?>"><i class="icon-download"></i> Load</a>
                                                    <?php if ($file['is_loaded']): ?>
                                                        <a class="btn btn-small" href="admin.php?pageaction=weatherfile&action=del&amp;dst=w&amp;type=<?php echo $wtype ?>&amp;setid=<?php echo $file['is_loaded']->id ?>&amp;prnfile=<?php echo $file['subdir'] . '/' . $file['file'] ?>"><i class="icon-remove"></i> Delete</a>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php echo $cls->showNotes($file) ?>
                                                </td>
                                            </tr>                                

                                        <?php endforeach; ?>

                                    <?php else: ?>
                                        <tr>
                                            <th width="20">&nbsp;</th>
                                            <td colspan="5" class="station-td"><?php echo $station_info['name'] ?> &raquo; <?php echo werise_weather_properties::getTypeDesc($wtype) ?> Data &raquo; NO DATA!</td>
                                        </tr>                                
                                    <?php endif; ?>                                    

                                <?php endforeach; ?>

                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </table>                            

            <?php endforeach; ?>

        <?php endif; ?>

    <?php endif; ?>

    <?php if ($cls->action === 'detail'): ?>
        <h3>Dataset</h3>
        <table class="table table-bordered adm-table">
            <tr>
                <td class="tr-gray">ID</td>
                <td><?php echo $cls->dataset_info->id ?></td>
            </tr>
            <tr>
                <td class="tr-gray">Date Processed</td>
                <td><?php echo $cls->dataset_info->upload_date ?> <span class="badge">ver <?php echo $cls->dataset_info->oryza_ver ?></span></td>
            </tr>
            <tr>
                <td class="tr-gray">Station</td>
                <td><?php echo $cls->dataset_station->station_name ?></td>
            </tr>            
            <tr>
                <td class="tr-gray">Region</td>
                <td><img class="icon-flag-<?php echo $cls->dataset_station->country_code ?>"> 
                    <?php echo $cls->dataset_station->subregion_name ?>, 
                    <?php echo $cls->dataset_station->topregion_name ?>, 
                    <?php echo werise_stations_country::getName($cls->dataset_station->country_code) ?>
                </td>
            </tr>            
            <tr>
                <td class="tr-gray">Year</td>
                <td><?php echo $cls->dataset_info->year ?> <?php echo werise_weather_properties::getTypeDesc($cls->dataset_info->wtype) ?></td>
            </tr>
        </table>

        <h3 style="margin-top: 25px">PRN File</h3>
        <div>
            <pre><?php echo 'under construction' ?></pre>
        </div>

        <div class="charts">
            <h3 style="margin-top: 25px">Weather Chart</h3>

            <div class="dselect" style="width:500px; margin-bottom:15px; padding: 5px 10px 5px 10px">
                <form id="chart_form" role="form" class="form" action="#">

                    <label class="control-label" for="chart_wvar"><?php __('Weather variable') ?></label>
                    <select class="form-control" name="chart_wvar" id="chart_wvar" style="width:250px">
                        <?php foreach ($cls->getWvars() as $wvar) : ?>
                            <option value="<?php echo $wvar ?>"><?php echo werise_weather_properties::getVarName($wvar) ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <div id="chart_pr" style="width:1000px;height:300px"></div>
        </div>

        <h3>Data</h3>

        <table class="table table-bordered adm-table">
            <tr class="tr-gray">
                <th width="110">Date</th>
                <th class="nbr" width="80">Rainfall</th>
                <th class="nbr" width="80">Min Temp.</th>
                <th class="nbr" width="80">Max Temp.</th>
                <?php if ($cls->dataset_irradiance_ok): ?>
                    <th class="nbr" width="80">Irradiance</th>
                <?php endif; ?>
                <?php if ($cls->dataset_sunshine_ok): ?>
                    <th class="nbr" width="80">Sunshine<br />Duration</th>
                <?php endif; ?>
                <th class="nbr" width="80">Vapor<br />Pressure</th>
                <th class="nbr" width="80">Wind<br />Speed</th>
            </tr>
            <?php foreach ($cls->dataset_data as $data): ?>
                <tr>
                    <td><?php echo $data->observe_date ?></td>
                    <td class="nbr"><?php echo $cls->nf($data->rainfall) ?></td>
                    <td class="nbr"><?php echo $cls->nf($data->min_temperature) ?></td>
                    <td class="nbr"><?php echo $cls->nf($data->max_temperature) ?></td>
                    <?php if ($cls->dataset_irradiance_ok): ?>
                        <td class="nbr"><?php echo $cls->nf($data->irradiance) ?></td>
                    <?php endif; ?>
                    <?php if ($cls->dataset_sunshine_ok): ?>
                        <td class="nbr"><?php echo $cls->nf($data->sunshine_duration) ?></td>
                    <?php endif; ?>
                    <td class="nbr"><?php echo $cls->nf($data->vapor_pressure) ?></td>
                    <td class="nbr"><?php echo $cls->nf($data->mean_wind_speed) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h3>10-day interval Data (decadal)</h3>

        <table class="table table-bordered adm-table">
            <tr class="tr-gray">
                <th width="110">From<br />Date</th>
                <th width="80" style="display:none">To<br />Date</th>
                <th class="nbr" width="80">Rainfall</th>
                <th class="nbr" width="80">Min Temp.</th>
                <th class="nbr" width="80">Max Temp.</th>
                <?php if ($cls->dataset_irradiance_ok): ?>
                    <th class="nbr" width="80">Irradiance</th>
                <?php endif; ?>
                <?php if ($cls->dataset_sunshine_ok): ?>
                    <th class="nbr" width="80">Sunshine<br />Duration</th>
                <?php endif; ?>
                <th class="nbr" width="80">Vapor<br />Pressure</th>
                <th class="nbr" width="80">Wind<br />Speed</th>
            </tr>
            <?php foreach ($cls->dataset_decadal as $data): ?>
                <tr>
                    <td><?php echo $data->observe_date ?></td>
                    <td style="display:none"><?php echo $data->observe_date2 ?></td>
                    <td class="nbr"><?php echo $cls->nf($data->rainfall) ?></td>
                    <td class="nbr"><?php echo $cls->nf($data->min_temperature) ?></td>
                    <td class="nbr"><?php echo $cls->nf($data->max_temperature) ?></td>
                    <?php if ($cls->dataset_irradiance_ok): ?>
                        <td class="nbr"><?php echo $cls->nf($data->irradiance) ?></td>
                    <?php endif; ?>
                    <?php if ($cls->dataset_sunshine_ok): ?>
                        <td class="nbr"><?php echo $cls->nf($data->sunshine_duration) ?></td>
                    <?php endif; ?>
                    <td class="nbr"><?php echo $cls->nf($data->vapor_pressure) ?></td>
                    <td class="nbr"><?php echo $cls->nf($data->mean_wind_speed) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <?php if ($cls->action === 'pctile'): ?>

        <h3>Station</h3>

        <table class="table table-bordered adm-table">
            <tr>
                <td class="tr-gray">Country</td>
                <td>
                    <img class="icon-flag-<?php echo $cls->pctile_station->country_code ?>"> 
                    <?php echo werise_stations_country::getName($cls->pctile_station->country_code) ?>
                </td>
            </tr>
            <tr>
                <td class="tr-gray">Region</td>
                <td><?php echo $cls->pctile_station->topregion_name ?></td>
            </tr>
            <tr>
                <td class="tr-gray">Sub-Region</td>
                <td><?php echo $cls->pctile_station->subregion_name ?></td>
            </tr>            
            <tr>
                <td class="tr-gray">Station</td>
                <td><?php echo $cls->pctile_station->station_name ?></td>
            </tr>
        </table>        

        <h3>20th Percentile</h3>
        <table class="table table-bordered adm-table">
            <tr class="tr-gray">
                <th width="80">From<br />Date</th>
                <th width="80" style="display: none">To<br />Date</th>
                <th class="nbr" width="80">Rainfall</th>
                <th class="nbr" width="80">Min Temp.</th>
                <th class="nbr" width="80">Max Temp.</th>
                <th class="nbr" width="80">Irradiance</th>
                <th class="nbr" width="80">Sunshine<br />Duration</th>
                <th class="nbr" width="80">Vapor<br />Pressure</th>
                <th class="nbr" width="80">Wind<br />Speed</th>
            </tr>
            <?php if (!is_null($cls->pctile_data20)): ?>
                <?php foreach ($cls->pctile_data20 as $data): ?>
                    <tr>
                        <td><?php echo $cls->fmtPctDate($data['observe_date']) ?></td>
                        <td style="display: none"><?php echo $data['observe_date2'] ?></td>
                        <td class="nbr"><?php echo $cls->nf($data['rainfall']) ?></td>
                        <td class="nbr"><?php echo $cls->nf($data['min_temperature']) ?></td>
                        <td class="nbr"><?php echo $cls->nf($data['max_temperature']) ?></td>
                        <td class="nbr"><?php echo $cls->nf($data['irradiance']) ?></td>
                        <td class="nbr"><?php echo $cls->nf($data['sunshine_duration']) ?></td>
                        <td class="nbr"><?php echo $cls->nf($data['vapor_pressure']) ?></td>
                        <td class="nbr"><?php echo $cls->nf($data['mean_wind_speed']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>

        <h3>50th Percentile</h3>
        <table class="table table-bordered adm-table">
            <tr class="tr-gray">
                <th width="80">From<br />Date</th>
                <th width="80" style="display: none">To<br />Date</th>
                <th class="nbr" width="80">Rainfall</th>
                <th class="nbr" width="80">Min Temp.</th>
                <th class="nbr" width="80">Max Temp.</th>
                <th class="nbr" width="80">Irradiance</th>
                <th class="nbr" width="80">Sunshine<br />Duration</th>
                <th class="nbr" width="80">Vapor<br />Pressure</th>
                <th class="nbr" width="80">Wind<br />Speed</th>
            </tr>
            <?php if (!is_null($cls->pctile_data50)): ?>
                <?php foreach ($cls->pctile_data50 as $data): ?>
                    <tr>
                        <td><?php echo $cls->fmtPctDate($data['observe_date']) ?></td>
                        <td style="display: none"><?php echo $data['observe_date2'] ?></td>
                        <td class="nbr"><?php echo $cls->nf($data['rainfall']) ?></td>
                        <td class="nbr"><?php echo $cls->nf($data['min_temperature']) ?></td>
                        <td class="nbr"><?php echo $cls->nf($data['max_temperature']) ?></td>
                        <td class="nbr"><?php echo $cls->nf($data['irradiance']) ?></td>
                        <td class="nbr"><?php echo $cls->nf($data['sunshine_duration']) ?></td>
                        <td class="nbr"><?php echo $cls->nf($data['vapor_pressure']) ?></td>
                        <td class="nbr"><?php echo $cls->nf($data['mean_wind_speed']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>        

        <h3>80th Percentile</h3>
        <table class="table table-bordered adm-table">
            <tr class="tr-gray">
                <th width="80">From<br />Date</th>
                <th width="80" style="display: none">To<br />Date</th>
                <th class="nbr" width="80">Rainfall</th>
                <th class="nbr" width="80">Min Temp.</th>
                <th class="nbr" width="80">Max Temp.</th>
                <th class="nbr" width="80">Irradiance</th>
                <th class="nbr" width="80">Sunshine<br />Duration</th>
                <th class="nbr" width="80">Vapor<br />Pressure</th>
                <th class="nbr" width="80">Wind<br />Speed</th>
            </tr>
            <?php if (!is_null($cls->pctile_data80)): ?>
                <?php foreach ($cls->pctile_data80 as $data): ?>
                    <tr>
                        <td><?php echo $cls->fmtPctDate($data['observe_date']) ?></td>
                        <td style="display: none"><?php echo $data['observe_date2'] ?></td>
                        <td class="nbr"><?php echo $cls->nf($data['rainfall']) ?></td>
                        <td class="nbr"><?php echo $cls->nf($data['min_temperature']) ?></td>
                        <td class="nbr"><?php echo $cls->nf($data['max_temperature']) ?></td>
                        <td class="nbr"><?php echo $cls->nf($data['irradiance']) ?></td>
                        <td class="nbr"><?php echo $cls->nf($data['sunshine_duration']) ?></td>
                        <td class="nbr"><?php echo $cls->nf($data['vapor_pressure']) ?></td>
                        <td class="nbr"><?php echo $cls->nf($data['mean_wind_speed']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>        

        <h3>10-day interval Data (decadal) for ALL years</h3>

        <table class="table table-bordered adm-table">
            <tr class="tr-gray">
                <th width="70">Year<br />Decadal</th>
                <th width="80">From<br />Date</th>
                <th width="80" style="display:none">To<br />Date</th>
                <th class="nbr" width="80">Rainfall</th>
                <th class="nbr" width="80">Min Temp.</th>
                <th class="nbr" width="80">Max Temp.</th>
                <th class="nbr" width="80">Irradiance</th>
                <th class="nbr" width="80">Sunshine<br />Duration</th>
                <th class="nbr" width="80">Vapor<br />Pressure</th>
                <th class="nbr" width="80">Wind<br />Speed</th>
            </tr>
            <?php foreach ($cls->pctile_decadal as $data): ?>
                <tr>
                    <td><?php echo $data->year . '-' . $data->decadal ?></td>
                    <td><?php echo $data->observe_date ?></td>
                    <td style="display:none"><?php echo $data->observe_date2 ?></td>
                    <td class="nbr"><?php echo $cls->nf($data->rainfall) ?></td>
                    <td class="nbr"><?php echo $cls->nf($data->min_temperature, 2) ?></td>
                    <td class="nbr"><?php echo $cls->nf($data->max_temperature, 2) ?></td>
                    <td class="nbr"><?php echo $cls->nf($data->irradiance, 2) ?></td>
                    <td class="nbr"><?php echo $cls->nf($data->sunshine_duration, 2) ?></td>
                    <td class="nbr"><?php echo $cls->nf($data->vapor_pressure, 2) ?></td>
                    <td class="nbr"><?php echo $cls->nf($data->mean_wind_speed, 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>        

    <?php endif; ?>

    <?php if ($cls->action === 'grasp'): ?>
        <h3>Dataset</h3>
        <table class="table table-bordered adm-table">
            <tr>
                <td class="tr-gray">Country</td>
                <td><img class="icon-flag-<?php echo $cls->dataset_station->country_code ?>"> <?php echo werise_stations_country::getName($cls->dataset_station->country_code) ?></td>
            </tr>
            <tr>
                <td class="tr-gray">Region</td>
                <td><?php echo $cls->dataset_station->topregion_name ?></td>
            </tr>
            <tr>
                <td class="tr-gray">Sub-Region</td>
                <td><?php echo $cls->dataset_station->subregion_name ?></td>
            </tr>
            <tr>
                <td class="tr-gray">Station</td>
                <td><?php echo $cls->dataset_station->station_name ?></td>
            </tr>            
        </table>

        <?php if (count($cls->grasp_files) > 0): ?>
            <div class="well" style="width:600px">
                <ul>
                    <?php foreach ($cls->grasp_files as $file): ?>
                        <li><?php echo $file ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php else: ?>
            <div class="dselect" style="width:600px; margin:10px 0 15px 3px;">

                <form id="upload_form" role="form" class="form" enctype="multipart/form-data" action="admin.php?pageaction=weatherfile" method="post">
                    <input name="action" type="hidden" value="grasp" />
                    <input name="prnfile" type="hidden" value="<?php echo $cls->dataset_station->country_code . '-' . $cls->dataset_station->station_id ?>" />

                    <legend>Upload Raw GRASP Files</legend>

                    <fieldset style="margin: 0">

                        <label class="control-label" for="pr">Rainfall (PR):</label>
                        <input class="form-control" id="pr" name="pr" type="file" style="width:550px" />

                        <label class="control-label" for="tn">Minimum Temperature (TN):</label>
                        <input class="form-control" id="tn" name="tn" type="file" style="width:550px" />

                        <label class="control-label" for="tx">Maximum Temperature (TX):</label>
                        <input class="form-control" id="tx" name="tx" type="file" style="width:550px" />

                        <label class="control-label" for="ws">Wind Speed (WS):</label>
                        <input class="form-control" id="ws" name="ws" type="file" style="width:550px" />

                        <p><input type="submit" class="btn btn-small" name="grasp" value="Upload" /></p>
                    </fieldset>
                </form>
            </div>
        <?php endif; ?>
    <?php endif; ?>    

    <?php if ($cls->action !== 'list' && $cls->action !== 'detail'): ?>
        <p><a class="btn btn-small" href="admin.php?pageaction=weatherfile"><i class="icon-repeat"></i> Back to Directory List</a></p>
    <?php endif; ?>
</div>


<script type="text/javascript">
    /**
     * page behaviours
     */
    jQuery(function () {

        jQuery("#show_only_loaded").change(function () {
            if (jQuery(this).is(':checked') == true)
            {
                jQuery('.tr_nloaded').hide();
            } else
            {
                jQuery('.tr_nloaded').show();
            }
        });

        jQuery("#show-container").change(function () {
            jQuery('#realtime-container').hide();
            jQuery('#forecast-container').hide();
            jQuery('#' + jQuery(this).val()).show();
        });

    });
</script>

<?php if ($cls->action === 'detail') : ?>
    <script type="text/javascript" src="js/highcharts.js"></script>
    <script type="text/javascript" src="js/highcharts-more.js"></script>
    <script type="text/javascript" src="gzip.php?file=admin-weatherfile-chart"></script>
    <script type="text/javascript">
        /**
         * page behaviours
         */
        jQuery(function () {
            window.seriesdata = <?php echo json_encode($cls->dataset_data); ?>;
            makeChart();
            jQuery("#chart_wvar").change(function () {
                makeChart();
            });
        });
    </script>
<?php endif; ?>