<div class="width-center">

    <h1>Cumulative Distribution Function based Downscaling Method (CDF-DM)</h1>
    <div id="help-btn">
        <p>Executes external Fortran program CDF-DM.</p>
        <p><button class="btn btn-info btn-small" onclick="javascript:launch_help('q9')"><i class="icon-question-sign"> </i> Help</button></p>
    </div>

    <?php if ($cls->arg_region === 0) : ?>

        <?php foreach ($cls->getRegions() as $country => $regions) : ?>

            <h3><img class="icon-flag-<?php echo $country ?>" style="margin-top: 8px" /><?php echo werise_stations_country::getName($country) ?></h3>
            <table class="table table-bordered adm-table">
                <tr class="tr-gray">
                    <th width="300">Region</th>
                    <th width="250">SINTEX-F</th>
                </tr>                

                <?php foreach ($regions as $region_id => $subregions) : ?>
                    <tr data="<?php echo $region_id ?>">
                        <td class="region-td"><a class="region_action" href="#"><?php echo $subregions['region_name'] ?></a></td>
                        <td><?php echo $subregions['year_data'] ?></td>
                    </tr>
                    <?php foreach ($subregions['sub'] as $subregion) : ?>
                        <tr data="<?php echo $subregion->subregion_id ?>">
                            <td style="padding-left: 20px"><a class="region_action" href="#"><?php echo $subregion->subregion_name ?></a></td>
                            <td><?php echo $subregion->year_data ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </table>                        
        <?php endforeach; ?>


    <?php else: ?>

        <div class="dselect" style="width:500px; margin-bottom:15px; padding: 5px 10px 15px 10px">
            <div class="pull-left" style="padding-top: 5px"><b>Region:</b> [<span id="region_id"><?php echo $cls->arg_region ?></span>] <?php echo $cls->fmtRegion() ?></div> 
            <?php if ( (count($cls->raw) > 0) && $cls->action !== 'chart') : ?>
                <div class="pull-right"><input type="button" class="btn btn-small" id="chart_btn" value="Weather Chart" /></div>
            <?php endif; ?>
            <div class="clear">&nbsp;</div>
        </div>

        <?php if ($cls->action !== '' && $cls->action !== 'chart') : ?>
            <h3 style="margin-top: 0">Process Results</h3>

            <div class="dselect" style="margin-bottom:10px;width:600px">
                <?php foreach ($cls->debug as $debug): ?>
                    <?php if (is_array($debug)): ?>
                        <pre><?php print_r($debug) ?></pre>
                    <?php else: ?>
                        <?php echo $debug ?><br />
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>    
        <?php endif; ?>

        <?php if ($cls->action !== 'chart') : ?>
            <h3 style="margin-top: 0">Raw SINTEX-F Data</h3>

            <table class="table table-bordered adm-table" style="margin:3px">
                <tr>
                    <th width="130">From</th>
                    <th width="130">To</th>
                    <th width="70">PR</th>
                    <th width="70">TN</th>
                    <th width="70">TX</th>
                    <th width="70">WS</th>
                </tr>            
                <?php if (count($cls->raw) === 0): ?>
                    <tr>                        
                        <td colspan="6"><div class="dselect" style="margin-bottom:10px;width:600px">No data found.</div></td>
                    </tr>
                <?php else: ?>

                    <?php foreach ($cls->raw as $monthyear => $raw): ?>
                        <tr>
                            <td><?php echo $cls->fmtMonth($monthyear) ?></td>
                            <td><?php echo $cls->fmtMonth($raw['to']) ?></td>
                            <td><?php echo $cls->fmtStatus($raw['details'][0]['pr']) ?></td>
                            <td><?php echo $cls->fmtStatus($raw['details'][0]['tn']) ?></td>
                            <td><?php echo $cls->fmtStatus($raw['details'][0]['tx']) ?></td>
                            <td><?php echo $cls->fmtStatus($raw['details'][0]['ws']) ?></td>
                        </tr>
                    <?php endforeach; ?>

                <?php endif; ?>
                <?php if (_ADM_ENV!=='PROD') : ?>        
                <tr>
                    <td colspan="6">
                        <div class="dselect" style="width:600px; margin:10px 0 15px 3px;">

                            <form id="upload_form" role="form" class="form" enctype="multipart/form-data" action="/admin.php?pageaction=cdfdm" method="post">
                                <input id="action" name="action" type="hidden" value="uploadraw" />
                                <input id="raw_region" name="region_id" type="hidden" value="<?php echo $cls->arg_region ?>" />

                                <legend>Upload Raw SINTEX-F Files</legend>

                                <fieldset style="margin: 0">

                                    <label class="control-label" for="pr">Rainfall (PR):</label>
                                    <input class="form-control" id="pr" name="pr" type="file" style="width:550px" />

                                    <label class="control-label" for="tn">Minimum Temperature (TN):</label>
                                    <input class="form-control" id="tn" name="tn" type="file" style="width:550px" />

                                    <label class="control-label" for="tx">Maximum Temperature (TX):</label>
                                    <input class="form-control" id="tx" name="tx" type="file" style="width:550px" />

                                    <label class="control-label" for="ws">Wind Speed (WS):</label>
                                    <input class="form-control" id="ws" name="ws" type="file" style="width:550px" />

                                    <p><input type="submit" class="btn btn-small" name="uploadraw" value="Upload" /></p>
                                </fieldset>
                            </form>
                        </div>
                    </td></tr>
                <?php endif; ?>
            </table>                        

            <?php if (_ADM_ENV!=='PROD') : ?>
            
            <h3 style="margin-top: 0">General Circulation Model (GCM)</h3>

            <table class="table table-bordered adm-table" style="margin:3px">
                <tr class="tr-gray">
                    <th colspan="2">GCM Folder: <span style="font-weight:100"><?php echo $cls->datafiles['gcm'][0] ?></span></th>
                </tr>
                <tr class="tr-gray">
                    <th width="180">File</th>
                    <th width="400">Info</th>
                </tr>

                <?php if (!$cls->datafiles['gcm'][1]) : ?>
                    <tr>
                        <td colspan="2">No files found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($cls->datafiles['gcm'][1] as $file) : ?>
                        <tr>
                            <td><?php echo $file[0] ?></td>
                            <td><?php echo ($file[1] === 0) ? '' : $file[1] . ' TO ' . $file[2] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php if (count($cls->raw) > 0): ?>
                    <tr style="display:none">
                        <td colspan="2">
                            <form id="gcm_form" role="form" class="form" action="/admin.php?pageaction=cdfdm" method="post">
                                <input id="action" name="action" type="hidden" value="rawtogcm" />
                                <input id="gcm_region" name="region_id" type="hidden" value="<?php echo $cls->arg_region ?>" />

                                <fieldset style="margin: 0">
                                    <input type="submit" class="btn btn-small" name="rawtogcm" value="Create GCM Files" />
                                </fieldset>
                            </form>
                        </td>
                    </tr>
                <?php endif; ?>
            </table>

            <h3>Observation Data (OBS)</h3>

            <table class="table table-bordered adm-table" style="margin:3px">
                <tr class="tr-gray">
                    <th colspan="2">OBS Folder: <span style="font-weight:100"><?php echo $cls->datafiles[werise_cdfdm_folder::_SRC_OBS][0] ?></span></th>
                </tr>
                <tr class="tr-gray">
                    <th colspan="2">
                        Last PRN Import: <span style="font-weight:100"><?php echo $cls->getLastHistoryLog() ?></span>
                        <span id="station_id_val" data="<?php echo $cls->arg_station_id ?>" style="display:none"></span>                    
                    </th>
                </tr>            
                <tr class="tr-gray">
                    <th width="180">File</th>
                    <th width="400">Info</th>
                </tr>

                <?php if (!$cls->datafiles[werise_cdfdm_folder::_SRC_OBS][1]) : ?>
                    <tr>
                        <td colspan="2">No files found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($cls->datafiles[werise_cdfdm_folder::_SRC_OBS][1] as $file) : ?>
                        <tr>
                            <td><?php echo $file[0] ?></td>
                            <td><?php echo ($file[1] === 0) ? '' : $file[1] . ' TO ' . $file[2] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>

                <tr>
                    <td colspan="2">
                        <form id="obsreal_form" role="form" class="form" action="/admin.php?pageaction=cdfdm" method="post">
                            <input id="action" name="action" type="hidden" value="obsreal" />
                            <input name="region_id" type="hidden" value="<?php echo $cls->arg_region ?>" />

                            <fieldset style="margin: 0">
                                <label for="station_id">Station: </label>
                                <select class="form-control" name="station_id" id="station_id" style="width:200px">
                                    <option value="0" >&nbsp;</option>
                                    <?php foreach ($cls->getStations() as $station) : ?>
                                        <option value="<?php echo $station->station_id ?>" ><?php echo $station->station_name ?></option>
                                    <?php endforeach; ?>
                                </select>               
                            </fieldset>    
                            <fieldset style="margin: 0">    
                                <input type="submit" class="btn btn-small" name="realobs" value="Import from Weather Files (PRN format)" />
                            </fieldset>
                        </form>
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <form id="script_form" role="form" class="form" action="/admin.php?pageaction=cdfdm" method="post">
                            <input id="action" name="action" type="hidden" value="downscale" />
                            <input id="ftype" name="ftype" type="hidden" value="real-time" />
                            <input id="gcm_region" name="region_id" type="hidden" value="<?php echo $cls->arg_region ?>" />
                            <fieldset style="margin: 0">
                                <input type="submit" class="btn btn-small" name="downscale" value="Downscale" />
                            </fieldset>
                        </form>
                    </td>
                </tr>                  

                <tr class="tr-gray">
                    <th colspan="2">OUT Folder: <span style="font-weight:100"><?php echo $cls->datafiles[werise_cdfdm_folder::_SRC_OUT][0] ?></span></th>
                </tr>
                <tr class="tr-gray">
                    <th width="180">File</th>
                    <th width="400">Info</th>
                </tr>

                <?php if (!$cls->datafiles[werise_cdfdm_folder::_SRC_OUT][1]) : ?>
                    <tr>
                        <td colspan="2">No files found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($cls->datafiles[werise_cdfdm_folder::_SRC_OUT][1] as $file) : ?>
                        <tr>
                            <td><?php echo $file[0] ?></td>
                            <td><?php echo ($file[1] === 0) ? '' : $file[1] . ' TO ' . $file[2] ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <tr>
                        <td colspan="2">
                            <form id="out_form" role="form" class="form" action="/admin.php?pageaction=cdfdm_convert" method="post">
                                <input id="action" name="action" type="hidden" value="export" />
                                <input id="ftype" name="ftype" type="hidden" value="real-time" />
                                <input name="region_id" type="hidden" value="<?php echo $cls->arg_region ?>" />
                                <input id="ftype" name="year" type="hidden" value="<?php echo $cls->outmaxyear ?>" />

                                <fieldset style="margin: 0">
                                    <label for="station2_id">Station: </label>  
                                    <span id="station2_id_val" data="<?php echo $cls->arg_station_id ?>" style="display:none"></span>
                                    <select class="form-control" name="station_id" id="station2_id" style="width:200px">
                                        <option value="0" >&nbsp;</option>
                                        <?php foreach ($cls->getStations() as $station) : ?>
                                            <option value="<?php echo $station->station_id ?>" ><?php echo $station->station_name ?></option>
                                        <?php endforeach; ?>
                                    </select>               
                                </fieldset>    
                                <fieldset style="margin: 0">    
                                    <input type="submit" class="btn btn-small" name="createprn" value="Export to Weather Files (PRN Format)" />
                                </fieldset>
                            </form>
                        </td>
                    </tr>                       

                <?php endif; ?>

            </table>

            <div style="display:none">
                <h3>GRASP Data</h3>
                <table class="table table-bordered adm-table" style="margin:3px">
                    <tr class="tr-gray">
                        <th colspan="2">OBS Folder: <span style="font-weight:100"><?php echo $cls->datafiles['obsgrasp'][0] ?></span></th>
                    </tr>
                    <tr class="tr-gray">
                        <th width="180">File</th>
                        <th width="400">Info</th>
                    </tr>

                    <?php if (!$cls->datafiles['obsgrasp'][1]) : ?>
                        <tr>
                            <td colspan="2">No files found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($cls->datafiles['obsgrasp'][1] as $file) : ?>
                            <tr>
                                <td><?php echo $file[0] ?></td>
                                <td><?php echo ($file[1] === 0) ? '' : $file[1] . ' TO ' . $file[2] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <tr class="tr-gray">
                        <th colspan="2">OUT Folder: <span style="font-weight:100"><?php echo $cls->datafiles['outgrasp'][0] ?></span></th>
                    </tr>
                    <tr class="tr-gray">
                        <th width="180">File</th>
                        <th width="400">Info</th>
                    </tr>

                    <?php if (!$cls->datafiles['outgrasp'][1]) : ?>
                        <tr>
                            <td colspan="2">No files found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($cls->datafiles['outgrasp'][1] as $file) : ?>
                            <tr>
                                <td><?php echo $file[0] ?></td>
                                <td><?php echo ($file[1] === 0) ? '' : $file[1] . ' TO ' . $file[2] ?></td>
                            </tr>
                        <?php endforeach; ?>

                        <tr>
                            <td colspan="2">
                                <form id="script_form" role="form" class="form" action="/admin.php?pageaction=cdfdm" method="post">
                                    <input id="gcm_region" name="region_id" type="hidden" value="<?php echo $cls->arg_region ?>" />
                                    <fieldset style="margin: 0">
                                        <input type="submit" class="btn btn-small" name="convert-prn" value="Convert to PRN format" />
                                    </fieldset>
                                </form>
                            </td>
                        </tr>
                    <?php endif; ?>

                </table>
            </div>
            
            <?php endif; ?>
            
            
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($cls->action === 'chart') : ?>    
        <div class="dselect" style="width:500px; margin-bottom:15px; padding: 5px 10px 15px 10px">            

            <form id="chart_form" role="form" class="form" action="/admin.php?pageaction=cdfdm" method="post">    
                <input type="hidden" name="action" value="chart" />
                <input type="hidden" name="region_id" value="<?php echo $cls->arg_region ?>" />
                <fieldset style="margin-top:0">
                    <legend>Chart Options</legend>                

                    <label class="control-label" for="chart_year"><?php __('Year') ?></label>
                    <span id="chart_year_val" data="<?php echo $cls->arg_chart_year ?>" style="display:none"></span>
                    <select class="form-control" name="chart_year" id="chart_year" style="width:90px">
                        <?php foreach ($cls->chart_years as $rec) : ?>
                            <option value="<?php echo $rec->year ?>" ><?php echo $rec->year ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label class="control-label" for="chart_wvar"><?php __('Weather data') ?></label>
                    <span id="chart_sourcecol_val" data="<?php echo $cls->arg_chart_cdfdm_sourcecol ?>" style="display:none"></span>
                    <select class="form-control" name="chart_sourcecol" id="chart_sourcecol" style="width:250px" size="4">
                        <?php foreach (werise_cdfdm_file::getTypes() as $cdfdm_col) : ?>
                            <option value="<?php echo $cdfdm_col ?>"><?php echo werise_cdfdm_chart::getWvarField($cdfdm_col) ?></option>
                        <?php endforeach; ?>
                    </select>                     

                </fieldset>
                <button class="form-control btn btn-success" id="show" type="submit"><i class="icon-picture icon-white"></i> <?php __('Show Chart') ?></button>
            </form>            

        </div>        
        <div class="charts">
            <h3 style="margin-top: 25px">Weather Chart</h3>
            <div id="chart_pr" style="width:1000px;height:300px"></div>
        </div>
    <?php endif; ?>    
