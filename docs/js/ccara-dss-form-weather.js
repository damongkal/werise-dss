/**
 * page items
 */
var item_chart = '#chart1';
var item_country = '#country';
var item_station = '#station';
var item_tyear = '#type_year';
var item_wvar = '#wvar';

/**
 * page behaviours
 */
jQuery(function() {
    
        jQuery(item_chart).width(screen.width - 80);

        populateStation('ID',item_station,'w');

        jQuery(item_country).change(function() {
            populateStation(jQuery(this).val(),item_station,'w');
            populateYear2(false, item_tyear);
        });

        jQuery(item_station).change(function() {
            populateYear(jQuery(item_country).val(),jQuery(this).val(),'w',[item_tyear]);
        });

        jQuery('#frm').submit(function() {
            getWeatherData();
            return false;
        });
    });


/**
 * for chart: get properties of variables
 * index
 * 0 = unit of measurement used 
 * 1 = maximum value
 */
function getWeatherVarProps(wvar)
{
    if (wvar==0)
    {
        return ['mm/decade',400];
    }
    if (wvar==1 || wvar==2)
    {
        return ['°C',45];
    }   
    if (wvar==3)
    {
        return ['MJ/m²',35];
    }
    if (wvar==4)
    {
        return ['kPa',6];
    }
    if (wvar==5)
    {
        return ['m/s',10];
    }
    return 'unit';
}


/**
 * 
 */
function getWeatherData()
{
    hideErrorChart();
    jQuery('#advisory').hide();
    jQuery('#datagrid1').hide();
    
    var country = jQuery(item_country).val();
    var station_id = jQuery(item_station).val();
    var wvar = jQuery(item_wvar).val();
    var type_year = jQuery(item_tyear).val();
    var year = type_year.substr(1, 4);
    var wtype = type_year.substr(0, 1);

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
    if (year=='')
    {
        showErrorChart('Please select Year.');
        return;
    }    
    if (wvar==-1)
    {
        showErrorChart('Please select Measured Variable.');
        return;
    }
        
    jQuery(item_chart).html('<img src="images/ajax-loader.gif" />');    
    jQuery.ajax({
        type: "GET",
        url: "ajax_weather_chart.php",
        data: "country="+country+"&station="+station_id+"&year="+year+"&wvar="+wvar+"&wtype="+wtype,
        success: function (html) {
            var data = JSON.parse(html);
            if (data.wvar!=false)
            {
                showWeatherChart(data.chart);
                if (wvar==0)
                {    
                    showAdvisory(data.advisory);
                }    
                if (data.grid!=false)
                {
                    showDatagrid(data.grid); 
                }
                
            } else
            {
                showErrorChart('');
            }
        },
        error: function (e, t, n) {
            alert('ajax error: getWeatherData');
            return
        }
    })
}

/**
 * 
 */
function showWeatherChart(ajaxdata)
{
    jQuery(item_chart).height('500px');    
    
    var station_name = jQuery(item_station+' option:selected').html();
    var wvar = jQuery(item_wvar).val();
    var wvar_name = jQuery(item_wvar+' option:selected').html();
    var wvar_props = getWeatherVarProps(wvar);
    var type_year = jQuery(item_tyear).val();
    var year = type_year.substr(1, 4);
    var wtype = type_year.substr(0, 1);
    var graphtype = 'column';
    if(wvar>0)
    {
        graphtype = 'scatter';
    }
    if(wvar==1)
    {
        graphtype = 'arearange';
    }    
    
    jQuery(item_chart).highcharts({
        chart: {
            type: 'line'
        },
        credits: {
            enabled: false
        },        
        title: {
            text: getWeatherType(wtype) + ' Weather Data',
            x: -20 //center
        },
        subtitle: {
            text: 'Station: ' + station_name + ' | Year: ' + year,
            x: -20
        },
        xAxis: {
            title: {
                text: ajaxdata.chart_period
            },            
            type: 'datetime',
            dateTimeLabelFormats: { // don't display the dummy year
                month: '%b',
                year: '%b'
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
                text: wvar_name + ' [' + wvar_props[0] + ']'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }],
            min: 0,
            max: ajaxdata.max
        },
        legend: {
            layout: 'vertical',
            align: 'center',
            verticalAlign: 'bottom',
            borderWidth: 1
        },
        tooltip: {
            crosshairs:true,
            formatter: function() {
                 var yval = '';
                 if (typeof this.point.low == 'undefined') {
                     yval = this.y  + ' ' + wvar_props[0];
                 } else
                 {
                     yval = this.point.low + ' ' + wvar_props[0] + ' - ' + this.point.high + ' ' + wvar_props[0];
                 }
                 return '<b>'+ this.series.name +'</b><br/>'+
                 Highcharts.dateFormat('%e. %b', this.x) +': '+ yval;
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
    var wvar_name = jQuery(item_wvar+' option:selected').html();
    
    // tabular data
	jQuery('#datagrid1').TidyTable(
	{
		columnTitles : ['Month', 'Decadal', wvar_name, 'P20', 'P50', 'P80'],
		columnValues : ajaxdata
	});
            
    jQuery('#datagrid1').show();
}

function showAdvisory(ajaxdata)
{
    var m_names = new Array("January", "February", "March", 
    "April", "May", "June", "July", "August", "September", 
    "October", "November", "December");    
    var tmp_date = '';
    var tmp_month = '';
    var tmp_data = '';
    var tmp_date2 = '';
    var tmp_month2 = '';
    var tmp_data2 = '';    
    
    // rainfall
    jQuery('#f-rain').html(ajaxdata.f_rain);
    
    // fertilizer
    if (ajaxdata.fert_apply==false)
    {
        jQuery("#fert-apply li").remove();
    } else
    {    
        jQuery("#fert-apply li").remove();
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
    }
    
    jQuery('#advisory').show();
}