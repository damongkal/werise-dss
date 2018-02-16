<div class="width-center">
    <div id="help-btn">
        <p>Displays the Oryza2000 data available.</p>
    </div>

    <?php if ($cls->action !== 'detail'): ?>
        <h3><?php echo werise_weather_properties::getTypeDesc(werise_weather_properties::_REALTIME) ?> Data</h3>
        <table class="table table-bordered adm-table">
            <tr class="tr-gray">
                <th width="40">ID</th>
                <th width="60">Year</th>
                <th width="150">Variety</th>
                <th width="200">Fertilization</th>
                <th width="80">Displayed<br /> in graphs?</th>
            </tr>
            <?php $last_country = '' ?>
            <?php $last_station = '' ?>
            <?php foreach (oryza_data::getDatasets(array('wtype'=>werise_weather_properties::_REALTIME)) as $rec) : ?>

                <?php if($last_country!=$rec->country_code): ?>
                <tr class="tr-gray">
                    <td colspan="5" style="font-family:courier;font-weight:700;font-size: 1.6em"><img class="icon-flag-<?php echo $rec->country_code ?>" /><?php echo werise_stations_country::getName($rec->country_code) ?></td>
                </tr>
                <?php $last_station = '' ?>
                <?php endif; ?>

                <?php if($last_station!=$rec->station_id): ?>
                <tr class="tr-gray">
                    <td colspan="5" style="font-family:courier;font-weight:700;font-size: 1.2em"><?php echo $cls->getStationName($rec->country_code, $rec->station_id) ?></td>
                </tr>
                <?php endif; ?>

                <tr>
                    <td><a href="admin.php?pageaction=oryzaref&action=detail&id=<?php echo $rec->id ?>"><span class="badge"><?php echo $rec->id ?></span></a></td>
                    <td><?php echo $rec->year ?></td>
                    <td><?php echo $rec->variety ?></td>
                    <td><?php echo $rec->fert ?></td>
                    <td><?php echo ($rec->is_disabled==1) ? 'NO' : 'YES' ?></td>
                </tr>

                <?php $last_country = $rec->country_code ?>
                <?php $last_station = $rec->station_id ?>

            <?php endforeach; ?>
        </table>

        <h3><?php echo werise_weather_properties::getTypeDesc(werise_weather_properties::_FORECAST) ?> Data</h3>
        <table class="table table-bordered adm-table">
            <tr class="tr-gray">
                <th width="40">ID</th>
                <th width="60">Year</th>
                <th width="150">Variety</th>
                <th width="200">Fertilization</th>
                <th width="80">Displayed<br /> in graphs?</th>
            </tr>
            <?php $last_country = '' ?>
            <?php $last_station = '' ?>
            <?php foreach (oryza_data::getDatasets(array('wtype'=>werise_weather_properties::_FORECAST)) as $rec) : ?>

                <?php if($last_country!=$rec->country_code): ?>
                <tr class="tr-gray">
                    <td colspan="5" style="font-family:courier;font-weight:700;font-size: 1.6em"><img class="icon-flag-<?php echo $rec->country_code ?>" /><?php echo werise_stations_country::getName($rec->country_code) ?></td>
                </tr>
                <?php $last_station = '' ?>
                <?php endif; ?>

                <?php if($last_station!=$rec->station_id): ?>
                <tr class="tr-gray">
                    <td colspan="5" style="font-family:courier;font-weight:700;font-size: 1.2em"><?php echo $cls->getStationName($rec->country_code, $rec->station_id) ?></td>
                </tr>
                <?php endif; ?>

                <tr>
                    <td><a href="admin.php?pageaction=oryzaref&action=detail&id=<?php echo $rec->id ?>"><span class="badge"><?php echo $rec->id ?></span></a></td>
                    <td><?php echo $rec->year ?></td>
                    <td><?php echo $rec->variety ?></td>
                    <td><?php echo $cls->fmtFert($rec->fert) ?></td>
                    <td><?php echo ($rec->is_disabled==1) ? 'NO' : 'YES' ?></td>
                </tr>

                <?php $last_country = $rec->country_code ?>
                <?php $last_station = $rec->station_id ?>

            <?php endforeach; ?>
        </table>

    <?php endif; ?>

    <?php if ($cls->action === 'detail'): ?>        
        
        <h3>Station</h3>
        <p>
            <img class="icon-flag-<?php echo $cls->station->country_code ?>"> <?php echo $cls->station->station_name ?>, <?php echo $cls->station->subregion_name ?>, <?php echo $cls->station->topregion_name ?>, <?php echo werise_stations_country::getName($cls->station->country_code) ?>
        </p>            
        
        <?php foreach ($cls->datasets as $idx => $dataset) : ?>                    
        
        <h3>Dataset</h3>

        <table class="table table-bordered adm-table">
            <tr>
                <td class="tr-gray">ID</td>
                <td><?php echo $dataset['dataset_info']->id ?></td>
            </tr>
            <tr>
                <td class="tr-gray">Date Processed</td>
                <td><?php echo $dataset['dataset_info']->upload_date ?> <span class="badge">ver <?php echo $dataset['dataset_info']->oryza_ver ?></span></td>
            </tr>
            <tr>
                <td class="tr-gray">Year</td>
                <td><?php echo $dataset['dataset_info']->year ?> <?php echo werise_weather_properties::getTypeDesc($dataset['dataset_info']->wtype) ?></td>
            </tr>
            <tr>
                <td class="tr-gray">Variety</td>
                <td><?php echo $dataset['dataset_info']->variety ?></td>
            </tr>
            <tr>
                <td class="tr-gray">Fertilization</td>
                <td><?php echo werise_oryza_fertilizer::getTypeDesc($dataset['dataset_info']->fert) ?></td>
            </tr>
        </table>
        
        <h4>Data</h4>

        <table class="table table-bordered adm-table">
            <tr class="tr-gray">
                <th width="40">Run</th>
                <th width="110">Decadal</th>
                <th width="50" style="text-align: right">Yield</th>
                <th width="200">Fertilizer Schedule</th>
                <th width="50" style="text-align: right">Emer<br />gence</th>
                <th width="50" style="text-align: right">Panicle<br /> Init.</th>
                <th width="50" style="text-align: right">Flower<br />ing</th>
                <th width="50" style="text-align: right">Harvest</th>
            </tr>
            <?php foreach ($dataset['dataset_data'] as $data): ?>
                <tr>
                    <td><?php echo $data->runnum ?></td>
                    <td><?php echo $data->observe_date ?></td>
                    <td style="text-align: right"><?php echo $data->yield ?></td>
                    <td><?php echo $data->fert ?></td>
                    <td style="text-align: right"><?php echo $data->emergence ?></td>
                    <td style="text-align: right"><?php echo $data->panicle_init ?></td>
                    <td style="text-align: right"><?php echo $data->flowering ?></td>
                    <td style="text-align: right"><?php echo $data->harvest ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        
        <h4>Grain Yield Chart</h4>
        <div id="yieldchart-<?php echo $dataset['dataset_info']->id ?>"></div>        

        <?php endforeach; ?>
    <?php endif; ?>

</div>

<script type="text/javascript" src="js/highcharts.js"></script>
<script type="text/javascript" src="js/highcharts-more.js"></script>
<script type="text/javascript" src="gzip.php?group=oryzaadmin"></script>
<script type="text/javascript">
    /**
     * page behaviours
     */
    jQuery(function() {
        var station_name = 'location';
        var chart = new OryzaChart();
        var chart_data;
        <?php foreach ($cls->datasets as $idx => $dataset) : ?>                    
        chart_data = [{data:<?php echo json_encode($dataset['chart_data']) ?>}];        
        chart.callHighCharts('#yieldchart-<?php echo $dataset['dataset_info']->id ?>', chart_data, station_name);
        <?php endforeach; ?>
    });
</script>