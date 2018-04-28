<section id="main-content">
    <div class="container">

        <p class="lead">
            Displays the records of website usage.
        </p>

        <h4>Weather Advisory Access Log</h4>

        <div class="table-responsive">
            <table class="table table-bordered table-condensed">
                <tr class="tr-gray">
                    <th width="120">Date</th>
                    <th>User</th>
                    <th>Location</th>
                    <th>Year</th>
                    <th>Type</th>
                </tr>            
                <?php $weather_access_log = $cls->getWeatherAccessLog(); ?>
                <?php if ($weather_access_log) : ?>
                    <?php foreach ($weather_access_log as $wlog) : ?>
                        <tr>                            
                            <td><?php echo $wlog->create_date ?></td>
                            <td><a href="admin.php?pageaction=users&userid=<?php echo $wlog->userid ?>"><?php echo $wlog->username ?></a></td>
                            <td>
                                <span class="badge"><?php echo $wlog->station_id ?></span> <?php echo $wlog->station_name ?>, <?php echo $wlog->sub_region ?>, <?php echo $wlog->top_region ?>, <?php echo $wlog->country_code ?>
                            </td>
                            <td><?php echo $wlog->year ?></td>
                            <td><?php echo $wlog->wtype ?></td>
                        </tr>                
                    <?php endforeach; ?>                    
                <?php endif; ?>
            </table>
        </div>
        
        <h4>Crop Advisory Access Log</h4>

        <div class="table-responsive">
            <table class="table table-bordered table-condensed">
                <tr class="tr-gray">
                    <th width="120">Date</th>
                    <th>User</th>
                    <th>Location</th>
                    <th>Year</th>
                    <th>Type</th>
                    <th>Variety</th>
                    <th>Fert.</th>
                </tr>            
                <?php $oryza_access_log = $cls->getOryzaAccessLog(); ?>
                <?php if ($oryza_access_log) : ?>
                    <?php foreach ($oryza_access_log as $olog) : ?>
                        <tr>                            
                            <td><?php echo $olog->create_date ?></td>
                            <td><a href="admin.php?pageaction=users&userid=<?php echo $olog->userid ?>"><?php echo $olog->username ?></a></td>
                            <td>
                                <span class="badge"><?php echo $olog->station_id ?></span> <?php echo $olog->station_name ?>, <?php echo $olog->sub_region ?>, <?php echo $olog->top_region ?>, <?php echo $olog->country_code ?>
                            </td>
                            <td><?php echo $olog->year ?></td>
                            <td><?php echo $olog->wtype ?></td>
                            <td><?php echo $olog->variety ?></td>
                            <td><?php echo $olog->fert ?></td>
                        </tr>                
                    <?php endforeach; ?>                    
                <?php endif; ?>
            </table>
        </div>        

    </div>
</section>