</div>

<script type="text/javascript" src="gzip.php?file=dss-common-dropdown"></script>
<script type="text/javascript">
            /**
             * page behaviours
             */
            jQuery(function () {
                jQuery('.region_action').click(function () {
                    var region_id = jQuery(this).parent().parent().attr('data');
                    window.location = "admin.php?pageaction=cdfdm&region_id=" + region_id;
                });
                jQuery('#chart_btn').click(function () {
                    var region_id = jQuery("#region_id").html();
                    window.location = "admin.php?pageaction=cdfdm&action=chart&region_id=" + region_id;
                });
                DropDown.setDefault('station_id', jQuery("#station_id_val").attr('data'));
                DropDown.setDefault('station2_id', jQuery("#station_id_val").attr('data'));
            });
</script>

<?php if ($cls->action === 'chart') : ?>        
    <script type="text/javascript" src="js/highcharts.js"></script>
    <script type="text/javascript" src="js/highcharts-more.js"></script>
    <script type="text/javascript" src="gzip.php?file=dss-cdfdm-chart"></script>
    <script type="text/javascript">
            /**
             * page behaviours
             */
            jQuery(function () {
                DropDown.setDefault('chart_year', jQuery("#chart_year_val").attr('data'));
                DropDown.setDefault('chart_sourcecol', jQuery("#chart_sourcecol_val").attr('data'));
                var chart = new CdfdmChart();
                var charttitle = '<?php echo werise_cdfdm_chart::getWvarField($cls->arg_chart_cdfdm_sourcecol) ?>';
                var seriesdata = <?php echo json_encode($cls->chart) ?>;
                chart.callHighCharts(charttitle, seriesdata);
            });
    </script>
<?php endif; ?>    
