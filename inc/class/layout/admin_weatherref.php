<div class="width-center">
    <div id="help-btn">
        <p>Displays the weather data available.</p>
    </div>
    
    <h3><?php echo werise_weather_properties::getTypeDesc(werise_weather_properties::_REALTIME) ?> Data</h3>
    <table class="table table-bordered adm-table">
        <tr class="tr-gray">
            <th width="80">ID</th>            
            <th width="100">Year</th>
            <th width="80">Ver.</th>
            <th width="100">Graphs?</th>
            <th width="250">Station Name</th>
            <th width="150">Geo. (Lat/Lon/Alt)</th>            
        </tr>
        <?php $last_country = '' ?>        
        <?php $last_station = '' ?>
        <?php foreach (weather_data::getDatasets(null,werise_weather_properties::_REALTIME) as $rec) : ?>
        
            <?php if($last_country!=$rec->country_code): ?>
            <tr class="tr-gray">
                <td colspan="6" style="font-family:courier;font-weight:700;font-size: 1.6em"><img class="icon-flag-<?php echo $rec->country_code ?>" /><?php echo werise_stations_country::getName($rec->country_code) ?></td>
            </tr>
            <?php $last_station = '' ?>
            <?php endif; ?>
            
            <?php if($last_station!=$rec->station_id): ?>
            <tr class="tr-gray">
                <td colspan="6" style="font-family:courier;font-weight:700;font-size: 1.2em"><?php echo '['.$rec->station_id.'] '.$cls->getStationName($rec->country_code, $rec->station_id) ?></td>
            </tr>
            <?php endif; ?>                     
            
            <tr>
                <td><?php echo $rec->id ?></td>
                <td><?php echo $rec->year ?></td>
                <td><?php echo $rec->oryza_ver ?></td>
                <td><?php echo ($rec->is_disabled==1) ? 'NO' : 'YES' ?></td>
                <td><?php echo $cls->getComment($rec->id,'STATIONNAME') ?></td>
                <td><?php echo $cls->getComment($rec->id,'GEO') ?></td>
            </tr>
        
            <?php $last_country = $rec->country_code ?>
            <?php $last_station = $rec->station_id ?>
        
        <?php endforeach; ?>
    </table>
    
    <h3><?php echo werise_weather_properties::getTypeDesc(werise_weather_properties::_FORECAST) ?> Data</h3>
    <table class="table table-bordered adm-table">
        <tr class="tr-gray">
            <th width="80">ID</th>
            <th width="150">Year</th>
            <th width="150">Displayed in graphs?</th>
        </tr>
        <?php $last_country = '' ?>        
        <?php $last_station = '' ?>
        <?php foreach (weather_data::getDatasets(null,werise_weather_properties::_FORECAST) as $rec) : ?>
        
            <?php if($last_country!=$rec->country_code): ?>
            <tr class="tr-gray">
                <td colspan="3" style="font-family:courier;font-weight:700;font-size: 1.6em"><img class="icon-flag-<?php echo $rec->country_code ?>" /><?php echo werise_stations_country::getName($rec->country_code) ?></td>
            </tr>
            <?php $last_station = '' ?>
            <?php endif; ?>
            
            <?php if($last_station!=$rec->station_id): ?>
            <tr class="tr-gray">
                <td colspan="3" style="font-family:courier;font-weight:700;font-size: 1.2em"><?php echo $cls->getStationName($rec->country_code, $rec->station_id) ?></td>
            </tr>
            <?php endif; ?>                     
            
            <tr>
                <td><?php echo $rec->id ?></td>
                <td><?php echo $rec->year ?></td>
                <td><?php echo ($rec->is_disabled==1) ? 'YES' : 'NO' ?></td>
            </tr>
        
            <?php $last_country = $rec->country_code ?>
            <?php $last_station = $rec->station_id ?>
        
        <?php endforeach; ?>
    </table>    
</div>