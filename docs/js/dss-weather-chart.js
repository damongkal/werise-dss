function WeatherChart()
{
    var type_year =  WeatherForm.getYear();
    var year = parseInt(type_year.substr(1, 4));
    var wtype = type_year.substr(0, 1);
    
    /**
     *
     * @returns {undefined}
     */
    this.showChart = function () {
        var wvar = 0;
        hideErrorChart();
        jQuery('#homeimages').hide();
        for (i = 1; i <= 5; i++)
        {
            if (jQuery("#wvar" + i).is(':checked'))
            {
                wvar = i;
                if (i<3)
                {
                    wvar = i - 1;
                }
                getData('#chart' + i, wvar);
            }
        }
    };
    /**
     *
     * @param {type} item_chart
     * @param {type} wvar
     * @returns {undefined}
     */
    function getData(item_chart, wvar)
    {
        jQuery(item_chart).show();

        // build URL
        var url = new UrlBuilder('');
        url.addArg('pageaction', 'weather');
        url.addArg('country', WeatherForm.getCountry());
        url.addArg('station', WeatherForm.getStation());
        url.addArg('year', year);
        url.addArg('wtype', wtype);
        url.addArg('wvar', wvar);
        weriseApp.ajax(url.getUrl()).done(function (data) {
            if (data.chart == false)
            {
                hideLoaderChart(item_chart);
                showErrorChart(_t('No data available.'));
                return;
            }
            if (data.wvar !== false)
            {
                callHighCharts(item_chart, wvar, data.chart);
                // toggle chart visibility
                var wvar_opt = item_chart.replace('chart', 'wvar_chart');
                jQuery(wvar_opt).show();
                // show other info
                jQuery('.afterload').show();
            }
        });
    }
    /**
     *
     * @param {type} item_chart
     * @param {type} wvar
     * @param {type} ajaxdata
     * @returns {undefined}
     */
    function callHighCharts(item_chart, wvar, ajaxdata)
    {
        // resize viewport
        var scrw = screen.width;
        if (scrw > 1300)
        {
            scrw = 800;
        }
        jQuery(item_chart).width(scrw - 0).height('300px');

        var station_name = jQuery('#location_name').val();
        var wvar_props = getWeatherVarProps(wvar);
        var wvar_unit = wvar_props[0];
        var wvar_name = _t(wvar_props[2]);

        jQuery(item_chart).highcharts({
            chart: {
                type: 'line',
                borderRadius: 10,
                backgroundColor: '#D5FFB5',
                borderWidth: 1,
                zoomType: 'y'
            },
            credits: {
                enabled: false
            },
            title: {
                text: getWeatherType(wtype) + ' ' + _t('Weather Data'),
                x: -20 //center
            },
            subtitle: {
                text: _t('Location') + ': ' + station_name,
                x: -20
            },
            xAxis: {
                title: {
                    text: ajaxdata.chart_period
                },
                type: 'datetime',
                dateTimeLabelFormats: {// don't display the dummy year
                    month: '%b',
                    year: '%b'
                },
                plotBands: [{
                        from: ajaxdata.wetseason.from,
                        to: ajaxdata.wetseason.to,
                        color: 'rgba(0, 29, 185, 0.3)',
                        label: {
                            text: _t('Wet Season'),
                            style: {
                                color: '#000069',
                                fontSize: '18px'
                            },
                            y: 40
                        }
                    }]
            },
            yAxis: {
                title: {
                    text: wvar_name + ' [' + wvar_unit + ']'
                },
                plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }],
                min: ajaxdata.min,
                max: ajaxdata.max
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'top',
                borderWidth: 1,
                backgroundColor: '#ffffff'
            },
            tooltip: {
                crosshairs: true,
                formatter: function () {
                    var yval = '';
                    if (typeof this.point.low === 'undefined') {
                        yval = this.y + ' ' + wvar_unit;
                    } else
                    {
                        yval = this.point.low + ' ' + wvar_unit + ' - ' + this.point.high + ' ' + wvar_unit;
                    }
                    return '<b>' + this.series.name + '</b><br/>' +
                            Highcharts.dateFormat('%e. %b', this.x) + ': ' + yval;
                }
            },
            series: ajaxdata.series
        });
    }
}
