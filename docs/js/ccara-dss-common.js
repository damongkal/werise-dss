var m_names = new Array("January", "February", "March", 
    "April", "May", "June", "July", "August", "September", 
    "October", "November", "December");

function formatDate(my_date,my_format)
{
    var tmp_date = new Date(my_date);
    var tmp_month = tmp_date.getMonth();
    if (my_format=='m-d')
    {
        return m_names[tmp_month] + '-' + tmp_date.getDate();     
    } else
    {
        return tmp_date.getFullYear() + '-' + m_names[tmp_month] + '-' + tmp_date.getDate();
    }
}

/**
 * execute AJAX call to get station data
 * @params string country_key ID of country
 * @params string station_id html ID of form select for station
 */
function populateStation(country_key, item_station_id, ctype)
{
    showLoader(item_station_id);
    jQuery.ajax({
        type: "GET",
        url: "ajax_lookup_table.php",
        data: "action=station&country="+country_key+"&ctype="+ctype,
        success: function (html) {
            var data = JSON.parse(html);
            populateStation2(data, item_station_id);
        },
        error: function (e, t, n) {
            alert('ajax error: populateStation');
            return;
        }
    })
}

/**
 * populate station with AJAX result
 * @params string data JSON format of stations
 * @params string station_id html ID of form select for station
 */
function populateStation2(data, item_station_id)
{
    hideLoader(item_station_id);
    
    var dropdown = jQuery(item_station_id);
    jQuery(item_station + ' option').remove();
    dropdown.append(jQuery('<option></option>').attr("class",'select-extra').attr("value",0).text('Station »'));
    
    jQuery.each(data, function(i){
        dropdown.append(jQuery("<option></option>").attr("value",data[i].station_id).text(data[i].station_name));
    });
}

/**
 * execute AJAX call to get station years data
 * @params string country_key ID of country
 * @params int station_key ID of station
 * @params string dbsource (w)eather / (o)ryza ?
 * @params string station_id html ID of form select for station
 */
function populateYear(country_key, station_key, dbsource, item_tyear_ids)
{
    showLoader(item_tyear_ids[0]);
    if (station_key==0)
    {
        return;
    }

    jQuery.ajax({
        type: "GET",
        url: "ajax_lookup_table.php",
        data: "action=stationyear&country="+country_key+"&station="+station_key+"&dbsource="+dbsource,
        success: function (html) {
            var data = JSON.parse(html);
            for (var i = 0; i < item_tyear_ids.length; i++) {
                populateYear2(data, item_tyear_ids[i]);
            }
        },
        error: function (e, t, n) {
            alert('ajax error: populateYear');
            return;
        }
    })
}

/**
 * populate year with AJAX result
 * @params string data JSON format of years
 * @params string station_id html ID of form select for year
 */
function populateYear2(data, item_tyear_id)
{
    hideLoader(item_tyear_id);
    var dropdown = jQuery(item_tyear_id);
    jQuery(item_tyear_id+' option').remove();
    dropdown.append(jQuery("<option></option>").attr("value",'').text('Year »'));
    
    if (data!=false)
    {
        var last_wtype = '';
        jQuery.each(data, function(i){
            if (data[i].wtype != last_wtype)
            {
                dropdown.append(jQuery("<option></option>").attr("class",'select-extra').attr("value",'').text(getWeatherType(data[i].wtype)));
            }
            dropdown.append(jQuery("<option></option>").attr("value",data[i].wtype+data[i].year).text(data[i].year));
            last_wtype = data[i].wtype;
        });
    }
}

/**
 * description of weather data type
 */
function getWeatherType(wtype)
{
    var dtext = 'Real-time';
    if (wtype=='f')
    {
        dtext = 'Forecast';
    }    
    return dtext;
}

/**
 * display error in chart div
 */
function showErrorChart(error_txt)
{
    var error_txt2 = 'There was a problem generating the data you requested. Please contact the website Administrator.';
    if (error_txt=='')
    {
        jQuery('#dss-error-box').html(error_txt2).show();
    } else
    {
        jQuery('#dss-error-box').html(error_txt).show();
    }
}

/**
 * display error in chart div
 */
function hideErrorChart()
{
    jQuery('#dss-error-box').hide();
}

function showLoader(item_id)
{
    jQuery(item_id).css("background-image", "url('images/ajax-loader2.gif')"); 
    jQuery(item_id).css("background-repeat", "no-repeat");     
    jQuery(item_id).css("color", "#ffffff");
}

function hideLoader(item_id)
{
    jQuery(item_id).css("background-image", ""); 
    jQuery(item_id).css("color", "#555555");
}