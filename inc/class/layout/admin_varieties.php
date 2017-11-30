<div class="width-center">
    <div id="help-btn">
        <p>Displays the rice varieties.</p>
        <button class="btn btn-info btn-small" onclick="javascript:launch_help('q12')"><i class="icon-question-sign"> </i> Help</button>
    </div>

    <h2><?php echo _t('Rice varieties') ?></h2>    

    <table class="table table-bordered adm-table">
        <tr class="tr-gray">
            <th colspan="3">&nbsp;</th>
            <th colspan="2">Maturity</th>
            <th colspan="2">Yield</th>
            <th colspan="2">Total Water Requirement<br />Dry direct seeding</th>
            <th colspan="2">Total Water Requirement<br />Transplanting</th>
        </tr>
        <tr class="tr-gray">
            <th width="70">ID</th>
            <th width="100">Code</th>
            <th width="150">Name</th>
            <th width="80">Min.<br />(days)</th>
            <th width="80">Max.<br />(days)</th>
            <th width="80">Avg.<br />(t/ha)</th>
            <th width="80">Potential<br />(t/ha)</th>
            <th width="80">Depth<br />(mm)</th>
            <th width="80">Volume<br />(1,000,000L/ha)</th>
            <th width="80">Depth<br />(mm)</th>
            <th width="80">Volume<br />(1,000,000L/ha)</th>
        </tr>

        <?php foreach ($cls->getAll() as $rec) : ?>    

            <tr>
                <td><?php echo $rec->id ?></td>
                <td><?php echo $rec->code ?></td>
                <td><?php echo $rec->name ?></td>
                <td style="text-align: right"><?php echo $rec->maturity_min ?></td>
                <td style="text-align: right"><?php echo $rec->maturity_max ?></td>
                <td style="text-align: right"><?php echo $rec->yield_avg ?></td>
                <td style="text-align: right"><?php echo $rec->yield_potential ?></td>
                <td style="text-align: right"><?php echo $rec->getDdsDepth() ?></td>
                <td style="text-align: right"><?php echo $rec->getDdsVolume() ?></td>
                <td style="text-align: right"><?php echo $rec->getTpDepth() ?></td>
                <td style="text-align: right"><?php echo $rec->getTpVolume() ?></td>
            </tr>

        <?php endforeach; ?>
    </table>

</div>