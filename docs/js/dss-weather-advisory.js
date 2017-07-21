/**
 * DEPRECATED!!!
 *
function showAdvisory(ajaxdata)
{    
    var tmp_date = '';
    var tmp_month = '';
    var tmp_data = '';
    var tmp_date2 = '';
    var tmp_month2 = '';
    var tmp_data2 = '';

    // rainfall
    if (ajaxdata.adv_rain.advisory_code>0)
    {
        var fake_sowdate = 0;
        var fake_harvestdate = 99999999999999;
        // rain category
        var tmp = "javascript:launch_help('q1')";
        jQuery('#f-rain').html('<a href="'+tmp+'">'+ajaxdata.adv_rain.advisory_cat+'</a>. '+ajaxdata.adv_rain.advisory_txt);        
        // rain onset
        jQuery('#rain-onset').html(ajaxdata.rain_onset);        
        // flood dates
        var wetdates = ajaxdata.wet_dates;
        var adv_wetdates = WeatherAdvisory.getWetDates(fake_sowdate,fake_harvestdate,wetdates);
        jQuery('#wet-dates').html(adv_wetdates);               
        // drought dates
        var drydates = ajaxdata.dry_dates;
        var adv_drydates = WeatherAdvisory.getDryDates(fake_sowdate,fake_harvestdate,drydates);
        jQuery('#dry-dates').html(adv_drydates);                
        
        jQuery('.adv-rainfall').show();
        jQuery('#advisory').show();
    }

    // fertilizer
    jQuery("#fert-apply li").remove();
    if (ajaxdata.fert_apply!==false)
    {
        for (var i=0; i<ajaxdata.fert_apply.length; i++)
        {
            tmp_date = new Date(ajaxdata.fert_apply[i]['from'][0]);
            tmp_month = tmp_date.getMonth();
            tmp_data = ajaxdata.fert_apply[i]['from'][1];
            tmp_date2 = new Date(ajaxdata.fert_apply[i]['to'][0]);
            tmp_month2 = tmp_date2.getMonth();
            tmp_data2 = ajaxdata.fert_apply[i]['to'][1];
            jQuery("#fert-apply").append('<li>' + m_names[tmp_month] + ' ' + tmp_date.getDate() + ' (rain: ' + tmp_data + ') to ' + m_names[tmp_month2] + ' ' + tmp_date2.getDate() + ' (rain: ' + tmp_data2 + ')</li>');
        }
        jQuery('#adv-fertilizer').show();
        jQuery('#advisory').show();
    }    
}*/