<div class="width-center">
    <header>
        <h1 class="title"><?php echo _CURRENT_OPT ?></h1>
    </header>

    <h3>Available Datasets</h3>

    <table class="table table-bordered adm-table">
        <tr class="tr-gray">
            <th width="40">ID</th>
            <th width="200">Station</th>
            <th width="160">Action</th>
        </tr>
        <?php $last_country = '' ?>
        <?php foreach (oryza_data::getAllDatasets() as $rec) : ?>

            <?php if ($last_country != $rec->country_code): ?>
                <tr class="tr-gray">
                    <td colspan="3" style="font-family:courier;font-weight:700;font-size: 1.6em"><img class="icon-flag-<?php echo $rec->country_code ?>" /><?php echo werise_stations_country::getName($rec->country_code) ?></td>
                </tr>
            <?php endif; ?>

            <tr>
                <td><?php echo $rec->station_id ?></td>
                <td><?php echo $rec->station_name ?></td>
                <td><a class="btn btn-small" href="admin.php?pageaction=export&country=<?php echo $rec->country_code ?>&amp;station=<?php echo $rec->station_id ?>"><i class="icon-download"></i> Export</a></td>
            </tr>

            <?php $last_country = $rec->country_code ?>

        <?php endforeach; ?>
    </table>

</div>