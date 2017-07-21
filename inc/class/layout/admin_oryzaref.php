<div class="width-center">
    <div id="help-btn">
        <p>Displays the Oryza2000 data available.</p>
    </div>
    
    <h3><?php echo werise_weather_properties::getTypeDesc(werise_weather_properties::_REALTIME) ?> Data</h3>
    <table class="table table-bordered adm-table">
        <tr class="tr-gray">
            <th width="80">ID</th>
            <th width="100">Year</th>
            <th width="150">Variety</th>
            <th width="150">Fertilization</th>
            <th width="100">Displayed in graphs?</th>
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
                <td><?php echo $rec->id ?></td>
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
            <th width="80">ID</th>
            <th width="100">Year</th>
            <th width="150">Variety</th>
            <th width="150">Fertilization</th>
            <th width="100">Displayed in graphs?</th>
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
                <td><?php echo $rec->id ?></td>
                <td><?php echo $rec->year ?></td>
                <td><?php echo $rec->variety ?></td>
                <td><?php echo $rec->fert ?></td>
                <td><?php echo ($rec->is_disabled==1) ? 'NO' : 'YES' ?></td>
            </tr>
        
            <?php $last_country = $rec->country_code ?>
            <?php $last_station = $rec->station_id ?>
        
        <?php endforeach; ?>
    </table>
</div>