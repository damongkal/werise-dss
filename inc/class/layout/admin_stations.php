<div class="width-center">
    <div id="help-btn">
        <p>Displays the contents of the database.</p>
    </div>
    
    <?php foreach ($cls->stations as $country_code => $regions) : ?>

        <h3><img class="icon-flag-<?php echo $country_code ?>" style="margin-top: 8px" /><?php echo werise_stations_country::getName($country_code) ?></h3>

        <table class="table table-bordered adm-table">
            <tr>
                <th class="hide-td" width="20"></th>
                <th class="hide-td" width="20"></th>
                <th class="hide-td" width="260"></th>
                <th class="hide-td" width="280"></th>
                <th class="hide-td" width="120"></th>
                <th class="hide-td" width="120"></th>
                <th class="hide-td" width="120"></th>
            </tr>

            <?php foreach ($regions as $region_id => $subregions) : ?>

                <tr class="tr-gray region-td">
                    <td colspan="7"><?php echo $cls->getRegionName($region_id) ?></td>
                </tr>

                <tr class="tr-gray">
                    <th>&nbsp;</th>
                    <th>ID</th>
                    <th>Station</th>
                    <th>Geo-Location (lat/lon/alt)</th>
                    <th>Historical Data</th>
                    <th>Forecast Data</th>
                    <th>Oryza2000</th>
                </tr>

                <?php foreach ($subregions as $subregion_id => $stations) : ?>

                    <tr class="tr-gray subregion-td">
                        <td>&nbsp;</td>
                        <td colspan="6"><?php echo $cls->getRegionName($subregion_id) ?></td>
                    </tr>

                    <?php foreach ($stations as $station) : ?>
                        <tr>
                            <td><span class="label label-<?php echo ($station->is_enabled == 1) ? "success" : "important" ?>"><i class="icon-<?php echo ($station->is_enabled == 1) ? "ok-circle" : "remove-circle" ?>"></i></span></td>
                            <td><a href="admin.php?pageaction=weatherref&action=detail&station_id=<?php echo $country_code.'-'.$station->station_id ?>"><span class="badge"><?php echo $station->station_id ?></span></a></td>
                            <td><?php echo $station->station_name ?></td>
                            <td><?php echo $cls->fmtLatLon($station) ?></td>
                            <td><?php echo $cls->fmtDataFiles($station->historical, $station->historicaldb) ?></td>
                            <td><?php echo $cls->fmtDataFiles($station->forecast,$station->forecastdb) ?></td>
                            <td><?php echo $cls->fmtDataFiles($station->oryza) ?></td>
                        </tr>
                    <?php endforeach; ?>

                <?php endforeach; ?>
            <?php endforeach; ?>
        </table>
    <?php endforeach; ?>        
         
</div>




<script type="text/javascript">
    jQuery(function () {
        jQuery('.mapbtn').click(function () {
            var latlon = (jQuery(this).attr('data'));
            var latlon2 = latlon.split(";");
            var lat = parseFloat(latlon2[0]);
            var lon = parseFloat(latlon2[1]);
            window.open("https://www.google.com.ph/maps/@" + lat + "," + lon + ",13z");
        });
    });
</script>