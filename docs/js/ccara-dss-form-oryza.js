/**
 * page items
 */
var item_chart = '#chart1';
var item_country = '#country';
var item_station = '#station';
var item_tyear = '#type_year';

/**
 * page behaviours
 */
jQuery(function() {
    
    var years = [item_tyear, '#type_year2', '#type_year3', '#type_year4'];       
    jQuery(item_chart).width(screen.width - 80);
        
    populateStation('ID',item_station,'o');

    jQuery(item_country).change(function() {
        populateStation(jQuery(this).val(),item_station,'o');
        for (var i = 0; i < years.length; i++) {
            populateYear2(false, years[i]);
        }
    });

    jQuery(item_station).change(function() {
        populateYear(jQuery(item_country).val(),jQuery(this).val(),'o',years);
    });        
    
    jQuery(years[0]).change(function() {
        populateVariety(
            jQuery(item_country).val(),
            jQuery(item_station).val(),
            jQuery(this).val(),
            '#variety');
    });    
    
    jQuery(years[1]).change(function() {
        populateVariety(
            jQuery(item_country).val(),
            jQuery(item_station).val(),
            jQuery(this).val(),
            '#variety2');
    });

    jQuery(years[2]).change(function() {
        populateVariety(
            jQuery(item_country).val(),
            jQuery(item_station).val(),
            jQuery(this).val(),
            '#variety3');
    });
    
    jQuery(years[3]).change(function() {
        populateVariety(
            jQuery(item_country).val(),
            jQuery(item_station).val(),
            jQuery(this).val(),
            '#variety4');
    });    

    jQuery('#frm').submit(function() {
        getOryzaData();
        return false;
    });
    
    jQuery('#preferred_sowdate').change(function() {
        jQuery('.hi_yld_tr_all').hide();
        jQuery('#opt-planting3').show();
        jQuery('#hi_yld_tr_'+jQuery(this).val()).show();
        jQuery('#hi_yld_trf_'+jQuery(this).val()).show();
    });    

});

/**
 * execute AJAX call to get variety
 * @params string country_key ID of country
 * @params string station_key ID of station
 * @params string year 
 * @params string station_id html ID of form select for variety
 */
function populateVariety(country_key, station_key, type_year, item_id)
{
    var year = type_year.substr(1, 4);
    
    if (year==0)
    {
        return;
    }     
        
    showLoader(item_id);    
    jQuery.ajax({
        type: "GET",
        url: "ajax_lookup_table.php",
        data: "action=varieties&country="+country_key+'&station='+station_key+'&year='+year,
        success: function (html) {
            var data = JSON.parse(html);
            populateVariety2(data, item_id);
        },
        error: function (e, t, n) {
            alert('ajax error: populateVariety');
            return;
        }
    })
}

/**
 * populate station with AJAX result
 * @params string data JSON format of varieties
 * @params string item_id html ID of form select
 */
function populateVariety2(data, item_id)
{
    hideLoader(item_id);
    
    var dropdown = jQuery(item_id);
    jQuery(item_id + ' option').remove();
    dropdown.append(jQuery('<option></option>').attr("class",'select-extra').attr("value",'').text('Variety »'));
    
    jQuery.each(data, function(i){
        dropdown.append(jQuery("<option></option>").attr("value",data[i][0]).text(data[i][1]));
    });
}

/**
 *
 */
