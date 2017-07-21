<div class="width-center">
    <div id="help-btn">
        <p>Displays the fertilizer schedules that is used during Oryza2000 runs.</p>
        <button class="btn btn-info btn-small" onclick="javascript:launch_help('q12')"><i class="icon-question-sign"> </i> Help</button>
    </div>

    <h2><?php echo _t('General Recommendation') ?></h2>    

    <table class="table table-bordered adm-table">
        <tr class="tr-gray">
            <th colspan="4">Fertilizer (NPK) Schedule</th>
        </tr>
        <tr class="tr-gray">
            <th width="180">Station</th>
            <th width="180">Basal</th>
            <th width="180">Top Dress 1</th>
            <th width="180">Top Dress 2</th>
        </tr>
        <?php $last_v = '' ?>
        <?php foreach ($cls->getAll('1') as $rec) : ?>

            <?php if ($last_v != $rec->country_code): ?>
                <tr class="tr-gray">
                    <td colspan="4"><img class="icon-flag-<?php echo $rec->country_code ?>" /><?php echo werise_stations_country::getName($rec->country_code) ?></td>
                </tr>        
            <?php endif; ?>        

            <tr>
                <td><?php echo $rec->station_name ?></td>
                <td><?php echo $cls->formatNPK($rec,1) ?></td>
                <td><?php echo $cls->formatNPK($rec,2) ?></td>
                <td><?php echo $cls->formatNPK($rec,3) ?></td>
            </tr>

            <?php $last_v = $rec->country_code ?>

        <?php endforeach; ?>
    </table>
    
    <h2><?php echo _t('Specific Recommendation by Target Yield') ?></h2>    

    <table class="table table-bordered adm-table">
        <tr class="tr-gray">
            <th width="180">&nbsp;</th>
            <th colspan="3">Fertilizer (NPK) Schedule</th>
        </tr>
        <tr class="tr-gray">
            <th width="180">Yield (t/ha)</th>
            <th width="180">Basal</th>
            <th width="180">Top Dress 1</th>
            <th width="180">Top Dress 2</th>
        </tr>
        <?php $last_v = '' ?>
        <?php foreach ($cls->getAll('2') as $rec) : ?>

            <?php if ($last_v != $rec->variety): ?>
                <tr class="tr-gray">
                    <td colspan="4"><?php echo $rec->variety ?></td>
                </tr>        
            <?php endif; ?>        

            <tr>
                <td><?php echo $rec->yld ?></td>
                <td><?php echo $cls->formatNPK($rec,1) ?></td>
                <td><?php echo $cls->formatNPK($rec,2) ?></td>
                <td><?php echo $cls->formatNPK($rec,3) ?></td>
            </tr>

            <?php $last_v = $rec->variety ?>

        <?php endforeach; ?>
    </table>    

</div>