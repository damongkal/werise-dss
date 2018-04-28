<div class="width-center">
    <div id="help-btn">
        <p>Displays the weather data available.</p>
    </div>

    <?php if ($cls->view === 'station') : ?>

        <h3>Weather Station</h3>

        <table class="table table-bordered adm-table">
            <tr>
                <td class="tr-gray">ID</td>
                <td><?php echo $cls->station_data->station_id ?></td>
            </tr>
            <tr>
                <td class="tr-gray">Station</td>
                <td><?php echo $cls->station_data->station_name ?></td>
            </tr>
            <tr>
                <td class="tr-gray">Region</td>
                <td><img class="icon-flag-<?php echo $cls->station_data->country_code ?>">
                    <?php echo $cls->station_data->subregion_name ?>,
                    <?php echo $cls->station_data->topregion_name ?>,
                    <?php echo werise_stations_country::getName($cls->station_data->country_code) ?>
                </td>
            </tr>
        </table>

        <h3>Oryza 2000 Simulation Data</h3>

        <?php if ($cls->oryza_data[werise_weather_properties::_FORECAST]) : ?>

            <table class="table table-bordered adm-table">
                <tr class="tr-gray">
                    <th width="40">ID</th>
                    <th width="60">Year</th>
                    <th width="150">Variety</th>
                    <th width="200">Fertilization</th>
                    <th width="80">Displayed<br /> in graphs?</th>
                </tr>

                <?php foreach ($cls->oryza_data[werise_weather_properties::_FORECAST] as $rec) : ?>

                    <tr>
                        <td><a href="admin.php?pageaction=oryzaref&action=detail&id=<?php echo $rec->id ?>"><span class="badge"><?php echo $rec->id ?></span></a></td>
                        <td><?php echo $rec->year ?></td>
                        <td><?php echo $rec->variety ?></td>
                        <td><?php echo $cls->fmtFert($rec->fert) ?></td>
                        <td><?php echo ($rec->is_disabled == 1) ? 'NO' : 'YES' ?></td>
                    </tr>

                <?php endforeach; ?>

            </table>

        <?php else: ?>

            <div class="alert alert-info alert-condensed" style="width:400px">No records.</div>

        <?php endif; ?>

        <?php foreach (array(werise_weather_properties::_REALTIME, werise_weather_properties::_FORECAST) as $wtype) : ?>

            <h3><?php echo werise_weather_properties::getTypeDesc($wtype) ?> Weather Data</h3>

            <?php if ($cls->weather_data[$wtype]) : ?>

                <table class="table table-bordered adm-table">
                    <tr class="tr-gray">
                        <th width="40">ID</th>
                        <th width="40">Year</th>
                        <th width="40">Graphs?</th>
                        <th width="300">Station Name</th>
                        <th width="300">Geo. (Lat/Lon/Alt)</th>
                    </tr>
                    <?php foreach ($cls->weather_data[$wtype] as $rec) : ?>

                        <tr>
                            <td><a href="admin.php?pageaction=weatherfile&action=detail&id=<?php echo $rec->id ?>"><span class="badge"><?php echo $rec->id ?></span></a></td>
                            <td><?php echo $rec->year ?></td>
                            <td><?php echo ($rec->is_disabled == 1) ? 'NO' : 'YES' ?></td>
                            <td><?php echo $cls->getComment($rec->id, 'STATIONNAME') ?></td>
                            <td><?php echo $cls->getComment($rec->id, 'GEO') ?></td>
                        </tr>

                    <?php endforeach; ?>
                </table>

            <?php else: ?>

                <div class="alert alert-info alert-condensed" style="width:400px">No records.</div>

            <?php endif; ?>

        <?php endforeach; ?>

    <?php endif; ?>

    <?php if ($cls->view === 'list') : ?>

        <h3><?php echo werise_weather_properties::getTypeDesc(werise_weather_properties::_REALTIME) ?> Data</h3>
        <table class="table table-bordered adm-table">
            <tr class="tr-gray">
                <th width="40">ID</th>
                <th width="40">Year</th>
                <th width="40">Graphs?</th>
                <th width="250">Station Name</th>
                <th width="150">Geo. (Lat/Lon/Alt)</th>
            </tr>
            <?php $last_country = '' ?>
            <?php $last_station = '' ?>
            <?php foreach (weather_data::getDatasets(null, werise_weather_properties::_REALTIME) as $rec) : ?>

                <?php if ($last_country != $rec->country_code): ?>
                    <tr class="tr-gray">
                        <td colspan="6" style="font-family:courier;font-weight:700;font-size: 1.6em"><img class="icon-flag-<?php echo $rec->country_code ?>" /><?php echo werise_stations_country::getName($rec->country_code) ?></td>
                    </tr>
                    <?php $last_station = '' ?>
                <?php endif; ?>

                <?php if ($last_station != $rec->station_id): ?>
                    <tr class="tr-gray">
                        <td colspan="6" style="font-family:courier;font-weight:700;font-size: 1.2em"><?php echo '[' . $rec->station_id . '] ' . $cls->getStationName($rec->country_code, $rec->station_id) ?></td>
                    </tr>
                <?php endif; ?>

                <tr>
                    <td><?php echo $rec->id ?></td>
                    <td><?php echo $rec->year ?></td>
                    <td><?php echo ($rec->is_disabled == 1) ? 'NO' : 'YES' ?></td>
                    <td><?php echo $cls->getComment($rec->id, 'STATIONNAME') ?></td>
                    <td><?php echo $cls->getComment($rec->id, 'GEO') ?></td>
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
            <?php foreach (weather_data::getDatasets(null, werise_weather_properties::_FORECAST) as $rec) : ?>

                <?php if ($last_country != $rec->country_code): ?>
                    <tr class="tr-gray">
                        <td colspan="3" style="font-family:courier;font-weight:700;font-size: 1.6em"><img class="icon-flag-<?php echo $rec->country_code ?>" /><?php echo werise_stations_country::getName($rec->country_code) ?></td>
                    </tr>
                    <?php $last_station = '' ?>
                <?php endif; ?>

                <?php if ($last_station != $rec->station_id): ?>
                    <tr class="tr-gray">
                        <td colspan="3" style="font-family:courier;font-weight:700;font-size: 1.2em"><?php echo $cls->getStationName($rec->country_code, $rec->station_id) ?></td>
                    </tr>
                <?php endif; ?>

                <tr>
                    <td><?php echo $rec->id ?></td>
                    <td><?php echo $rec->year ?></td>
                    <td><?php echo ($rec->is_disabled == 1) ? 'YES' : 'NO' ?></td>
                </tr>

                <?php $last_country = $rec->country_code ?>
                <?php $last_station = $rec->station_id ?>

            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>