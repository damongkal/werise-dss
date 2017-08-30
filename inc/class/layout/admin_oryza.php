<div class="width-center">
    <div id="help-btn">
        <p>Process grain yield simulations thru Oryza2000 and store results in the database.</p>
        <button class="btn btn-info btn-small" onclick="javascript:launch_help('q10')"><i class="icon-question-sign"> </i> Help</button>
    </div>

    <?php if ($cls->action === 'del') : ?>
        <p><a class="btn btn-small" href="admin.php?pageaction=oryza"><i class="icon-repeat"></i> Back to Directory List</a></p>
        <?php if ($cls->action_ret[0]) : ?>
            <p class="alert alert-success" style="display:block">dataset was deleted successfully</p>
        <?php else: ?>
            <p class="alert alert-error" style="display:block"><b>ERROR</b> : <?php echo $cls->action_ret[0] ?> </p>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($cls->action === 'load') : ?>
        <p><a class="btn btn-small" href="admin.php?pageaction=oryza"><i class="icon-repeat"></i> Back to Directory List</a></p>
        <?php foreach ($cls->action_ret as $action_ret) : ?>
            <h2>Dataset</h2>
            <?php if ($action_ret[0] === true) : ?>
                <?php $dataset = $action_ret[1] ?>
                <pre style="width:300px"><?php print_r($dataset['dataset'][0]); ?></pre>
                <p><i>op.dat</i></p>
                <pre style="width:600px"><?php print_r($dataset['db_op']); ?></pre>
                <p><i>res.dat</i></p>
                <pre style="width:600px"><?php print_r($dataset['db_res']); ?></pre>
                <p class="alert alert-success" style="display:block">dataset was loaded successfully!</p>
            <?php else: ?>
                <pre style="width:300px"><?php print_r($action_ret[1]['dataset']); ?></pre>
                <p class="alert alert-error" style="display:block"><b>ERROR</b> : <?php echo $action_ret[1]['error'] ?> </p>
            <?php endif; ?>
        <?php endforeach ?>
        <p><a class="btn btn-small" href="admin.php?pageaction=oryza"><i class="icon-repeat"></i> Back to Directory List</a></p>
    <?php endif; ?>

    <?php if ($cls->action === 'list'): ?>
        
        <?php $wtype = 'f' ?>

        <div id ="dataselection" style="width:500px">
            <h3 style="margin-top: 0">List Options</h3>
            <form id="list-options" class="form">            
                <label class="checkbox">    
                    <input type="checkbox" name="show_only_loaded" id="show_only_loaded" value="1" /> Show only loaded files?
                </label>        
            </form>    
        </div>

        <h3>Oryza2000 Program</h3>
        <p style="font-family:Courier, serif">
            <?php $batchfile = _DATA_SUBDIR_ORYZA . 'oryza.bat'; ?>
            <?php $batchfileinfo = file_get_contents($batchfile); ?>
            <span style="font-weight: 700">MS-DOS batch file:</span> <?php echo $batchfile ?>
        </p>                    
        <pre><?php echo $batchfileinfo ?></pre>

        <?php if ($cls->files['a']) : ?>

            <?php foreach ($cls->files['a'] as $country => $topregions) : ?>

                <h3><img class="icon-flag-<?php echo $country ?>" style="margin-top: 8px" /><?php echo werise_stations_country::getName($country) ?></h3>

                <table class="table table-bordered adm-table">      

                    <?php foreach ($topregions as $topregion_id => $topregion_info) : ?>

                        <tr class="tr-gray region-td">
                            <td colspan="6"><?php echo $topregion_info['name'] ?></td>
                        </tr>

                        <?php foreach ($topregion_info['subregion'] as $subregion_id => $subregion_info) : ?>        

                            <tr class="tr-gray subregion-td">
                                <td>&nbsp;</td>
                                <td colspan="5"><?php echo $subregion_info['name'] ?></td>
                            </tr>                                

                            <?php foreach ($subregion_info['station'] as $station_id => $station_info) : ?>                                        

                                <tr class="tr-gray">
                                    <th width="20">&nbsp;</th>
                                    <th width="120">File</th>
                                    <th width="50">Year</th>
                                    <th width="140">Is displayed to<br /> yield chart?</th>
                                    <th width="180">Action</th>
                                    <th width="400">Notes</th>
                                </tr>                                 

                                <tr>
                                    <td>&nbsp;</td>
                                    <td colspan="5" class="station-td"><?php echo $station_info['name'] ?></td>
                                </tr>                                     

                                <?php foreach ($station_info[$wtype] as $file) : ?>                                        

                                    <tr class="<?php echo ( $file['is_oryza_loaded'] ) ? 'tr_loaded' : 'tr_nloaded' ?>">
                                        <td>&nbsp;</td>
                                        <td><?php echo $file['file'] ?></td>
                                        <td><?php echo $file['year'] ?></td>
                                        <td>
                                            <?php if ($file['is_oryza_loaded']): ?>
                                                <?php echo $file['is_oryza_loaded']->upload_date . ' <a href="admin.php?pageaction=oryzaref&action=detail&id=' . $file['is_oryza_loaded']->id . '"><span class="badge">' . $file['is_oryza_loaded']->id . "</a>" ?>
                                            <?php else: ?>
                                                <span style="color:#ff0000"><i class="icon-eye-close"></i> NO</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo $cls->showActionElements($file, $wtype) ?>
                                        </td>
                                        <td><?php echo $cls->showNotes($file) ?></td>
                                    </tr>                                

                                <?php endforeach; ?>

                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </table>                            

            <?php endforeach; ?>

        <?php endif; ?>        

    <?php endif; ?>     

</div>

<script type="text/javascript">
    /**
     * page behaviours
     */
    jQuery(function () {

        jQuery('#realtime-container').hide();

        jQuery("#show_only_loaded").change(function () {
            if (jQuery(this).is(':checked') == true)
            {
                jQuery('.tr_nloaded').hide();
            } else
            {
                jQuery('.tr_nloaded').show();
            }
        });

        jQuery("#show-container").change(function () {
            jQuery('#realtime-container').hide();
            jQuery('#forecast-container').hide();
            jQuery('#' + jQuery(this).val()).show();
        });

    });
</script>