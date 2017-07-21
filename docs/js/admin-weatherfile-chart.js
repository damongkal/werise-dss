function makeChart() {
    var chart = new WeatherfileChart();
    var wvar = parseInt(jQuery('#chart_wvar').val());
    var wvar_props = getWeatherVarProps(wvar);
    var charttitle = 'Weather Data';
    var tmpseriesdata = [], seriesdata = [];
    var wvar_date = 0;
    var wvar_val = 0;
    for (var i = 0; i < window.seriesdata.length; i++) {
        wvar_date = new Date(window.seriesdata[i].observe_date);
        if (wvar===0) {
            wvar_val = parseFloat(window.seriesdata[i].rainfall);
        }
        if (wvar===1) {
            wvar_val = parseFloat(window.seriesdata[i].min_temperature);
        }
        if (wvar===2) {
            wvar_val = parseFloat(window.seriesdata[i].max_temperature);
        }
        if (wvar===3) {
            wvar_val = parseFloat(window.seriesdata[i].irradiance);
        }
        if (wvar===4) {
            wvar_val = parseFloat(window.seriesdata[i].vapor_pressure);
        }
        if (wvar===5) {
            wvar_val = parseFloat(window.seriesdata[i].mean_wind_speed);
        }
        if (wvar===6) {
            wvar_val = parseFloat(window.seriesdata[i].sunshine_duration);
        }
        tmpseriesdata.push([wvar_date.getTime(),wvar_val]);
    }
    seriesdata.push({data:tmpseriesdata,name:wvar_props[2]});
    chart.callHighCharts(charttitle, seriesdata);
}
function WeatherfileChart()
{
    /**
     *
     * @param {type} seriesdata
     * @returns {undefined}
     */
    this.callHighCharts = function (charttitle,seriesdata) {

        if(seriesdata===false) {
            return;
        }
        var item_chart = '#chart_pr';

        jQuery(item_chart).highcharts({
            chart: {
                type: 'line',
                borderRadius: 10,
                backgroundColor: '#D5FFB5',
                borderWidth: 1,
                zoomType: 'x'
            },
            credits: {
                enabled: false
            },
            title: {
                text: charttitle
            },
            xAxis: {
                title: {
                    text: 'Date'
                },
                type: 'datetime'
            },
            yAxis: {
                min: 0
            },
            legend: {
                layout: 'vertical',
                align: 'center',
                verticalAlign: 'bottom',
                borderWidth: 1,
                backgroundColor: '#ffffff'
            },
            series: seriesdata
        });
        jQuery(item_chart).show();
    };
}
