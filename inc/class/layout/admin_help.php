<div class="width-center">

    <div id="help-guide">
        <p id="q1" class="question">What is rainfall category?</p>
        <div class="answer">
            <img src="images/rain-category01.jpg" /><br /><br />
            <img src="images/rain-category02.jpg" /><br /><br />
            There are three (3) categories for rainfall amount.<br />
            <b>1. Normal</b><br />
            When rainfall amount is between drought event (20th percentile) and flood event (80th percentile)<br />
            <b>2. Above Normal</b><br />
            When rainfall amount is greater than flood event (80th percentile)<br />
            <img src="images/chartdef01_2.jpg" /><br />
            <b>3. Below Normal</b><br />
            When rainfall amount is less than drought event (20th percentile)<br />
            <img src="images/chartdef02_2.jpg" />
        </div>

        <p id="q2" class="question">When does onset of rain happen?</p>
        <div class="answer">
            <img src="images/rain-onset.jpg" /><br /><br />
            Onset of rain occurs when total rainfall amount from the sowing date has accumulated to 30mm.<br />
            example:<br />
            sowing date: Jun-01<br />
            rainfall schedule:<br />
            Jun-01 : 10mm<br />
            Jun-11 : 10mm<br />
            Jun-21 : 10mm &raquo; <b>onset of rain!</b><br />
            Jul-01 : 10mm
        </div>


        <p id="q3" class="question">How is Expected flooding dates computed?</p>
        <div class="answer">
            <img src="images/flooding-dates.jpg" /><br /><br />
            Flooding is expected when 10-day rainfall is above normal or the 80th percentile.
        </div>

        <p id="q4" class="question">How is Expected drought dates computed?</p>
        <div class="answer">
            <img src="images/dry-dates.jpg" /><br /><br />
            Drought is expected when 10-day rainfall is below normal or the 20th percentile.
        </div>

        <p id="q5" class="question">How is weather chart data computed?</p>
        <div class="answer">
            <img src="images/weather-chart.jpg" /><br /><br />
            Data is displayed in 10-day accumulated values (decadal). Percentile computation is applied to the data. We get the 20th percentile to determine drought events and the 80th percentile to determine flood events.
        </div>

        <p id="q6" class="question">How is grain yield chart data computed?</p>
        <div class="answer">
            <img src="images/grainyield-chart.jpg" /><br /><br />
            Data displayed is from Oryza2000 output. Grain Yield values is extracted from <i>op.dat</i> file under <i>WRR14</i> column.
        </div>

        <p id="q7" class="question">How is optimum sowing dates computed?</p>
        <div class="answer">
            <img src="images/optimum-dates.jpg" /><br /><br />
            Percentile computation is applied on all expected grain yield values. Optimum sowing dates are those records with expected grain yield higher than 80th percentile rank.
        </div>

        <p id="q8" class="question">How is the crop calendar computed?</p>
        <div class="answer">
            <img src="images/crop-calendar.jpg" /><br />
            <ul>
            <li><b><i>Sowing Date</i></b> : from op.dat file under column RUNNUM</li>
            <li><b><i>Panicle Init.</i></b> : from res.dat file under column DVS (value reaches 0.65)</li>
            <li><b><i>Flowering</i></b> : from res.dat file under column DVS (value reaches 1)</li>
            <li><b><i>Harvest Date</i></b> : from res.dat file under column DVS (value reaches 2)</li>
            <li><b><i>Yield</i></b> : from op.dat file under column WRR14</li>
            <li><b><i>Fertilizer Schedule</i></b> : see <a href="/admin.php?pageaction=rcm">Fertilizer Application Reference</a></li>
            </ul>
        </div>

        <?php if (_opt(sysoptions::_ADM_SHOW_MENU)) : ?>
        <p id="q11" class="question">Admin howto's: Weather Data Files</p>
        <div class="answer">
            Weather data files are the most essential part of the database. WeRise has accumulated weather data files from various stations. These files are encoded in Oryza2000 format. The files are structured as illustrated below.<br />
            <img src="images/weather-file-structure.png" /><br /><br />
            
            <b>Functions:</b>
            <ul>
                <li><i>Load all stations</i> - Stores all weather data files for the specified country.</li>
                <li><i>Load all years</i> - Stores all weather data files for the specified station.</li>
                <li><i>Load</i> - Stores the specified station.</li>
                <li><i>Delete</i> - Removes from the database the specified station.</li>
            </ul>
        
        </div>

        <p id="q9" class="question">Admin howto's: SINTEX Data Files</p>
        <div class="answer">
            When weather Forecast data are provided in SINTEX format, Oryza2000 cannot read them. Therefore, we need to convert from SINTEX format to Oryza2000 format. The SINTEX files are structured as illustrated below.<br />
            <img src="images/weather-file-structure.png" /><br />
            Click on the "convert" button to process a specific station. After conversion, files are created in weather forecast folders that are in Oryza2000 format.
        </div>

        <p id="q10" class="question">Admin howto's: Oryza2000 Interface</p>
        <div class="answer">
            WeRise computes the simulated grain yields by using Oryza2000. First, the files needed by Oryza2000 should be in place. The files are structured as illustrated below:<br />
            <img src="images/oryza2000-file-structure.png" />
            <p>
            Fertilizer schedule is listed in <a href="#q12">Fertilizer Application Reference</a>. You can also adjust the settings in the <a href="#q14">Preferences</a> section.</p>
            <b>Functions:</b>
            <ul>
                <li><i>Load</i> - Runs Oryza2000 for the specified station.</li>
                <li><i>Delete</i> - Removes from the database the specified station.</li>
            </ul>
        </div>

        <p id="q12" class="question">Admin howto's: Fertilizer Application Reference</p>
        <div class="answer">
            Fertilizer schedules is one of the variables required by Oryza2000. We list here the schedule for reference. Adjustment to the schedule can be requested to the Administrator.
        </div>

        <p id="q13" class="question">Admin howto's: Stations</p>
        <div class="answer">
            We list here the stations where weather data was was observed and collected.
        </div>

        <p id="q14" class="question">Admin howto's: Preferences</p>
        <div class="answer">
            Set the options that is required for the operation of the website.
        </div>
        <?php endif; ?>

    </div>

</div>

<script type="text/javascript">
    jQuery('#block-header').hide();
    jQuery('#footerv1').hide();
</script>
    }