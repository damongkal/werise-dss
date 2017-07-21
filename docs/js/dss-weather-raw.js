/**
 * display raw data
 * @param {type} ajaxdata
 * @returns {undefined}
 */
function showDatagrid(ajaxdata)
{
    var wvar_name = jQuery(item_wvar+' option:selected').html();
    jQuery('#datagrid1').TidyTable(
    {
	columnTitles : ['Month', 'Decadal', wvar_name, 'P20', 'P50', 'P80'],
	columnValues : ajaxdata
    });
}

function showRawRainfall(ajaxdata)
{
    if (ajaxdata.adv_rain.advisory_code===0)    
    {
        return;
    }
    var raw_rain = '<ul>';
    raw_rain = raw_rain + '<li>' + ajaxdata.adv_rain.year + ' Wet Season Total: ' + ajaxdata.adv_rain.wsrain + '</li>';
    raw_rain = raw_rain + '<li>Percentile (20%): ' + ajaxdata.adv_rain.p20 + '</li>';
    raw_rain = raw_rain + '<li>Percentile (80%): ' + ajaxdata.adv_rain.p80 + '</li>';
    raw_rain = raw_rain + '<li>Code: ' + ajaxdata.adv_rain.advisory_code + '</li>';
    raw_rain = raw_rain + '<li>Advisory: ' + ajaxdata.adv_rain.advisory_cat + '. ' + ajaxdata.adv_rain.advisory_txt + '</li>';
    raw_rain = raw_rain + '</ul>';
    jQuery('#raw_rain').html(raw_rain);
}