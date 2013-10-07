<?php
include('bootstrap.php');
define('_INIT','oryza');
include('layout_header.php');
?>

<div id ="dataselection">
<form name="frm" action="#" method="post" id ="frm" class="form-inline" style="width:500px;margin:auto">  
    
    <div style="padding: 3px">
        <i class="icon-filter"></i>
        Select Dataset:
    </div>

    <div style="padding: 3px">

        <div class="bfh-selectbox bfh-countries" data-countryList="ID,LA,PH,TH" data-country="ID" data-flags="true" style="float:left">
            <input id="country" name="country" type="hidden" value="">
            <a class="bfh-selectbox-toggle" role="button" data-toggle="bfh-selectbox" href="#">
                <span class="bfh-selectbox-option input-small" data-option=""></span>
                <b class="caret"></b>
            </a>
            <div class="bfh-selectbox-options" style="min-width:120px">
                <input type="text" class="bfh-selectbox-filter" style="display:none">
                <div role="listbox">
                    <ul role="option" style="width:100px">
                    </ul>
                </div>
            </div>
        </div>

        &nbsp;&nbsp;
        <select style="width:240px" name="station" id="station">
            <option value="0">Station</option>
        </select>

        <div style="padding:10px 0 0 0">
            <label>Set 1: </label>
            
            <select name="type_year" id="type_year" style="width:90px">
                <option value="">Year »</option>
            </select>
            &nbsp;&nbsp;
            
            <select name="variety" id="variety" style="width:120px">
                <option value="">Variety »</option>
            </select>
            &nbsp;&nbsp;
            
            <select name="fertil" id="fertil" style="width:200px">
                <option value="">Fertilization »</option>
                <option value="0">No Fertilizer</option>
                <option value="1">General Recommendation</option>
            </select>
        </div>
        
        <div style="padding:10px 0 0 0">
            <label>Set 2: </label>
            
            <select name="type_year2" id="type_year2" style="width:90px">
                <option value="">Year »</option>
            </select>
            &nbsp;&nbsp;
            
            <select name="variety2" id="variety2" style="width:120px">
                <option value="">Variety »</option>
            </select>
            &nbsp;&nbsp;
            
            <select name="fertil2" id="fertil2" style="width:200px">
                <option value="">Fertilization »</option>
                <option value="0">No Fertilizer</option>
                <option value="1">General Recommendation</option>
            </select>
        </div>
        
        <div style="padding:10px 0 0 0">
            <label>Set 3: </label>
            
            <select name="type_year3" id="type_year3" style="width:90px">
                <option value="">Year »</option>
            </select>
            &nbsp;&nbsp;
            
            <select name="variety3" id="variety3" style="width:120px">
                <option value="">Variety »</option>
            </select>
            &nbsp;&nbsp;
            
            <select name="fertil3" id="fertil3" style="width:200px">
                <option value="">Fertilization »</option>
                <option value="0">No Fertilizer</option>
                <option value="1">General Recommendation</option>
            </select>
        </div>
        
        <div style="padding:10px 0 0 0">
            <label>Set 4: </label>
            
            <select name="type_year4" id="type_year4" style="width:90px">
                <option value="">Year »</option>
            </select>
            &nbsp;&nbsp;
            
            <select name="variety4" id="variety4" style="width:120px">
                <option value="">Variety »</option>
            </select>
            &nbsp;&nbsp;
            
            <select name="fertil4" id="fertil4" style="width:200px">
                <option value="">Fertilization »</option>
                <option value="0">No Fertilizer</option>
                <option value="1">General Recommendation</option>
            </select>
        </div>        
        
        <div class="text-right" style="padding:10px 0 0 0">
            <input style="font-size:12px" id="show" type="submit" value="Show Graph »" />
        </div>        

    </div>

</form>
</div>

<div id="dss-error-box" class="alert alert-error"></div>

<div id="chart1" class="chart"></div>

<div id="advisory" style="padding:0 0 0 40px;display:none">

    <h3>Advisory</h3>

    <ol>
        <li id="rainfall-adv">
            <span id="f-year"></span> wet-season rainfall is <span id="f-rain"></span><br /><br />
        </li>
        <li id="opt-sow-adv">
            Optimum sowing dates for rainfed rice crop<br />
            
            <table id="opt-planting2" class="table table-bordered table-condensed" style="width:280px">
                <thead>
                    <tr style="background-color: #567B11">
                        <th style="color:#ffffff;width:180px">Date</th>
                        <th style="color:#ffffff;width:100px;text-align:right">Yield (t/ha)</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>    
            </table>
        </li>
        <li id="fert-apply-adv">
            Fertilizer may be applied on these dates if sowing date is 
            <select name="preferred_sowdate" id="preferred_sowdate" style="width:150px">
                <option value="">Sow Date »</option>
            </select>
            
            <table id="opt-planting3" class="table table-bordered table-condensed" style="display:none;width:280px">
                <thead>
                    <tr style="background-color: #567B11">
                        <th style="color:#ffffff;width:180px">Sowing Date</th>
                        <th style="color:#ffffff;width:100px;text-align:right">Yield (t/ha)</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>    
            </table>
        </li>        
        <li id="fert-apply-adv2" style="display:none">
            Fertilizer may be applied on these dates:<br />
            <ul id="fert-apply">
            </ul>            
        </li>
        </li>
        <!--li>
            Attainable rainfed rice yield of <span id="selected-varieties"></span> crops that are sown between <span id="fert-from"></span> and <span id="fert-from"></span> would be higher than <span id="local-check"></span>.
        </li-->
    </ol>
    
</div>

<div id="datagrid1" class="datagrid-dss"></div>


<?php include('layout_footer.php'); ?>