function getOryzaData()
{
    hideErrorChart();
    jQuery('#datagrid1').hide();
    
    var country = jQuery(item_country).val();
    var station_id = jQuery(item_station).val();
    
    if (country=='')
    {
        showErrorChart('Please select Country.');
        return;
    }
    if (station_id==0)
    {
        showErrorChart('Please select Station.');
        return;
    }      
    
    // set 1
    var type_year = jQuery(item_tyear).val();
    var year = type_year.substr(1, 4);
    var wtype = type_year.substr(0, 1);
    var variety = jQuery('#variety').val();
    var fertil = jQuery('#fertil').val();
    var set1 = "&year="+year+"&wtype="+wtype+"&variety="+variety+"&fert="+fertil;

    if (type_year=='')
    {
        showErrorChart('Please select Year for Set 1.');
        return;
    }
    if (variety=='')
    {
        showErrorChart('Please select Variety for Set 1.');
        return;
    }
    if (fertil=='')
    {
        showErrorChart('Please select Fertilization for Set 1.');
        return;
    }    
    
    // set 2
    type_year = jQuery('#type_year2').val();
    year = type_year.substr(1, 4);
    wtype = type_year.substr(0, 1);
    variety = jQuery('#variety2').val();
    fertil = jQuery('#fertil2').val();
    var set2 = "&year2="+year+"&wtype2="+wtype+"&variety2="+variety+"&fert2="+fertil;

    if (type_year!='')
    {
        if (variety=='')
        {
            showErrorChart('Please select Variety for Set 2.');
            return;
        }
        if (fertil=='')
        {
            showErrorChart('Please select Fertilization for Set 2.');
            return;
        }    
    }

    // set 3
    type_year = jQuery('#type_year3').val();
    year = type_year.substr(1, 4);
    wtype = type_year.substr(0, 1);
    variety = jQuery('#variety3').val();
    fertil = jQuery('#fertil3').val();
    var set3 = "&year3="+year+"&wtype3="+wtype+"&variety3="+variety+"&fert3="+fertil;
    
    if (type_year!='')
    {
        if (variety=='')
        {
            showErrorChart('Please select Variety for Set 3.');
            return;
        }
        if (fertil=='')
        {
            showErrorChart('Please select Fertilization for Set 3.');
            return;
        }    
    }
    
    // set 4
    type_year = jQuery('#type_year4').val();
    year = type_year.substr(1, 4);
    wtype = type_year.substr(0, 1);
    variety = jQuery('#variety4').val();
    fertil = jQuery('#fertil4').val();    
    var set4 = "&year4="+year+"&wtype4="+wtype+"&variety4="+variety+"&fert4="+fertil;
    
    if (type_year!='')
    {
        if (variety=='')
        {
            showErrorChart('Please select Variety for Set 4.');
            return;
        }
        if (fertil=='')
        {
            showErrorChart('Please select Fertilization for Set 4.');
            return;
        }    
    }
    
    jQuery(item_chart).html('<img src="images/ajax-loader.gif" />');    
    jQuery.ajax({
        type: "GET",
        url: "ajax_oryza_chart.php",
        data: "country="+country+"&station="+station_id+set1+set2+set3+set4,
        success: function (html) {
            var data = JSON.parse(html);
            if (data==false)
            {
                showErrorChart('');
                return;
            }            
            showOryzaChart(data.chart);
            showAdvisory(data.advisory);
            populateSowDate(data.chart.series[0].data);
            if (data.grid!=false)
            {
                showDatagrid(data.grid);
            }
        },
        error: function (e, t, n) {
            alert('ajax error: getOryzaChart');
            return
        }
    })
}

/**
 *
 */
function showOryzaChart(ajaxdata)
{
    jQuery(item_chart).height('500px');        
    var station_name = jQuery(item_station+' option:selected').html();
    
    jQuery(item_chart).highcharts({
        chart: {
            type: 'spline'
        },
        credits: {
            enabled: false
        },        
        title: {
            text: 'Simulated Attainable Grain Yield'
        },
        subtitle: {
            text: 'Station : ' + station_name
        },
        legend: {
            layout: 'vertical',
            align: 'center',
            verticalAlign: 'bottom',
            borderWidth: 1
        },        
        xAxis: {
            title: {
                text: 'Sowing Date'
            },            
            type: 'datetime',
            dateTimeLabelFormats: { // don't display the dummy year
                month: '%b',
                year: '%b',
				day: '%b'
            },
            plotBands : [{ 
                    from: ajaxdata.wetseason.from,
                    to: ajaxdata.wetseason.to,
                    color: 'rgba(68, 170, 213, 0.1)',
                    label: {
                        text: 'Wet Season',
                        style: {
                            color: '#606060'
                        }
                    }
            }]               
        },
        yAxis: {
            title: {
                text: 'Grain yield (t/ha)'
            },
            min: 0
        },
        tooltip: {
            formatter: function() {
                return '<b>'+ this.series.name +'</b><br/>'+
                Highcharts.dateFormat('%e. %b', this.x) +': '+ this.y +' t/ha';
            }
        },
        series: ajaxdata.series
    });
    
    // hide the dataset selection
    jQuery('#dataselection').hide();
    document.getElementById('chart1').scrollIntoView();
}

function showDatagrid(ajaxdata)
{
    var grid_titles = new Array();
    grid_titles[0] = 'Date';
    grid_titles[1] = 'Set 1';
    //alert(jQuery('#type_year2').val());
    if (jQuery('#type_year2').val()!='')
    {
        grid_titles[2] = 'Set 2';
    }
    if (jQuery('#type_year3').val()!='')
    {
        grid_titles[3] = 'Set 3';
    }
    if (jQuery('#type_year4').val()!='')
    {
        grid_titles[4] = 'Set 4';
    }
        
    // tabular data
    jQuery('#datagrid1').TidyTable(
    {
        columnTitles : grid_titles,
        columnValues : ajaxdata
    });
            
    jQuery('#datagrid1').show();        
}

