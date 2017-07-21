<div class="width-center">
    <div id="help-btn">
        <p>Convert CDF-DM data files into Oryza2000 weather data files.</p>        
        <p><button class="btn btn-info btn-small" onclick="javascript:launch_help('q9')"><i class="icon-question-sign"> </i> Help</button></p>
    </div>

    <div id ="dataselection" style="width:600px; margin-bottom:15px;display:none">

        <form id="export_form" class="form" action="/admin.php" method="get">                        
            <input id="pageaction" name="pageaction" type="hidden" value="sintex" />
            <input id="action" name="action" type="hidden" value="export" />
            <input id="country" name="country" type="hidden" value="" />
            <input id="station" name="station" type="hidden" value="" />
            
            <fieldset style="margin-top: 0">
                <legend>Conversion Options</legend>            
            
                <label class="control-label" for="year">Year:</label>
                <input id="year" name="year" type="text" style="width:70px" value="<?php echo $cls->arg_year ?>"  /> 
                <span class="help-block">(if blank, all available years will be created)</span>

                <label class="control-label" for="output_wtype">Oryza2000 Weather Data Output:</label>            
                <label class="radio">
                  <input type="radio" name="output_wtype" id="output_wtype_r" value="r" <?php echo (($cls->arg_wtype==werise_weather_properties::_REALTIME) ? 'checked="checked"':'') ?>>
                  Historical &raquo; <?php echo _DATA_DIR . werise_weather_file::getFolder('r') ?>
                </label>            
                <label class="radio">
                  <input type="radio" name="output_wtype" id="output_wtype_f" value="f" <?php echo (($cls->arg_wtype==werise_weather_properties::_FORECAST) ? 'checked="checked"':'') ?>>
                  Forecast &raquo; <?php echo _DATA_DIR . werise_weather_file::getFolder('f') ?>
                </label>

                <label class="checkbox">
                    <input type="checkbox" name="overwrite_file" id="overwrite_file" value="1" <?php echo (($cls->arg_override==1) ? 'checked="checked"':'') ?> /> Overwrite output file if exists?
                </label>
            
            </fieldset>            
        </form>
    </div>

    <?php if ($cls->action==='export') : ?>

        <p><a class="btn btn-small" href="admin.php?pageaction=sintex"><i class="icon-repeat"></i> Back to Directory List</a></p>

        <?php if ($cls->is_error===true) : ?>

        <div class="well" style="width:800px">
            <?php echo $cls->action_ret ?>
        </div>

		<?php else : ?>

        <h3>Source: CDFDM OUT Files</h3>
        <div class="well" style="width:600px">
            <p>
                Year : <?php echo $cls->arg_year ?>
            </p>
            <ul>
            <?php foreach($cls->files_sintex as $sintex_file) : ?>
                <li><?php echo $sintex_file ?> </li>
            <?php endforeach; ?>
            </ul>
        </div>
        
        <h3>Destination: Forecast Files (PRN)</h3>
        <div class="well" style="width:600px">
            <ul>
            <?php foreach($cls->files_forecast as $year => $forecast_file) : ?>
                <li><?php echo $forecast_file ?> </li>
            <?php endforeach; ?>
            </ul>
        </div>        
        
        <div id="compute_formula">

        <h3>Special Constants</h3>
        <div class="well">
            <ul>
                <li><b>pval = </b> <?php echo $cls->specialvars['phi_const'] ?> </li>
                <li><b>t1 = </b> <?php echo $cls->specialvars['t1'] ?> </li>
                <li><b>t2 = </b> <?php echo $cls->specialvars['t2'] ?> </li>
                <li><b>t3 = </b> <?php echo $cls->specialvars['t3'] ?> </li>
            </ul>
        </div>

        <h3>Computations</h3>

        <a href="sintex-to-oryza.xlsx">Excel Sheet Computation Reference</a>

        <p style="font-weight: 700">phi (&phi;)</p>
        <div class="well">
            <b>&phi;</b> = (<b>pval</b>) &bull; <b>&pi;</b> / (180) <br />
            &nbsp; = <?php echo $cls->specialvars['phi'] ?>
        </div>

        <p style="font-weight: 700">Tmin</p>
        <div class="well">
            <b>Tmin</b> = (0.6108) &bull; e <sup>( (17.27) &bull; (tn) / ( (tn) + (237.3) ) )</sup>
        </div>

        <!--p style="font-weight: 700">Tmax</p>
        <div class="well" style="width:500px">
            Tmax = (0.6108) &bull; e <sup>( (17.27) * (tx) / ( (tx) + (237.3) ) )</sup>
        </div -->

        <p style="font-weight: 700">dr</p>
        <div class="well">
            <b>y</b> = (2) &bull; <b>&pi;</b> &bull; (<b>doy</b>) / (365) <br />
            <b>dr</b> = (1) + ( (0.033) &bull; cos(<b>y</b>) )
        </div>

        <p style="font-weight: 700">delta (&delta;)</p>
        <div class="well">
            <b>y</b> = (2) &bull; <b>&pi;</b> &bull; (<b>doy</b>) / (365) <br />
            <b>&delta;</b> = (0.409) &bull; sin( (<b>y</b>) - (1.39) )
        </div>

        <p style="font-weight: 700">omega (&omega;)</p>
        <div class="well">
            <b>&omega;</b> = cos<sup>-1</sup>( -(tan(<b>&phi;</b>)) &bull; tan(<b>&delta;</b>) )
        </div>

        <p style="font-weight: 700">sro</p>
        <div class="well">
            <b>a</b> = <b>&omega;</b> &bull; sin(<b>&phi;</b>) &bull; sin(<b>&delta;</b>)<br />
            <b>b</b> = cos(<b>&phi;</b>) &bull; cos(<b>&delta;</b>) &bull; sin(<b>&omega;</b>)<br />
            <b>sro</b> = ( 24 &bull; 60 / <b>&pi;</b> ) &bull; (0.082) &bull; (<b>dr</b>) &bull; (<b>a</b>+<b>b</b>)
        </div>

        <p style="font-weight: 700">Irradiance (rad)</p>
        <div class="well">
            <b>c</b> = ( (<b>tx</b>)-(<b>tn</b>) )<sup><b>t3</b></sup> <br />
            <b>rad</b> = (1000) &bull; (<b>sro</b>) &bull; (<b>t1</b>) &bull; ( 1 - e<sup>(-<b>t2</b>) &bull; (<b>c</b>)</sup> )
        </div>

        </div>

        <h3>Oryza Output Mapping</h3>

            <table class="table table-bordered adm-table">
                <tr>
                <tr>
                    <th>ORYZA</th>
                    <th>SINTEX</th>
                </tr>
                <tr>
                    <td>irradiance (kJ/m<sup>2</sup>)</td>
                    <td>rad</td>
                </tr>
                <tr>
                    <td>minimum temperature (&deg;C)</td>
                    <td>tn</td>
                </tr>
                <tr>
                    <td>maximum temperature (&deg;C)</td>
                    <td>tx</td>
                </tr>
                <tr>
                    <td>vapor pressure (kPa)</td>
                    <td>Tmin</td>
                </tr>
                <tr>
                    <td>mean wind speed (m/s)</td>
                    <td>ws</td>
                </tr>
                <tr>
                    <td>precipitation (mm/d)</td>
                    <td>pr</td>
                </tr>
            </table>

        <?php foreach($cls->action_ret as $year => $year_csv) : ?>

        <h3><?php echo werise_weather_properties::getTypeDesc($cls->arg_wtype) ?> for <?php echo $year ?> <button class="btn btn-small" onclick="javascript:showYear(<?php echo $year ?>)"><i class="icon-eye-open"> </i> Show</button></h3>

        <div class="sintex_output_year" id="sintex_output_<?php echo $year ?>">
            
        <div class="well" style="width:800px">
            <ul>
                <li><strong>Computation:</strong> <?php echo $cls->files_compute[$year] ?> </li>
                <li><strong>Output Forecast:</strong> <?php echo $cls->files_forecast[$year] ?> </li>
            </ul>
        </div>

        <table class="table table-bordered adm-table">
            <tr class="tr-gray">
                <th colspan="6">SINTEX</th>
                <th colspan="6">COMPUTED</th>
            </tr>
            <tr class="tr-gray">
                <th width="120">Date</th>
                <th width="30">doy</th>
                <th width="70">pr</th>
                <th width="70">tn</th>
                <th width="70">tx</th>
                <th width="70">ws</th>
                <th width="70">Tmin</th>
                <th width="70">dr</th>
                <th width="70">&delta;</th>
                <th width="70">&omega;</th>
                <th width="70">sro</th>
                <th width="70">rad</th>
            </tr>
            <?php foreach($year_csv as $comp) : ?>
                <tr>
                    <td><?php echo $comp['yr']; ?></td>
                    <td><?php echo $comp['doy']; ?></td>
                    <td><?php echo $comp['pr']; ?></td>
                    <td><?php echo $comp['tn']; ?></td>
                    <td><?php echo $comp['tx']; ?></td>
                    <td><?php echo $comp['ws']; ?></td>
                    <td><?php echo $cls->fn($comp['tmin']); ?></td>
                    <td><?php echo $cls->fn($comp['dr']); ?></td>
                    <td><?php echo $cls->fn($comp['delta']); ?></td>
                    <td><?php echo $cls->fn($comp['omega']); ?></td>
                    <td><?php echo $cls->fn($comp['sro']); ?></td>
                    <td><?php echo $cls->fn($comp['rad'],0); ?></td>
                <tr>
            <?php endforeach; ?>
        </table>
        </div>   
        <?php endforeach; ?> 

	<?php endif;?>

    <?php endif;?>
</div>

<script type="text/javascript">

    function export_sintex(country,station)
    {
        jQuery('#country').val(country);
        jQuery('#station').val(station);
        jQuery("#export_form").submit();
    }
    
    function showYear(year)
    {
        jQuery('#sintex_output_'+year).show();
    }
</script>