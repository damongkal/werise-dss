<section id="main-content">
    <div class="container">

        <p class="lead">
            Overview of all available data
        </p>

        <?php if ($cls->action === 'list'): ?>    

            <?php foreach ($cls->stations as $country_code => $regions) : ?>

                <h4><span class="flag-icon flag-icon-<?php echo strtolower($country_code) ?>" style="border:1px solid #ccc"></span> <?php echo werise_stations_country::getName($country_code) ?></h4>

                <div class="table-responsive">
                    <table id="db-overview" class="table table-bordered table-condensed">

                        <?php foreach ($regions as $region_id => $subregions) : ?>

                            <tr class="region-td">
                                <td colspan="7"><?php echo $cls->getRegionName($region_id) ?></td>
                            </tr>

                            <tr class="tr-gray">
                                <th>&nbsp;</th>
                                <th width="60">ID</th>
                                <th>Station</th>
                                <th>Geo-Location (lat/lon/alt)</th>
                                <th width="120">Historical Data</th>
                                <th width="120">Forecast Data</th>
                                <th width="120">Oryza2000</th>
                            </tr>

                            <?php foreach ($subregions as $subregion_id => $stations) : ?>

                                <tr class="subregion-td">
                                    <td>&nbsp;</td>
                                    <td colspan="6"><?php echo $cls->getRegionName($subregion_id) ?></td>
                                </tr>

                                <?php foreach ($stations as $station) : ?>                    
                                    <tr>
                                        <td><span class="label label-<?php echo ($station->is_enabled == 1) ? "success" : "important" ?>"><i class="icon-<?php echo ($station->is_enabled == 1) ? "ok-circle" : "remove-circle" ?>"></i></span></td>
                                        <td><a href="admin.php?pageaction=weatherref&action=detail&station_id=<?php echo $country_code . '-' . $station->station_id ?>"><span class="badge"><?php echo $station->station_id ?></span></a></td>
                                        <td><?php echo $station->station_name ?></td>
                                        <td><?php echo $cls->fmtLatLon($station) ?></td>
                                        <td><?php echo $cls->fmtDataFiles($station->historical, $station->historicaldb) ?></td>
                                        <td><?php echo $cls->fmtDataFiles($station->forecast, $station->forecastdb) ?></td>
                                        <td><?php echo $cls->fmtDataFiles($station->oryza) ?></td>
                                    </tr>
                                <?php endforeach; ?>

                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </table>
                </div>
            <?php endforeach; ?>        

        <?php endif; ?>

    </div>
</section>    




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