function showAdvisory(ajaxdata)
{
    var fertil = jQuery('#fertil').val();    
    
    // rainfall
    var type_year = jQuery(item_tyear).val();
    var year = type_year.substr(1, 4);
    jQuery('#f-year').html(year);
    jQuery('#f-rain').html(ajaxdata.f_rain);
    
    // high yield
    var hi_yld_rec = '';
    var fert_apply = '';
    var prot = jQuery("#opt-planting2").find("tbody");    
    var prot2 = jQuery("#opt-planting3").find("tbody");    
    for (var i=0; i<ajaxdata.hi_yld.length; i++)
    {
        hi_yld_rec = ajaxdata.hi_yld[i];
        //tmp_date = new Date(hi_yld_rec[0]);
        //tmp_month = tmp_date.getMonth();        
        fert_apply = showFertAdvisory(hi_yld_rec['fert']);        
        if (hi_yld_rec[2]==true)
        {
            prot.append('<tr class="hi_yld_tr"><td>' + formatDate(hi_yld_rec[0],'') + '</td><td style="text-align:right">' + hi_yld_rec[1] + '</td></tr>');         
            prot.append('<tr class="hi_yld_fert_apply"><td colspan="2">Fertilizer may be applied on these dates:<br />'+fert_apply+'</td></tr>');
        }  
        prot2.append('<tr id="hi_yld_tr_' + hi_yld_rec['key'] + '" class="hi_yld_tr_all" style="display:none;background-color: #8CB730"><td>' + formatDate(hi_yld_rec[0],'') + '</td><td style="text-align:right">' + hi_yld_rec[1] + '</td></tr>');         
        prot2.append('<tr id="hi_yld_trf_' + hi_yld_rec['key'] + '" class="hi_yld_tr_all" style="display:none"><td colspan="2">'+fert_apply+'</td></tr>');
    }
    
    // fertilizer
    if (ajaxdata.fert_apply==false)
    {
        jQuery("#fert-apply li").remove();
    } else
    {
        var fert_rec = '';
        jQuery("#fert-apply li").remove();
        for (var j=0; j<ajaxdata.fert_apply.length; j++)
        {
            fert_rec = ajaxdata.fert_apply[j];
            jQuery("#fert-apply").append('<li>' + formatDate(fert_rec['from'][0],'') + ' (rain: ' + fert_rec['from'][1] + ') to ' + formatDate(fert_rec['to'][0],'') + ' (rain: ' + fert_rec['to'][1] + ')</li>');    
        }   
    }
    
    jQuery('#advisory').show();
    if (fertil==0)
    {
        jQuery('#fert-apply-adv').hide();
    }
}

function showFertAdvisory(fert_data_array)
{
    var tmp_date = '';   
    var fert_data_rec = '';
    var fert_apply = '<ul>';
    var from_rain = 0;
    var to_rain = 0;
    var txt_rain = '';
    for (var k=0; k<fert_data_array.length; k++)
    {
        fert_data_rec = fert_data_array[k];
        tmp_date = new Date(fert_data_rec['from'][0]);
        from_rain = fert_data_rec['from'][1];
        to_rain = fert_data_rec['to'][1];
        if (from_rain>to_rain)
        {
            from_rain = fert_data_rec['to'][1];
            to_rain = fert_data_rec['from'][1];        
        }
        if (from_rain==to_rain)
        {
            txt_rain = from_rain;
        } else
        {
            txt_rain = from_rain + ' to ' + to_rain;
        }
        fert_apply = fert_apply + '<li>' + tmp_date.getFullYear() + ' ' + formatDate(fert_data_rec['from'][0],'m-d') + ' to ' + formatDate(fert_data_rec['to'][0],'m-d') + '<br/>Rainfall : ' + txt_rain + '</li>';
    }
    fert_apply = fert_apply  + '</ul>';
    if (fert_apply=='<ul></ul>')
    {
        fert_apply = 'None';
    }    
    return fert_apply;
}

function populateSowDate(data)
{
    var dropdown = jQuery('#preferred_sowdate');    
    jQuery.each(data, function(i){
        dropdown.append(jQuery("<option></option>").attr("value",i).text(formatDate(data[i][0],'m-d')));
    });
}