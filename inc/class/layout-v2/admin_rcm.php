<section id="main-content">
    <div class="container">

        <p class="lead">
            Fertilizer schedules that is used during Oryza2000 runs.
        </p>

        <h4><?php echo _t('General Recommendation') ?></h4>    

        <div class="table-responsive">
            <table class="table table-bordered table-condensed">
                <tr class="tr-gray">
                    <th rowspan="2">Station</th>
                    <th colspan="2">Basal</th>
                    <th colspan="2">Top Dress 1</th>
                    <th colspan="2">Top Dress 2</th>
                </tr>
                <tr class="tr-gray">
                    <th width="100">NPK</th>
                    <th width="100">DAT</th>
                    <th width="100">NPK</th>
                    <th width="100">DAT</th>
                    <th width="100">NPK</th>
                    <th width="100">DAT</th>
                </tr>
                <?php $last_v = '' ?>
                <?php foreach ($cls->getAll('1') as $rec) : ?>

                    <?php if ($last_v != $rec->country_code): ?>
                        <tr class="tr-gray">
                            <td colspan="7">
                                <span class="flag-icon flag-icon-<?php echo strtolower($rec->country_code) ?>"></span>
                                <?php echo werise_stations_country::getName($rec->country_code) ?>
                            </td>
                        </tr>        
                    <?php endif; ?>        

                    <tr>
                        <td><?php echo $rec->station_name ?></td>
                        <td><?php echo $cls->formatNPK($rec, 1) ?></td>
                        <td><?php echo $rec->n1day ?></td>
                        <td><?php echo $cls->formatNPK($rec, 2) ?></td>
                        <td><?php echo $rec->n2day ?></td>
                        <td><?php echo $cls->formatNPK($rec, 3) ?></td>
                        <td><?php echo $rec->n3day ?></td>
                    </tr>

                    <?php $last_v = $rec->country_code ?>

                <?php endforeach; ?>
            </table>
        </div>

        <?php if (false): ?>
        <h2><?php echo _t('Specific Recommendation by Target Yield') ?></h2>    

        <div class="table-responsive">
            <table class="table table-bordered table-condensed">
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
                        <td><?php echo $cls->formatNPK($rec, 1) ?></td>
                        <td><?php echo $cls->formatNPK($rec, 2) ?></td>
                        <td><?php echo $cls->formatNPK($rec, 3) ?></td>
                    </tr>

                    <?php $last_v = $rec->variety ?>

                <?php endforeach; ?>
            </table>    
        </div>
        <?php endif; ?>

    </div>
</section>    