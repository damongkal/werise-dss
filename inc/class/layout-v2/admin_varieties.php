<section id="main-content">
    <div class="container">

        <p class="lead">
            Displays the rice varieties.
        </p>

        <div class="table-responsive">
            <table class="table table-bordered table-condensed">
                <tr class="tr-gray">
                    <th colspan="2">&nbsp;</th>
                    <th colspan="2">Maturity</th>
                    <th colspan="2">Yield</th>
                    <th colspan="2">Total Water Requirement<br />Dry direct seeding</th>
                    <th colspan="2">Total Water Requirement<br />Transplanting</th>
                </tr>
                <tr class="tr-gray">
                    <th width="70">ID</th>
                    <th>Name / Code</th>
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
                        <td>
                            <?php echo $rec->name ?>
                            <span class="badge badge-secondary"><?php echo $rec->code ?></span>
                        </td>
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

    </div>
</section>    